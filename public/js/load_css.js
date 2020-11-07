for (let i = 0; i <= load_srcs.length; i++) {
    if (load_srcs[i])
        generateLink(load_srcs[i]);
}
function generateLink(src) {
    const head = document.getElementsByTagName('HEAD')[0];

    let link = document.createElement('link');
    link.rel = 'stylesheet';

    link.type = 'text/css';

    link.href = src;

    head.appendChild(link);
}
