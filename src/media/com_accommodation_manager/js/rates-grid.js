document.addEventListener('DOMContentLoaded', function () {
  var MOBILE_BREAKPOINT = 768;
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
        var offset = periodWidth + 'px';
        typologyHeader.style.left = offset;

        var typologyCells = table.querySelectorAll('.rates-grid__typology');
        typologyCells.forEach(function (cell) {
          cell.style.left = offset;
        });
      };

      updateStickyOffset();
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

    // ── Mobile cards: transform table to period-grouped cards ──
    var isMultiRoom = wrapper.dataset.layout === 'multi-room';
    var cardsContainer = null;

    function buildCards() {
      if (cardsContainer) {
        return;
      }

      cardsContainer = document.createElement('div');
      cardsContainer.className = 'rates-cards';

      if (isMultiRoom) {
        buildMultiRoomCards();
      } else {
        buildSingleRoomCards();
      }

      wrapper.parentNode.insertBefore(cardsContainer, wrapper.nextSibling);
    }

    // Multi-room: period → rooms → typologies per room
    function buildMultiRoomCards() {
      var headers = table.querySelectorAll('thead th');
      var roomNames = [];

      for (var i = 2; i < headers.length; i++) {
        roomNames.push(headers[i].textContent.trim());
      }

      var bodyRows = table.querySelectorAll('tbody .rates-grid__row');
      var periodGroups = [];
      var currentPeriod = null;

      bodyRows.forEach(function (row) {
        var periodCell = row.querySelector('.rates-grid__period');

        if (periodCell) {
          currentPeriod = {
            title: periodCell.querySelector('.rates-grid__period-title').textContent.trim(),
            dates: periodCell.querySelector('.rates-grid__period-dates').textContent.trim(),
            typologyRows: []
          };
          periodGroups.push(currentPeriod);
        }

        var typologyCell = row.querySelector('.rates-grid__typology');
        var rateCells = row.querySelectorAll('.rates-grid__rate');
        var rateValues = [];

        rateCells.forEach(function (cell) {
          rateValues.push(cell.innerHTML.trim());
        });

        if (currentPeriod) {
          currentPeriod.typologyRows.push({
            name: typologyCell.textContent.trim(),
            rates: rateValues
          });
        }
      });

      periodGroups.forEach(function (group, groupIndex) {
        var card = createPeriodCard(group, groupIndex);

        roomNames.forEach(function (roomName, roomIndex) {
          var roomDiv = document.createElement('div');
          roomDiv.className = 'rates-card__room';

          var nameDiv = document.createElement('div');
          nameDiv.className = 'rates-card__room-name';
          nameDiv.textContent = roomName;
          roomDiv.appendChild(nameDiv);

          var ratesDiv = document.createElement('div');
          ratesDiv.className = 'rates-card__room-rates';

          group.typologyRows.forEach(function (typRow) {
            var rateRow = document.createElement('div');
            rateRow.className = 'rates-card__rate-row';
            rateRow.innerHTML =
              '<span class="rates-card__typology">' + typRow.name + '</span>' +
              '<span class="rates-card__rate">' + typRow.rates[roomIndex] + '</span>';
            ratesDiv.appendChild(rateRow);
          });

          roomDiv.appendChild(ratesDiv);
          card.appendChild(roomDiv);
        });

        cardsContainer.appendChild(card);
      });
    }

    // Single-room: period → typologies with rates (no room grouping)
    function buildSingleRoomCards() {
      var headers = table.querySelectorAll('thead th');
      var typologyNames = [];

      for (var i = 1; i < headers.length; i++) {
        typologyNames.push(headers[i].textContent.trim());
      }

      var bodyRows = table.querySelectorAll('tbody .rates-grid__row');

      bodyRows.forEach(function (row, rowIndex) {
        var periodCell = row.querySelector('.rates-grid__period');

        if (!periodCell) {
          return;
        }

        var group = {
          title: periodCell.querySelector('.rates-grid__period-title').textContent.trim(),
          dates: periodCell.querySelector('.rates-grid__period-dates').textContent.trim()
        };

        var card = createPeriodCard(group, rowIndex);

        var ratesDiv = document.createElement('div');
        ratesDiv.className = 'rates-card__room-rates';

        var rateCells = row.querySelectorAll('.rates-grid__rate');

        rateCells.forEach(function (cell, cellIndex) {
          var rateRow = document.createElement('div');
          rateRow.className = 'rates-card__rate-row';
          rateRow.innerHTML =
            '<span class="rates-card__typology">' + typologyNames[cellIndex] + '</span>' +
            '<span class="rates-card__rate">' + cell.innerHTML.trim() + '</span>';
          ratesDiv.appendChild(rateRow);
        });

        card.appendChild(ratesDiv);
        cardsContainer.appendChild(card);
      });
    }

    function createPeriodCard(group, index) {
      var card = document.createElement('div');
      card.className = 'rates-card' + (index % 2 ? ' rates-card--alt' : '');

      var periodDiv = document.createElement('div');
      periodDiv.className = 'rates-card__period';
      periodDiv.innerHTML =
        '<span class="rates-card__period-title">' + group.title + '</span>' +
        '<span class="rates-card__period-dates">' + group.dates + '</span>';
      card.appendChild(periodDiv);

      return card;
    }

    function toggleView() {
      var isMobile = window.innerWidth <= MOBILE_BREAKPOINT;

      if (isMobile) {
        buildCards();
        wrapper.style.display = 'none';
        cardsContainer.style.display = '';
      } else {
        wrapper.style.display = '';

        if (cardsContainer) {
          cardsContainer.style.display = 'none';
        }

        // Recalculate desktop-only features
        if (periodHeader && typologyHeader) {
          var periodWidth = periodHeader.offsetWidth;
          var offset = periodWidth + 'px';
          typologyHeader.style.left = offset;
          table.querySelectorAll('.rates-grid__typology').forEach(function (cell) {
            cell.style.left = offset;
          });
        }

        checkScroll();
      }
    }

    // Debounced resize handler for view toggle + desktop recalculations
    var resizeTimer;

    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(toggleView, 150);
    });

    // Initial state
    toggleView();
  });
});
