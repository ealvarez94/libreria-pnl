<?php
/**
 * Plugin Name: Librería de PNLs
 * Description: Plugin para gestionar PDFs de sesiones de la Junta General y sus votaciones.
 * Version: 1.0.0
 * Author: Enol Alvarez Molinuevo
 * Text Domain: https://www.introvisual.com/
 */

// Hook para registrar el Custom Post Type
add_action('init', 'libreria_pnls_register_post_type');

function libreria_pnls_register_post_type() {
    $labels = array(
        'name' => __('Sesiones', 'libreria-pnls'),
        'singular_name' => __('Sesión', 'libreria-pnls'),
        'menu_name' => __('Librería de PNLs', 'libreria-pnls'),
        'name_admin_bar' => __('Sesión', 'libreria-pnls'),
        'add_new' => __('Añadir Nueva', 'libreria-pnls'),
        'add_new_item' => __('Añadir Nueva Sesión', 'libreria-pnls'),
        'new_item' => __('Nueva Sesión', 'libreria-pnls'),
        'edit_item' => __('Editar Sesión', 'libreria-pnls'),
        'view_item' => __('Ver Sesión', 'libreria-pnls'),
        'all_items' => __('Todas las Sesiones', 'libreria-pnls'),
        'search_items' => __('Buscar Sesiones', 'libreria-pnls'),
        'not_found' => __('No se encontraron sesiones.', 'libreria-pnls'),
        'not_found_in_trash' => __('No se encontraron sesiones en la papelera.', 'libreria-pnls')
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-media-document',
        'supports' => array('title', 'editor', 'custom-fields', 'thumbnail'),
        'rewrite' => array('slug' => 'sesiones'),
    );

    register_post_type('sesion', $args);
}

// Incluir el archivo de la taxonomía
require_once plugin_dir_path(__FILE__) . 'taxonomias.php';

// Incluir el archivo del shortcode
require_once plugin_dir_path(__FILE__) . 'shortcode-libreria-pnl.php';

// Incluir el archivo de personalizaciones
require_once plugin_dir_path(__FILE__) . 'customizations.php';

// Función para encolar scripts y estilos
function libreria_pnl_enqueue_scripts() {
    // Encolar jQuery (viene por defecto en WordPress)
    wp_enqueue_script('jquery');

    // Encolar el script personalizado
    wp_enqueue_script('libreria-pnl-script', plugin_dir_url(__FILE__) . 'js/libreria-pnl.js', array('jquery'), null, true);
}

// Hook para encolar scripts en el frontend
add_action('wp_enqueue_scripts', 'libreria_pnl_enqueue_scripts');

add_action('wp_enqueue_scripts', 'libreria_pnl_enqueue_scripts');