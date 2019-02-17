<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Model;

    /**
     * Class SubtopicModel
     * @package LetsShoot\Sachkundetrainer\Backend\Model
     */
    class SubtopicModel implements \JsonSerializable {
        /**
         * @var int $subtopic_id
         */
        private $subtopic_id;

        /**
         * @var string $subtopic_name
         */
        private $subtopic_name;

        /**
         * @var string $subtopic_number
         */
        private $subtopic_number;

        /**
         * @var int $subtopic_topic_parent_id
         */
        private $subtopic_topic_parent_id;

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
        public function getSubtopicId(): int
        {
            return $this->subtopic_id;
        }

        /**
         * @param int $subtopic_id
         */
        public function setSubtopicId(int $subtopic_id): void
        {
            $this->subtopic_id = $subtopic_id;
        }

        /**
         * @return string
         */
        public function getSubtopicName(): string
        {
            return $this->subtopic_name;
        }

        /**
         * @param string $subtopic_name
         */
        public function setSubtopicName(string $subtopic_name): void
        {
            $this->subtopic_name = $subtopic_name;
        }

        /**
         * @return string
         */
        public function getSubtopicNumber(): string
        {
            return $this->subtopic_number;
        }

        /**
         * @param string $subtopic_number
         */
        public function setSubtopicNumber(string $subtopic_number): void
        {
            $this->subtopic_number = $subtopic_number;
        }

        /**
         * @return int
         */
        public function getSubtopicTopicParentId(): int
        {
            return $this->subtopic_topic_parent_id;
        }

        /**
         * @param int $subtopic_topic_parent_id
         */
        public function setSubtopicTopicParentId(int $subtopic_topic_parent_id): void
        {
            $this->subtopic_topic_parent_id = $subtopic_topic_parent_id;
        }
    }
