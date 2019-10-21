<?php
function code_account_render_callback() {
    $myAccount = new MyAccount();
    $model = new CodeAdeManager();
    $view = new ViewMyAccount();
    $current_user = wp_get_current_user();
    if(is_page() && in_array('etudiant', $current_user->roles)){
        $myAccount->modifyMyCodes();
        $years = $model->getCodeYear();
        $groups = $model->getCodeGroup();
        $halfgroups = $model->getCodeHalfgroup();
        return $view->displayModifyMyCodes($current_user, $years, $groups, $halfgroups);
    }
}

function block_code_account() {
    wp_register_script(
        'code_account-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/code-account', array(
        'editor_script' => 'code_account-script',
        'render_callback' => 'code_account_render_callback'
    ));
}
add_action( 'init', 'block_code_account' );