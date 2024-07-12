<?php

namespace App\Repositories;

use Carbon\Carbon;
use DateTimeInterface;
use Medoo\Medoo;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;


class SqliteLikeRepository implements LikeRepositoryInterface
{
    private Medoo $database;
    private LoggerInterface $logger;

    public function __construct(Medoo $database, LoggerInterface $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }

    public function insertLike(
        string $targetId,
        string $targetType
    ): void
    {
        $response = $this->database->insert('likes', [
            'id' => Uuid::uuid4()->toString(),
            'target_id' => $targetId,
            'target_type' => $targetType,
            'timestamp' => Carbon::now()->format(DateTimeInterface::ATOM),
        ]);

        if ($response) {
            $this->logger->info('Inserted a like!');
        } else {
            $this->logger->error('Failed to insert a like!');
        }
    }
}