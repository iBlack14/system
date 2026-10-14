// Optional browser check. Install Playwright outside the app; never submit CRUD forms.
// UI_BASE_URL, UI_EMAIL, UI_PASSWORD required. PLAYWRIGHT_MODULE may be an absolute path.
import assert from 'node:assert/strict';
const { chromium } = await import(process.env.PLAYWRIGHT_MODULE || 'playwright');
const base = process.env.UI_BASE_URL;
assert(base && process.env.UI_EMAIL && process.env.UI_PASSWORD, 'Set UI_BASE_URL, UI_EMAIL and UI_PASSWORD');
const browser = await chromium.launch({ args: ['--no-sandbox'] });
try {
    const page = await browser.newPage({ viewport: { width: 390, height: 844 } });
    page.setDefaultTimeout(10000);
    await page.goto(new URL('/login', base).href, { waitUntil: 'domcontentloaded' });
    await page.locator('[name=email]').fill(process.env.UI_EMAIL);
    await page.locator('[name=password]').fill(process.env.UI_PASSWORD);
    await Promise.all([page.waitForURL('**/dashboard'), page.locator('button[type=submit]').click()]);
    const paths = await page.locator('.sidebar-menu a[href]').evaluateAll(links =>
        [...new Set(links.filter(link => !link.href.endsWith('#')).map(link => new URL(link.href).pathname))]);
    let errors = [];
    page.on('pageerror', error => errors.push(error.message));
    page.on('dialog', async dialog => { errors.push(dialog.message()); await dialog.dismiss(); });
    for (const path of paths) {
        errors = [];
        const response = await page.goto(new URL(path, base).href, { waitUntil: 'domcontentloaded' });
        assert.equal(response.status(), 200, path);
        await page.waitForFunction(() => {
            const table = document.getElementById('listadoTable');
            return !table || !!table.querySelector('tbody tr');
        });
        assert.equal(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), true, path + ' overflows');
        if (await page.locator('#btnCreate').isVisible()) {
            await page.locator('#btnCreate').click();
            const modal = page.locator('.modal.show');
            await modal.waitFor();
            await page.waitForTimeout(400); // Bootstrap transition and existing focus timer.
            assert.equal(await modal.evaluate(el => el.scrollWidth <= innerWidth), true, path + ' modal overflows');
            await modal.locator('[data-bs-dismiss=modal]').first().click();
            await page.waitForTimeout(400);
        }
        assert.deepEqual(errors, [], path + ' browser errors');
        console.log('PASS', path);
    }
    for (const width of [320, 390, 768, 1366]) {
        await page.setViewportSize({ width, height: 844 });
        await page.goto(new URL('/reportes/pagos', base).href, { waitUntil: 'domcontentloaded' });
        const detail = page.locator('[data-bs-toggle=collapse]').first();
        if (await detail.count()) {
            await detail.click();
            await page.waitForTimeout(400);
            assert.equal(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), true, 'Payment detail overflows at ' + width);
            await detail.click();
            await page.waitForTimeout(400);
            assert.equal(await page.locator('tr.collapse').first().isVisible(), false);
        }
        console.log('PASS payment details', width);
    }
} finally {
    await browser.close();
}
