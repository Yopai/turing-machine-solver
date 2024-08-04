<?php

namespace App\Http;

class FileResponse implements HttpResponseInterface
{

    /**
     * @param string $filename
     */
    public function __construct(private string $filename)
    {
    }

    public function send()
    {
        if ($type = $this->getContentType($this->filename)) {
            header("Content-Type: $type");
        }
        readfile($this->filename);
    }

    /**
     * @param $filename
     * @return null|string
     */
    public function getContentType($filename): ?string
    {
        return match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'js' => 'text/javascript',
            'json' => 'application/json',
            'css' => 'text/css',
            default => \mime_content_type($filename)
        };
    }
}