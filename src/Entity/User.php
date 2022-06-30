<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private ?string $id;

    #[ORM\Id]
    #[ORM\Column(name: 'status', type: 'integer', length: 1, options: ['default' => 0])]
    private int $status = 0;

    #[ORM\Column(name: 'name', type: 'string', length: 25)]
    private ?string $name = null;

    #[ORM\OneToMany(
        mappedBy: 'users',
        targetEntity: Order::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Order $orders;
}