<?php
/**
 * Fonction qui est reliée au bloc
 * Affiche le bouton d'abonnement aux notifications
 * @return string Return la vue du formulaire
 */
function subscription_render_callback() {
    $view = new UserView();
    if(is_page()){
        return $view->displayButtonSubscription();
    }
}

/**
 * Bloc qui permet de créer une alerte
 */
function block_subscription() {
    wp_register_script(
        'subscription-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/subscription', array(
        'editor_script' => 'subscription-script',
        'render_callback' => 'subscription_render_callback'
    ));
}
add_action( 'init', 'block_subscription' );