<?php declare(strict_types=1);

/*
Plugin Name: Live on train
Plugin URI: https://liveontrain.maltehuebner.com
Description: Description to follow.
Author: Malte Hübner
Version: 0.1
Author URI: https://maltehuebner.de/
*/

use Caldera\LiveOnTrainPlugin\Autoloader;
use Caldera\LiveOnTrainPlugin\PostType\JourneyPostType;

require_once __DIR__.'/Autoloader.php';
spl_autoload_register([new Autoloader(), 'autoload']);

new JourneyPostType();

add_action('widgets_init', function(){
    register_widget(Caldera\LiveOnTrainPlugin\Widget\TimeLeftWidget::class);
});
