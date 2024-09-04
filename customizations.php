<?php
// Añadir una clase al body en el panel de administración
function mpp_add_custom_admin_body_class($classes) {
    // Verifica si estamos en una página de edición de publicaciones
    if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] === 'sesion') {
        $classes .= ' post-type-sesion'; // Añade la clase al body
    }
    return $classes;
}
add_filter('admin_body_class', 'mpp_add_custom_admin_body_class');

// Enqueue el CSS en el panel de administración
function mpp_enqueue_admin_styles() {
    // Asegúrate de que la ruta al archivo CSS sea correcta
    wp_enqueue_style('mpp-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css');
}
add_action('admin_enqueue_scripts', 'mpp_enqueue_admin_styles');
