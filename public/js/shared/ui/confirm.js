/**
 * shared/ui/confirm.js
 * Centralized SweetAlert2 dialog helpers.
 *
 * Usage:
 *   import { confirmDelete } from '../../shared/ui/confirm.js';
 *
 *   const ok = await confirmDelete('Nama Pekerjaan');
 *   if (ok) { // proceed }
 */

import Swal from 'https://cdn.jsdelivr.net/npm/sweetalert2@11/+esm';

// -------------------------------------------------------------------
// Base Swal instance styled to match the app's design tokens
// -------------------------------------------------------------------
export const AppSwal = Swal.mixin({
    customClass: {
        popup:          'app-swal-popup',
        title:          'app-swal-title',
        htmlContainer:  'app-swal-html',
        confirmButton:  'app-swal-confirm',
        cancelButton:   'app-swal-cancel',
        icon:           'app-swal-icon',
    },
    buttonsStyling: false,   // ← We supply our own Tailwind classes
    reverseButtons:  true,   // Cancel on left, Confirm on right
});

// -------------------------------------------------------------------
// confirmDelete(label)
// Shows a blocking "Are you sure?" dialog for a destructive action.
// Returns true if user clicked the Confirm (red) button.
// -------------------------------------------------------------------
export async function confirmDelete(label = 'item ini') {
    const result = await AppSwal.fire({
        icon:               'warning',
        title:              'Hapus Pekerjaan?',
        html:               `Data <strong>${label}</strong> beserta semua rincian AHS di dalamnya akan dihapus secara permanen.`,
        showCancelButton:   true,
        confirmButtonText:  'Ya, Hapus',
        cancelButtonText:   'Batal',
        focusCancel:        true,       // Default focus on "Batal" for safety
    });
    return result.isConfirmed;
}

// -------------------------------------------------------------------
// confirmDeleteCategory(label)
// For deleting an entire category row + its sub-items.
// -------------------------------------------------------------------
export async function confirmDeleteCategory(label = 'kategori ini') {
    const result = await AppSwal.fire({
        icon:               'warning',
        title:              'Hapus Kategori?',
        html:               `Semua item pekerjaan di dalam <strong>${label}</strong> akan dihapus dari RAB.`,
        showCancelButton:   true,
        confirmButtonText:  'Ya, Hapus',
        cancelButtonText:   'Batal',
        focusCancel:        true,
    });
    return result.isConfirmed;
}

// -------------------------------------------------------------------
// confirmInfo(title, html)
// A neutral info dialog (no destructive action).
// -------------------------------------------------------------------
export async function confirmInfo(title, html) {
    const result = await AppSwal.fire({
        icon:               'info',
        title,
        html,
        showCancelButton:   true,
        confirmButtonText:  'Lanjutkan',
        cancelButtonText:   'Batal',
        focusConfirm:       true,
    });
    return result.isConfirmed;
}

// -------------------------------------------------------------------
// confirmAction(title, html, confirmText)
// A generic warning dialog.
// -------------------------------------------------------------------
export async function confirmAction(title, html, confirmText = 'Ya, Lanjutkan') {
    const result = await AppSwal.fire({
        icon:               'warning',
        title,
        html,
        showCancelButton:   true,
        confirmButtonText:  confirmText,
        cancelButtonText:   'Batal',
        focusCancel:        true,
    });
    return result.isConfirmed;
}
