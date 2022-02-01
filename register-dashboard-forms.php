<?php

/**
 * Helper function - checks if role exists
 * @param $role
 * @return bool
 */
function role_exists( $role ) {
  if( ! empty( $role ) ) {
    return $GLOBALS['wp_roles']->is_role( $role );
  }
  
  return false;
}

add_action( 'admin_post_create_user', 'dashboard_create_user' );

/**
 * Handles the creation of a user from the dashboard
 * @return void
 */
function dashboard_create_user() {
    echo '<pre>';
    echo print_r($_POST);
    echo '</pre>';

    $login = $_POST['login'];
    $password = $_POST['password1'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($_POST['password1'] != $_POST['password2']) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Les mots de passe ne correspondent pas.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }
    
    if ($_POST['login'] == '') {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le login est vide.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if ($_POST['email'] == '') {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'L\'email est vide.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if ($_POST['role'] == '') {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le rôle est vide.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if (username_exists($login)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le login est déjà utilisé.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if (email_exists($email)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'L\'email est déjà utilisé.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if (is_email($email) == false) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'L\'email n\'est pas valide.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    if (!role_exists($role)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => 'Le rôle n\'existe pas.'
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    $id = wp_create_user( $login, $password, $email );

    if (is_wp_error($id)) {
        wp_redirect(
            add_query_arg(
                array(
                    'message' => 'danger',
                    'message_content' => $id->get_error_message()
                ),
                home_url('/users/create')
            )
        );

        exit;
    }

    wp_redirect(
        add_query_arg(
            array(
                'message' => 'success',
                'message_content' => 'User created successfully'
            ),
            home_url('/users/create')
        )
    );
}