<?php

function code_ade_render_callback() {
    $codeAde = new CodeAde();
    $model = new CodeAdeManager();
    $view = new CodeAdeView();
    if(is_page()){
        $badCodesYears = $model->codeNotBound(0);
        $badCodesGroups = $model->codeNotBound(1);
        $badCodesHalfgroups = $model->codeNotBound(2);
        $badCodes = [$badCodesYears, $badCodesGroups, $badCodesHalfgroups];

        $string = "";
        if(sizeof($badCodesYears) < 1 || sizeof($badCodesGroups) < 1 || sizeof($badCodesHalfgroups) < 1){
            $string .= $view->displayUnregisteredCode($badCodes);
        }
        $codeAde->insertCode();
        return $view->displayFormAddCode().$string;
    }
}

function block_code_ade() {
    wp_register_script(
        'code_ade-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-code', array(
        'editor_script' => 'code_ade-script',
        'render_callback' => 'code_ade_render_callback'
    ));
}
add_action( 'init', 'block_code_ade' );