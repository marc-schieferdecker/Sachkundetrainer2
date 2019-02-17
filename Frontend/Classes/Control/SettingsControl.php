<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;
    use LetsShoot\Sachkundetrainer\Frontend\View\ErrorView;
    use LetsShoot\Sachkundetrainer\Frontend\View\ModalView;

    /**
     * Class SettingsControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class SettingsControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Settings() {
            if($this->getSession()->getUserlevel() < APPLICATION_USERLEVEL_USER) {
                $error = new ErrorView();
                $error->setRightsMissing();
                $this->view->assign('RightsMissing', $error->render());
            }
            else {
                // Reset already answered questions
                if($this->__getRequestVar('reset_answered')) {
                    $result = $this->getApi()->Call('User', 'Update', array('id'=>$this->getUser()->user_id,'user_answered'=>''));
                    if(!isset($result->errorCode)) {
                        // Reload user data
                        $user = $this->getApi()->Call('User', 'FindByApiKey', array('api_key'=>$this->getSession()->getApiKey()));
                        if($user && !isset($user->errorCode)) {
                            $this->setUser($user);
                            $this->view->assign('_user', $user);
                        }
                        // Add modal
                        $ModalView = new ModalView();
                        $ModalView->setDefaultMsgSuccess();
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

                // Save configuration
                if($this->__getRequestVar('save')) {
                    $config = array(
                        'disable_duplicate_questions' => $this->__getRequestVar('disable_duplicate_questions'),
                        'timelimit_to_answere' => $this->__getRequestVar('timelimit_to_answere'),
                        'show_comments' => $this->__getRequestVar('show_comments')
                    );
                    $result = $this->getApi()->Call('User', 'Update', array('id'=>$this->getUser()->user_id,'user_config'=>$config));
                    if(!isset($result->errorCode)) {
                        // Reload user data
                        $user = $this->getApi()->Call('User', 'FindByApiKey', array('api_key'=>$this->getSession()->getApiKey()));
                        if($user && !isset($user->errorCode)) {
                            $this->setUser($user);
                            $this->view->assign('_user', $user);
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
            }
            // Output
            $this->render(true);
        }
    }