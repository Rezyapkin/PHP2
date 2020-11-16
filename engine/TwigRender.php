<?php

namespace app\engine;


use app\interfaces\IRenderer;

class TwigRender implements IRenderer
{

    protected const TEMPLATE_DIR = ROOT_DIR . "/twigTemplates";

    public function __construct()
    {
         $loader = new \Twig\Loader\FilesystemLoader(static::TEMPLATE_DIR);
         $this->twig = new \Twig\Environment($loader);

    }    

    public function renderTemplate($template, $params = []) {
        return $this->twig->render($template . '.twig', $params);
    }
}