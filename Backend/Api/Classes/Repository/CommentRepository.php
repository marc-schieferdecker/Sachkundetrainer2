<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Repository;

    use LetsShoot\Sachkundetrainer\Backend\Repository;

    /**
     * Class CommentRepository
     * @package LetsShoot\Sachkundetrainer\Backend\Repository
     */
    class CommentRepository extends Repository {
        /**
         * @var array $config array with table definitions for repo base class
         */
        private $repoConfig = array(
            'datatable' => 'comment',
            'idfield' => 'comment_id',
            'orderfield' => 'comment_timestamp',
            'orderby' => 'ASC',
            'model' => 'CommentModel',
            'autoload_relations' => array(
                'user' => array(
                    'local_field' => 'comment_user_id',
                    'foreign_field' => 'user_id',
                    'foreign_repository' => 'PublicuserRepository',
                    'result_is_array' => false
                ),
            )
        );
        public function __construct() {
            parent::__construct($this->repoConfig);
        }

        /**
         * @param int $limit
         */
        public function applyLimit(int $limit): void {
            $config = $this->getConfig();
            $config['limit_sql'] = "LIMIT 0,$limit";
            $this->setConfig($config);
        }
    }
