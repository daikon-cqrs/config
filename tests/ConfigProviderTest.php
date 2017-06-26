<?php

namespace Accordia\Test\PhpConfig;

use Accordia\PhpConfig\ConfigProvider;
use Accordia\PhpConfig\ConfigProviderInterface;
use Accordia\PhpConfig\ConfigProviderParams;
use Accordia\PhpConfig\YamlConfigLoader;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    const CONFIG_FIXTURE = [
        "config" => [
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
            new ConfigProviderParams("config", "global"),
            self::CONFIG_FIXTURE
        );

        $this->assertInstanceOf(ConfigProviderInterface::class, $configProvider);
        $this->assertEquals("127.0.0.1", $configProvider->get("couchdb::host"));
        $this->assertEquals("couchdb", $configProvider->get("config::couchdb::user"));
        $this->assertEquals(self::CONFIG_FIXTURE["config"]["couchdb"], $configProvider->get("couchdb::*"));
    }

    /**
     * @dataProvider configParamsProvider
     */
    public function testGetLoadedConfigValue(array $paramsFixture)
    {
        $configProvider = new ConfigProvider(
            new ConfigProviderParams("config", "couchdb", $paramsFixture),
            self::CONFIG_FIXTURE
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
        $paramsFixture["connections"]["lookup_patterns"][] = "dev.connection.yml";
        $configProvider = new ConfigProvider(
            new ConfigProviderParams("config", "couchdb", $paramsFixture),
            self::CONFIG_FIXTURE
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
                "namespaces" => [
                    "hlx.security" => [
                        __DIR__."/Fixture"
                    ]
                ],
                "lookup_patterns" => [
                    "connection.yml"
                ]
            ]
        ];
        return [ [ $paramsFixture ] ];
    }
}
