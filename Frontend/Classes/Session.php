<?php
    namespace LetsShoot\Sachkundetrainer\Frontend;

    if(!defined('APPLICATION_SESSION_VAR')) {
        throw new \Exception('APPLICATION_SESSION_VAR not defined.');
    }

    /**
     * Class Session
     */
    class Session {
        /**
         * @var string $api_key
         */
        private $api_key;

        /**
         * @var int $userlevel
         */
        private $userlevel;

        /**
         * Session constructor.
         */
        public function __construct() {
            // Defaults
            if(!isset($_SESSION[APPLICATION_SESSION_VAR]['api_key'])) {
                $_SESSION[APPLICATION_SESSION_VAR]['api_key'] = null;
            }
            if(!isset($_SESSION[APPLICATION_SESSION_VAR]['userlevel'])) {
                $_SESSION[APPLICATION_SESSION_VAR]['userlevel'] = null;
            }

            // API Key
            $this->setApiKey($_SESSION[APPLICATION_SESSION_VAR]['api_key']);
            // Userlevel
            $this->setUserlevel($_SESSION[APPLICATION_SESSION_VAR]['userlevel']);
        }

        /**
         * @return string
         */
        public function getApiKey(): ?string
        {
            return $this->api_key;
        }

        /**
         * @param string $api_key
         */
        public function setApiKey(?string $api_key): void
        {
            $_SESSION[APPLICATION_SESSION_VAR]['api_key'] = $api_key;
            $this->api_key = $api_key;
        }

        /**
         * @return int
         */
        public function getUserlevel(): ?int
        {
            return $this->userlevel;
        }

        /**
         * @param int $userlevel
         */
        public function setUserlevel(?int $userlevel): void
        {
            $_SESSION[APPLICATION_SESSION_VAR]['userlevel'] = $userlevel;
            $this->userlevel = $userlevel;
        }
    }
