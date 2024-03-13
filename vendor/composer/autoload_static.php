<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdb8179e49432d9d176d832c8e264a855
{
    public static $files = array (
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '2a3c2110e8e0295330dc3d11a4cbc4cb' => __DIR__ . '/..' . '/php-webdriver/webdriver/lib/Exception/TimeoutException.php',
        'a0e8785a675240969c3cc6f49c026fc3' => __DIR__ . '/../..' . '/src/scrapper.php',
        'a14cfc457b14ed76cbce7b7393ddff2f' => __DIR__ . '/../..' . '/src/scrapper_leclerc.php',
        '98474befb61b60e24e9bb6ff84c34780' => __DIR__ . '/../..' . '/src/scrapper_carrefour.php',
        '8e1423934575c5208506a073a8b45049' => __DIR__ . '/../..' . '/src/scrapper_intermarche.php',
        '7f80cfb93ce3d7997da2426ba80533ad' => __DIR__ . '/../..' . '/src/scrapper_auchan.php',
        '98def43c10d72f7a9d7dd5633939f28c' => __DIR__ . '/../..' . '/src/scrapper_monoprix.php',
        '97a1bf1dae3a20475e51c125d71c0186' => __DIR__ . '/../..' . '/src/scrapper_systemeu.php',
    );

    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'chrismile0\\scrapper\\' => 20,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\Process\\' => 26,
        ),
        'F' => 
        array (
            'Facebook\\WebDriver\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'chrismile0\\scrapper\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/process',
        ),
        'Facebook\\WebDriver\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-webdriver/webdriver/lib',
        ),
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'PhpToken' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdb8179e49432d9d176d832c8e264a855::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdb8179e49432d9d176d832c8e264a855::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdb8179e49432d9d176d832c8e264a855::$classMap;

        }, null, ClassLoader::class);
    }
}
