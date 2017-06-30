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
    private const FIX_SETTINGS = [
        "couchdb" => [
            "host" => "127.0.0.1",
            "port" => 5984,
            "transport" => "https",
            "user" => "couchdb",
            "password" => "couchdb",
        ]
    ];

    /**
     * @dataProvider paramsProvider
     */
    public function testGetArrayLoadedValue(array $params)
    {
        $configProvider = new ConfigProvider(new ConfigProviderParams($params));

        $this->assertEquals("127.0.0.1", $configProvider->get("settings.couchdb.host"));
        $this->assertEquals("couchdb", $configProvider->get("settings.couchdb.user"));
        $this->assertEquals(self::FIX_SETTINGS["couchdb"], $configProvider->get("settings.couchdb"));
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testGetYamlLoaderConfig(array $params)
    {
        $configProvider = new ConfigProvider(new ConfigProviderParams($params));

        $expected = require __DIR__."/Fixture/expectation_1.php";
        $this->assertEquals(
            $expected["connections"],
            $configProvider->get("connections")
        );
        $this->assertEquals(
            $expected["connections"]["hlx-security"],
            $configProvider->get("connections.hlx-security")
        );
    }

    /**
     * @dataProvider paramsProvider
     */
    public function testGetCascadedYamlLoaderConfig(array $params)
    {
        $params["connections"]["sources"][] = "dev.connection.yml";
        $configProvider = new ConfigProvider(new ConfigProviderParams($params));

        $expected = require __DIR__."/Fixture/expectation_2.php";
        $this->assertEquals(
            $expected["connections"],
            $configProvider->get("connections")
        );
        $this->assertEquals(
            $expected["connections"]["hlx-security"],
            $configProvider->get("connections.hlx-security")
        );
    }

    public function paramsProvider()
    {
        $paramsFixture = [
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "sources" => self::FIX_SETTINGS,
            ],
            "connections" => [
                "loader" => YamlConfigLoader::class,
                "schema" => __DIR__."/connection_schema.php", // not implemented yet
                "locations" => [
                    __DIR__."/Fixture",
                ],
                "sources" => [
                    "connection.yml",
                ],
            ]
        ];
        return [ [ $paramsFixture ] ];
    }
}
