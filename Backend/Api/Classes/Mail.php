<?php
    namespace LetsShoot\Sachkundetrainer\Backend;

    use LetsShoot\Sachkundetrainer\Backend\Model\ConfigModel;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    /**
     * Class Mail - Helper Class for easy PHPMailer usage
     * @package LetsShoot\Sachkundetrainer\Backend
     */
    class Mail extends PHPMailer {
        /**
         * @var array $config
         */
        private $config;

        /**
         * Mail constructor.
         * @param array $config
         * @param bool|null $exceptions
         */
        public function __construct(array $config, bool $exceptions = null) {
            parent::__construct($exceptions);
            $this->config = $config;
        }

        /**
         * @param string $to_email
         * @param string $to_name
         * @param string $subject
         * @param string $mailBody
         * @throws Exception
         */
        public function send(string $to_email, string $to_name, string $subject, string $mailBody) {
            $this->setFrom($this->config['mail']['mail_from'], $this->config['mail']['mail_from_name']);
            if (!empty($this->config['mail']['mail_cc_to'])) {
                $this->addCC($this->config['mail']['mail_cc_to'], $this->config['mail']['mail_cc_to_name']);
            }
            $this->addAddress($to_email,$to_name);
            $this->isHTML(true);
            $this->CharSet = 'UTF-8';
            $this->Subject = $subject;
            $this->Body = $mailBody . $this->config['mail']['mail_signature'];
            $this->AltBody = strip_tags($this->Body);
            parent::send();
        }
    }