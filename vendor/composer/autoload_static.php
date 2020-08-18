<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf086e065d9f055d9bb59a21caee218a5
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Box\\Spout\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Box\\Spout\\' => 
        array (
            0 => __DIR__ . '/..' . '/box/spout/src/Spout',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf086e065d9f055d9bb59a21caee218a5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf086e065d9f055d9bb59a21caee218a5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
