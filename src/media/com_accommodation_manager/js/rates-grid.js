document.addEventListener('DOMContentLoaded', function () {
  var wrappers = document.querySelectorAll('.rates-grid-wrapper');

  wrappers.forEach(function (wrapper) {
    var table = wrapper.querySelector('.rates-grid');

    if (!table) {
      return;
    }

    // ── Zebra striping + period group hover ──
    var rows = table.querySelectorAll('tbody .rates-grid__row');
    var alt = false;
    var groups = [];
    var currentGroup = [];

    rows.forEach(function (row) {
      if (row.querySelector('.rates-grid__period')) {
        if (currentGroup.length) {
          groups.push(currentGroup);
        }
        currentGroup = [];
        alt = !alt;
      }

      if (alt) {
        row.classList.add('rates-grid__row-alt');
      }

      currentGroup.push(row);
    });

    if (currentGroup.length) {
      groups.push(currentGroup);
    }

    // Group hover: highlight all rows in the same period group
    groups.forEach(function (group) {
      group.forEach(function (row) {
        row.addEventListener('mouseenter', function () {
          group.forEach(function (r) { r.classList.add('rates-grid__row-hover'); });
        });
        row.addEventListener('mouseleave', function () {
          group.forEach(function (r) { r.classList.remove('rates-grid__row-hover'); });
        });
      });
    });

    // ── Sticky typology column: calculate left offset from period column width ──
    var periodHeader = table.querySelector('.rates-grid__header-period');
    var typologyHeader = table.querySelector('.rates-grid__header-typology');

    if (periodHeader && typologyHeader) {
      var updateStickyOffset = function () {
        var periodWidth = periodHeader.offsetWidth;
        typologyHeader.style.left = periodWidth + 'px';

        var typologyCells = table.querySelectorAll('.rates-grid__typology');
        typologyCells.forEach(function (cell) {
          cell.style.left = periodWidth + 'px';
        });
      };

      updateStickyOffset();

      // Recalculate on resize
      var resizeTimer;
      window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(updateStickyOffset, 150);
      });
    }

    // ── Scroll hint: detect overflow and scroll position ──
    var checkScroll = function () {
      var isScrollable = wrapper.scrollWidth > wrapper.clientWidth;
      var isEnd = wrapper.scrollLeft + wrapper.clientWidth >= wrapper.scrollWidth - 2;

      wrapper.classList.toggle('is-scrollable', isScrollable);
      wrapper.classList.toggle('is-scrolled-end', isEnd);
    };

    checkScroll();
    wrapper.addEventListener('scroll', checkScroll, { passive: true });
    window.addEventListener('resize', checkScroll);
  });
});
