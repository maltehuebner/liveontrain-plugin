<?php declare(strict_types=1);

namespace Caldera\LiveOnTrainPlugin\Widget;

use WP_Widget;

class TimeLeftWidget extends WP_Widget
{
    protected $fieldList = [
        'title' => 'Titel',
        'dateTime' => 'Ziel-Zeitpunkt',
        'text' => 'Text',
    ];

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

        echo sprintf($instance['text'], $daysLeft);

        echo $args['after_widget'];
    }

    public function form($instance): void
    {
        foreach ($this->fieldList as $key => $field)
        {
            $value = !empty($instance[$key]) ? $instance[$key] : esc_html__(sprintf('%s neu', $field), 'caldera_journey');

            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id($key)); ?>">
                    <?php esc_attr_e(sprintf('%s:', $field), 'caldera_journey'); ?>
                </label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id($key)); ?>" name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="text" value="<?php echo esc_attr($value); ?>" />
            </p>
            <?php
        }
    }

    public function update($newInstance, $old_instance): array
    {
        $instance = array();

        foreach ($this->fieldList as $key => $field) {
            $instance[$key] = (!empty( $newInstance[$key])) ? sanitize_text_field($newInstance[$key]) : '';
        }

        return $instance;
    }
}
