<?php
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

use Symfony\Component\Yaml\Yaml;

final class YamlConfigLoader implements ConfigLoaderInterface
{
    private $yamlParser;

    public function __construct(Yaml $yamlParser = null)
    {
        $this->yamlParser = $yamlParser ?? new Yaml;
    }

    public function load(array $locations, array $sources): array
    {
        $loadedConfigs = [];
        foreach ($locations as $location) {
            if (substr($location, -1) !== '/') {
                $location .= '/';
            }
            foreach ($sources as $source) {
                $filepath = $location.$source;
                if (is_readable($filepath)) {
                    $loadedConfigs = array_replace_recursive(
                        $loadedConfigs,
                        $this->yamlParser->parse(file_get_contents($filepath))
                    );
                }
            }
        }
        return $loadedConfigs;
    }

    public function serialize(array $config): string
    {
        // not implemented yet
        return '';
    }

    public function deserialize(string $serializedConfig): array
    {
        // not implemented yet
        return [];
    }
}
