<?php
    namespace LetsShoot\Sachkundetrainer\Frontend;

    use TYPO3Fluid\Fluid\View\TemplatePaths;
    use TYPO3Fluid\Fluid\View\TemplateView;

    /**
     * Class Control
     * @package LetsShoot\Sachkundetrainer\Backend
     */
    class Control {
        /**
         * @var Api $api
         */
        private $api;

        /**
         * @var Session $session
         */
        private $session;

        /**
         * @var array $request
         */
        private $request = array();

        /**
         * @var TemplateView $view
         */
        protected $view;

        /**
         * @var \stdClass $user
         */
        private $user;

        /**
         * Control constructor.
         * @param string $controllerName
         * @param string $controllerAction
         */
        public function __construct(string $controllerName, string $controllerAction) {
            // Set request data (_REQUEST has priority because of mixed method data)
            $this -> setRequest($_REQUEST);
            if(count($_GET) && !count($_POST)) {
                $this -> setRequest($_GET);
            }
            if(!count($_GET) && count($_POST)) {
                $this -> setRequest($_POST);
            }

            // Create session object
            $session = new Session();

            // Create API
            $this->setApi(new Api());

            // Create Session
            $this->setSession($session);

            // Set API Key from session
            $this->getApi()->setApiKey($this->getSession()->getApiKey());

            // Create base paths to fluid templates
            $paths = new TemplatePaths();
            $paths->setLayoutRootPaths(array(APPLICATION_LAYOUTS_PATH));
            $paths->setTemplateRootPaths(array(APPLICATION_TEMPLATES_PATH));
            $paths->setPartialRootPaths(array(APPLICATION_PARTIALS_PATH));
            // Create Fluid View
            $view = new TemplateView();
            $renderingContext = $view->getRenderingContext();
            $renderingContext->setTemplatePaths($paths);
            $renderingContext->setControllerName($controllerName);
            $renderingContext->setControllerAction($controllerAction);
            $view->setRenderingContext($renderingContext);
            // Set default properties and create view
            $view->assign('ctrl', $controllerName);
            $view->assign('action', $controllerAction);
            $view->assign('api_key', $this->getSession()->getApiKey());
            $view->assign('userlevel', $this->getSession()->getUserlevel());
            $view->assign('request', $this->getRequest());
            $view->assign('APPLICATION_DIRECTORY', APPLICATION_DIRECTORY);
            $view->assign('APPLICATION_USERLEVEL_GUEST', APPLICATION_USERLEVEL_GUEST);
            $view->assign('APPLICATION_USERLEVEL_USER', APPLICATION_USERLEVEL_USER);
            $view->assign('APPLICATION_USERLEVEL_ADMIN', APPLICATION_USERLEVEL_ADMIN);
            $view->assign('APPLICATION_GUEST_KEY', APPLICATION_GUEST_KEY);
            $user = $this->getApi()->Call('User', 'FindByApiKey', array('api_key'=>$this->getSession()->getApiKey()));
            if($user && !isset($user->errorCode)) {
                $view->assign('_user', $user);
                $this->setUser($user);
            }
            $this->setView($view);
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
         * @param string $var
         * @param $value
         */
        protected function __setRequestVar(string $var, $value): void
        {
            $this->request[$var] = $value;
        }

        /**
         * Render template
         * @param bool $output
         */
        public function render(bool $output = true) {
            if($output) {
                echo $this->view->render();
            }
            else {
                $this->view->render();
            }
        }

        /**
         * @param string $ctrl
         * @param string $do
         * @param array $params
         */
        public function Redirect(string $ctrl, string $action = '', array $params = array()) {
            if(count($params)) {
                header('Location:/Frontend/?ctrl='.$ctrl.'&action='.$action.'&'.http_build_query($params));
            }
            else {
                header('Location:/Frontend/?ctrl='.$ctrl.'&action='.$action);
            }
        }

        /**
         * @return Api
         */
        public function getApi(): Api
        {
            return $this->api;
        }

        /**
         * @param Api $api
         */
        public function setApi(Api $api): void
        {
            $this->api = $api;
        }

        /**
         * @return Session
         */
        public function getSession(): Session
        {
            return $this->session;
        }

        /**
         * @param Session $session
         */
        public function setSession(Session $session): void
        {
            $this->session = $session;
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
         * @param bool $setRequestGlobal
         */
        public function setRequest(array $request, bool $setRequestGlobal = false): void
        {
            if($setRequestGlobal) {
                $_REQUEST = $request;
            }
            $this->request = $request;
        }

        /**
         * @param TemplateView $view
         */
        public function setView(TemplateView $view)
        {
            $this->view = $view;
        }

        /**
         * @return \stdClass
         */
        public function getUser(): \stdClass
        {
            return $this->user;
        }

        /**
         * @param \stdClass $user
         */
        public function setUser(\stdClass $user): void
        {
            $user->user_config = json_decode($user->user_config);
            $user->user_answered = json_decode($user->user_answered);
            // Apply default config
            if(!$user->user_config) {
                $user->user_config = array();
                $user->user_config['disable_duplicate_questions'] = 0;
                $user->user_config['timelimit_to_answere'] = 0;
                $user->user_config['show_comments'] = 1;
            }
            // Apply default array of answeres already answered
            if(!$user->user_answered) {
                $user->user_answered = array();
            }
            $this->user = $user;
        }
    }
