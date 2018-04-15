jQuery(document).ready(function($) {
    $('body').on('click', '[data-toggle="dws_collapse"]', function() {
        var target = $(this).data('target');
        $(target).toggle('in');
    });
});