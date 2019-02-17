<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\ViewHelpers;

    use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

    class NumberformatViewHelper extends AbstractViewHelper {
        /**
         * @return void
         */
        public function initializeArguments() {
            $this->registerArgument('number', 'int', 'Number to format', true);
            $this->registerArgument('decimals', 'int', 'Decimals', false);
            $this->registerArgument('dec_point', 'int', 'Decimal point', false);
            $this->registerArgument('thousands_seperator', 'int', 'Thousands seperator', false);
        }

        public function render(): string {
            return number_format(
                $this->arguments['number'],
                $this->arguments['decimals'] ? $this->arguments['decimals'] : 2,
                $this->arguments['dec_point'] ? $this->arguments['dec_point'] : ',',
                $this->arguments['thousands_seperator'] ? $this->arguments['thousands_seperator'] : '.'
            );
        }
    }