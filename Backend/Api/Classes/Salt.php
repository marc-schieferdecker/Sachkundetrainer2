<?php
    namespace LetsShoot\Sachkundetrainer\Backend;

    if(!defined('APPLICATION_PASSWORD_SALT')) {
        throw new \Exception('APPLICATION_PASSWORD_SALT not defined.');
    }

    /**
     * Class Salt
     * @package LetsShoot\Sachkundetrainer\Backend
     */
    class Salt {
        public static function Salt(string $pw): string {
            return md5($pw . APPLICATION_PASSWORD_SALT . strrev($pw) . str_rot13($pw));
        }

        public static function getRandomApiKey(): string {
            return md5( APPLICATION_PASSWORD_SALT . time() . rand());
        }
    }
