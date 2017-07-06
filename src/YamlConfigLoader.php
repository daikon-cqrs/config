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
        return array_reduce($locations, function (array $config, string $location) use ($sources): array {
            if (substr($location, -1) !== '/') {
                $location .= '/';
            }
            return array_replace_recursive($config, $this->loadSources($location, $sources));
        }, []);
    }

    private function loadSources($location, array $sources)
    {
        return array_reduce($sources, function (array $config, string $source) use ($location): array {
            $filepath = $location.$source;
            if (is_readable($filepath)) {
                return array_replace_recursive($config, $this->yamlParser->parse(file_get_contents($filepath)));
            }
            return $config;
        }, []);
    }
}
