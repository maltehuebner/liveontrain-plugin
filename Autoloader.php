<?php declare(strict_types=1);

namespace Caldera\LiveOnTrainPlugin;

class Autoloader
{
    const PREFIX = 'Caldera\\LiveOnTrainPlugin\\';

    public function autoload(string $classname): bool
    {
        $prefixLength = strlen(self::PREFIX);

        if (strncmp(self::PREFIX, $classname, $prefixLength) !== 0) {
            return false;
        }

        $relativeClassname = substr($classname, $prefixLength);

        $filename = sprintf('%s/%s.php', __DIR__, str_replace('\\', '/', $relativeClassname));

        require_once $filename;

        return true;
    }
}
