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

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'url' => $this->url,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
        ];
    }
}
