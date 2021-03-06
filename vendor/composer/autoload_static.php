<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd14401b4fd6fb742159e65ba40cf3204
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd14401b4fd6fb742159e65ba40cf3204::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd14401b4fd6fb742159e65ba40cf3204::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd14401b4fd6fb742159e65ba40cf3204::$classMap;

        }, null, ClassLoader::class);
    }
}
