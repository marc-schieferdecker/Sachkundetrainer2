<?php
    namespace LetsShoot\Sachkundetrainer\Backend;

    if(!defined('APPLICATION_LOG_FILE')) {
        throw new \Exception('APPLICATION_LOG_FILE not defined.');
    }
    if(!defined('APPLICATION_LOG_LEVEL_DEBUG')) {
        throw new \Exception('APPLICATION_LOG_LEVEL_DEBUG not defined.');
    }
    if(!defined('APPLICATION_LOG_LEVEL_WARN')) {
        throw new \Exception('APPLICATION_LOG_LEVEL_DEBUG not defined.');
    }
    if(!defined('APPLICATION_LOG_LEVEL_INFO')) {
        throw new \Exception('APPLICATION_LOG_LEVEL_DEBUG not defined.');
    }
    if(!defined('APPLICATION_LOG_LEVEL_CRITICAL')) {
        throw new \Exception('APPLICATION_LOG_LEVEL_CRITICAL not defined.');
    }
    if(!defined('APPLICATION_CRITICAL_EMAIL_FROM')) {
        throw new \Exception('APPLICATION_CRITICAL_EMAIL_FROM not defined.');
    }
    if(!defined('APPLICATION_CRITICAL_EMAIL_TO')) {
        throw new \Exception('APPLICATION_CRITICAL_EMAIL_TO not defined.');
    }
    if(!defined('APPLICATION_LOG_LEVEL')) {
        throw new \Exception('APPLICATION_LOG_LEVEL not defined.');
    }

    /**
     * Class Logger
     * @package LetsShoot\Sachkundetrainer\Backend
     */
    class Logger {
        /**
         * @var resource $fileHandler
         */
        private $fileHandler;

        /**
         * @var int $loglevel_debug
         */
        private $loglevel_debug = APPLICATION_LOG_LEVEL_DEBUG;

        /**
         * @var int $loglevel_warn
         */
        private $loglevel_warn = APPLICATION_LOG_LEVEL_WARN;

        /**
         * @var int $loglevel_info
         */
        private $loglevel_info = APPLICATION_LOG_LEVEL_INFO;

        /**
         * @var int $loglevel_critical
         */
        private $loglevel_critical = APPLICATION_LOG_LEVEL_CRITICAL;

        /**
         * @var string $logger_critical_email_from
         */
        private $logger_critical_email_from = APPLICATION_CRITICAL_EMAIL_FROM;

        /**
         * @var string $logger_critical_email_to
         */
        private $logger_critical_email_to = APPLICATION_CRITICAL_EMAIL_TO;

        /**
         * @var int $loggger_log_level
         */
        private $loggger_log_level = APPLICATION_LOG_LEVEL;

        /**
         * @var bool $logger_logged_something
         */
        private $logger_logged_something = false;
        
        /**
         * Logger constructor.
         */
        public function __construct() {
            $this->fileHandler = fopen(APPLICATION_LOG_FILE, 'a');
        }

        /**
         * Example: $log->Log(APPLICATION_LOG_LEVEL_DEBUG, 'this is a debug string.');
         * @param int $level
         * @param mixed $msg
         */
        public function Log(int $level, $msg) {
            // Convert to string
            if(!is_string($msg)) {
                $msg = print_r($msg, true);
            }

            // Set log str
            $level_str = '';
            $level_str = $level & $this -> getLoglevelCritical() ? 'CRITICAL' : $level_str;
            $level_str = $level & $this -> getLoglevelDebug() ? 'DEBUG' : $level_str;
            $level_str = $level & $this -> getLoglevelInfo() ? 'INFO' : $level_str;
            $level_str = $level & $this -> getLoglevelWarn() ? 'WARN' : $level_str;
            $log_str = date("Y-m-d H:i:s") . substr((string)microtime(), 1, 3) . " $level_str: " . trim( $msg ) . "\n";

            // Check if desired level is activated in application config an add msg to log
            if($level & $this->getLogggerLogLevel()) {
                fwrite($this->getFileHandler(), $log_str);
            }
            // If critical msg send mail
            if($level & $this->getLoglevelCritical()) {
                if(!empty($this->getLoggerCriticalEmailFrom()) && !empty($this->getLoggerCriticalEmailTo())) {
                    mail($this->getLoggerCriticalEmailTo(), 'CRITICAL APPLICATION ALERT', $log_str, 'From: ' . $this->getLoggerCriticalEmailFrom());
                }
            }
            
            $this->setLoggerLoggedSomething(true);
        }

        /**
         * mark the end of request in log file
         */
        public function MarkEndOfRequest() {
            if($this->isLoggerLoggedSomething()) {
                fwrite($this->getFileHandler(), str_repeat('-', 70) . "\n");
            }
        }

        /**
         * @return resource
         */
        private function getFileHandler()
        {
            return $this->fileHandler;
        }

        /**
         * @return int
         */
        public function getLoglevelDebug(): int
        {
            return $this->loglevel_debug;
        }

        /**
         * @return int
         */
        public function getLoglevelWarn(): int
        {
            return $this->loglevel_warn;
        }

        /**
         * @return int
         */
        public function getLoglevelInfo(): int
        {
            return $this->loglevel_info;
        }

        /**
         * @return int
         */
        public function getLoglevelCritical(): int
        {
            return $this->loglevel_critical;
        }

        /**
         * @return string
         */
        public function getLoggerCriticalEmailFrom(): string
        {
            return $this->logger_critical_email_from;
        }

        /**
         * @return string
         */
        public function getLoggerCriticalEmailTo(): string
        {
            return $this->logger_critical_email_to;
        }

        /**
         * @return int
         */
        public function getLogggerLogLevel(): int
        {
            return $this->loggger_log_level;
        }

        /**
         * @return bool
         */
        public function isLoggerLoggedSomething(): bool
        {
            return $this->logger_logged_something;
        }

        /**
         * @param bool $logger_logged_something
         */
        public function setLoggerLoggedSomething(bool $logger_logged_something): void
        {
            $this->logger_logged_something = $logger_logged_something;
        }
    }
