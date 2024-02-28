<?php

    function getBlogPostQuery(WP_REST_Request $request) {
        $args = [
            'posts_per_page' => 1,
            'cat' => 15,
            'date_query' => [
                [
                    'column' => 'post_date',
                    'after' => '- 2 days'
                ]
            ]
        ];
        $query = new WP_Query($args);
        if (empty($query->posts)) {
            return new WP_Error('no_posts', __('No post found'), ['status' => 404]);
        }

        $posts = $query->posts;
        $controller = new WP_REST_Posts_Controller('post');

        foreach ($posts as $post) {
            $response = $controller->prepare_item_for_response($post, $request);
            $data[] = $controller->prepare_response_for_collection($response);
        }
        $response = new WP_REST_Response($data, 200);

        return $response;
    }

    function getBlogPost() {
        register_rest_route( 'dioramas/v1', '/pid/blogpost', [
            'methods' => 'GET',
            'callback' => 'getBlogPostQuery',
        ]);
    }
    add_action('rest_api_init', 'getBlogPost');
