<?php
// Registrar el shortcode [libreria_pnl]
function libreria_pnl_shortcode($atts) {
    // Obtener la URL del directorio del plugin
    $plugin_dir_url = plugin_dir_url(__FILE__);

    // Definir los iconos de los partidos (las rutas relativas a la carpeta `img`)
    $partidos_iconos = array(
        'PSOE' => $plugin_dir_url . 'img/psoe.png',
        'PP' => $plugin_dir_url . 'img/pp.png',
        'VOX' => $plugin_dir_url . 'img/vox.png',
        'Convocatoria por Asturias' => $plugin_dir_url . 'img/convocatoria.png',
        'Grupo Mixto' => $plugin_dir_url . 'img/grupo_mixto.png'
    );

    // Obtener todas las sesiones
    $args = array(
        'post_type' => 'sesion',
        'posts_per_page' => -1
    );
    $sesiones = new WP_Query($args);

    // Comenzar la salida del contenido
    $output = '<table class="tabla-votaciones" style="width:100%; border-collapse: collapse;">';
    $output .= '<thead><tr>';
    $output .= '<th style="border: 1px solid #ddd; padding: 8px;">Sesión</th>';
    $output .= '<th style="border: 1px solid #ddd; padding: 8px;">Votaciones</th>';
    $output .= '<th style="border: 1px solid #ddd; padding: 8px;">Descargar</th>';
    $output .= '</tr></thead>';
    $output .= '<tbody>';

    if ($sesiones->have_posts()) {
        while ($sesiones->have_posts()) {
            $sesiones->the_post();

            // Obtener el nombre de la sesión
            $nombre_sesion = get_the_title();

            // Obtener el JSON desde el campo personalizado
            $votaciones_json = get_field('votaciones_json');

            // Decodificar el JSON en un array asociativo
            $votaciones = json_decode($votaciones_json, true);

            // Obtener el PDF
            $pdf_url = get_field('archivo_pdf');

            // Comenzar la fila de la tabla
            $output .= '<tr>';
            $output .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($nombre_sesion) . '</td>';

            // Crear la columna de votaciones
            $output .= '<td style="border: 1px solid #ddd; padding: 8px;">';
            if ($votaciones && is_array($votaciones)) {
                foreach ($votaciones as $votacion) {
                    $partido = $votacion['partido'];
                    $votacion_resultado = $votacion['votacion'];
                    $color = '';

                    // Asignar color según el resultado de la votación
                    switch ($votacion_resultado) {
                        case 'A Favor':
                            $color = 'green';
                            break;
                        case 'En Contra':
                            $color = 'red';
                            break;
                        case 'Abstención':
                            $color = 'white';
                            break;
                    }

                    // Obtener el icono del partido
                    $partido_icono = isset($partidos_iconos[$partido]) ? $partidos_iconos[$partido] : '';

                    // Agregar la información al contenido de salida
                    $output .= '<img src="' . esc_url($partido_icono) . '" alt="' . esc_attr($partido) . '" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 10px;" />';
                    $output .= '<span style="background-color:' . esc_attr($color) . '; width: 10px; height: 10px; display:inline-block; border-radius:50%; vertical-align: middle; margin-right: 5px;"></span>';
                }
            } else {
                $output .= 'No se encontraron votaciones registradas.';
            }
            $output .= '</td>';

            // Crear la columna del botón de descarga
            if ($pdf_url) {
                $output .= '<td style="border: 1px solid #ddd; padding: 8px;"><a href="' . esc_url($pdf_url) . '" class="boton-descargar" style="background-color: #0073aa; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">Descargar</a></td>';
            } else {
                $output .= '<td style="border: 1px solid #ddd; padding: 8px;">No disponible</td>';
            }

            $output .= '</tr>';
        }
    } else {
        $output .= '<tr><td colspan="3" style="border: 1px solid #ddd; padding: 8px;">No se encontraron sesiones.</td></tr>';
    }

    $output .= '</tbody></table>';

    // Restaurar el post original
    wp_reset_postdata();

    return $output;
}

// Hook para registrar el shortcode
add_shortcode('libreria_pnl', 'libreria_pnl_shortcode');
