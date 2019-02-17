<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Control;

    use LetsShoot\Sachkundetrainer\Backend\Control;
    use LetsShoot\Sachkundetrainer\Backend\ErrorResponse;
    use LetsShoot\Sachkundetrainer\Backend\Repository\UserRepository;
    use LetsShoot\Sachkundetrainer\Backend\Model\UserModel;
    use LetsShoot\Sachkundetrainer\Backend\Salt;

    /**
     * Class UserControl
     * @package LetsShoot\Sachkundetrainer\Backend\Control
     */
    class UserControl extends Control {
        /**
         * UserControl constructor.
         */
        public function __construct() {
            parent::__construct();

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

        public function Delete() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN && $this->getUser()->getUserId() != $this->__getRequestVar('id')) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $UserRepository = new UserRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $UserModel = $UserRepository->findById( $this->__getRequestVar('id') );
                    if($UserModel) {
                        if($UserRepository->deleteEntryById($UserModel)) {
                            $this->setResponse($UserModel);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $UserModel);
                        }
                        else {
                            $Error = new ErrorResponse();
                            $Error->setErrorDeleteObject();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
                    }
                    else {
                        $Error = new ErrorResponse();
                        $Error->setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }

        public function Add()
        {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $data = $this->getRequest();
                // Remove forbidden fields
                unset($data['user_id']);
                unset($data['user_api_key']);
                // Salt password
                $data['user_password'] = Salt::Salt($data['user_password']);
                // Convert config array
                if($this->__getRequestVar('user_config')) {
                    $data['user_config'] = json_encode($data['user_config']);
                }
                // Convert answered array
                if($this->__getRequestVar('user_answered')) {
                    $data['user_answered'] = json_encode($data['user_answered']);
                }

                // Repo
                $UserRepository = new UserRepository();

                // Gen object
                $UserModel = new UserModel();
                foreach ($UserRepository->getFieldlist() AS $fdef) {
                    if (isset($data[$fdef['field']])) {
                        $setMethod = $UserRepository->__getMethodNameFromFieldname($fdef['field'], 'set');
                        $UserModel->$setMethod($data[$fdef['field']]);
                    }
                }
                $UserModel->setUserApiKey(Salt::getRandomApiKey());
                // Restrict userlevel
                $ownUserlevel = $this->getUser()->getUserUserlevel();
                $desiredUserlevel = $UserModel->getUserUserlevel();
                if ($desiredUserlevel > $ownUserlevel && !empty($desiredUserlevel)) {
                    $UserModel->setUserUserlevel($ownUserlevel);
                }

                // Check if user email is unique
                $UseremailCheck = $UserRepository->findAll(array('user_email' => $this->__getRequestVar('user_email')));
                if (count($UseremailCheck) == 0) {
                    // Insert
                    $insertId = $UserRepository->insertEntry($UserModel);
                    if ($insertId) {
                        $DataObject = $UserRepository->findById($insertId);
                        $this->setResponse($DataObject);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                    } else {
                        $Error = new ErrorResponse();
                        $Error->setErrorAddingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorDuplicateEmailInClient();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                }
            }
        }

        public function CreateApiKey() {
            // Need to be client admin or updating own user entry
            if( $this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN && $this->getUser()->getUserId() != $this->__getRequestVar('id') ) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $UserRepository = new UserRepository();
                    $UserModel = $UserRepository->findById($this->__getRequestVar('id'));
                    if($UserModel) {
                        // Set new static api key to user object
                        $UserModel->setUserApiKey(Salt::getRandomApiKey());

                        // Update
                        if ($UserRepository->updateEntry($UserModel)) {
                            $DataObject = $UserRepository->findById($this->__getRequestVar('id'));
                            $this->setResponse($DataObject);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                        } else {
                            $Error = new ErrorResponse();
                            $Error->setErrorUpdateObject();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
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
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }

        public function Update() {
            // Need to be client admin or updating own user entry
            if( $this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN && $this->getUser()->getUserId() != $this->__getRequestVar('id') ) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $data = $this->getRequest();
                    // Remove forbidden fields
                    unset($data['user_id']);
                    unset($data['user_client_id']);
                    unset($data['user_api_key']);
                    if( !empty( $data['user_password'] )) {
                        $data['user_password'] = Salt::Salt($data['user_password']);
                    }
                    // Convert config array
                    if($this->__getRequestVar('user_config')) {
                        $data['user_config'] = json_encode($data['user_config']);
                    }
                    // Convert answered array
                    if($this->__getRequestVar('user_answered')) {
                        $data['user_answered'] = json_encode($data['user_answered']);
                    }

                    $UserRepository = new UserRepository();
                    $UserModel = $UserRepository->findById($this->__getRequestVar('id'));
                    if($UserModel) {
                        $OriginalUserLevel = $UserModel->getUserUserlevel();
                        // Check for email change
                        $emailFailure = false;
                        if($UserModel->getUserEmail() != $data['user_email']) {
                            // Check if user email is unique
                            $UseremailCheck = $UserRepository->findAll(array('user_email' => $data['user_email']));
                            if (count($UseremailCheck) > 0) {
                                $emailFailure = true;
                            }
                        }
                        // No duplicate email adresses in client
                        if($emailFailure === false) {
                            // Set data
                            foreach ($UserRepository->getFieldlist() AS $fdef) {
                                if (isset($data[$fdef['field']])) {
                                    $setMethod = $UserRepository->__getMethodNameFromFieldname($fdef['field'],'set');
                                    // Do not overwrite password when empty man!
                                    if ($fdef['field'] != 'user_password' || ($fdef['field'] == 'user_password' && !empty($data['user_password']))) {
                                        $UserModel->$setMethod($data[$fdef['field']]);
                                    }
                                }
                            }
                            // Restrict userlevel
                            $desiredUserlevel = $UserModel->getUserUserlevel();
                            if ($OriginalUserLevel != $desiredUserlevel && $this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                                $UserModel->setUserUserlevel($OriginalUserLevel);
                            }
                            // Update
                            if ($UserRepository->updateEntry($UserModel)) {
                                $DataObject = $UserRepository->findById($this->__getRequestVar('id'));
                                $this->setResponse($DataObject);
                                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                            } else {
                                $Error = new ErrorResponse();
                                $Error->setErrorUpdateObject();
                                $this->setResponse($Error);
                                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                            }
                        } else {
                            $Error = new ErrorResponse();
                            $Error->setErrorDuplicateEmailInClient();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
                    }
                    else {
                        $Error = new ErrorResponse();
                        $Error->setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }

        public function FindAll() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $UserRepository = new UserRepository();
                $DataObject = $UserRepository->findAll();
                $this->setResponse($DataObject);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
            }
        }

        public function FindByApiKey() {
            $this->setResponse($this->getUser());
            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $this->getUser());
        }

        public function FindById() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN && $this->getUser()->getUserId() != $this->__getRequestVar('id')) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $UserRepository = new UserRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $DataObject = $UserRepository->findById($this->__getRequestVar('id'));
                    if($DataObject) {
                        $this->setResponse($DataObject);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                    }
                    else {
                        $Error = new ErrorResponse();
                        $Error->setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                    }
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }

        public function FindByFields() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                // Build search array
                $searchArray = array();
                $searchFields = array(
                    'user_email',
                    'user_name'
                );
                foreach ($searchFields AS $field) {
                    if (isset($this->getRequest()[$field]) && $this->getRequest()[$field] !== '') {
                        $searchArray[$field] = '%' . $this->getRequest()[$field] . '%';
                    }
                }

                // Get data
                $UserRepository = new UserRepository();
                $DataObject = $UserRepository->findAll($searchArray, 'OR');
                $this->setResponse($DataObject);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
            }
        }

        public function IncreaseCountWrong() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $UserRepository = new UserRepository();
                $DataObject = $UserRepository->IncreaseCountField('user_count_wrong', $this->getUser()->getUserId());
                if($DataObject) {
                    $this->setResponse($DataObject);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                }
                else {
                    $Error = new ErrorResponse();
                    $Error->setErrorReadingObject();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                }
            }
        }

        public function IncreaseCountRight() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $UserRepository = new UserRepository();
                $DataObject = $UserRepository->IncreaseCountField('user_count_right', $this->getUser()->getUserId());
                if($DataObject) {
                    $this->setResponse($DataObject);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                }
                else {
                    $Error = new ErrorResponse();
                    $Error->setErrorReadingObject();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                }
            }
        }
    }
