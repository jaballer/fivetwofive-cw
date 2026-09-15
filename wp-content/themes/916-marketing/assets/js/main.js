/**
 * 916 Marketing — landing page behavior (front page only).
 */
(function () {
  // FAQ accordion: one answer open at a time.
  var items = document.querySelectorAll('.faq-item');
  function setOpen(item, open) {
    item.setAttribute('data-open', String(open));
    item.querySelector('.faq-q').setAttribute('aria-expanded', String(open));
  }
  items.forEach(function (item) {
    item.querySelector('.faq-q').addEventListener('click', function () {
      var wasOpen = item.getAttribute('data-open') === 'true';
      items.forEach(function (i) { setOpen(i, false); });
      setOpen(item, !wasOpen);
    });
  });
})();
