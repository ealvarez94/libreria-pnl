jQuery(document).ready(function($) {
    if (jQuery('body').hasClass('home')) {
        console.log("Estamos en la pagina de inicio");

        jQuery('.libreria-pnl-shortcode-container').each(function() {
            var container = jQuery(this);

            if (container.find('.mostrar-resultados-btn').length) {
                container.find('.mostrar-resultados-btn').on('click', function() {
                    console.log("Boton de resultados clicado en home");

                    jQuery(this).closest('.resultado-votacion').toggleClass('show');
                    console.log("Clase 'show' alternada en home");
                });
            }

            if (container.find('.area-button').length) {
                container.find('.area-button').on('click', function() {
                    var areaSlug = jQuery(this).data('area');

                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'filtrar_sesiones_por_area',
                            area_slug: areaSlug,
                        },
                        success: function(response) {
                            console.log("Respuesta del AJAX:", response);
                            jQuery('#sesiones-filtradas').html(response);
                        }
                    });
                });
            }
        });
    }
});