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
use Daikon\Config\ConfigProvider;
use Daikon\Config\ConfigProviderInterface;
use Daikon\Config\ConfigProviderParams;
use Daikon\Config\ConfigProviderParamsInterface;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    private const CONFIG_FIXTURE = [
        'project' => [
            'environment' => 'development'
        ],
        'auth' => [
            'simple.credentials' => [
                'username' => 'superuser',
                'password' => 'p455w0rd'
            ]
        ]
    ];

    /**
     * @dataProvider provideSut
     */
    public function testHas(ConfigProviderInterface $sut)
    {
        $this->assertTrue($sut->has("settings"));
        $this->assertTrue($sut->has("settings.project"));
        $this->assertTrue($sut->has("settings.project.environment"));
        $this->assertTrue($sut->has("settings.auth.simple.credentials.username"));

        $this->assertFalse($sut->has("foobar"));
        $this->assertFalse($sut->has("settings.foobar"));
        $this->assertFalse($sut->has("settings.auth.simple.foobar"));
    }

    /**
     * @dataProvider provideSut
     */
    public function testGet(ConfigProviderInterface $sut)
    {
        $this->assertEquals(self::CONFIG_FIXTURE, $sut->get("settings"));
        $this->assertEquals(
            self::CONFIG_FIXTURE["project"],
            $sut->get("settings.project")
        );
        $this->assertEquals("development", $sut->get("settings.project.environment"));
        $this->assertEquals("superuser", $sut->get("settings.auth.simple.credentials.username"));
        $this->assertEquals(
            self::CONFIG_FIXTURE["auth"]["simple.credentials"],
            $sut->get("settings.auth.simple.credentials")
        );
    }

    /**
     * @dataProvider provideSut
     */
    public function testGetWithDefault(ConfigProviderInterface $sut)
    {
        $this->assertEquals("development", $sut->get("settings.project.environment", "bar"));
        $this->assertEquals("bar", $sut->get("foo", "bar"));
        $this->assertNull($sut->get("foo"));
    }

    public function testInterpolation()
    {
        $sut = new ConfigProvider(new ConfigProviderParams([
            "settings" => [
                "loader" => ArrayConfigLoader::class,
                "sources" => [
                    'project' => [
                        'secret' => 'c4ntgu35th15'
                    ],
                    'auth' => [
                        'simple' => [
                            'username' => 'superuser',
                            'password' => '${settings.project.secret}'
                        ]
                    ]
                ]
            ]
        ]));
        $this->assertEquals("c4ntgu35th15", $sut->get("settings.auth.simple.password"));
    }

    public function provideSut()
    {
        $configProvider = new ConfigProvider(
            new ConfigProviderParams([
                "settings" => [
                    "loader" => ArrayConfigLoader::class,
                    "sources" => self::CONFIG_FIXTURE
                ]
            ])
        );
        return [ [ $configProvider ] ];
    }
}
