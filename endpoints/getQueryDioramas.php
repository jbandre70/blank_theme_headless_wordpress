<?php
    function getQueryDioramas(WP_REST_Request $request) {
        $args = [
            'posts_per_page' => 100,
            'meta_key' => 'completed_on',
            'orderby' => 'meta_value',
            'order' => 'desc',
            'cat' => '14'
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

    function orderCat() {
        register_rest_route( 'dioramas/v1', '/pid/dioramapage', [
            'methods' => 'GET',
            'callback' => 'getQueryDioramas',
        ]);
    }
    add_action('rest_api_init', 'orderCat');

    function api_remove_extra_data($data, $post, $context) {
        if ($context !== 'view' || is_wp_error($data) ) {
            unset($data->data['link']);
            unset($data->data['date_gmt']);
            unset($data->data['date_gmt']);
            unset($data->data['modified']);
            unset($data->data['modified_gmt']);
            unset($data->data['type']);
            unset($data->data['author']);
            unset($data->data['status']);
            unset($data->data['featured_media']);
            unset($data->data['comment_status']);
            unset($data->data['ping_status']);
            unset($data->data['sticky']);
            unset($data->data['sticky']);
            unset($data->data['template']);
            $data->remove_link('collection');
            $data->remove_link('self');
            $data->remove_link('about');
            $data->remove_link('author');
            $data->remove_link('replies');
            $data->remove_link('version-history');
            $data->remove_link('https://api.w.org/featuredmedia');
            $data->remove_link('https://api.w.org/attachment');
            $data->remove_link('https://api.w.org/term');
            $data->remove_link('curies');
            $data->remove_link('predecessor-version');
            return $data;
        }
    }
    add_filter('rest_prepare_post', 'api_remove_extra_data', 12, 3);
