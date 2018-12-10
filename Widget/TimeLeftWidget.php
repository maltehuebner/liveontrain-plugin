<?php declare(strict_types=1);

namespace Caldera\LiveOnTrainPlugin\Widget;

use WP_Widget;

class TimeLeftWidget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'caldera_journey_widget_timeleft',
            esc_html__('Time Left', 'caldera_journey'), [
                'description' => esc_html__('Zählt die Tage', 'caldera_journey'),
            ]);
    }

    public function widget($args, $instance): void
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $dateTime = new \DateTime($instance['dateTime']);
        $now = new \DateTime();

        $diffInterval = $dateTime->diff($now);
        $diffInterval->format('%a');
        $daysLeft = $diffInterval->format('%a');

        echo sprintf('Noch %d Pendler-Tage von Kiel nach Hamburg.', $daysLeft);

        echo $args['after_widget'];
    }

    public function form($instance): void
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('New title', 'caldera_journey');
        $dateTime = !empty($instance['dateTime']) ? $instance['dateTime'] : esc_html__('New date time', 'caldera_journey');

        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'caldera_journey'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'dateTime')); ?>">
                <?php esc_attr_e('Zeitpunkt:', 'caldera_journey'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dateTime')); ?>" name="<?php echo esc_attr($this->get_field_name('dateTime')); ?>" type="text" value="<?php echo esc_attr($dateTime); ?>" />
        </p>
        <?php
    }

    public function update($newInstance, $old_instance): array
    {
        $instance = array();

        $instance['title'] = (!empty( $newInstance['title'])) ? sanitize_text_field($newInstance['title']) : '';
        $instance['dateTime'] = (!empty( $newInstance['dateTime'])) ? sanitize_text_field($newInstance['dateTime']) : '';

        return $instance;
    }

}
