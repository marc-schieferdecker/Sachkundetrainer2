<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Service;

    /**
     * Class GeolocationService
     * @package LetsShoot\Sachkundetrainer\Backend\Service
     */
    class GeolocationService {
        /**
         * Google api base url
         */
        const google_api_url = 'https://maps.googleapis.com/maps/api/geocode/json?';

        /**
         * @var string $google_api_key
         */
        private $google_api_key;

        /**
         * @var string $proxy_host
         */
        private $proxy_host;

        /**
         * @var int $proxy_port
         */
        private $proxy_port;

        /**
         * @var string $proxy_user
         */
        private $proxy_user;

        /**
         * @var string $proxy_pass
         */
        private $proxy_pass;

        /**
         * GeolocationService constructor.
         * @param string $google_api_key
         * @param string $proxy_host
         * @param int $proxy_port
         * @param string $proxy_user [optional]
         * @param string $proxy_pass [optional]
         */
        public function __construct(string $google_api_key, string $proxy_host = '', int $proxy_port = 0, string $proxy_user = '', string $proxy_pass = '') {
            $this->setGoogleApiKey($google_api_key);
            $this->setProxyHost($proxy_host);
            $this->setProxyPort($proxy_port);
            $this->setProxyUser($proxy_user);
            $this->setProxyPass($proxy_pass);
        }

        /**
         * get lat/lon by address string
         * @param string $address
         * @return array('status','lat','lon')
         */
        public function getLocationByAdress(string $address): array {
            // Default result array
            $result = array(
                'status' => false,
                'lat' => false,
                'lon' => false
            );

            // API Request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->getApiURL() . 'address='.urlencode($address).'&key='.$this->getGoogleApiKey());
            if(!empty($this->getProxyHost())) {
                curl_setopt($ch, CURLOPT_PROXY, $this->getProxyHost().':'.$this->getProxyPort());
            }
            if(!empty($this->getProxyUser())) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->getProxyUser().':'.$this->getProxyPass());
            }
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $response = curl_exec($ch);
            curl_close($ch);

            // Get json from respone
            $jsonResponse = json_decode($response, true);
            if($jsonResponse && $jsonResponse['status'] == 'OK') {
                $result['status'] = true;
                $result['lat'] = $jsonResponse['results'][0]['geometry']['location']['lat'];
                $result['lon'] = $jsonResponse['results'][0]['geometry']['location']['lng'];
            }

            return $result;
        }

        /**************************************************************************************************************
         * Getters and setters
         */

        /**
         * get google geoloactin api url constant
         * @return string
         */
        private function getApiURL(): string {
            return self::google_api_url;
        }

        /**
         * @return string
         */
        public function getGoogleApiKey(): string
        {
            return $this->google_api_key;
        }

        /**
         * @param string $google_api_key
         */
        public function setGoogleApiKey(string $google_api_key)
        {
            $this->google_api_key = $google_api_key;
        }

        /**
         * @return string
         */
        public function getProxyHost(): string
        {
            return $this->proxy_host;
        }

        /**
         * @param string $proxy_host
         */
        public function setProxyHost(string $proxy_host)
        {
            $this->proxy_host = $proxy_host;
        }

        /**
         * @return int
         */
        public function getProxyPort(): int
        {
            return $this->proxy_port;
        }

        /**
         * @param int $proxy_port
         */
        public function setProxyPort(int $proxy_port)
        {
            $this->proxy_port = $proxy_port;
        }

        /**
         * @return string
         */
        public function getProxyUser(): string
        {
            return $this->proxy_user;
        }

        /**
         * @param string $proxy_user
         */
        public function setProxyUser(string $proxy_user)
        {
            $this->proxy_user = $proxy_user;
        }

        /**
         * @return string
         */
        public function getProxyPass(): string
        {
            return $this->proxy_pass;
        }

        /**
         * @param string $proxy_pass
         */
        public function setProxyPass(string $proxy_pass)
        {
            $this->proxy_pass = $proxy_pass;
        }
    }