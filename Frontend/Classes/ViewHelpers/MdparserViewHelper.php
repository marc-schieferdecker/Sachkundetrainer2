<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\ViewHelpers;

    use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
    use LetsShoot\Sachkundetrainer\Frontend\Util\Parsedown;

    class MdparserViewHelper extends AbstractViewHelper {
        /**
         * @return void
         */
        public function initializeArguments() {
            $this->registerArgument('file', 'string', 'Path to MD file to parse and return as html', true);
        }

        public function render(): string {
            $file = preg_replace('#[^a-z\.]#i','',basename($this->arguments['file'],'.md'));
            $md = new Parsedown();
            $pd = $md->parse(file_get_contents(APPLICATION_DIRECTORY.'/../Documentation/'.$file.'.md'));
            if($pd) {
                $pd = str_replace( './', './?ctrl=Help&readmefile=', $pd );
                return $pd;
            }
            else return '';
        }
    }
