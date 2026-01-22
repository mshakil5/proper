$(function () {
    // Category Filter
    $('#categoryPills .pill').on('click', function () {
        $('#categoryPills .pill').removeClass('active');
        $(this).addClass('active');
        var filter = $(this).data('filter');
        filterProducts(filter);
        if ($(window).width() < 576) $('html,body').animate({ scrollTop: $('.cards-grid').offset().top - 70 }, 300);
    });

    // Search Functionality
    $('#productSearch').on('keyup', function () {
        var searchTerm = $(this).val().toLowerCase();
        filterProductsBySearch(searchTerm);
    });

    // Clear Search
    $('#clearSearch').on('click', function () {
        $('#productSearch').val('');
        $('#cardsGrid .food-card').show();
        $('#searchResults').html('');
    });

    function filterProducts(filter) {
        if (filter === 'all') {
            $('#cardsGrid .food-card').show();
        } else {
            $('#cardsGrid .food-card').each(function () {
                var cat = $(this).data('cat');
                if (cat === filter) $(this).show(); else $(this).hide();
            });
        }
    }

    function filterProductsBySearch(searchTerm) {
        let visibleCount = 0;
        
        $('#cardsGrid .food-card').each(function () {
            var title = $(this).find('.card-title').text().toLowerCase();
            var desc = $(this).find('.card-desc').text().toLowerCase();
            var category = $(this).find('.tag').text().toLowerCase();
            
            if (title.includes(searchTerm) || desc.includes(searchTerm) || category.includes(searchTerm)) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        if (visibleCount === 0 && searchTerm !== '') {
            $('#searchResults').html('<div class="search-no-results"><i class="fas fa-search"></i><p>No products found</p></div>');
        } else {
            $('#searchResults').html('');
        }
    }
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