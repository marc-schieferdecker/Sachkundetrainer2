<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Model;

    /**
     * Class FavouriteModel
     * @package LetsShoot\Sachkundetrainer\Backend\Model
     */
    class FavouriteModel implements \JsonSerializable {
        /**
         * @var int $favourite_id
         */
        private $favourite_id;

        /**
         * @var int $user_id
         */
        private $user_id;

        /**
         * @var int $question_id
         */
        private $question_id;

        /**
         * convert to json parseable object
         */
        public function jsonSerialize() {
            $jsonObject = array();
            $vars = get_class_vars(get_class($this));
            foreach($vars AS $var => $val) {
                if(!is_array($this->$var)) {
                    $jsonObject[$var] = $this->$var;
                }
                else {
                    foreach($this->$var AS $item) {
                        $jsonObject[$var][] = $item->jsonSerialize();
                    }
                }
            }
            return $jsonObject;
        }

        /**
         * @return int
         */
        public function getFavouriteId(): int
        {
            return $this->favourite_id;
        }

        /**
         * @param int $favourite_id
         */
        public function setFavouriteId(int $favourite_id): void
        {
            $this->favourite_id = $favourite_id;
        }

        /**
         * @return int
         */
        public function getUserId(): int
        {
            return $this->user_id;
        }

        /**
         * @param int $user_id
         */
        public function setUserId(int $user_id): void
        {
            $this->user_id = $user_id;
        }

        /**
         * @return int
         */
        public function getQuestionId(): int
        {
            return $this->question_id;
        }

        /**
         * @param int $question_id
         */
        public function setQuestionId(int $question_id): void
        {
            $this->question_id = $question_id;
        }
    }
