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
use Daikon\Config\ConfigProviderParams;
use PHPUnit\Framework\TestCase;

final class ConfigProviderParamsTest extends TestCase
{
    public function testHasScope()
    {
        $settings = [
            "core" => [
                "project_name" => "Generic Project",
                "project_version" => "0.4.2",
            ]
        ];
        $provider = new ConfigProviderParams([
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "sources" => $settings,
            ]
        ]);
        $this->assertTrue($provider->hasScope("settings"));
        $this->assertFalse($provider->hasScope("foobar"));
    }

    public function testGetLoader()
    {
        $settings = [
            "core" => [
                "project_name" => "Generic Project",
                "project_version" => "0.4.2",
            ]
        ];
        $provider = new ConfigProviderParams([
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "sources" => $settings,
            ]
        ]);
        $this->assertInstanceOf(ArrayConfigLoader::class, $provider->getLoader("settings"));
    }

    public function testGetSources()
    {
        $settings = [
            "core" => [
                "project_name" => "Generic Project",
                "project_version" => "0.4.2",
            ]
        ];
        $provider = new ConfigProviderParams([
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "sources" => $settings,
            ]
        ]);
        $this->assertEquals($settings, $provider->getSources("settings"));
    }

    public function testGetLocations()
    {
        $locations = [ "location_one", "location_two", ];
        $provider = new ConfigProviderParams([
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "sources" => [],
                "locations" => [
                    "location_one",
                    "location_two",
                ]
            ]
        ]);
        $this->assertEquals($locations, $provider->getLocations("settings"));
    }

    /**
     * @expectedException \Exception
     */
    public function testEmptyParams()
    {
        new ConfigProviderParams([]);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingSources()
    {
        new ConfigProviderParams([
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "locations" => [],
            ]
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidSources()
    {
        new ConfigProviderParams([
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "sources" => "foobar",
            ]
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidLocations()
    {
        new ConfigProviderParams([
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "sources" => [],
                "locations" => "foobar",
            ]
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingLoader()
    {
        new ConfigProviderParams([
            "settings" => [
                "sources" => [],
            ]
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidLoader()
    {
        new ConfigProviderParams([
            "settings" => [
                "loader" => "foobar",
                "sources" => [],
            ]
        ]);
    }
}
