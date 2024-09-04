<?php
// Registrar el shortcode [libreria_pnl]
function libreria_pnl_shortcode($atts) {
    // Asegurarse de que solo se ejecute en una página de sesión
    if (get_post_type() != 'sesion') {
        return '<p>Este shortcode solo funciona en sesiones específicas.</p>';
    }

    // Obtener el JSON desde el campo personalizado
    $votaciones_json = get_field('votaciones_json');

    // Decodificar el JSON en un array asociativo
    $votaciones = json_decode($votaciones_json, true);

    // Definir los iconos de los partidos (sustituye 'url_del_icono' con la URL real del icono)
    $partidos_iconos = array(
        'PSOE' => 'url_del_icono_psoe',
        'PP' => 'url_del_icono_pp',
        'VOX' => 'url_del_icono_vox',
        'Convocatoria por Asturias' => 'url_del_icono_convocatoria',
        'Grupo Mixto' => 'url_del_icono_grupo_mixto'
    );

    // Comenzar la salida del contenido
    $output = '';

    // Verificar que el JSON fue decodificado correctamente y contiene datos
    if ($votaciones && is_array($votaciones)) {
        $output .= '<ul class="votaciones">';
        
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
            $output .= '<li style="list-style: none; margin-bottom: 10px;">';
            $output .= '<img src="' . esc_url($partido_icono) . '" alt="' . esc_attr($partido) . '" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 10px;" />';
            $output .= '<span style="background-color:' . esc_attr($color) . '; width: 10px; height: 10px; display:inline-block; border-radius:50%; vertical-align: middle; margin-right: 5px;"></span>';
            $output .= '<span>' . esc_html($partido) . '</span>';
            $output .= '</li>';
        }
        
        $output .= '</ul>';
    } else {
        $output .= '<p>No se encontraron votaciones registradas.</p>';
    }

    return $output;
}

// Hook para registrar el shortcode
add_shortcode('libreria_pnl', 'libreria_pnl_shortcode');