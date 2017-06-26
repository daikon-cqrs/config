<?php

namespace Daikon\Config;

use Symfony\Component\Yaml\Yaml;

final class YamlConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var Yaml
     */
    private $yamlParser;

    /**
     * @param Yaml|null $yamlParser
     */
    public function __construct(Yaml $yamlParser = null)
    {
        $this->yamlParser = $yamlParser ?? new Yaml;
    }

    /**
     * @param mixed[] $lookup_paths
     * @param string[] $lookup_patterns
     * @return mixed[]
     */
    public function load(array $namespaces, array $lookup_patterns): array
    {
        $loadedNamespaces = [];
        foreach ($namespaces as $namespace => $lookup_paths) {
            $namespaceConfig = [];
            foreach ($lookup_paths as $lookup_path) {
                foreach ($lookup_patterns as $lookup_pattern) {
                    if (substr($lookup_path, -1) !== "/") {
                        $lookup_path .= "/";
                    }
                    $filepath = $lookup_path.$lookup_pattern;
                    if (is_readable($filepath)) {
                        $namespaceConfig = array_replace_recursive(
                            $namespaceConfig,
                            $this->yamlParser::parse(file_get_contents($filepath))
                        );
                    }
                }
            }
            $loadedNamespaces[$namespace] = $namespaceConfig;
        }
        return $loadedNamespaces;
    }

    /**
     * @param mixed[] $config
     * @return string
     */
    public function serialize(array $config): string
    {
        // not implemented yet
        return '';
    }

    /**
     * @param string $serializedConfig
     * @return mixed[]
     */
    public function deserialize(string $serializedConfig): array
    {
        // not implemented yet
        return [];
    }
}
