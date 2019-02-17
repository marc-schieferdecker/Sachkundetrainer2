<?php
    namespace LetsShoot\Sachkundetrainer\Frontend\View;

    use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
    use TYPO3Fluid\Fluid\View\TemplatePaths;
    use TYPO3Fluid\Fluid\View\TemplateView;

    class SuccessView extends TemplateView {
        /**
         * ErrorView constructor.
         * @param string $controllerName
         * @param string $controllerAction
         * @param RenderingContextInterface|null $context
         */
        public function __construct(string $controllerName = 'Success', string $controllerAction = 'Success', RenderingContextInterface $context = null)
        {
            // Construct TemplateView
            parent::__construct($context);

            // Create base paths to fluid templates
            $paths = new TemplatePaths();
            $paths->setLayoutRootPaths(array(APPLICATION_LAYOUTS_PATH));
            $paths->setTemplateRootPaths(array(APPLICATION_TEMPLATES_PATH));
            $paths->setPartialRootPaths(array(APPLICATION_PARTIALS_PATH));

            // Set paths and MVC context
            $renderingContext = $this->getRenderingContext();
            $renderingContext->setTemplatePaths($paths);
            $renderingContext->setControllerName($controllerName);
            $renderingContext->setControllerAction($controllerAction);
            $this->setRenderingContext($renderingContext);

            // Set default title
            $this->setDefaultTitle();
        }

        /**
         * @param string $title
         */
        public function setTitle(string $title) {
            $this->assign('title', $title);
        }

        /**
         * @param string $msg
         */
        public function setMessage(string $msg) {
            $this->assign('message', $msg);
        }

        /**
         * @param string $api_key
         */
        public function setMessageNewApiKey(string $api_key) {
            $this->assign('message', 'Ihr neuer API-Schlüssel lautet: ' . $api_key);
        }

        /**
         * Set default title
         */
        public function setDefaultTitle()
        {
            $this->assign('title', 'Aktion erfolgreich');
        }
    }