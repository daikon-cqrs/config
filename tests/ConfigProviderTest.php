<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Daikon\Test\Config;

use Daikon\Config\ArrayConfigLoader;
use Daikon\Config\ConfigProvider;
use Daikon\Config\ConfigProviderInterface;
use Daikon\Config\ConfigProviderParams;
use Daikon\Config\YamlConfigLoader;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    const PRELOADED_CONFIG = [
        "couchdb" => [
            "host" => "127.0.0.1",
            "port" => 5984,
            "transport" => "https",
            "user" => "couchdb",
            "password" => "couchdb"
        ]
    ];

    /**
     * @dataProvider configParamsProvider
     */
    public function testGetArrayLoaderConfig(array $paramsFixture)
    {
        $configProvider = new ConfigProvider(
            new ConfigProviderParams($paramsFixture, "settings::couchdb")
        );

        $this->assertInstanceOf(ConfigProviderInterface::class, $configProvider);
        $this->assertEquals("127.0.0.1", $configProvider->get("couchdb::host"));
        $this->assertEquals("couchdb", $configProvider->get("settings::couchdb::user"));
        $this->assertEquals(self::PRELOADED_CONFIG["couchdb"], $configProvider->get("couchdb::*"));
    }

    /**
     * @dataProvider configParamsProvider
     */
    public function testGetYamlLoaderConfig(array $paramsFixture)
    {
        $configProvider = new ConfigProvider(
            new ConfigProviderParams($paramsFixture, "settings::couchdb")
        );

        $expected = require __DIR__."/Fixture/expectation_1.php";
        $this->assertEquals(
            $expected["connections"],
            $configProvider->get("connections::*::*")
        );
        $this->assertEquals(
            $expected["connections"]["hlx.security"],
            $configProvider->get("connections::hlx.security::*")
        );
    }

    /**
     * @dataProvider configParamsProvider
     */
    public function testGetCascadedYamlLoaderConfig(array $paramsFixture)
    {
        $paramsFixture["connections"]["sources"][] = "dev.connection.yml";
        $configProvider = new ConfigProvider(
            new ConfigProviderParams($paramsFixture, "settings::couchdb")
        );

        $expected = require __DIR__."/Fixture/expectation_2.php";
        $this->assertEquals(
            $expected["connections"],
            $configProvider->get("connections::*::*")
        );
        $this->assertEquals(
            $expected["connections"]["hlx.security"],
            $configProvider->get("connections::hlx.security::*")
        );
    }

    public function configParamsProvider()
    {
        $paramsFixture = [
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "schema" => __DIR__."/connection_schema.php", // not implemented yet
                "sources" => self::PRELOADED_CONFIG
            ],
            "connections" => [
                "loader" => YamlConfigLoader::class,
                "schema" => __DIR__."/connection_schema.php", // not implemented yet
                "locations" => [
                    __DIR__."/Fixture"
                ],
                "sources" => [
                    "connection.yml"
                ]
            ]
        ];
        return [ [ $paramsFixture ] ];
    }
}
