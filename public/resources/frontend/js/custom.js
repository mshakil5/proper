$(function () {
    $('#categoryPills .pill').on('click', function () {
        $('#categoryPills .pill').removeClass('active');
        $(this).addClass('active');
        var filter = $(this).data('filter');
        if (filter === 'all') {
            $('#cardsGrid .food-card').show();
        } else {
            $('#cardsGrid .food-card').each(function () {
                var cat = $(this).data('cat');
                if (cat === filter) $(this).show(); else $(this).hide();
            });
        }
        if ($(window).width() < 576) $('html,body').animate({ scrollTop: $('.cards-grid').offset().top - 70 }, 300);
    });
});

// Success
function showSuccess(msg) {
    Swal.fire({
        icon: 'success',
        title: msg ?? 'Success!',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        allowOutsideClick: false,
        didOpen: (toast) => {
            toast.parentElement.style.zIndex = '99999';
            toast.style.zIndex = '99999';
        }
    });
}

// Error
function showError(msg) {
    Swal.fire({
        icon: 'error',
        title: msg ?? 'Something went wrong!',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        allowOutsideClick: false,
        didOpen: (toast) => {
            toast.parentElement.style.zIndex = '99999';
            toast.style.zIndex = '99999';
        }
    });
}

window.showConfirm = function (msg, callback) {
    Swal.fire({
        title: msg ?? 'Are you sure?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        reverseButtons: true,
        customClass: {
            popup: 'swal-confirm-popup',
            confirmButton: 'swal-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
};