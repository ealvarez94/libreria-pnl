<?php
// Función para registrar la taxonomía 'Áreas'
function crear_taxonomia_areas() {
    $labels = array(
        'name'              => _x('Áreas', 'taxonomy general name'),
        'singular_name'     => _x('Área', 'taxonomy singular name'),
        'search_items'      => __('Buscar Áreas'),
        'all_items'         => __('Todas las Áreas'),
        'parent_item'       => __('Área Superior'),
        'parent_item_colon' => __('Área Superior:'),
        'edit_item'         => __('Editar Área'),
        'update_item'       => __('Actualizar Área'),
        'add_new_item'      => __('Añadir Nueva Área'),
        'new_item_name'     => __('Nombre de Nueva Área'),
        'menu_name'         => __('Áreas'),
    );

    $args = array(
        'hierarchical'      => true, // Como categorías
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'area'),
    );

    register_taxonomy('area', array('sesion'), $args);
}
add_action('init', 'crear_taxonomia_areas', 0);
