<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers;

use Medas\ApiKeys\NamedTokenManager;
use Medas\HttpRequestHandler\{Request\HeaderFinder, RequestFactory, ResponseTypes\Response};
use Medas\RestRequestHandler\Responses\SuccessResponse;
use Medas\Routing\{Methods\Post, Route};

#[Route('logout')]
readonly class LogoutController
{
    public function __construct(
        private HeaderFinder      $headerFinder,
        private NamedTokenManager $namedTokenManager,
        private RequestFactory    $requestFactory,
    )
    {
    }

    #[Post]
    public function logout(): Response
    {
        $header = $this->headerFinder->find(
            $this->requestFactory->getWithoutExceptions()->serverData,
            'Authorization'
        );

        if ($header === null) {
            return new SuccessResponse(false);
        }

        if (!str_starts_with($header, 'Bearer ')) {
            return new SuccessResponse(false);
        }

        $token = substr($header, 7);

        $this->namedTokenManager->delete($token);

        return new SuccessResponse(true);
    }
}
