<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers;

use Medas\Core\{Attributes\EventListener, Attributes\Service, Events\AllowedAccess};
use Medas\HttpRequestHandler\{Authorization\AuthorizationVote, Request\Method};

#[Service]
readonly class LoginVoteHandler
{
    #[EventListener]
    public function handle(AuthorizationVote $vote): void
    {
        if (in_array($vote->request->uri->uri, ['/login', '/logout'], true)
                && $vote->request->method === Method::Post) {
            $vote->allowedAccess = AllowedAccess::Allowed;
        }
    }
}
