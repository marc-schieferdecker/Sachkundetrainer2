<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0fa0ab486b28a0b9d13d1cfbdf9a7eb0
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TYPO3Fluid\\Fluid\\' => 17,
        ),
        'L' => 
        array (
            'LetsShoot\\Sachkundetrainer\\Frontend\\' => 36,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TYPO3Fluid\\Fluid\\' => 
        array (
            0 => __DIR__ . '/..' . '/typo3fluid/fluid/src',
        ),
        'LetsShoot\\Sachkundetrainer\\Frontend\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Classes',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0fa0ab486b28a0b9d13d1cfbdf9a7eb0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0fa0ab486b28a0b9d13d1cfbdf9a7eb0::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
