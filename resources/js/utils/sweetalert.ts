import Swal from 'sweetalert2';

const swalCustom = Swal.mixin({
    customClass: {
        confirmButton: 'px-8 py-3 rounded-xl bg-gray-900 text-white dark:bg-white dark:text-black text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:scale-105 active:scale-95 transition-all mx-2',
        cancelButton: 'px-8 py-3 rounded-xl bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-gray-200 transition-all mx-2',
        popup: 'rounded-[2.5rem] border-none shadow-2xl p-10 dark:bg-[#0f0f0f] dark:text-white',
        title: 'text-2xl font-black uppercase tracking-tight text-gray-900 dark:text-white',
        htmlContainer: 'text-sm font-medium text-gray-500 dark:text-gray-400 mt-4',
    },
    buttonsStyling: false,
});

export const confirmDelete = async (title = 'Are you sure?', text = "You won't be able to revert this!") => {
    const result = await swalCustom.fire({
        title,
        text,
        icon: 'warning',
        iconColor: '#d4af37',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true,
    });

    return result.isConfirmed;
};

export const notifySuccess = (title: string, text: string) => {
    swalCustom.fire({
        title,
        text,
        icon: 'success',
        iconColor: '#10b981',
        timer: 2000,
        showConfirmButton: false,
    });
};

export const notifyError = (title: string, text: string) => {
    swalCustom.fire({
        title,
        text,
        icon: 'error',
        iconColor: '#ef4444',
        confirmButtonText: 'OK',
    });
};

export default swalCustom;
