document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('delete-modal');
    const form  = document.getElementById('delete-form');
    const cancel = document.getElementById('delete-cancel');

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
