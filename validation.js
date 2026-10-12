// Check for empty form fields before submitting to PHP.
const form = document.querySelector('form[action]');

if (form)
{
    form.addEventListener('submit', function (event) {
        const fields = form.querySelectorAll('input[name], select[name], textarea[name]');

        const hasEmpty = Array.from(fields).some(function (field) {
            if (field.type === 'hidden' || field.dataset.optional === 'true') return false;
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

// Update project search results without reloading the dashboard.
const searchInput = document.querySelector('input[name="keyword"]');
const searchResults = document.querySelector('.project-results');

if (searchInput && searchResults)
{
    let latestSearch = 0;

    searchInput.addEventListener('input', function () {
        const searchNumber = ++latestSearch;
        const keyword = encodeURIComponent(searchInput.value);

        fetch('dashboard.php?ajax=1&keyword=' + keyword)
            .then(function (response) {
                if (!response.ok) throw new Error('Search failed');
                return response.text();
            })
            .then(function (html) {
                // Ignore older AJAX responses to show only the latest search results.
                if (searchNumber === latestSearch) {
                    searchResults.innerHTML = html;
                }
            });
    });
}