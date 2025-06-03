<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers;

use Medas\ApiKeys\NamedTokenManager;
use Medas\Core\Attributes\ConfigValue;
use Medas\EntityManager\Repository;
use Medas\EntityManager\Selector\Selectors\WithValues;
use Medas\HttpRequestHandler\{Attributes\BodyArgument, ResponseTypes\Response};
use Medas\RestRequestHandler\{
    ConfigOptions\UsersClass,
    Responses\BadRequestResponse,
    Responses\EntityResponse
};
use Medas\Routing\{Methods\Post, Route};
use Medas\Users\UserInterface;

#[Route('login')]
readonly class LoginController
{
    public function __construct(
        private Repository        $repository,
        private NamedTokenManager $namedTokenManager,

        #[ConfigValue(UsersClass::class)]
        private string            $usersClass,
    )
    {
    }

    #[Post]
    public function login(
        #[BodyArgument('userName')]
        string $userName,

        #[BodyArgument('password')]
        string $password
    ): Response
    {
        /** @var UserInterface|null $user */
        $user = $this->repository->fetchOne(new WithValues(
            $this->usersClass,
            ['logonName' => $userName, 'isActive' => true]
        ));

        if (!$user) {
            return new BadRequestResponse();
        }

        if (!password_verify($password, $user->passwordHash())) {
            return new BadRequestResponse();
        }

        if (password_needs_rehash($user->passwordHash(), PASSWORD_DEFAULT)) {
            $user->setPasswordHash(password_hash($password, PASSWORD_DEFAULT));
        }

        if ($user->isBlocked()) {
            return new BadRequestResponse();
        }

        return new EntityResponse([
            'authToken' => $this->namedTokenManager->create((string) $user->id()),
            'user' => [
                'displayName' => $user->displayName(),
            ],
        ]);
    }
}
