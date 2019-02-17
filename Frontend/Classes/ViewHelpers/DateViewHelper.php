<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\ViewHelpers;

    use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

    class DateViewHelper extends AbstractViewHelper {
        /**
         * @return void
         */
        public function initializeArguments() {
            $this->registerArgument('format', 'string', 'Date format (e.g: "d.m.Y H:i")', true);
            $this->registerArgument('timestamp', 'int', 'Unix timestamp to format', true);
        }

        public function render(): string {
            return date($this->arguments['format'], $this->arguments['timestamp']);
        }
    }