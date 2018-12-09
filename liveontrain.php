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
        $departureStation = get_post_meta($post->ID, 'departure_station', true);
        $arrivalStation = get_post_meta($post->ID, 'arrival_station', true);

        $title = sprintf('%s: %s–%s', $postDateTime->format('d. F Y'), $departureStation, $arrivalStation);
    }

    return $title;
}, 10, 2 );

add_action('add_meta_boxes', function() {
    add_meta_box('caldera_journey_meta','Fahrtdetails', function(WP_Post $post): void
    {
        wp_nonce_field('caldera_journey_meta','caldera_journey_meta_nonce');

        echo '<p><label for="departure_station">';
        echo 'Abfahrtsbahnhof';
        echo '</label>';
        echo '<br />';
        echo '<input type="text" id="departure_station" name="departure_station" value="'.get_post_meta($post->ID, 'departure_station', true).'" />';
        echo '</p>';

        echo '<p><label for="arrival_station">';
        echo 'Ankunftsbahnhof';
        echo '</label>';
        echo '<br />';
        echo '<input type="text" id="arrival_station" name="arrival_station" value="'.get_post_meta($post->ID, 'arrival_station', true).'" />';
        echo '</p>';

    },['caldera_journey']);
});

add_action('save_post', function(int $postId): ?int
{
    if ( ! isset( $_POST['caldera_journey_meta_nonce'] ) ) {
        return $postId;
    }

    $nonce = $_POST['caldera_journey_meta_nonce'];

    if (!wp_verify_nonce($nonce, 'caldera_journey_meta')) {
        return $postId;
    }

    if ('caldera_journey' === $_POST['post_type'] && !current_user_can('edit_post', $postId)) {
        return $postId;
    }

    $post = get_post($postId);

    update_post_meta($post->ID, 'departure_station', sanitize_text_field($_POST['departure_station']));
    update_post_meta($post->ID, 'arrival_station', sanitize_text_field($_POST['arrival_station']));

    return null;
});
