<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Control;

    use LetsShoot\Sachkundetrainer\Backend\Control;
    use LetsShoot\Sachkundetrainer\Backend\ErrorResponse;
    use LetsShoot\Sachkundetrainer\Backend\Model\QuestionModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository\QuestionRepository;

    /**
     * Class QuestionControl
     * @package LetsShoot\Sachkundetrainer\Backend\Control
     */
    class QuestionControl extends Control {
        /**
         * QuestionControl constructor.
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
                $QuestionRepository = new QuestionRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $QuestionModel = $QuestionRepository->findById( $this->__getRequestVar('id') );
                    if($QuestionModel) {
                        if($QuestionRepository->deleteEntryById($QuestionModel)) {
                            $this->setResponse($QuestionModel);
                            $this->getLogger()->Log(APPLICATION_LOG_LEVEL_DEBUG, $QuestionModel);
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
                unset($data['question_id']);
                // Convert
                $data['question_count_wrong'] = intval($data['question_count_wrong']);
                $data['question_count_right'] = intval($data['question_count_right']);

                // Repo
                $QuestionRepository = new QuestionRepository();

                // Gen object
                $QuestionModel = new QuestionModel();
                foreach ($QuestionRepository->getFieldlist() AS $fdef) {
                    if (isset($data[$fdef['field']])) {
                        $setMethod = $QuestionRepository->__getMethodNameFromFieldname($fdef['field'], 'set');
                        $QuestionModel->$setMethod($data[$fdef['field']]);
                    }
                }

                // Insert
                $insertId = $QuestionRepository->insertEntry($QuestionModel);
                if ($insertId) {
                    $DataObject = $QuestionRepository->findById($insertId);
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
                    unset($data['question_id']);

                    $QuestionRepository = new QuestionRepository();
                    $QuestionModel = $QuestionRepository->findById($this->__getRequestVar('id'));
                    if($QuestionModel) {
                        // Set data
                        foreach ($QuestionRepository->getFieldlist() AS $fdef) {
                            if (isset($data[$fdef['field']])) {
                                $setMethod = $QuestionRepository->__getMethodNameFromFieldname($fdef['field'],'set');
                                $QuestionModel->$setMethod($data[$fdef['field']]);
                            }
                        }
                        // Update
                        if ($QuestionRepository->updateEntry($QuestionModel)) {
                            $DataObject = $QuestionRepository->findById($this->__getRequestVar('id'));
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
                $QuestionRepository = new QuestionRepository();

                $sort_by = $this->__getRequestVar('sort_by');
                $sort_order = $this->__getRequestVar('sort_order');
                if($sort_by || $sort_order) {
                    $repoConf = $QuestionRepository->getConfig();
                    if($sort_by) {
                        $repoConf['orderfield'] = $sort_by;
                    }
                    if($sort_order) {
                        $repoConf['orderby'] = $sort_order;
                    }
                    $QuestionRepository->setConfig($repoConf);
                }
                $limit = intval($this->__getRequestVar('limit'));
                if($limit) {
                    $QuestionRepository->applyLimit($limit);
                }

                $DataObject = $QuestionRepository->findAll();
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
                $QuestionRepository = new QuestionRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $DataObject = $QuestionRepository->findById($this->__getRequestVar('id'));
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

        public function FindByRandom() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $topic_id = intval($this->__getRequestVar('topic_id'));
                $subtopic_id = intval($this->__getRequestVar('subtopic_id'));
                $hard = intval($this->__getRequestVar('hard'));
                if($this->getUser()->getUserUserlevel() > APPLICATION_USERLEVEL_GUEST) {
                    $favourites = intval($this->__getRequestVar('favourites'));
                }
                else {
                    $favourites = 0;
                }

                // Open repository
                $QuestionRepository = new QuestionRepository();

                // Disable already answered questions? (do not use this option if favourite or hard questions are requested)
                $user_config = json_decode($this->getUser()->getUserConfig(),true);
                if($user_config && $user_config['disable_duplicate_questions'] && $favourites == 0 && $hard == 0) {
                    $user_answered = json_decode($this->getUser()->getUserAnswered(),true);
                    if(is_array($user_answered) && count($user_answered)) {
                        $QuestionRepository->restrictByQuestionIds($user_answered);
                    }
                }

                if($favourites) {
                    $DataObject = $QuestionRepository->FindByRandomFavourite($this->getUser()->getUserId());
                }
                elseif($hard) {
                    $DataObject = $QuestionRepository->FindByRandomHard($topic_id, $subtopic_id);
                }
                else {
                    $DataObject = $QuestionRepository->FindByRandom($topic_id, $subtopic_id);
                }
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

        public function IncreaseCountWrong() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $QuestionRepository = new QuestionRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $DataObject = $QuestionRepository->IncreaseCountField('question_count_wrong', $this->__getRequestVar('id'));
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

        public function IncreaseCountRight() {
            if($this->getUser()->getUserUserlevel() < APPLICATION_USERLEVEL_GUEST) {
                $Error = new ErrorResponse();
                $Error -> setErrorUserrightsMissing();
                $this->setResponse($Error);
                $this->getLogger()->Log(APPLICATION_LOG_LEVEL_WARN, $Error->getErrorMsg());
            }
            else {
                $QuestionRepository = new QuestionRepository();
                if ($this->__getRequestVar('id') !== null && !empty($this->__getRequestVar('id'))) {
                    $DataObject = $QuestionRepository->IncreaseCountField('question_count_right', $this->__getRequestVar('id'));
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
