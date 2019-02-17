<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Repository;

    use LetsShoot\Sachkundetrainer\Backend\Repository;

    /**
     * Class TopicRepository
     * @package LetsShoot\Sachkundetrainer\Backend\Repository
     */
    class TopicRepository extends Repository {
        /**
         * @var array $config array with table definitions for repo base class
         */
        private $repoConfig = array(
            'datatable' => 'topic',
            'idfield' => 'topic_id',
            'orderfield' => 'topic_number',
            'orderby' => 'ASC',
            'model' => 'TopicModel',
            'autoload_relations' => array(
                'subtopics' => array(
                    'local_field' => 'topic_id',
                    'foreign_field' => 'subtopic_topic_parent_id',
                    'foreign_repository' => 'SubtopicRepository',
                    'result_is_array' => true
                ),
            ),
        );
        public function __construct() {
            parent::__construct($this->repoConfig);
        }
    }
