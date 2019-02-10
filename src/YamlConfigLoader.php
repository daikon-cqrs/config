<?php
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Config;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;

final class YamlConfigLoader implements ConfigLoaderInterface
{
    /** @var Yaml */
    private $yamlParser;

    /** @var Finder */
    private $finder;

    public function __construct(Yaml $yamlParser = null)
    {
        $this->yamlParser = $yamlParser ?? new Yaml;
        $this->finder = $finder ?? new Finder;
    }

    /**
     * @param array $locations
     * @param array $sources
     * @return mixed[]
     */
    public function load(array $locations, array $sources): array
    {
        return array_reduce(
            $locations,
            /**
             * @param array $config
             * @param string|string[] $location
             * @return array
             */
            function (array $config, $location) use ($sources): array {
                return array_replace_recursive($config, $this->loadSources($location, $sources));
            },
            []
        );
    }

    /**
     * @param string|string[] $location
     * @param string[] $sources
     * @return mixed[]
     */
    private function loadSources($location, array $sources): array
    {
        $location = array_filter((array) $location, 'is_dir');

        if (empty($location) || empty($sources)) {
            return [];
        }

        return array_reduce($sources, function (array $config, string $source) use ($location): array {
            foreach ($this->finder
                ->create()
                ->files()
                ->ignoreUnreadableDirs()
                ->in($location)
                ->name($source)
                ->sortByName() as $file) {
                if ($file->isReadable()) {
                    $config = array_replace_recursive($config, $this->yamlParser->parse($file->getContents()) ?? []);
                };
            }
            return $config;
        }, []);
    }
}
