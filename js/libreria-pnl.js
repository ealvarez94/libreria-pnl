jQuery(document).ready(function($) {
    $('.area-button').on('click', function() {
        var areaSlug = $(this).data('area');

        $.ajax({
            url: ajaxurl, // WordPress proporciona la variable ajaxurl por defecto
            type: 'POST',
            data: {
                action: 'filtrar_sesiones_por_area',
                area_slug: areaSlug,
            },
            success: function(response) {
                $('#sesiones-filtradas').html(response);
            }
        });
    });
});
