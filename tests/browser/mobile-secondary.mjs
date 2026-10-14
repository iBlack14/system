// Read-only UI regression: native Bootstrap modals, no form submissions.
import assert from 'node:assert/strict';
const { chromium } = await import(process.env.PLAYWRIGHT_MODULE || 'playwright');
const base = process.env.UI_BASE_URL;
assert(base && process.env.UI_EMAIL && process.env.UI_PASSWORD, 'Set UI_BASE_URL, UI_EMAIL and UI_PASSWORD');
const browser = await chromium.launch({args: ['--no-sandbox']});
try {
    const page = await browser.newPage();
    page.setDefaultTimeout(15000);
    await page.goto(new URL('/login', base).href, {waitUntil: 'domcontentloaded'});
    await page.locator('[name=email]').fill(process.env.UI_EMAIL);
    await page.locator('[name=password]').fill(process.env.UI_PASSWORD);
    await Promise.all([page.waitForURL('**/dashboard'), page.locator('button[type=submit]').click()]);
    // Enforce no writes after login, including accidental submissions.
    await page.route(new URL('/**', base).href, route =>
        ['GET', 'HEAD'].includes(route.request().method()) ? route.continue() : route.abort());
    for (const viewport of [{width:320,height:740}, {width:568,height:360}]) {
        await page.setViewportSize(viewport);
        for (const path of ['/ordenestrabajo','/ordenesservicio','/ventas','/cotizaciones']) {
            await page.goto(new URL(path, base).href, {waitUntil:'domcontentloaded'});
            assert.equal(await page.evaluate(() => document.compatMode), 'CSS1Compat', path + ' must use standards mode');
            await page.waitForFunction(() => !!document.querySelector('#listadoTable tbody tr'));
            const ids = await page.locator('.modal').evaluateAll(modals => modals.map(modal => modal.id));
            for (const id of ids) {
                // Layout-only check, not a test of the business workflow that launches it.
                await page.evaluate(id => bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show(), id);
                const modal = page.locator('.modal.show');
                await modal.waitFor();
                await page.waitForTimeout(600);
                const layout = await modal.evaluate(modal => {
                    const body = modal.querySelector('.modal-body');
                    const footer = modal.querySelector('.modal-footer')?.getBoundingClientRect();
                    return {
                        fits: body.scrollWidth <= body.clientWidth,
                        footerVisible: !footer || (footer.top >= 0 && footer.bottom <= innerHeight + 1),
                        labelled: !!document.getElementById(modal.getAttribute('aria-labelledby')),
                        closeLabelled: [...modal.querySelectorAll('.btn-close')].every(button => button.getAttribute('aria-label')),
                        controlsInForm: [...modal.querySelectorAll('input[name], select[name], textarea[name], button[type=submit]')]
                            .every(field => field.form === modal.querySelector('form'))
                    };
                });
                assert.deepEqual(layout, {fits:true, footerVisible:true, labelled:true, closeLabelled:true, controlsInForm:true}, path + ' #' + id + ' at ' + viewport.width);
                await modal.locator('[data-bs-dismiss=modal]').first().click();
                await page.waitForTimeout(500);
                console.log('PASS', viewport.width, path, id);
            }
        }
    }
} finally {
    await browser.close();
}
