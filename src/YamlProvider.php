<?php
namespace Overture\FileSystemProvider;

use Overture\OvertureProviderInterface;

class YamlProvider implements OvertureProviderInterface
{
    /**
     * Get a value for a configuration key
     *
     * @param string $key The configuration key
     *
     * @return string
     */
    public function get($key)
    {
        return 'placeholder';
    }
}
