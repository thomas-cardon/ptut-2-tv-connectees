<?php

/**
 * Fonction qui est reliée au bloc
 * Créer une alerte via un formulaire
 * @return string Return la vue du formulaire
 */
function admininterface_render_callback() {
    $alert = new Alert();
    $model = new AlertManager();
    $view = new ViewAlert();
    if(is_page()){
        $years = $model->getCodeYear();
        $groups = $model->getCodeGroup();
        $halfgroups = $model->getCodeHalfgroup();

        $alert->createAlert();
        return $view->displayAlertCreationForm($years, $groups, $halfgroups);
    }
}

/**
 * Bloc qui permet de créer une alerte
 */
function block_admininterface() {
    wp_register_script(
        'admininterface-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/admin-interface', array(
        'editor_script' => 'admininterface-script',
        'render_callback' => 'admininterface_render_callback'
    ));
}
add_action( 'init', 'block_admininterface' );