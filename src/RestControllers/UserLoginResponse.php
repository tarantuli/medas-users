<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers;

use Medas\RestRequestHandler\Responses\EntityResponse;
use Medas\Users\Entities\UserInterface;

readonly class UserLoginResponse
{
    public function __construct(
        public UserInterface  $user,
        public EntityResponse $response,
    )
    {
    }
}
