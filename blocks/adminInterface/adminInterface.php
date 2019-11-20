<?php

/**
 * Fonction qui est reliée au bloc
 * Affiche l'interface de gestion pour l'admin
 * @return string Return la vue du formulaire
 */
function admininterface_render_callback()
{
    if (is_page()) {
        $admin = new Admin();
        return $admin->changeMyWebsite();
    }
}

/**
 * Bloc qui permet de gérer le site
 */
function block_admininterface()
{
    wp_register_script(
        'admininterface-script',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-data')
    );

    register_block_type('tvconnecteeamu/admin-interface', array(
        'editor_script' => 'admininterface-script',
        'render_callback' => 'admininterface_render_callback'
    ));
}

add_action('init', 'block_admininterface');