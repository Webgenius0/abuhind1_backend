import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/**
 * Small auto-dismissing toast notification.
 */
window.adminToast = function (message, type = 'success') {
    if (window.toastr) {
        type === 'error' ? toastr.error(message) : toastr.success(message);
        return;
    }

    const tones = {
        success: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/70 dark:text-emerald-300',
        error: 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/50 dark:bg-red-950/70 dark:text-red-300',
    };
    const icons = {
        success: 'fa-circle-check',
        error: 'fa-circle-exclamation',
    };

    const toast = document.createElement('div');
    toast.className =
        'fixed bottom-6 right-6 z-[80] flex items-start gap-3 max-w-sm px-4 py-3 rounded-2xl shadow-dropdown border ' +
        (tones[type] ?? tones.success) +
        ' transition-all duration-300 translate-y-2 opacity-0';
    toast.innerHTML =
        '<i class="fa-solid ' +
        (icons[type] ?? icons.success) +
        ' mt-0.5"></i><p class="text-sm font-medium"></p>';
    toast.querySelector('p').textContent = message;

    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
    });

    setTimeout(() => {
        toast.classList.add('translate-y-2', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
};

/**
 * Promise-based confirmation dialog styled like the rest of the admin panel.
 */
window.adminConfirm = function ({
    title = 'Are you sure?',
    text = '',
    confirmText = '',
    cancelText = 'Cancel',
    danger = false,
} = {}) {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            title,
            text,
            icon: danger ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: danger ? '#ef4444' : '#3085d6',
            cancelButtonColor: '#64748b',
            confirmButtonText: confirmText || (danger ? 'Yes, Delete' : 'Yes, Change'),
            cancelButtonText: cancelText,
        }).then((result) => result.isConfirmed);
    }

    return Promise.resolve(window.confirm(text || title));
};

/**
 * JSON fetch wrapper with CSRF + error normalization.
 */
window.adminRequest = async function (url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers ?? {}),
        },
    });

    let data = null;
    try {
        data = await response.json();
    } catch {
        // Non-JSON responses fall through to the generic error below.
    }

    if (!response.ok) {
        throw new Error(data?.message || 'Something went wrong. Please try again.');
    }

    return data ?? {};
};

/**
 * Toggle an item's active status from a switch button.
 */
window.adminToggleStatus = async function (button, url) {
    const confirmed = await window.adminConfirm({
        title: 'Change status?',
        text: 'Are you sure you want to toggle this status?',
        confirmText: 'Yes, Change',
    });

    if (!confirmed) return;

    button.disabled = true;

    try {
        const data = await window.adminRequest(url);
        window.adminToast(data.message || 'Status updated successfully.');
        window.setTimeout(() => window.location.reload(), 600);
    } catch (error) {
        button.disabled = false;
        window.adminToast(error.message, 'error');
    }
};

/**
 * Toggle an item status from a checkbox styled with the reference switch markup.
 */
window.adminToggleSwitch = async function (checkbox, url) {
    const confirmed = await window.adminConfirm({
        title: 'Change status?',
        text: 'Are you sure you want to toggle this status?',
        confirmText: 'Yes, Change',
    });

    if (!confirmed) {
        checkbox.checked = !checkbox.checked;
        return;
    }

    checkbox.disabled = true;

    try {
        const data = await window.adminRequest(url, { method: 'POST' });
        window.adminToast(data.message || 'Status updated successfully.');
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable) {
            const dt = window.jQuery('.dataTable, table.table').filter(function () {
                return window.jQuery.fn.DataTable.isDataTable(this);
            }).first();
            if (dt.length) {
                dt.DataTable().ajax.reload(null, false);
                return;
            }
        }
        window.setTimeout(() => window.location.reload(), 600);
    } catch (error) {
        checkbox.checked = !checkbox.checked;
        window.adminToast(error.message, 'error');
    } finally {
        checkbox.disabled = false;
    }
};

/**
 * Delete an item behind a confirmation dialog.
 */
window.adminDelete = async function (url, text = 'This action cannot be undone.') {
    const confirmed = await window.adminConfirm({
        title: 'Are you sure?',
        text,
        confirmText: 'Yes, Delete',
        danger: true,
    });

    if (!confirmed) return;

    try {
        const data = await window.adminRequest(url, { method: 'DELETE' });
        window.adminToast(data.message || 'Deleted successfully.');
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable) {
            const dt = window.jQuery('.dataTable, table.table').filter(function () {
                return window.jQuery.fn.DataTable.isDataTable(this);
            }).first();
            if (dt.length) {
                dt.DataTable().ajax.reload(null, false);
                return;
            }
        }
        window.setTimeout(() => window.location.reload(), 600);
    } catch (error) {
        window.adminToast(error.message, 'error');
    }
};
