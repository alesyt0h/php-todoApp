// Loads the polyfill if the browser doesn't support Intl.RelativeTimeFormat
if (Intl === void 0 || typeof Intl.RelativeTimeFormat !== 'function') {

    var script = document.createElement('script');

    script.src = webRoot + '/js/rtf-polyfill.js';
    document.write(script.outerHTML);

    script.src = webRoot + '/js/en.json';
    document.write(script.outerHTML);
}