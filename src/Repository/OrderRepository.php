<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

class OrderRepository
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function findByUserId(string $userId): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->join(User::class, 'user', Join::WITH, 'o.user = user')
            ->where('user.id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getResult();
    }
}