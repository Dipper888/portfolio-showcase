// Check for empty form fields before submitting to PHP.
const form = document.querySelector('form[action]');

if (form)
{
    form.addEventListener('submit', function (event) {
        const fields = form.querySelectorAll('input[name], select[name], textarea[name]');

        const hasEmpty = Array.from(fields).some(function (field) {
            if (field.type === 'file') {
                return field.files.length === 0;
            }
            return field.value.trim() === '';
        });

        const previousError = form.querySelector('.js-error');
        if (previousError) previousError.remove();

        if (hasEmpty)
        {
            event.preventDefault();

            const error = document.createElement('div');
            error.className = 'alert alert-danger js-error';
            error.setAttribute('role', 'alert');
            error.textContent = 'Please fill in all fields.';
            form.prepend(error);
        }
    });
}