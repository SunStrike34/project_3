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

    public static function redirect(string $pass): void
    {
        header("Location: " . $pass);
        exit();
    }

    public function getPage(array $pass): void
    {
        echo $this->templates->render("{$pass[0]}");
    }
}