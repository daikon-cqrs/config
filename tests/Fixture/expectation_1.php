<?php

return [
    'connections' =>[
        'hlx.security' => [
            'event_source' => [
                'class' => 'Honeybee\\Infrastructure\\DataAccess\\Connector\\GuzzleConnector',
                'settings' => [
                    'auth' => [
                        'username' => 'couchdb',
                        'password' => 'couchdb'
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
