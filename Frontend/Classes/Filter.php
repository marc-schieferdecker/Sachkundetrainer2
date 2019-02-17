<?php
    namespace LetsShoot\Sachkundetrainer\Frontend;

    if(!defined('APPLICATION_SESSION_VAR')) {
        throw new \Exception('APPLICATION_SESSION_VAR not defined.');
    }

    class Filter {
        /**
         * Session Variable for storing filters
         */
        const FilterSessionVar = 'filters';

        /**
         * @var array $request
         */
        private $request;

        /**
         * @var array $filters
         */
        private $filters = array();

        /**
         * Filter constructor. Set request and restore filter array from session.
         * @param array $request
         */
        public function __construct(array $request) {
            $this->request = $request;
            if(isset($_SESSION[APPLICATION_SESSION_VAR][self::FilterSessionVar]) && is_array($_SESSION[APPLICATION_SESSION_VAR][self::FilterSessionVar])) {
                $this->filters = $_SESSION[APPLICATION_SESSION_VAR][self::FilterSessionVar];
            }
        }

        /**
         * On destruct store filter array in session
         */
        public function __destruct() {
            $_SESSION[APPLICATION_SESSION_VAR][self::FilterSessionVar] = $this->filters;
        }

        /**
         * Return request array with applied filters
         * @return array
         */
        public function getFilteredRequest() {
            foreach($this->filters AS $actionFilters) {
                if(is_array($actionFilters)) {
                    foreach ($actionFilters AS $filter => $value) {
                        if($value !== null) {
                            $this->request[$filter] = $value;
                        }
                    }
                }
            }
            return $this->request;
        }

        /**
         * Set filter value
         * @param string $action
         * @param string $variable
         * @param $value
         */
        public function setFilter(string $action, string $variable, $value) {
            $this->filters[$action][$variable] = $value;
        }

        /**
         * Register filter variable
         * @param string $action
         * @param string $variable
         * @param $value
         */
        public function registerFilter(string $action, string $variable) {
            if(!isset($this->filters[$action][$variable])) {
                if(!isset($this->filters[$action][$variable])) {
                    $this->filters[$action][$variable] = null;
                }
            }
        }

        /**
         * Clear filter variable
         * @param string $action
         * @param string $variable
         */
        public function unregisterFilter(string $action, string $variable) {
            if(isset($this->filters[$action][$variable])) {
                unset($this->filters[$action][$variable]);
            }
        }

        /**
         * Clear filters of an action
         * @param string $action
         */
        public function clearFilter(string $action) {
            if(isset($this->filters[$action])) {
                unset($this->filters[$action]);
            }
        }

        /**
         * Make filters for action exclusive and remove other filters
         * @param string $action
         */
        public function createExlusiveActionFilter(string $action) {
            foreach($this->filters AS $_action => $filters) {
                if($_action != $action) {
                    unset($this->filters[$_action]);
                }
            }
        }
    }