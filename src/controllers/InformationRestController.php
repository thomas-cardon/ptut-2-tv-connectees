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
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'                => $this->get_collection_params(),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args'                =>  array(
                        'title'   => array(
                            'type'        => 'string',
                            'required'    => true,
                            'description' => __('Information title'),
                        ),
                        'content'   => array(
                            'type'        => 'string',
                            'required'    => true,
                            'description' => __('Information content'),
                        ),
                        'expiration-date'   => array(
                            'type'        => 'string',
                            'required'    => true,
                            'description' => __('Information expiration date'),
                        ),
                    ),
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
        // Get an instance of the information manager
        $information = new Information();

        // Try to grab offset and limit from parameters
        $offset = $request->get_param('offset');
        $limit = $request->get_param('limit');

        return new WP_REST_Response($information->getList($offset, $limit), 200);
    }

    /**
     * Creates a single information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_item($request)
    {
        // Get an instance of the information manager
        $information = new Information();

        // Set information data
        $information->setTitle($request->get_param('title'));
        $information->setAuthor(wp_get_current_user()->ID);
        $information->setCreationDate(date('Y-m-d'));
        $information->setExpirationDate($request->get_param('expiration-date'));
        $information->setAdminId(null);
        $information->setContent($request->get_param('content'));
        $information->setType('text');

        // Try to insert the information
        if ($information->insert())
            return new WP_REST_Response(null, 200);

        return new WP_REST_Response('Could not insert the information', 400);
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
     * Checks if a given request has access to create an information.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
     */
    public function create_item_permissions_check($request)
    {
        return $this->get_items_permissions_check($request);
    }
}
