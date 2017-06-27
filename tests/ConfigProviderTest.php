<?php

namespace Daikon\Test\Config;

use Daikon\Config\ConfigProvider;
use Daikon\Config\ConfigProviderInterface;
use Daikon\Config\ConfigProviderParams;
use Daikon\Config\YamlConfigLoader;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    const PRELOADED_CONFIG = [
        "settings" => [
            "couchdb" => [
                "host" => "127.0.0.1",
                "port" => 5984,
                "transport" => "https",
                "user" => "couchdb",
                "password" => "couchdb"
            ]
        ]
    ];

    public function testGetInitialConfigValue()
    {
        $configProvider = new ConfigProvider(
            self::PRELOADED_CONFIG,
            new ConfigProviderParams([], "settings::global")
        );

        $this->assertInstanceOf(ConfigProviderInterface::class, $configProvider);
        $this->assertEquals("127.0.0.1", $configProvider->get("couchdb::host"));
        $this->assertEquals("couchdb", $configProvider->get("settings::couchdb::user"));
        $this->assertEquals(self::PRELOADED_CONFIG["settings"]["couchdb"], $configProvider->get("couchdb::*"));
    }

    /**
     * @dataProvider configParamsProvider
     */
    public function testGetLoadedConfigValue(array $paramsFixture)
    {
        $configProvider = new ConfigProvider(
            self::PRELOADED_CONFIG,
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
    public function testGetLoadedCascadedConfigValue(array $paramsFixture)
    {
        $paramsFixture["connections"]["sources"][] = "dev.connection.yml";
        $configProvider = new ConfigProvider(
            self::PRELOADED_CONFIG,
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
