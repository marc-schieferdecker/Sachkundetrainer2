<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Control;

    use LetsShoot\Sachkundetrainer\Backend\Control;
    use LetsShoot\Sachkundetrainer\Backend\ErrorResponse;
    use LetsShoot\Sachkundetrainer\Backend\Model\SubtopicModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository\SubtopicRepository;

    /**
     * Class SubtopicControl
     * @package LetsShoot\Sachkundetrainer\Backend\Control
     */
    class SubtopicControl extends Control {
        /**
         * SubtopicControl constructor.
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
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $SubtopicRepository = new SubtopicRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $SubtopicModel = $SubtopicRepository->findById( $this->__getRequestVar('id') );
                    if($SubtopicModel) {
                        if($SubtopicRepository->deleteEntryById($SubtopicModel)) {
                            $this->setResponse($SubtopicModel);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $SubtopicModel);
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
                unset($data['subtopic_id']);

                // Repo
                $SubtopicRepository = new SubtopicRepository();

                // Gen object
                $SubtopicModel = new SubtopicModel();
                foreach ($SubtopicRepository->getFieldlist() AS $fdef) {
                    if (isset($data[$fdef['field']])) {
                        $setMethod = $SubtopicRepository->__getMethodNameFromFieldname($fdef['field'], 'set');
                        $SubtopicModel->$setMethod($data[$fdef['field']]);
                    }
                }

                // Insert
                $insertId = $SubtopicRepository->insertEntry($SubtopicModel);
                if ($insertId) {
                    $DataObject = $SubtopicRepository->findById($insertId);
                    $this->setResponse($DataObject);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorAddingObject();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                }
            }
        }

        public function Update() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_ADMIN) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $data = $this->getRequest();
                    // Remove forbidden fields
                    unset($data['subtopic_id']);

                    $SubtopicRepository = new SubtopicRepository();
                    $SubtopicModel = $SubtopicRepository->findById($this->__getRequestVar('id'));
                    if($SubtopicModel) {
                        // Set data
                        foreach ($SubtopicRepository->getFieldlist() AS $fdef) {
                            if (isset($data[$fdef['field']])) {
                                $setMethod = $SubtopicRepository->__getMethodNameFromFieldname($fdef['field'],'set');
                                $SubtopicModel->$setMethod($data[$fdef['field']]);
                            }
                        }
                        // Update
                        if ($SubtopicRepository->updateEntry($SubtopicModel)) {
                            $DataObject = $SubtopicRepository->findById($this->__getRequestVar('id'));
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
                } else {
                    $Error = new ErrorResponse();
                    $Error->setErrorIdParameterEmpty();
                    $this->setResponse($Error);
                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
                }
            }
        }

        public function FindAll() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $SubtopicRepository = new SubtopicRepository();
                $DataObject = $SubtopicRepository->findAll();
                $this->setResponse($DataObject);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
            }
        }

        public function FindById() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $SubtopicRepository = new SubtopicRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $DataObject = $SubtopicRepository->findById($this->__getRequestVar('id'));
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
    }
