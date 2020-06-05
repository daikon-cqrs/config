<?php declare(strict_types=1);
/**
 * This file is part of the daikon-cqrs/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Daikon\Test\Config;

use Assert\AssertionFailedException;
use Daikon\Config\ArrayConfigLoader;
use Daikon\Config\ConfigProviderParams;
use PHPUnit\Framework\TestCase;

final class ConfigProviderParamsTest extends TestCase
{
    private const LOCATIONS_FIXTURE = ['location_one', 'location_two'];

    private const SOURCES_FIXTURE = [
        'core' => [
            'project_name' => 'Generic Project',
            'project_version' => '0.4.2'
        ]
    ];

    public function testHasScope(): void
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

    public function testGetLoader(): void
    {
        $provider = new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => self::SOURCES_FIXTURE
            ]
        ]);
        $this->assertInstanceOf(ArrayConfigLoader::class, $provider->getLoader('settings'));
    }

    public function testGetSources(): void
    {
        $provider = new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => self::SOURCES_FIXTURE
            ]
        ]);
        $this->assertEquals(self::SOURCES_FIXTURE, $provider->getSources('settings'));
    }

    public function testGetLocations(): void
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

    public function testEmptyParams(): void
    {
        $this->expectException(AssertionFailedException::class);
        new ConfigProviderParams([]);
    }

    public function testMissingSources(): void
    {
        $this->expectException(AssertionFailedException::class);
        new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'locations' => []
            ]
        ]);
    }

    public function testInvalidSources(): void
    {
        $this->expectException(AssertionFailedException::class);
        new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => 'foobar'
            ]
        ]);
    }

    public function testInvalidLocations(): void
    {
        $this->expectException(AssertionFailedException::class);
        new ConfigProviderParams([
            'settings' => [
                'loader' => ArrayConfigLoader::class,
                'sources' => [],
                'locations' => 'foobar'
            ]
        ]);
    }

    public function testMissingLoader(): void
    {
        $this->expectException(AssertionFailedException::class);
        new ConfigProviderParams([
            'settings' => [
                'sources' => []
            ]
        ]);
    }

    public function testInvalidLoader(): void
    {
        $this->expectException(AssertionFailedException::class);
        new ConfigProviderParams([
            'settings' => [
                'loader' => 'foobar',
                'sources' => []
            ]
        ]);
    }
}
