<?php

namespace App\Helpers;

class FileData
{

    public function __construct(
        public string $name,
        public string $path,
        public string $url,
        public string $mimeType,
        public int $size)
    {

    }
}
