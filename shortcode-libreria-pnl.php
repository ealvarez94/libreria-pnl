<?php
// Registrar el shortcode [libreria_pnl]
function libreria_pnl_shortcode($atts) {
    // Obtener la URL del directorio del plugin
    $plugin_dir_url = plugin_dir_url(__FILE__);

    // Obtener las áreas disponibles para los botones
    $output = libreria_pnl_get_areas_buttons();

    // Div contenedor para las sesiones que se actualizará dinámicamente
    $output .= '<div id="sesiones-filtradas">';
    $output .= libreria_pnl_get_sesiones(); // Generar el contenido inicial
    $output .= '</div>';

    // Incluir el script de AJAX para el filtrado dinámico
    $output .= '<script>
        jQuery(document).ready(function($) {
            $(".area-button").on("click", function() {
                var areaSlug = $(this).data("area");

                $.ajax({
                    url: "' . admin_url('admin-ajax.php') . '",
                    type: "POST",
                    data: {
                        action: "filtrar_sesiones_por_area",
                        area_slug: areaSlug
                    },
                    success: function(response) {
                        $("#sesiones-filtradas").html(response);
                    }
                });
            });
        });
    </script>';

    return $output;
}

// Hook para registrar el shortcode
add_shortcode('libreria_pnl', 'libreria_pnl_shortcode');

// Función para generar los botones de las áreas
function libreria_pnl_get_areas_buttons() {
    $areas = get_terms(array(
        'taxonomy' => 'area',
        'hide_empty' => false,
    ));

    $output = '<div class="area-buttons" style="margin-bottom: 20px;">';

    $output .= '<button class="area-button" data-area="" style="background-color: #0073aa; color: white; padding: 10px 20px; margin-right: 10px; border: none; cursor: pointer; border-radius: 3px;">Todas</button>';

    foreach ($areas as $area) {
        $output .= '<button class="area-button" data-area="' . esc_attr($area->slug) . '" style="background-color: #0073aa; color: white; padding: 10px 20px; margin-right: 10px; border: none; cursor: pointer; border-radius: 3px;">' . esc_html($area->name) . '</button>';
    }

    $output .= '</div>';

    return $output;
}

// Función para obtener las sesiones en función del área seleccionada
function libreria_pnl_get_sesiones($area_slug = '') {
    $plugin_dir_url = plugin_dir_url(__FILE__);

    $partidos_iconos = array(
        'PSOE' => $plugin_dir_url . 'img/psoe.png',
        'PP' => $plugin_dir_url . 'img/pp.png',
        'VOX' => $plugin_dir_url . 'img/vox.png',
        'Convocatoria por Asturias' => $plugin_dir_url . 'img/convocatoria.png',
        'Grupo Mixto' => $plugin_dir_url . 'img/grupo_mixto.png'
    );

    $args = array(
        'post_type' => 'sesion',
        'posts_per_page' => -1,
    );

    if ($area_slug) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'area',
                'field'    => 'slug',
                'terms'    => $area_slug,
            ),
        );
    }

    $sesiones = new WP_Query($args);

    $output = '<table class="tabla-votaciones" style="width:100%; border-collapse: collapse; margin-top: 20px;">';
    $output .= '<thead><tr>';
    $output .= '<th style="border: 1px solid #ddd; padding: 8px;">Sesión</th>';
    $output .= '<th style="border: 1px solid #ddd; padding: 8px;">Votaciones</th>';
    $output .= '<th style="border: 1px solid #ddd; padding: 8px;">Descargar</th>';
    $output .= '</tr></thead>';
    $output .= '<tbody>';

    if ($sesiones->have_posts()) {
        while ($sesiones->have_posts()) {
            $sesiones->the_post();

            $nombre_sesion = get_the_title();
            $votaciones_json = get_field('votaciones_json');
            $votaciones = json_decode($votaciones_json, true);
            $pdf_url = get_field('documento_pdf');

            $output .= '<tr>';
            $output .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($nombre_sesion) . '</td>';

            $output .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">';
            if ($votaciones && is_array($votaciones)) {
                foreach ($votaciones as $votacion) {
                    $partido = $votacion['partido'];
                    $votacion_resultado = $votacion['votacion'];
                    $color = '';

                    switch ($votacion_resultado) {
                        case 'A Favor':
                            $color = 'green';
                            break;
                        case 'En Contra':
                            $color = 'red';
                            break;
                        case 'Abstención':
                            $color = 'yellow';
                            break;
                    }

                    $partido_icono = isset($partidos_iconos[$partido]) ? $partidos_iconos[$partido] : '';

                    $output .= '<div style="display: inline-block; text-align: center; margin-bottom: 15px; margin-right: 20px;">';
                    $output .= '<img src="' . esc_url($partido_icono) . '" alt="' . esc_attr($partido) . '" style="width: auto; height: 50px; max-width: 150px; display: block; margin: 0 auto 10px auto;" />';
                    $output .= '<div style="background-color:' . esc_attr($color) . '; width: 15px; height: 15px; display:inline-block; border-radius:50%; margin-top: 5px;"></div>';
                    $output .= '</div>';
                }
            } else {
                $output .= 'No se encontraron votaciones registradas.';
            }
            $output .= '</td>';

            if ($pdf_url) {
                $output .= '<td style="border: 1px solid #ddd; padding: 8px;"><a href="' . esc_url($pdf_url) . '" class="icono-descargar" style="text-decoration: none; display: block; text-align: center;"><img src="' . esc_url($plugin_dir_url . 'img/icono-descargar.png') . '" alt="Descargar" style="width: 24px; height: 24px;"></a></td>';
            } else {
                $output .= '<td style="border: 1px solid #ddd; padding: 8px;">No disponible</td>';
            }

            $output .= '</tr>';
        }
    } else {
        $output .= '<tr><td colspan="3" style="border: 1px solid #ddd; padding: 8px;">No se encontraron sesiones.</td></tr>';
    }

    $output .= '</tbody></table>';

    wp_reset_postdata();

    return $output;
}

// Manejar la solicitud AJAX para filtrar sesiones por área
function filtrar_sesiones_por_area_ajax() {
    $area_slug = isset($_POST['area_slug']) ? sanitize_text_field($_POST['area_slug']) : '';
    echo libreria_pnl_get_sesiones($area_slug);
    wp_die();
}

// Hooks para manejar AJAX
add_action('wp_ajax_filtrar_sesiones_por_area', 'filtrar_sesiones_por_area_ajax');
add_action('wp_ajax_nopriv_filtrar_sesiones_por_area', 'filtrar_sesiones_por_area_ajax');