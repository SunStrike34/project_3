<?php

namespace App\Controllers;

use League\Plates\Engine;

Class RenderController
{
    private $templates;

    public function __construct(Engine $templates)
    {
        $this->templates = $templates;
    }

    public function getPage(string $page, $params = []): void
    {
        echo $this->templates->render("{$page}", $params);
    }
}
