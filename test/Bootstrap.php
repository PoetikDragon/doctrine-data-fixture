<?php

declare(strict_types=1);

namespace ApiSkeletonsTest\Doctrine\DataFixture;

use Laminas\Loader\AutoloaderFactory;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);
date_default_timezone_set('UTC');

/**
 * Test bootstrap, for setting up autoloading
 *
 * @subpackage UnitTest
 */
class Bootstrap
{

    /**
     * Initialise the application
     *
     * @return void
     */
    public static function init(): void
    {
        static::initAutoloader();
    }

    /**
     * Initialise the autoloader
     *
     * @return void
     */
    protected static function initAutoloader(): void
    {
        $vendorPath   = static::findParentPath('vendor');
        $composerPath = $vendorPath . '/autoload.php';
        if (is_readable($composerPath)) {
            require $composerPath;
            return;
        }

        require static::getZendPath($vendorPath) . '/Zend/Loader/AutoloaderFactory.php';
        AutoloaderFactory::factory([
            'Laminas\Loader\StandardAutoloader' => [
                'autoregister_zf' => true,
                'namespaces'      => [
                    'ApiSkeletons\Doctrine\DataFixture' => __DIR__ . '/../src',
                    __NAMESPACE__                 => __DIR__,
                    'Test'                        => __DIR__ . '/../vendor/Test/',
                ],
            ],
        ]);
    }

    /**
     * Find the parent path
     *
     * @param $path
     *
     * @return string|null
     */
    protected static function findParentPath($path): ?string
    {
        $dir         = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return null;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }

    /**
     * Get the zend framework library path
     *
     * @param string $vendorPath
     *
     * @return string
     */
    protected static function getZendPath(string $vendorPath): string
    {
        $environment = getenv('ZF2_PATH');
        if ($environment) {
            return $environment;
        }

        if (defined('ZF2_PATH')) {
            return ZF2_PATH;
        }

        $libraryPath = $vendorPath . '/ZF2/library';
        if (is_dir($libraryPath)) {
            return $libraryPath;
        }

        throw new \RuntimeException(
            'Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.'
        );
    }
}

Bootstrap::init();
