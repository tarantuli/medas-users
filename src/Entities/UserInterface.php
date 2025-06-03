<?php

declare(strict_types=1);

namespace Medas\Users\Entities;

use Medas\Core\Interfaces\HasId;

interface UserInterface extends HasId
{
    public function logonName(): string;

    public function passwordHash(): string;

    public function setPasswordHash(string $hash): void;

    public function isConfirmed(): bool;

    public function isActive(): bool;

    public function isBlocked(): bool;

    public function displayName(): string|null;
}
