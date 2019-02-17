<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Control;

    use LetsShoot\Sachkundetrainer\Backend\Control;
    use LetsShoot\Sachkundetrainer\Backend\Repository\UserRepository;
    use LetsShoot\Sachkundetrainer\Backend\Salt;
    use LetsShoot\Sachkundetrainer\Backend\ErrorResponse;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    /**
     * Class ApiControl
     * @package LetsShoot\Sachkundetrainer\Backend
     */
    class ApiControl extends Control {
        /**
         * ApiControl constructor.
         */
        public function __construct() {
            parent::__construct(false);

            // Action
            if(!method_exists($this->getResponse(),'getErrorCode')) {
                $action = $this->__getRequestVar('action');
                // Check if action parameter is set, if not set error
                if ($action !== null) {
                    if (method_exists($this, $action)) {
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, "CALL " . get_class($this) . '->' . $action . ': ' . print_r($this->getRequest(), true));
                        $this->$action();
                    } else {
                        $Error = new ErrorResponse();
                        $Error->setErrorActionNotImplemented();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorNoActionSet();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                }
            }

            // Mark end of request in log
            $this->getLogger()->MarkEndOfRequest();
        }

        public function RequestPasswordReset() {
            if($this->__getRequestVar('email')) {
                // Build search array
                $searchArray = array('user_email' => $this->__getRequestVar('email'));
                // Get data
                $UserRepository = new UserRepository();
                $DataObject = $UserRepository->findAll($searchArray);
                // Answer with empty response to prevent checking if an email address is present in the database
                $this->setResponse(
                    array('user_id' => 0)
                );
                if(is_array($DataObject) && count($DataObject) == 1) {
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);

                    // Send mail with static api key to user
                    $PHPMailer = new PHPMailer(true);
                    try {
                        $PHPMailer->setFrom(APPLICATION_MAIL_FROM, APPLICATION_MAIL_NAME);
                        $PHPMailer->addAddress($DataObject[0]->getUserEmail(), $DataObject[0]->getUserName());
                        $PHPMailer->isHTML(true);
                        $PHPMailer->CharSet = 'UTF-8';
                        $PHPMailer->Subject = 'Waffensachkunde-Trainer: Kennwort vergessen';
                        $PHPMailer->Body = '<p>Sie haben Ihr Waffensachkunde-Trainer Kennwort vergessen? Kein Problem!</p>
                            <p>Melden Sie sich mit folgendem Link an und ändern Sie dann Ihr Kennwort:</p>
                            <p>'.APPLICATION_MAIL_WEB_URI.'?api_key='.$DataObject[0]->getUserApiKey().'</p>
                            <p>--<br/>
                            Bitte antworten Sie nicht auf diese E-Mail, diese E-Mail wurde automatisch erzeugt.</p>';
                        $PHPMailer->AltBody = strip_tags($PHPMailer->Body);
                        $PHPMailer->send();
                    } catch (Exception $e) {
                        $Error = new ErrorResponse();
                        $Error->setErrorException();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $PHPMailer->ErrorInfo);
                    }
                }
            }
            else {
                $Error = new ErrorResponse();
                $Error->setErrorParametersMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
            }
        }

        public function FindByLogin() {
            if(
                isset($this->getRequest()['email']) && isset($this->getRequest()['password'])
                 &&
                !empty($this->getRequest()['email']) && !empty($this->getRequest()['password'])
            ) {
                // Build search array
                $searchArray = array(
                    'user_email' => $this->getRequest()['email'],
                    'user_password' => Salt::Salt($this->getRequest()['password'])
                );
                // Get data
                $UserRepository = new UserRepository();
                $DataObject = $UserRepository->findAll($searchArray);
                if(is_array($DataObject) && count($DataObject) == 1) {
                    $this->setResponse(
                        array('api_key'=>$DataObject[0]->getUserApiKey())
                    );
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                }
                else {
                    $Error = new ErrorResponse();
                    $Error->setErrorReadingObject();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                }
            }
            else {
                $Error = new ErrorResponse();
                $Error->setErrorParametersMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
            }
        }
    }
