<?php
/**
 * This file is part of the daikon-cqrs/config project.
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
    private const LOCATIONS_FIXTURE = [ 'location_one', 'location_two' ];

    private const SOURCES_FIXTURE = [
        'core' => [
            'project_name' => 'Generic Project',
            'project_version' => '0.4.2'
        ]
    ];

    public function testHasScope()
    {
        $provider = new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => self::SOURCES_FIXTURE
            ]
        ]);
        $this->assertTrue($provider->hasScope('settings'));
        $this->assertFalse($provider->hasScope('foobar'));
    }

    public function testGetLoader()
    {
        $provider = new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => self::SOURCES_FIXTURE
            ]
        ]);
        $this->assertInstanceOf(ArrayConfigLoader::class, $provider->getLoader('settings'));
    }

    public function testGetSources()
    {
        $provider = new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => self::SOURCES_FIXTURE
            ]
        ]);
        $this->assertEquals(self::SOURCES_FIXTURE, $provider->getSources('settings'));
    }

    public function testGetLocations()
    {
        $provider = new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => [],
                'locations' => self::LOCATIONS_FIXTURE
            ]
        ]);
        $this->assertEquals(self::LOCATIONS_FIXTURE, $provider->getLocations('settings'));
    }

    /**
     * @expectedException \Assert\AssertionFailedException
     */
    public function testEmptyParams()
    {
        new ConfigProviderParams([]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\AssertionFailedException
     */
    public function testMissingSources()
    {
        new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'locations' => []
            ]
        ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\AssertionFailedException
     */
    public function testInvalidSources()
    {
        new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => 'foobar'
            ]
        ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\AssertionFailedException
     */
    public function testInvalidLocations()
    {
        new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => [],
                'locations' => 'foobar'
            ]
        ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\AssertionFailedException
     */
    public function testMissingLoader()
    {
        new ConfigProviderParams([
            'settings' => [
                'sources' => []
            ]
        ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\AssertionFailedException
     */
    public function testInvalidLoader()
    {
        new ConfigProviderParams([
            'settings' => [
                'loader' => 'foobar',
                'sources' => []
            ]
        ]);
    } // @codeCoverageIgnore
}
