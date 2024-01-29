<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit81a52f65f4593aa6eb01a6fd51c3f209
{
    public static $files = array (
        '45a16669595eb3c0a9e2994e57fc3188' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v5p3.php',
    );

    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Carbon_Fields\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Carbon_Fields\\' => 
        array (
            0 => __DIR__ . '/..' . '/htmlburger/carbon-fields/core',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit81a52f65f4593aa6eb01a6fd51c3f209::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit81a52f65f4593aa6eb01a6fd51c3f209::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit81a52f65f4593aa6eb01a6fd51c3f209::$classMap;

        }, null, ClassLoader::class);
    }
}
