<?php

App::bind('auth', '\\app\\engine\\Auth', true);
App::bind('render', '\\app\\engine\\Render', true);
App::bind('router', '\\app\\engine\\Router', true);
App::bind('request', '\\app\\engine\\Request', true);
App::bind('session', '\\app\\engine\\Session', true);
App::bind(\app\interfaces\IRenderer::class, '\\app\\engine\\TwigRender'); 
App::bind('news', '\\app\\model\\repositories\\NewsRepository', true);
App::bind('products', '\\app\\model\\repositories\\ProductsRepository', true);
App::bind('users', '\\app\\model\\repositories\\UsersRepository', true);
App::bind('feedback', '\\app\\model\\repositories\\FeedbackRepository', true);
