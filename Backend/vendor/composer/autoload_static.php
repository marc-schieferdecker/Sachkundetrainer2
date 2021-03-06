<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit19499bd57118c06b0d2d0c73e255ec45
{
    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'claviska\\' => 9,
        ),
        'P' => 
        array (
            'PHPMailer\\' => 10,
        ),
        'L' => 
        array (
            'LetsShoot\\Sachkundetrainer\\Backend\\' => 35,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'claviska\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Api/External/claviska',
        ),
        'PHPMailer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Api/External',
        ),
        'LetsShoot\\Sachkundetrainer\\Backend\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Api/Classes',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit19499bd57118c06b0d2d0c73e255ec45::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit19499bd57118c06b0d2d0c73e255ec45::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
