<?php

add_action('widgets_init', 'alert_register_widget');
function alert_register_widget() {
    register_widget( 'WidgetAlert');
}

class WidgetAlert extends WP_Widget{

    public function __construct() {
        parent::__construct(
        // widget ID
            'alert_widget',
            // widget name
            __('Alert widget', ' teleconnecteeamu_widget_domain'),
            // widget description
            array( 'description' => __( 'Widget qui affiche des alertes', 'teleconnecteeamu_widget_domain' ), )
        );
    }

    public function widget( $args, $instance ) {
        $view = new Alert();
        $view->alertMain();
    }

    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) )
            $title = $instance[ 'title' ];
        else
            $title = __( 'Default Title', 'teleconnectee_widget_domain' );
        echo '
        <p>
            <label for="'.$this->get_field_id( 'title' ).'">'; _e( 'Title:' ); echo '</label>
            <input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $title ).'" />
        </p>';
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
}