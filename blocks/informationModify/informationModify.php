<?php

function information_modify_render_callback() {
    $information = new Information();
    $model = new InformationManager();
    $view = new ViewInformation();
    if(is_page()){
        $id = $information->getMyIdUrl();
        $result = $model->getInformationByID($id);
        $title = $result['title'];
        $content = $result['content'];
        $endDate = date('Y-m-d',strtotime($result['end_date']));
        $typeI = $result['type'];
        $information->modifyInformation();
        return $view->displayModifyInformationForm($title,$content,$endDate,$typeI);
    }
}

function block_information_modify() {
    wp_register_script(
        'information_modify-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-information', array(
        'editor_script' => 'information_modify-script',
        'render_callback' => 'information_modify_render_callback'
    ));
}
add_action( 'init', 'block_information_modify' );