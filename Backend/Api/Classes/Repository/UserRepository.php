<?php
    namespace LetsShoot\Sachkundetrainer\Backend\Repository;

    use LetsShoot\Sachkundetrainer\Backend\Model\UserModel;
    use LetsShoot\Sachkundetrainer\Backend\Repository;

    /**
     * Class UserRepository
     * @package LetsShoot\Sachkundetrainer\Backend\Repository
     */
    class UserRepository extends Repository {
        /**
         * @var array $config array with table definitions for repo base class
         */
        private $repoConfig = array(
            'datatable' => 'user',
            'idfield' => 'user_id',
            'orderfield' => 'user_id',
            'orderby' => 'ASC',
            'model' => 'UserModel',
        );
        public function __construct() {
            parent::__construct($this->repoConfig);
        }

        /**
         * Increase value of field by 1
         * @param string $field
         * @param int $id
         * @return UserModel|null
         */
        public function IncreaseCountField(string $field, int $id): ?UserModel
        {
            $conf = $this->getConfig();
            $id = intval($id);
            try {
                $q = $this->prepare("UPDATE ".$conf['datatable']." SET $field = $field + 1 WHERE ".$conf['idfield']." = $id");
                try {
                    $this->beginTransaction();
                    $q->execute();
                    $this->commit();

                    $this->getLogger()->Log(APPLICATION_LOG_LEVEL_INFO, 'increased '.$conf['datatable'].'.'.$field.' by 1');

                    return $this->findById($id);
                }
                catch( \PDOException $e ) {
                    $this -> rollback();
                    return null;
                }
            }
            catch( \PDOException $e ) {
                return null;
            }
        }
    }
