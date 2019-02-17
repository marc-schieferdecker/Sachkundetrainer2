<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Model;

    /**
     * Class PublicuserModel
     * @package LetsShoot\Sachkundetrainer\Backend\Model
     */
    class PublicuserModel implements \JsonSerializable {
        /**
         * @var int $user_id
         */
        private $user_id;

        /**
         * @var int $user_userlevel
         */
        private $user_userlevel;

        /**
         * @var string $user_email
         */
        private $user_email;

        /**
         * @var string $user_name
         */
        private $user_name;

        /**
         * @var int $user_count_wrong
         */
        private $user_count_wrong;

        /**
         * @var int $user_count_right
         */
        private $user_count_right;

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
        public function getUserUserlevel(): int
        {
            return $this->user_userlevel;
        }

        /**
         * @param int $user_userlevel
         */
        public function setUserUserlevel(int $user_userlevel): void
        {
            $this->user_userlevel = $user_userlevel;
        }

        /**
         * @return string
         */
        public function getUserEmail(): string
        {
            return $this->user_email;
        }

        /**
         * @param string $user_email
         */
        public function setUserEmail(string $user_email): void
        {
            $this->user_email = $user_email;
        }

        /**
         * @return string
         */
        public function getUserName(): string
        {
            return $this->user_name;
        }

        /**
         * @param string $user_name
         */
        public function setUserName(string $user_name): void
        {
            $this->user_name = $user_name;
        }

        /**
         * @return int
         */
        public function getUserCountWrong(): ?int
        {
            return $this->user_count_wrong;
        }

        /**
         * @param int $user_count_wrong
         */
        public function setUserCountWrong(int $user_count_wrong): void
        {
            $this->user_count_wrong = $user_count_wrong;
        }

        /**
         * @return int
         */
        public function getUserCountRight(): ?int
        {
            return $this->user_count_right;
        }

        /**
         * @param int $user_count_right
         */
        public function setUserCountRight(int $user_count_right): void
        {
            $this->user_count_right = $user_count_right;
        }
    }
