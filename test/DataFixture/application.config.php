<?php

declare(strict_types=1);

$modules = [
    'Laminas\Cache',
    'Laminas\Form',
    'Laminas\I18n',
];
if (class_exists('Laminas\Filter\Module')) {
    $modules[] = 'Laminas\Filter';
}
if (class_exists('Laminas\Hydrator')) {
    $modules[] = 'Laminas\Hydrator';
}
if (class_exists('Laminas\InputFilter')) {
    $modules[] = 'Laminas\InputFilter';
}

$modules = array_merge($modules, [
    'Laminas\Paginator',
    'Laminas\Router',
    'Laminas\Validator',
    'DoctrineModule',
    'DoctrineORMModule',
    'Db',
    'ApiSkeletons\Doctrine\DataFixture',
]);

return [
    'modules'                 => $modules,
    'module_listener_options' => [
        'config_glob_paths' => [
            __DIR__ . '/local.php',
        ],
        'module_paths'      => [
            __DIR__ . '/../vendor',
            'Db'                      => __DIR__ . '/module/Db/src',
            'ApiSkeletons\Doctrine\DataFixture' => __DIR__ . '/../..',
        ],
    ],
];
