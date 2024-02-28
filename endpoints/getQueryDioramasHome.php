<?php
    function getQueryDioramasHome(WP_REST_Request $request) {
        $page = $request['page'];
        $args = [
            'posts_per_page' => 6,
            'paged' => $page,
            'meta_key' => 'completed_on',
            'orderby' => 'meta_value',
            'order' => 'desc',
            'cat' => 14,
            'post_type' => 'post'
        ];
        $query = new WP_Query($args);

        if (empty($query->posts)) {
            return new WP_Error('no_posts', __('No post found'), ['status' => 404]);
        }

        $max_pages = $query->max_num_pages;
        $total = $query->found_posts;
        $posts = $query->posts;
        $controller = new WP_REST_Posts_Controller('post');

        foreach ($posts as $post) {
            $response = $controller->prepare_item_for_response($post, $request);
            $data[] = $controller->prepare_response_for_collection($response);
        }
        $response = new WP_REST_Response($data, 200);
        $response->header('X-WP-Total', $total);
        $response->header('X-WP-TotalPages', $max_pages);

        return $response;
    }


    function dioramaHome() {
        register_rest_route( 'dioramas/v1', '/pid/dioramahome/(?P<page>\d+)', [
            'methods' => 'GET',
            'callback' => 'getQueryDioramasHome',
            'args' => [
                'page' => [
                    'required' => true
                ]
            ]
        ]);
    }
    add_action('rest_api_init', 'dioramaHome');