<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit798b3a25198002669f69117ca3c6edee
{
    public static $prefixLengthsPsr4 = array (
        'U' => 
        array (
            'UnzerSDK\\examples\\' => 18,
            'UnzerSDK\\' => 9,
        ),
        'P' => 
        array (
            'Plugin\\s360_heidelpay_shop4\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'UnzerSDK\\examples\\' => 
        array (
            0 => __DIR__ . '/..' . '/unzerdev/php-sdk/examples',
        ),
        'UnzerSDK\\' => 
        array (
            0 => __DIR__ . '/..' . '/unzerdev/php-sdk/src',
        ),
        'Plugin\\s360_heidelpay_shop4\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit798b3a25198002669f69117ca3c6edee::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit798b3a25198002669f69117ca3c6edee::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit798b3a25198002669f69117ca3c6edee::$classMap;

        }, null, ClassLoader::class);
    }
}
