<?php

namespace Controllers;

use Models\Information;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class InformationRestController extends WP_REST_Controller
{
    /**
     * Constructor for the REST controller
     */
    public function __construct()
    {
        $this->namespace = 'amu-ecran-connectee/v1';
        $this->rest_base = 'information';
    }

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_items'),
                    'args'                => $this->get_collection_params(),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'create_item'),
                    'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_items($request)
    {
        // Get all the currently registered informations
        $information = new Information();

        // Try to grab offset and limit from parameters
        $offset = $request->get_param('offset');
        $limit = $request->get_param('limit');

        return new WP_REST_Response($information->getList($offset, $limit), 200);
    }

    /**
     * Retrieves the query params for collections.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params()
    {
        $query_params = [];

        $query_params['limit'] = array(
            'description' => __('Maximum number of information to fetch'),
            'type'        => 'integer',
            'default'     => 25,
        );

        $query_params['offset'] = array(
            'description' => __('Offset of the information to fetch'),
            'type'        => 'integer',
            'default'     => 0,
        );

        return apply_filters( 'rest_user_collection_params', $query_params );
    }

    /**
     * Creates a single information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_item($request)
    {
        return new WP_REST_Response('pute', 200);
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request)
    {
        $current_user = wp_get_current_user();
        return in_array("administrator", $current_user->roles);
    }



    /**
     * Checks if a given request has access to create an information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function create_item_permissions_check($request)
    {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Retrieves the user's schema, conforming to JSON Schema.
     *
     * @return array Item schema data.
     */
    public function get_item_schema() {
        if ($this->schema)
        {
            return $this->add_additional_fields_schema($this->schema);
        }

        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'user',
            'type'       => 'object',
            'properties' => array(
                'id'                 => array(
                    'description' => __( 'Unique identifier for the user.' ),
                    'type'        => 'integer',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'username'           => array(
                    'description' => __( 'Login name for the user.' ),
                    'type'        => 'string',
                    'context'     => array( 'edit' ),
                    'required'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => array( $this, 'check_username' ),
                    ),
                ),
                'name'               => array(
                    'description' => __( 'Display name for the user.' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'first_name'         => array(
                    'description' => __( 'First name for the user.' ),
                    'type'        => 'string',
                    'context'     => array( 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'last_name'          => array(
                    'description' => __( 'Last name for the user.' ),
                    'type'        => 'string',
                    'context'     => array( 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'email'              => array(
                    'description' => __( 'The email address for the user.' ),
                    'type'        => 'string',
                    'format'      => 'email',
                    'context'     => array( 'edit' ),
                    'required'    => true,
                ),
                'url'                => array(
                    'description' => __( 'URL of the user.' ),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'context'     => array( 'embed', 'view', 'edit' ),
                ),
                'description'        => array(
                    'description' => __( 'Description of the user.' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                ),
                'link'               => array(
                    'description' => __( 'Author URL of the user.' ),
                    'type'        => 'string',
                    'format'      => 'uri',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'locale'             => array(
                    'description' => __( 'Locale for the user.' ),
                    'type'        => 'string',
                    'enum'        => array_merge( array( '', 'en_US' ), get_available_languages() ),
                    'context'     => array( 'edit' ),
                ),
                'nickname'           => array(
                    'description' => __( 'The nickname for the user.' ),
                    'type'        => 'string',
                    'context'     => array( 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                ),
                'slug'               => array(
                    'description' => __( 'An alphanumeric identifier for the user.' ),
                    'type'        => 'string',
                    'context'     => array( 'embed', 'view', 'edit' ),
                    'arg_options' => array(
                        'sanitize_callback' => array( $this, 'sanitize_slug' ),
                    ),
                ),
                'registered_date'    => array(
                    'description' => __( 'Registration date for the user.' ),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => array( 'edit' ),
                    'readonly'    => true,
                ),
                'roles'              => array(
                    'description' => __( 'Roles assigned to the user.' ),
                    'type'        => 'array',
                    'items'       => array(
                        'type' => 'string',
                    ),
                    'context'     => array( 'edit' ),
                ),
                'password'           => array(
                    'description' => __( 'Password for the user (never included).' ),
                    'type'        => 'string',
                    'context'     => array(), // Password is never displayed.
                    'required'    => true,
                    'arg_options' => array(
                        'sanitize_callback' => array( $this, 'check_user_password' ),
                    ),
                ),
                'capabilities'       => array(
                    'description' => __( 'All capabilities assigned to the user.' ),
                    'type'        => 'object',
                    'context'     => array( 'edit' ),
                    'readonly'    => true,
                ),
                'extra_capabilities' => array(
                    'description' => __( 'Any extra capabilities assigned to the user.' ),
                    'type'        => 'object',
                    'context'     => array( 'edit' ),
                    'readonly'    => true,
                ),
            ),
        );

        $this->schema = $schema;

        return $this->add_additional_fields_schema( $this->schema );
    }
}
