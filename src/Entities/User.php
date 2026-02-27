<?php

declare(strict_types=1);

namespace Medas\Users\Entities;

use Medas\Core\{Interfaces\Uuid, Types\Binary};
use Medas\EntityManager\{Attributes\Entity, Attributes\Id, Attributes\IsUnique, Traits\Timestamps};
use Medas\Users\ConfigOptions;

#[Entity, Entity\StoreConfigOption(ConfigOptions\UsersStore::class)]
class User implements UserInterface
{
    use Timestamps;

    #[Id]
    public Uuid $id;

    #[IsUnique]
    public string $logonName;

    #[Binary]
    public string $passwordHash;

    public bool $isConfirmed = false;
    public bool $isActive = true;
    public bool $isBlocked = false;
    public string|null $displayName = null;

    public function id(): Uuid
    {
        return $this->id;
    }

    public function logonName(): string
    {
        return $this->logonName;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $hash): void
    {
        $this->passwordHash = $hash;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function displayName(): string|null
    {
        return $this->displayName;
    }
}
