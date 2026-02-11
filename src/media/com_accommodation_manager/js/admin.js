window.Joomla = window.Joomla || {};

(function (window, Joomla) {
    Joomla.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[ id ];

        if (!cb) return false;

        while (true) {
            cbx = f[ 'cb' + i ];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField   = document.createElement('input');

        inputField.type  = 'hidden';
        inputField.name  = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        Joomla.submitform(task);

        return false;
    };

    // Handle [data-task] buttons: set hidden task field before form submit
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-task]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var taskField = document.getElementById('task');
                if (taskField) {
                    taskField.value = this.getAttribute('data-task');
                }
            });
        });
    });
})(window, Joomla);