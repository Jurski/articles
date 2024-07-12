<?php

namespace App\Repositories;

interface LikeRepositoryInterface
{
    public function insertLike(
        string $targetId,
        string $targetType
    ): void;
}