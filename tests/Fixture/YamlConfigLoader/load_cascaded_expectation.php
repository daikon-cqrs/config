<?php
/**
 * This file is part of the daikon/config project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [
    'project-db' => [
        'event_source' => [
            'class' => 'Honeybee\\Infrastructure\\DataAccess\\Connector\\GuzzleDebugConnector',
            'settings' => [
                'auth' => [
                    'username' => 'foo',
                    'password' => 'bar'
                ],
                'host' => '${settings.couchdb.host}',
                'port' => '${settings.couchdb.port}',
                'transport' => '${settings.couchdb.transport}',
                'database' => 'hlx-security',
                'status_test' => '/'
            ]
        ]
    ]
];
