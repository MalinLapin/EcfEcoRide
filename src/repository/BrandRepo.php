<?php

namespace App\repository;

use App\model\BrandModel;


class BrandRepo extends BaseRepoSql
{
    protected string $tableName = 'brand';
    protected string $className = BrandModel::class;
}