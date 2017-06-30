<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [
    'connections' =>[
        'hlx.security' => [
            'event_source' => [
                'class' => 'Honeybee\\Infrastructure\\DataAccess\\Connector\\GuzzleDebugConnector',
                'settings' => [
                    'auth' => [
                        'username' => 'foo',
                        'password' => 'bar'
                    ],
                    'host' => '127.0.0.1',
                    'port' => '5984',
                    'transport' => 'https',
                    'database' => 'hlx-security',
                    'status_test' => '/'
                ]
            ]
        ]
    ]
];
