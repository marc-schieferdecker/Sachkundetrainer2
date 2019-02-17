<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Repository;

    use LetsShoot\Sachkundetrainer\Backend\Repository;

    /**
     * Class SubtopicRepository
     * @package LetsShoot\Sachkundetrainer\Backend\Repository
     */
    class SubtopicRepository extends Repository {
        /**
         * @var array $config array with table definitions for repo base class
         */
        private $repoConfig = array(
            'datatable' => 'subtopic',
            'idfield' => 'subtopic_id',
            'orderfield' => 'subtopic_number',
            'orderby' => 'ASC',
            'model' => 'SubtopicModel'
        );
        public function __construct() {
            parent::__construct($this->repoConfig);
        }
    }
