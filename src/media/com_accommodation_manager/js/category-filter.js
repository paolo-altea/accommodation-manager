document.addEventListener('DOMContentLoaded', function () {
  var container = document.querySelector('.rooms-category-filter');

  if (!container) {
    return;
  }

  var buttons = container.querySelectorAll('[data-filter-category]');
  var rooms = document.querySelectorAll('.com-accommodation-manager-rooms .room-item');

  buttons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var categoryId = btn.getAttribute('data-filter-category');

      // Update active state
      buttons.forEach(function (b) {
        b.classList.remove('active');
      });
      btn.classList.add('active');

      // Filter rooms
      rooms.forEach(function (room) {
        if (categoryId === '' || room.getAttribute('data-category') === categoryId) {
          room.style.display = '';
        } else {
          room.style.display = 'none';
        }
      });
    });
  });
});
