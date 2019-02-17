<?php
    namespace LetsShoot\Sachkundetrainer\Frontend;

    if(!defined('APPLICATION_API_URL')) {
        throw new \Exception('APPLICATION_API_URL not defined.');
    }
    if(!defined('APPLICATION_API_PROXY_HOST')) {
        throw new \Exception('APPLICATION_API_PROXY_HOST not defined.');
    }
    if(!defined('APPLICATION_API_PROXY_PORT')) {
        throw new \Exception('APPLICATION_API_PROXY_PORT not defined.');
    }
    if(!defined('APPLICATION_API_PROXY_USER')) {
        throw new \Exception('APPLICATION_API_PROXY_USER not defined.');
    }
    if(!defined('APPLICATION_API_PROXY_PASS')) {
        throw new \Exception('APPLICATION_API_PROXY_PASS not defined.');
    }

    /**
     * Class Api
     */
    class Api {
        /**
         * @var string $base_uri
         */
        private $base_uri = '';

        /**
         * @var string $api_key
         */
        private $api_key = '';

        /**
         * Api constructor.
         * @param string $api_key
         */
        public function __construct(string $api_key = '') {
            $this->setBaseUri(APPLICATION_API_URL);
            $this->setApiKey($api_key);
        }

        /**
         * @param string $api
         * @param string $action
         * @param array $params
         * @return mixed
         */
        public function Call(string $api, string $action, array $params = array()) {
            // Set API Key
            if(!isset($params['api_key'])) {
                $params['api_key'] = $this->getApiKey();
            }
            $params['action'] = $action;

            // Proxy configuration
            $proxy = '';
            $proxyauth = '';
            $proxy_host = APPLICATION_API_PROXY_HOST;
            $proxy_port = APPLICATION_API_PROXY_PORT;
            $proxy_user = APPLICATION_API_PROXY_USER;
            $proxy_pass = APPLICATION_API_PROXY_PASS;
            if(!empty($proxy_host) && !empty($proxy_port)) {
                $proxy = $proxy_host . ':' . $proxy_port;
                if(!empty($proxy_user)) {
                    $proxyauth = $proxy_user.':'.$proxy_pass;
                }
            }

            // Set $_FILES to params
            if($this->__hasUpload()) {
                foreach($_FILES AS $fkey => $fvals) {
                    if(is_string($fvals['tmp_name']) && is_uploaded_file($fvals['tmp_name'])) {
						$params[$fkey] = new \CURLFile($fvals['tmp_name'], $fvals['type'], $fvals['name']);
					}
					elseif (is_array($fvals['tmp_name'])) {
                        foreach( $fvals['tmp_name'] AS $key => $tmp_name) {
                            if(is_uploaded_file($tmp_name)) {
                                $params[$fkey . '[' . $key . ']'] = new \CURLFile($tmp_name, $fvals['type'][$key], $fvals['name'][$key]);
                            }
                        }
                    }
                }
            }

            // Set URI
            $url = $this->getBaseUri() . $api . '/';

            // Init curl and set data
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if(!empty($proxy)) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
                if(!empty($proxy)) {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
                }
            }
            curl_setopt($ch, CURLOPT_POST, true);
            if(!$this->__hasUpload()) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }
            else {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $result = curl_exec($ch);
            curl_close($ch);

            if($result !== false) {
                $result = json_decode($result);
            }

            return $result;
        }

        /**
         * @return string
         */
        public function getBaseUri(): string
        {
            return $this->base_uri;
        }

        /**
         * @param string $base_uri
         */
        public function setBaseUri(string $base_uri)
        {
            $this->base_uri = $base_uri;
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
            $this->api_key = $api_key;
        }

        /**
         * @return bool
         */
        private function __hasUpload(): bool {
            if(isset($_FILES)) {
                foreach($_FILES AS $file) {
                    if(is_array($file['size'])) {
                        foreach($file['size'] AS $size) {
                            if($size>0) {
                                return true;
                            }
                        }
                    }
                    else {
                        if($file['size']>0) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }
    }
