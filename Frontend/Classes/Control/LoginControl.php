<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\Control;

    use LetsShoot\Sachkundetrainer\Frontend\Control;

    /**
     * Class LoginControl
     * @package LetsShoot\Sachkundetrainer\Frontend\Control
     */
    class LoginControl extends Control {
        public function __construct(string $controllerName, string $controllerAction) {
            parent::__construct($controllerName, $controllerAction);
        }

        public function Login() {
            // Logout if logout called and logged in (toggle)
            if($this->getSession()->getApiKey()) {
                $this->getSession()->setApiKey(null);
                $this->getSession()->setUserlevel(null);
            }

            // Lost password (email set, password empty)
            if($this->__getRequestVar('do') == 'passwordlost' && $this->__getRequestVar('email')) {
                $result = $this->getApi()->Call('Api', 'RequestPasswordReset', array('email'=>$this->__getRequestVar('email')));
                $this->view->assign('messagepasswordlost', 'Wenn unter dieser E-Mail Adresse ein Account gefunden wurde, wird eine E-Mail mit einem Link an Sie gesendet, mit dem Sie sich anmelden können, um Ihr Kennwort zu ändern. Bitte schauen Sie auch in Ihren Spam-Ordner.');
                $this->view->assign('show', 'passwordlost');
            }

            // Create new account
            if($this->__getRequestVar('do') == 'new') {
                if($this->__getRequestVar('email') && $this->__getRequestVar('name') && $this->__getRequestVar('password')) {
                    if(mb_strlen($this->__getRequestVar('password')) >= 6) {
                        $api = $this->getApi();
                        $api->setApiKey(APPLICATION_ADMIN_KEY);
                        $result = $api->Call('User', 'Add', array(
                            'user_userlevel' => APPLICATION_USERLEVEL_USER,
                            'user_email' => $this->__getRequestVar('email'),
                            'user_name' => $this->__getRequestVar('name'),
                            'user_password' => $this->__getRequestVar('password')
                        ));
                        if (!isset($result->errorCode)) {
                            if(isset($result->user_api_key)) {
                                $this->getSession()->setApiKey($result->user_api_key);
                                $this->getSession()->setUserlevel($result->user_userlevel);
                                header('Location:./');
                            }
                            else {
                                $this->view->assign('messagenewaccount', 'Da hat irgendetwas nicht geklappt. Bitte versuchen Sie es noch einmal.');
                            }
                        }
                        else {
                            $this->view->assign('messagenewaccount', $result->errorMsg);
                        }
                    } else {
                        $this->view->assign('messagenewaccount', 'Das Kennwort ist zu kurz. Bitte mindestens 6 Zeichen verwenden.');
                    }
                } else {
                    $this->view->assign('messagenewaccount', 'Bitte alle Felder ausfüllen.');
                }
                $this->view->assign('show', 'newaccount');
            }

            // Try login
            if($this->__getRequestVar('do') == 'login' && $this->__getRequestVar('email') && $this->__getRequestVar('password')) {
                $result = $this->getApi()->Call('Api', 'FindByLogin', array(
                    'email' => $this->__getRequestVar('email'),
                    'password' => $this->__getRequestVar('password')
                ));
                if (!isset($result->errorCode) && !empty($result->api_key)) {
                    $UserResult = $this->getApi()->Call('User', 'FindByApiKey', array('api_key' => $result->api_key));
                    if (!isset($UserResult->errorCode) && !empty($UserResult->user_id)) {
                        $this->getSession()->setApiKey($UserResult->user_api_key);
                        $this->getSession()->setUserlevel($UserResult->user_userlevel);
                        header('Location:./');
                    } else {
                        // Login failed
                        $this->view->assign('messagestandardlogin', 'Keine Benutzerdaten zu diesem API Key gefunden.');
                    }
                } else {
                    $this->view->assign('messagestandardlogin', 'Die Anmeldung ist fehlgeschlagen.');
                }
                $this->view->assign('show', 'standardlogin');
            }

            // Output
            $this->render(true);
        }
    }