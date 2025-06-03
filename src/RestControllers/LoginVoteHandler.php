<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers;

use Medas\Core\Attributes\{EventListener, Service};
use Medas\HttpRequestHandler\{Authorization\AuthorizationVote, Request\Method};

#[Service]
readonly class LoginVoteHandler
{
    #[EventListener]
    public function handle(AuthorizationVote $vote): void
    {
        if ($vote->request->uri->uri === 'login' && $vote->request->method === Method::Post) {
            $vote->allowedAccess = true;
        }
    }
}
