// visitors_bucket_modal.js
$(document).on('click', '.js-checkout', function (e) {
    e.preventDefault();

    const el = this;
    const $el = $(el);

    const url = $el.data('url');
    const target = $el.data('target') || '_self';

    if (!url) return;

    if (target === '_blank') {
        window.open(url, '_blank', 'noopener');
    } else {
        window.location.href = url;
    }
});
