<?php

declare(strict_types=1);

namespace ApiSkeletonsTest\Doctrine\DataFixture;

use Db\Entity;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader as DoctrineLoader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;
use ApiSkeletons\Doctrine\DataFixture\Commands\ImportCommand;
use ApiSkeletons\Doctrine\DataFixture\Commands\ListCommand;
use ApiSkeletons\Doctrine\DataFixture\DataFixtureManager;
use ApiSkeletons\Doctrine\DataFixture\DataFixtureManagerFactory;
use ApiSkeletons\Doctrine\DataFixture\Loader;

class DataFixtureTest extends TestCase
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->loadServiceManager();
        $this->loadDummyData();
    }

    /**
     * Data provider for the valid commands test
     *
     * @return array<string[]>
     */
    public function dataProviderForTestValidCommands(): array
    {
        return [
            [
                'data-fixture:import',
                ImportCommand::class,
                'test-standard',
            ],
            [
                'data-fixture:list',
                ListCommand::class,
            ],
            [
                'data-fixture:list',
                ListCommand::class,
                'test-standard',
            ],
        ];
    }

    /**
     * Test valid commands
     *
     * @param string      $commandName
     * @param string      $className
     * @param string|null $arguments
     *
     * @dataProvider dataProviderForTestValidCommands
     *
     * @return void
     * @throws \Throwable
     */
    public function testValidCommands(string $commandName, string $className, string $arguments = null): void
    {
        $application = $this->createApplication();
        $command     = $application->get($commandName);
        $this->assertInstanceOf($className, $command);

        $parts  = [
            $commandName,
            $arguments,
        ];
        $input  = new StringInput(implode(' ', $parts));
        $output = new BufferedOutput;
        $application->doRun($input, $output);
        $this->assertTrue(true);
    }

    /**
     * Test can build doctrine data fixture manager
     *
     * @return void
     * @throws ContainerExceptionInterface
     */
    public function testCanBuildDoctrineDataFixtureManager(): void
    {
        $this->configureDataFixtureManager(
            $this->getDataFixtureManager('test-standard'),
            new DoctrineLoader
        );
        $this->assertTrue(true);
    }

    /**
     * Test can build standard data fixture manager
     *
     * @return void
     * @throws ContainerExceptionInterface
     */
    public function testCanBuildStandardDataFixtureManager(): void
    {
        $manager = $this->getDataFixtureManager('test-standard');
        $this->configureDataFixtureManager($manager, new Loader($manager));
        $this->assertTrue(true);
    }

    /**
     * Test can build dependent data fixture
     *
     * @return void
     * @throws ContainerExceptionInterface
     */
    public function testBuildDependentDataFixture(): void
    {
        $manager = $this->getDataFixtureManager('test-dependency');
        $this->configureDataFixtureManager($manager, new Loader($manager));
        $this->assertTrue(true);
    }

    /**
     * Test invalid groups are properly handled
     *
     * RuntimeException: Fixture group not found: invalid-group
     */
    public function testInvalidGroup(): void
    {
        $this->expectException(\RuntimeException::class);
        $manager = $this->getDataFixtureManager('invalid-group');
    }

    /**
     * Builds a new ServiceManager instance
     *
     * @return void
     */
    protected function loadServiceManager(): void
    {
        $this->serviceManager = new ServiceManager;
        $configuration        = require __DIR__ . '/application.config.php';
        $serviceManagerConfig = new ServiceManagerConfig(
            $configuration['service_manager'] ?? []
        );

        $serviceManagerConfig->configureServiceManager($this->serviceManager);
        $this->serviceManager->setService('ApplicationConfig', $configuration);
        $this->serviceManager->get('ModuleManager')->loadModules();
    }

    /**
     * Create the symfony application
     *
     * @return Application
     */
    protected function createApplication(): Application
    {
        $application = new Application;
        $application->addCommands([
            $this->serviceManager->get(ImportCommand::class),
            $this->serviceManager->get(ListCommand::class),
        ]);

        return $application;
    }

    /**
     * Loads dummy data into the object manager
     *
     * @return void
     * @throws \Exception
     */
    protected function loadDummyData(): void
    {
        $objectManager = $this->serviceManager->get(
            'doctrine.entitymanager.orm_default'
        );

        (new SchemaTool($objectManager))->createSchema($objectManager->getMetadataFactory()->getAllMetadata());

        $artist = new Entity\Artist;
        $artist->setName('ABBA');
        $artist->setCreatedAt(new \DateTimeImmutable('2011-12-18 13:17:17'));
        $objectManager->persist($artist);
        $objectManager->flush($artist);
    }

    /**
     * Get the data fixture manager
     *
     * @param string $group
     *
     * @return DataFixtureManager
     */
    protected function getDataFixtureManager(string $group): DataFixtureManager
    {
        return $this->serviceManager->build(
            DataFixtureManager::class,
            [DataFixtureManagerFactory::OPTION_GROUP => $group]
        );
    }

    /**
     * Configure the data fixture manager for testing
     *
     * @param DataFixtureManager $manager
     * @param DoctrineLoader     $loader
     *
     * @return void
     */
    protected function configureDataFixtureManager(DataFixtureManager $manager, DoctrineLoader $loader): void
    {
        $purger = new ORMPurger;
        foreach ($manager->getAll() as $fixture) {
            $loader->addFixture($fixture);
        }

        /**
         * @var EntityManagerInterface $objectManager
         */
        $objectManager = $manager->getObjectManager();
        $executor      = new ORMExecutor($objectManager, $purger);
        $executor->execute($loader->getFixtures(), true);
    }
}
