<?php declare(strict_types=1);

namespace Caldera\LiveOnTrainPlugin\PostTypeMeta;

interface JourneyMetaInterface
{
    public function renderConfigField(\WP_Post $post): void;
    public function load(\WP_Post $post, bool $useDefaultValue = true): ?string;
    public function save(\WP_Post $post): void;
    public function execute(\WP_Post $post): void;
}
