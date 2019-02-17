<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\ViewHelpers;

    use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
    use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
    use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
    use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
    use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

    class Nl2brViewHelper extends AbstractViewHelper {
        use CompileWithContentArgumentAndRenderStatic;

        /**
         * @var boolean
         */
        protected $escapeChildren = false;

        /**
         * @var boolean
         */
        protected $escapeOutput = false;

        /**
         * @return void
         */
        public function initializeArguments()
        {
            $this->registerArgument('value', 'mixed', 'The value to output');
        }

        /**
         * @param array $arguments
         * @param \Closure $renderChildrenClosure
         * @param RenderingContextInterface $renderingContext
         * @return mixed
         */
        public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
        {
            return nl2br($renderChildrenClosure());
        }

        /**
         * @param string $argumentsName
         * @param string $closureName
         * @param string $initializationPhpCode
         * @param ViewHelperNode $node
         * @param TemplateCompiler $compiler
         * @return mixed
         */
        public function compile($argumentsName, $closureName, &$initializationPhpCode, ViewHelperNode $node, TemplateCompiler $compiler)
        {
            $contentArgumentName = $this->resolveContentArgumentName();
            return sprintf(
                'isset(%s[\'%s\']) ? %s[\'%s\'] : %s()',
                $argumentsName,
                $contentArgumentName,
                $argumentsName,
                $contentArgumentName,
                $closureName
            );
        }
    }