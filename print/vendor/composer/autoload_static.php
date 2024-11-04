<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit60465519b8e96e8a3552f78fa921125d
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Mike42\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Mike42\\' => 
        array (
            0 => __DIR__ . '/..' . '/mike42/gfx-php/src/Mike42',
            1 => __DIR__ . '/..' . '/mike42/escpos-php/src/Mike42',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit60465519b8e96e8a3552f78fa921125d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit60465519b8e96e8a3552f78fa921125d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit60465519b8e96e8a3552f78fa921125d::$classMap;

        }, null, ClassLoader::class);
    }
}