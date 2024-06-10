<?php

namespace App\Repositories;

use App\Contracts\AbstractEloquentRepository;
use App\Contracts\FileRepositoryInterface;
use App\Enums\FileType;
use App\Helpers\FileData;
use App\Models\File;
use App\Models\Base;

class FileRepository extends AbstractEloquentRepository implements FileRepositoryInterface
{
    /**
     * @var File
     */
    protected Base $model;

    public function __construct(File $model)
    {
        parent::__construct($model);
    }
}
