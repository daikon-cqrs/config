<?php

namespace Daikon\Test\Config;

use Daikon\Config\ConfigPath;
use PHPUnit\Framework\TestCase;

final class ConfigPathTest extends TestCase
{
    public function testFullPathGiven()
    {
        $configPath = ConfigPath::fromPathString("settings::core::app_version", "config", "global");

        $this->assertEquals("settings", $configPath->getScope());
        $this->assertEquals("core", $configPath->getNamespace());
        $this->assertEquals("app_version", $configPath->getKey());
    }

    public function testWithNamespaceAndKeyGiven()
    {
        $configPath = ConfigPath::fromPathString("core::app_version", "config", "global");
        $this->assertEquals("config", $configPath->getScope());
        $this->assertEquals("core", $configPath->getNamespace());
        $this->assertEquals("app_version", $configPath->getKey());
    }

    public function testWithOnlyKeyGiven()
    {
        $configPath = ConfigPath::fromPathString("app_version", "config", "global");
        $this->assertEquals("config", $configPath->getScope());
        $this->assertEquals("global", $configPath->getNamespace());
        $this->assertEquals("app_version", $configPath->getKey());
    }

    public function testToString()
    {
        $configPath = ConfigPath::fromPathString("app_env", "config", "global");
        $this->assertEquals("config::global::app_env", (string)$configPath);
    }

    /**
     * @expectedException \Exception
     */
    public function testWithEmptyPathAndDefaultsGiven()
    {
        ConfigPath::fromPathString("", "", "");
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function testWithNoPathGiven()
    {
        ConfigPath::fromPathString();
    }

    /**
     * @expectedException \Exception
     */
    public function testMalformedPath()
    {
        ConfigPath::fromPathString("::core::app_version", "", "");
    }
}
