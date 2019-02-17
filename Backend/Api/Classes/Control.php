<?php
    namespace LetsShoot\Sachkundetrainer\Backend;

    use LetsShoot\Sachkundetrainer\Backend\Model\UserModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository\UserRepository;

    /**
     * Class Control
     * @package LetsShoot\Sachkundetrainer\Backend
     */
    class Control {
        /**
         * @var mixed $response
         */
        private $response = '';

        /**
         * @var int $recordsTotal
         */
        private $recordsTotal = null;

        /**
         * @var int $recordsFiltered
         */
        private $recordsFiltered = null;

        /**
         * @var int $draw
         */
        private $draw = null;

        /**
         * @var int $start
         */
        private $start = null;

        /**
         * @var array $request
         */
        private $request = array();

        /**
         * @var array $files
         */
        private $files = array();

        /**
         * @var UserModel $user
         */
        private $user;

        /**
         * @var Logger $logger
         */
        private $logger;

        /**
         * Control constructor.
         * @param bool $check_api_key
         */
        public function __construct(bool $check_api_key = true) {
            // Set post data
            $this -> setRequest(count($_GET) ? $_GET : $_POST);

            // Set file data
            $this -> setFiles($_FILES);

            // Set logger
            $this->logger = new Logger();

            // Check api key
            $api_key = $this->__getRequestVar('api_key');
            if($api_key !== null || $check_api_key == false) {
                // Get user
                if($check_api_key) {
                    $UserRepository = new UserRepository();
                    $UserResult = $UserRepository->findByFieldvalue('user_api_key', $api_key);
                    if (is_array($UserResult) && count($UserResult) == 1) {
                        $this->setUser($UserResult[0]);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $UserResult);
                    } else {
                        if(is_array($UserResult) && count($UserResult) > 1) {
                            // Multiple users with same api key wtf???
                            $Error = new ErrorResponse();
                            $Error->setErrorDuplicateApiKey();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
                        else {
                            // Wrong api key
                            $Error = new ErrorResponse();
                            $Error->setErrorApiKeyInvalid();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
                    }
                }
            }
            else {
                // Api key empty error
                $Error = new ErrorResponse();
                $Error->setErrorNoApiKey();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
        }

        /**
         * @param string $var
         * @return mixed
         */
        protected function __getRequestVar(string $var)
        {
            if(isset($this->getRequest()[$var]))
                return $this->getRequest()[$var];
            else
                return null;
        }

        /**
         * @return array
         */
        public function getFiles(): array
        {
            return $this->files;
        }

        /**
         * @param array $files
         */
        public function setFiles(array $files)
        {
            $this->files = $files;
        }

        /**
         * @return array
         */
        public function getRequest(): array
        {
            return $this->request;
        }

        /**
         * @param array $request
         */
        public function setRequest(array $request)
        {
            $this->request = $request;
        }

        /**
         * @return UserModel
         */
        public function getUser(): UserModel
        {
            return $this->user;
        }

        /**
         * @param UserModel $user
         */
        public function setUser(UserModel $user)
        {
            $this->user = $user;
        }

        /**
         * @return mixed
         */
        public function getResponse()
        {
            // Datatables integration
            if($this->getRecordsTotal() !== null) {
                $response = new \stdClass();
                $response -> recordsTotal = $this->getRecordsTotal();
                $response -> recordsFiltered = $this->getRecordsTotal();
                $response -> draw = $this->getDraw();
                $response -> start = $this->getStart();
                $response -> data = $this->response;
                return $response;
            }
            else {
                return $this->response;
            }
        }

        /**
         * @param mixed $response
         */
        public function setResponse($response)
        {
            $this->response = $response;
        }

        /**
         * @return mixed
         */
        public function getRecordsTotal()
        {
            return $this->recordsTotal;
        }

        /**
         * @param int $recordsTotal
         */
        public function setRecordsTotal(int $recordsTotal)
        {
            $this->recordsTotal = $recordsTotal;
        }

        /**
         * @return mixed
         */
        public function getRecordsFiltered()
        {
            return $this->recordsFiltered;
        }

        /**
         * @param int $recordsFiltered
         */
        public function setRecordsFiltered(int $recordsFiltered)
        {
            $this->recordsFiltered = $recordsFiltered;
        }

        /**
         * @return int
         */
        public function getDraw(): int
        {
            return $this->draw;
        }

        /**
         * @param int $draw
         */
        public function setDraw(int $draw)
        {
            $this->draw = $draw;
        }

        /**
         * @return int
         */
        public function getStart(): int
        {
            return $this->start;
        }

        /**
         * @param int $start
         */
        public function setStart(int $start)
        {
            $this->start = $start;
        }

        /**
         * @return Logger
         */
        public function getLogger(): Logger
        {
            return $this->logger;
        }
    }
