<?php


namespace app\controllers;

use app\interfaces\IController;
use app\interfaces\IRenderer;


class Controller implements IController
{
    protected $action;
    protected $defaultAction = 'index';
    protected $layout = 'main';
    protected $useLayout = true;

    protected $renderer;

    /**
     * Controller constructor.
     * @param $action
     */
    public function __construct(IRenderer $renderer)
    {
         $this->renderer = $renderer;
    }


    public function errorAction() {
        header('HTTP/1.0 404 Not Found');
        header('Status: 404 Not Found');
        return $this->render('404', []);
    }

    public function render($template, $params = []) {
        if ($this->useLayout) {
            return $this->renderTemplate("layouts/{$this->layout}", [
                'menu' => $this->renderTemplate('menu', $params),
                'content' => $this->renderTemplate($template, $params)
            ]);
        } else {
            return $this->renderTemplate($template, $params);
        }
    }


    public function renderTemplate($template, $params = []) {
        return $this->renderer->renderTemplate($template, $params);
    }

}