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

window.showLoader = function(message = 'Loading...') {
    let loader = document.getElementById('loadingModal');
    if (!loader) {
        const loaderHTML = `
        <div id="loadingModal" style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); display: none; align-items: center; justify-content: center; z-index: 99999;">
            <div style="background: white; padding: 40px 50px; border-radius: 16px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 320px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #ff8a00; margin-bottom: 20px; display: block;"></i>
                <h4 style="margin: 0 0 8px; color: #1a1a1a;">${message}</h4>
                <p style="margin: 0; color: #777; font-size: 14px;">Please wait...</p>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', loaderHTML);
        loader = document.getElementById('loadingModal');
    }
    loader.style.display = 'flex';
};

window.hideLoader = function() {
    const loader = document.getElementById('loadingModal');
    if (loader) $(loader).fadeOut(300);
};


$(window).on('scroll', function () {
    if ($(this).scrollTop() > 300) {
        $('#backToTop').addClass('show');
    } else {
        $('#backToTop').removeClass('show');
    }
});

$(document).on('click', '#backToTop', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('html, body').animate({ scrollTop: 0 }, 600);
    return false;
});