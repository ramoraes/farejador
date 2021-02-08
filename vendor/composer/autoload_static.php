<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit724c716b4d926bfca89670328e77326c
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Farejador\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Farejador\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit724c716b4d926bfca89670328e77326c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit724c716b4d926bfca89670328e77326c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit724c716b4d926bfca89670328e77326c::$classMap;

        }, null, ClassLoader::class);
    }
}
