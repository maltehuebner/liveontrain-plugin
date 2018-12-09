<?php declare(strict_types=1);

/*
Plugin Name: Live on train
Plugin URI: https://liveontrain.maltehuebner.com
Description: Description to follow.
Author: Malte Hübner
Version: 0.1
Author URI: https://maltehuebner.de/
*/


add_action('init', function(): void
{
    register_post_type( 'caldera_journey', [
            'labels' => [
                'name' => __( 'Fahrten' ),
                'singular_name' => __( 'Fahrt' )
            ],
            'public' => true,
            'has_archive' => true,
            'supports' => [
                'title',
                'editor',
                'thumbnail',
                'custom-fields',
                'revisions',
            ]
    ]);
});


add_action('pre_get_posts', function(WP_Query $query): WP_Query
{
    if (is_home() && $query->is_main_query()) {
        $postTypes = $query->get('post_type');

        if (!is_array($postTypes)) {
            $postTypes = [$postTypes];
        }

        $postTypes = array_merge($postTypes, ['caldera_journey', 'post']);

        $query->set('post_type', $postTypes);
    }

    return $query;
});

add_action('init', function(): void
{
    register_taxonomy( 'line', 'caldera_journey', [
        'hierarchical' => false,
        'label' => __('Linien'),
        'query_var' => 'line',
        'rewrite' => ['slug' => 'line']
    ]);
});


add_action('init', function(): void
{
    register_taxonomy( 'city', 'caldera_journey', [
        'hierarchical' => false,
        'label' => __('Stadt'),
        'query_var' => 'city',
        'rewrite' => ['slug' => 'city']
    ]);
});


add_filter('the_title', function(string $title = null, $id = null): string
{
    /** @var WP_Post $post */
    $post = get_post($id);

    if ('caldera_journey' === $post->post_type && !$post->post_title) {
        $postDateTime = new \DateTime($post->post_date);

        return $postDateTime->format('d.m.Y');
    }

    return $title;
}, 10, 2 );
