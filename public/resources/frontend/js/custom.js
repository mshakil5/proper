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

    $(document).on('click', '.open-product', function () {
        let productId = $(this).data('id');

        $.ajax({
            url: '/product',
            type: 'GET',
            data: { id: productId },
            success: function (res) {
                $('#productModal .modal-body').html(res.html);
                const modal = new bootstrap.Modal(document.getElementById('productModal'));
                modal.show();
            },
            error: function (err) {
                console.error('Error loading product:', err);
            }
        });
    });
});