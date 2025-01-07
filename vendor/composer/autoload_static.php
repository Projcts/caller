<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit62ee5827946bd15e1b1614a21e9a87c3
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Alisons\\Caller\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Alisons\\Caller\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit62ee5827946bd15e1b1614a21e9a87c3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit62ee5827946bd15e1b1614a21e9a87c3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit62ee5827946bd15e1b1614a21e9a87c3::$classMap;

        }, null, ClassLoader::class);
    }
}
