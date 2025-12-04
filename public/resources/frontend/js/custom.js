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