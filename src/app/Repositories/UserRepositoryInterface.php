<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function getBy(array $filters = []) : array;
}