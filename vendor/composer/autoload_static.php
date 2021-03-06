<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5833ecb6e6f570cc299861a1b0c4bd96
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Superl\\Permission\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Superl\\Permission\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/superl',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5833ecb6e6f570cc299861a1b0c4bd96::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5833ecb6e6f570cc299861a1b0c4bd96::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5833ecb6e6f570cc299861a1b0c4bd96::$classMap;

        }, null, ClassLoader::class);
    }
}
