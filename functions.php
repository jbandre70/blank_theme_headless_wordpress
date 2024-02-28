<?php
add_theme_support('post-thumbnails');
add_theme_support('title-tag');

include('endpoints/getQueryDioramas.php');
include('endpoints/getQueryDioramasHome.php');
include('endpoints/getQueryPhotos.php');
include('endpoints/getQueryPost.php');


add_action('init', function () {
    header("Access-Control-Allow-Origin: *");
});

add_action("rest_api_init", function ()
{
    register_rest_route("options", "/all", [
        "methods" => "GET",
        "callback" => "acf_options_route",
    ]);
});


add_filter('rest_endpoints', function ($endpoints)
{
    if (!isset($endpoints['/wp/v2/posts'])) {
        return $endpoints;
    }
    $endpoints['/wp/v2/posts'][0]['args']['per_page']['default'] = 100;
    return $endpoints;
});

function register_rest_images()
{
    register_rest_field(
        ['post', 'page'],
        'fimg_url',
        [
            'get_callback' => 'get_rest_featured_image',
            'update_callback' => null,
            'schema' => null
        ]
    );
}

function prefix_register_book_route()
{
    register_rest_route('my-namespace/v1', '/photostv/(?P<id>\d+)', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'prefix_get_book',
    ]);
}
add_action('rest_api_init', 'prefix_register_book_route');
function get_rest_featured_image($object, $field_name, $request)
{
    if ($object['featured_media']) {
        $img = wp_get_attachment_image_src($object['featured_media'], 'medium_large');
        return $img[0];
    }
    return false;
}

add_action('rest_api_init', 'register_rest_images');

function register_main_menu()
{
    register_nav_menu('jeandiorama_menu', __('jeandiorama'));
    register_nav_menu('footer_menu', __('footer jeandiorama'));
}

add_action('init', 'register_main_menu');

function wp_custom_post_type()
{
    $labels = [
        'name' => 'Photos pour Masonry',
        'singular_name' => 'Photo pour Masonry',
        'menu_name' => __('Photos auto'),
        'all_items' => __('Toutes les Photos auto'),
        'view_item' => __('Voir les sPhotos auto'),
        'add_new_item' => __('Ajouter une nouvelle Photos auto'),
        'add_new' => __('Ajouter'),
        'edit_item' => __('Editer la Photos auto'),
        'update_item' => __('Modifier la Photos auto'),
        'search_items' => __('Rechercher une Photos auto'),
        'not_found' => __('Non trouvée'),
        'not_found_in_trash' => __('Non trouvée dans la corbeille')
    ];
    $args = [
        'label' => __('Photos auto'),
        'description' => __('Tous sur Photos auto'),
        'labels' => $labels,
        'supports' => ['title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'],
        'show_in_rest' => true,
        'hierarchical' => false,
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'photos-tv']
    ];
    register_post_type('photostv', $args);
}
add_action('init', 'wp_custom_post_type', 0);
