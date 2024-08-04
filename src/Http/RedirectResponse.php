<?php

namespace App\Http;

use JetBrains\PhpStorm\NoReturn;

class RedirectResponse implements HttpResponseInterface
{

    /**
     * @param string $uri
     */
    public function __construct(private string $uri)
    {
    }

    #[NoReturn]
    public function send(): void
    {
        header('Location: '.$this->uri);
        exit;
    }
}