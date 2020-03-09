Doctrine Data Fixtures with Grouping for Laminas
================================================

[![Build status](https://api.travis-ci.org/API-Skeletons/zf-doctrine-data-fixture.svg)](http://travis-ci.org/API-Skeletons/zf-doctrine-data-fixture)
[![Gitter](https://badges.gitter.im/api-skeletons/open-source.svg)](https://gitter.im/api-skeletons/open-source)
[![Patreon](https://img.shields.io/badge/patreon-donate-yellow.svg)](https://www.patreon.com/apiskeletons)
[![Total Downloads](https://poser.pugx.org/API-Skeletons/zf-doctrine-data-fixture/downloads)](https://packagist.org/packages/API-Skeletons/zf-doctrine-data-fixture)


This provides command line support for Doctrine Fixtures in Laminas.
Often projects will have multiple sets of fixtures for different object managers or modules such as
from a 3rd party API.  When this is the case a tool which can run fixtures in groups is needed.
Additionally dependency injection must be available to the fixtures.  To accomplish these needs
this module uses a Service Manager configurable on a per-group basis.


Releases
--------

The 4.x release tags support PHP 5.6 and 7.0.

The 5.x release tags support PHP 7.1 and above.


Installation
------------

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
$ composer require api-skeletons/zf-doctrine-data-fixture ^2.0
```

Add this module to your application's configuration:

```php
'modules' => [
   ...
   'ApiSkeletons\Doctrine\DataFixture',
],
```

> ### zf-component-installer
>
> If you use [zf-component-installer](https://github.com/laminas/laminas-component-installer),
> that plugin will install doctrine-data-fixture as a module for you.


Configuration
--------------

This module builds on top of Doctrine configuration.  The configuration in a module which implements fixtures is:

```php
return [
    'doctrine' => [
        'fixture' => [
            'group1' => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'invokables' => [
                    'ModuleName\Fixture\FixtureOne' => 'ModuleName\Fixture\FixtureOne',
                ],
                'factories' => [
                    'ModuleName\Fixture\FixtureTwo' => 'ModuleName\Fixture\FixtureTwoFactory',
                ]
            ],
            'group2' => [
                'object_manager' => 'doctrine.entitymanager.orm_zf_doctrine_audit',
                ...
            ],
        ],
    ],
];
```

Each group is a [Laminas ServiceManager](https://docs.laminas.dev/laminas-servicemanager/) configuration.  This allows complete dependency injection control of your fixtures.


Listing Fixtures
----------------

```sh
index.php data-fixture:list [<group>]
```

List all object managers and their groups, list all groups for a given object manager, or specify object manager and group to list all fixtures for a group.


Executing Fixtures from Command Line
------------------

```sh
index.php data-fixture:import <group> [--purge-with-truncate] [--do-not-append]
```

The `<group>` is required.

Append is the default option.  This is inversed with the new --do-not-append

Options:

`--purge-with-truncate` if specified will purge the object manager's tables before running fixtures.

`--do-not-append` will delete all data in the database before running fixtures.


Executing Fixtures from Code
----------------------------

For unit testing or other times you must run your fixtures from within code
you must fetch the `DataFixtureManager` with `build` and pass the group name
to the service manager then load the fixtures into the `Loader` manually.

```php
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use ApiSkeletons\Doctrine\DataFixture\Loader;

// Run audit fixtures
$dataFixtureManager = $application->getServiceManager()
    ->build('ApiSkeletons\Doctrine\DataFixture\DataFixtureManager', ['group' => 'zf-doctrine-audit']);

$loader = new Loader($dataFixtureManager);
$purger = new ORMPurger();
$executor = new ORMExecutor($auditEntityManager, $purger);

foreach ($dataFixtureManager->getAll() as $fixture) {
    $loader->addFixture($fixture);
}

$executor->execute($loader->getFixtures(), true);
```

Getting Help
------------

```sh
index.php data-fixture:help
```


Important Notes
---------------

* You can only run one group at a time from the command line.  If you need to run more create a script to run them in sequence.
* The ServiceManager is injected into each DataFixtureManager at getServiceLocator() so you can use instantiators which run from that level.  This makes the DataFixtureManager work like a plugin manager defined with `$serviceListener->addServiceManager()`.
* You cannot use abstract factories.  Each fixture must be individually configured.

