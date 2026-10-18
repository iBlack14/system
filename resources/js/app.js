import './bootstrap';

window.convertHeicToJpeg = async (options) => {
    const { default: heic2any } = await import('heic2any');
    return heic2any(options);
};
