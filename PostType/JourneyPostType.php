<?php declare(strict_types=1);

namespace Caldera\LiveOnTrainPlugin\PostType;

use Caldera\LiveOnTrainPlugin\PostTypeMeta\JourneyMetaInterface;
use WP_Post;
use WP_Query;

class JourneyPostType
{
    protected $postTypeMetas = [];

    public function __construct()
    {
        $this->initPostType();
        $this->initFilter();
        $this->initTaxonomy();
        $this->initMetaBoxes();
        $this->initSaveHook();
    }

    protected function initPostType(): void
    {
        $this->postTypeMetas = [
            new \Caldera\LiveOnTrainPlugin\PostTypeMeta\ArrivalStation(),
            new \Caldera\LiveOnTrainPlugin\PostTypeMeta\DepartureStation(),
        ];

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

    }

    protected function initTaxonomy(): void
    {
        add_action('init', function(): void
        {
            register_taxonomy( 'line', 'caldera_journey', [
                'hierarchical' => false,
                'label' => __('Linien'),
                'query_var' => 'line',
                'rewrite' => ['slug' => 'line']
            ]);
        });
    }

    protected function initFilter(): void
    {
        add_filter('the_title', function(string $title = null, $id = null): string
        {
            /** @var WP_Post $post */
            $post = get_post($id);

            if ('caldera_journey' === $post->post_type && !$post->post_title) {
                $postDateTime = new \DateTime($post->post_date);
                $departureStation = get_post_meta($post->ID, 'caldera_liveontrain_departure_station', true);
                $arrivalStation = get_post_meta($post->ID, 'caldera_liveontrain_arrival_station', true);

                $title = sprintf('%s–%s', $departureStation, $arrivalStation);
            }

            return $title;
        }, 10, 2 );
    }

    protected function initMetaBoxes(): void
    {
        add_action('add_meta_boxes', function(): void
        {
            add_meta_box('caldera_journey_meta','Fahrtdetails', function(WP_Post $post): void
            {
                global $postTypeMetas;

                wp_nonce_field('caldera_journey_meta','caldera_journey_meta_nonce');

                /** @var JourneyMetaInterface $postTypeMeta */
                foreach ($postTypeMetas as $postTypeMeta) {
                    $postTypeMeta->renderConfigField($post);
                }
            },['caldera_journey']);
        });
    }

    protected function initSaveHook(): void
    {

        add_action('save_post', function(int $postId): ?int
        {
            global $postTypeMetas;

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

            /** @var JourneyMetaInterface $postTypeMeta */
            foreach ($postTypeMetas as $postTypeMeta) {
                $postTypeMeta->save($post);
            }

            return null;
        });
    }
}
