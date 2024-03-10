<?php

namespace App\Controllers;

Class RedirectController
{
    public static function redirect(string $page): void
    {
        header("Location: " . $page);
        exit();
    }
}
