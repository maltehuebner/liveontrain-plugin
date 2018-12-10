<?php declare(strict_types=1);

namespace Caldera\LiveOnTrainPlugin\PostTypeMeta;

abstract class AbstractJourneyMeta implements JourneyMetaInterface
{
    public function renderConfigField(\WP_Post $post): void
    {
        echo '<p><label for="'.$this->getIdentifier().'">';
        echo $this->getCaption();
        echo '</label>';
        echo '<br />';
        echo '<input type="text" id="'.$this->getIdentifier().'" name="'.$this->getIdentifier().'" value="'.esc_attr($this->load($post, false)).'" />';
        echo '</p>';
    }

    public function load(\WP_Post $post, bool $useDefaultValue = true): string
    {
        $value = get_post_meta($post->ID, $this->getIdentifier(), true);

        if ((!$value || 0 === strlen($value)) && $useDefaultValue) {
            $value = $this->getDefaultValue();
        }

        return $value;
    }

    public function save(\WP_Post $post): void
    {
        $value = sanitize_text_field($_POST[$this->getIdentifier()]);
        update_post_meta($post->ID, $this->getIdentifier(), $value);
    }

    protected function getIdentifier(): string
    {
        $reflectionClass = new \ReflectionClass($this);

        $identifier = strtolower(preg_replace(
                ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
                ["_$1", "_$1_$2"],
                lcfirst($reflectionClass->getShortName())
            )
        );

        $identifier = sprintf('caldera_liveontrain_%s', $identifier);

        return $identifier;
    }

    protected function getCaption(): string
    {
        return $this->getName();
    }

    protected function getName(): string
    {
        $reflectionClass = new \ReflectionClass($this);

        return $reflectionClass->getShortName();
    }

    protected function getDefaultValue(): string
    {
        return '';
    }

    public function execute(\WP_Post $post): void
    {

    }
}
