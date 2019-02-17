<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Repository;

    use LetsShoot\Sachkundetrainer\Backend\Model\PublicuserModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository;

    /**
     * Class PublicuserModel
     * @package LetsShoot\Sachkundetrainer\Backend\Repository
     */
    class PublicuserRepository extends Repository {
        /**
         * @var array $config array with table definitions for repo base class
         */
        private $repoConfig = array(
            'datatable' => 'user',
            'idfield' => 'user_id',
            'orderfield' => 'user_id',
            'orderby' => 'ASC',
            'model' => 'PublicuserModel',
        );
        public function __construct() {
            parent::__construct($this->repoConfig);
        }
    }
