<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers\Logins;

enum Result: int
{
    case Success = 1;
    case UserNotFound = 2;
    case WrongPassword = 3;
    case UserIsBlocked = 4;
    case UserIsNotConfirmed = 5;
}
