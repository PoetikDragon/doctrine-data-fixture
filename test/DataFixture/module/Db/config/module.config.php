<?php

declare(strict_types=1);

namespace Db;

use Doctrine\ORM\Mapping\Driver\XmlDriver;

return [
    'doctrine' => [
        'driver' => [
            'db_driver'   => [
                'class' => XmlDriver::class,
                'paths' => [__DIR__ . '/xml'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => 'db_driver',
                ],
            ],
        ],
    ],
];
