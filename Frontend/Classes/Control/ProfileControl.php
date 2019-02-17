<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;
    use LetsShoot\Sachkundetrainer\Frontend\View\ErrorView;
    use LetsShoot\Sachkundetrainer\Frontend\View\ModalView;
    use LetsShoot\Sachkundetrainer\Frontend\View\SuccessView;

    /**
     * Class ProfileControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class ProfileControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Profile() {
            // Save data?
            if($this->__getRequestVar('save')) {
                $request = $this->getRequest();

                // Check new password
                if(!empty($request['user_password'])) {
                    if(mb_strlen($request['user_password']) < 6) {
                        $error = new ErrorView();
                        $error->setPasswordToShort();
                        $this->view->assign('error',$error->render());
                        unset($request['user_password']);
                    }
                    elseif ($request['user_password'] != $request['user_password_check']) {
                        $error = new ErrorView();
                        $error->setPasswordNoMatch();
                        $this->view->assign('error',$error->render());
                        unset($request['user_password']);
                    }
                }

                // Set profile data
                $request['id'] = $this->getUser()->user_id;
                $result = $this->getApi()->Call('User', 'Update', $request);
                if(!isset($result->errorCode)) {
                    // Reload user data
                    $user = $this->getApi()->Call('User', 'FindByApiKey', array('api_key'=>$this->getSession()->getApiKey()));
                    if($user && !isset($user->errorCode)) {
                        $this->setUser($user);
                        $this->view->assign('_user', $user);
                        $this->view->assign('userlevel', $user->user_userlevel);
                        $this->getSession()->setUserlevel($user->user_userlevel);
                    }
                    // Add modal
                    $ModalView = new ModalView();
                    $ModalView->setDefaultMsgSaved();
                    $this->view->assign('modal',$ModalView->render());
                }
                else {
                    $error = new ErrorView();
                    $error->setTitle($result->errorCode);
                    $error->setMessage($result->errorMsg);
                    if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                        $error->setTrace($result->errorTrace);
                    }
                    $this->view->assign('error',$error->render());
                }
            }

            // Output
            $this->render(true);
        }

        public function CreateApikey() {
            // Create a new api key
            $result = $this->getApi()->Call('User', 'CreateApiKey', array('id'=>$this->getUser()->user_id));

            if(!isset($result->errorCode)) {
                // Set new api key
                $this->getSession()->setApiKey($result->user_api_key);
                $this->getApi()->setApiKey($this->getSession()->getApiKey());

                $success = new SuccessView();
                $success->setMessageNewApiKey($result->user_api_key);
                $this->view->assign('success',$success->render());
            }
            else {
                $error = new ErrorView();
                $error->setTitle($result->errorCode);
                $error->setMessage($result->errorMsg);
                if($this->getSession()->getUserlevel() == APPLICATION_USERLEVEL_ADMIN && APPLICATION_SHOW_ERRORTRACE) {
                    $error->setTrace($result->errorTrace);
                }
                $this->view->assign('error',$error->render());
            }

            // Output
            $this->render(true);
        }

    }