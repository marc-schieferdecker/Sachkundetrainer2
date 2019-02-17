<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Control;

    use LetsShoot\Sachkundetrainer\Backend\Control;
    use LetsShoot\Sachkundetrainer\Backend\ErrorResponse;
    use LetsShoot\Sachkundetrainer\Backend\Model\FavouriteModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository\FavouriteRepository;

    /**
     * Class FavouriteControl
     * @package LetsShoot\Sachkundetrainer\Backend\Control
     */
    class FavouriteControl extends Control {
        /**
         * FavouriteControl constructor.
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

        public function Remove() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                if ($this->__getRequestVar('question_id')) {
                    // Repo
                    $FavouriteRepository = new FavouriteRepository();

                    // Check if exists
                    $Favourite = $FavouriteRepository->FindByQuestionId($this->getUser()->getUserId(), $this->__getRequestVar('question_id'));
                    if ($Favourite) {
                        if ($FavouriteRepository->deleteEntryById($Favourite)) {
                            $this->setResponse($Favourite);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $Favourite);
                        } else {
                            $Error = new ErrorResponse();
                            $Error->setErrorDeleteObject();
                            $this->setResponse($Error);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
                        }
                    } else {
                        $Error = new ErrorResponse();
                        $Error->setErrorReadingObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
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
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                if($this->__getRequestVar('question_id')) {
                    $data = $this->getRequest();
                    $data['user_id'] = $this->getUser()->getUserId();

                    // Repo
                    $FavouriteRepository = new FavouriteRepository();

                    // Check if exists
                    $Favourite = $FavouriteRepository->FindByQuestionId($this->getUser()->getUserId(), $this->__getRequestVar('question_id'));
                    if($Favourite == null) {
                        // Gen object
                        $FavouriteModel = new FavouriteModel();
                        foreach ($FavouriteRepository->getFieldlist() AS $fdef) {
                            if (isset($data[$fdef['field']])) {
                                $setMethod = $FavouriteRepository->__getMethodNameFromFieldname($fdef['field'], 'set');
                                $FavouriteModel->$setMethod($data[$fdef['field']]);
                            }
                        }

                        // Insert
                        $insertId = $FavouriteRepository->insertEntry($FavouriteModel);
                        if ($insertId) {
                            $DataObject = $FavouriteRepository->findById($insertId);
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
                        $Error->setErrorDuplicateObject();
                        $this->setResponse($Error);
                        $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, $Error->getErrorMsg());
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
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_USER) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $FavouriteRepository = new FavouriteRepository();
                $DataObject = $FavouriteRepository->findByFieldvalue('user_id', $this->getUser()->getUserId());
                $this->setResponse($DataObject);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $DataObject);
            }
        }
    }
