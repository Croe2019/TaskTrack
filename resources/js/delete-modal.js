document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('delete-modal');
    const form  = document.getElementById('delete-form');
    const cancel = document.getElementById('delete-cancel');
    const deleteButtons = document.querySelectorAll('.js-delete');

    deleteButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();

            window.dispatchEvent(
                new CustomEvent('open-delete-modal', {
                    detail: { action: button.dataset.action },
                }),
            );
        });
    });

    if (!modal) return;

    window.addEventListener('open-delete-modal', (e) => {
        form.action = e.detail.action;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    cancel.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
});
