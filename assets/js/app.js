document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar')?.classList.toggle('show');
    document.getElementById('sidebarOverlay')?.classList.toggle('show');
});
document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
    document.getElementById('sidebar')?.classList.remove('show');
    document.getElementById('sidebarOverlay')?.classList.remove('show');
});

document.querySelectorAll('[data-queue-action]').forEach(btn => {
    btn.addEventListener('click', function () {
        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content ||
            document.body.dataset.csrfToken ||
            document.querySelector('input[name="csrf_token"]')?.value ||
            '';

        if (!csrfToken) {
            alert('Session expired. Please refresh the page and try again.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = document.body.dataset.queueActionUrl || (window.APP_BASE || '') + '/queues/action';
        const fields = {
            csrf_token: csrfToken,
            id: this.dataset.id,
            action: this.dataset.queueAction,
            redirect: location.pathname + location.search,
        };
        for (const [k, v] of Object.entries(fields)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = k;
            input.value = v;
            form.appendChild(input);
        }
        document.body.appendChild(form);
        form.submit();
    });
});
