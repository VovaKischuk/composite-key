<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private ?string $id;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'users')]
    #[
        ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id'),
        ORM\JoinColumn(name: 'user_status', referencedColumnName: 'status')
    ]
    private ?User $user = null;
}