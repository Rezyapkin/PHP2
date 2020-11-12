<?php

namespace app\engine;

use app\interfaces\IRenderer;

class Render implements IRenderer
{
    protected const TEMPLATE_DIR = ROOT_DIR . "/views";

    public function renderTemplate($template, $params = []) {
        ob_start();
        extract($params);
        $templatePath = (static::TEMPLATE_DIR) . '/' . $template . ".php";
        if (file_exists($templatePath)) {
            include $templatePath;
        }
        return ob_get_clean();
    }
}