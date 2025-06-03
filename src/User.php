<?php

declare(strict_types=1);

namespace Medas\Users;

use Medas\Core\Interfaces\Uuid;
use Medas\EntityManager\{Attributes\Entity, Attributes\Id, Attributes\IsUnique, Traits\Timestamps};

#[Entity, Entity\StoreConfigOption(ConfigOptions\UsersStore::class)]
class User implements UserInterface
{
    use Timestamps;

    #[Id]
    public Uuid $id;

    #[IsUnique]
    public string $logonName;

    public string $passwordHash;
    public bool $isActive = true;
    public bool $isBlocked = false;
    public string|null $displayName;

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
