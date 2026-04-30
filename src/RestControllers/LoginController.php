<?php

declare(strict_types=1);

namespace Medas\Users\RestControllers;

use Medas\Core\{Attributes\ConfigValue, Interfaces\AuthenticationTokenController};
use Medas\EntityManager\Repository;
use Medas\EntityManager\Selector\Selectors\WithValues;
use Medas\HttpRequestHandler\{Attributes\BodyArgument, ResponseTypes\Response};
use Medas\RestRequestHandler\{
    ConfigOptions\UsersClass,
    Responses\BadRequestResponse,
    Responses\EntityResponse
};
use Medas\Routing\{Methods\Post, Route};
use Medas\Users\{Entities\UserInterface, Events\LoginEvent};

#[Route('login')]
readonly class LoginController
{
    public function __construct(
        private AuthenticationTokenController $tokenController,
        private Repository                    $repository,

        #[ConfigValue(UsersClass::class)]
        private string                        $usersClass,
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
        $event = new LoginEvent($userName, $password);

        /** @var UserInterface|null $user */
        $user = $this->repository->fetchOne(new WithValues(
            $this->usersClass,
            ['logonName' => $userName, 'isActive' => true]
        ));

        if (!$user) {
            $event->result = Logins\Result::UserNotFound;

            dispatch($event);

            return new BadRequestResponse();
        }

        if (!password_verify($password, $user->passwordHash())) {
            $event->result = Logins\Result::WrongPassword;

            dispatch($event);

            return new BadRequestResponse();
        }

        if ($user->isBlocked()) {
            $event->result = Logins\Result::UserIsBlocked;

            dispatch($event);

            return new BadRequestResponse();
        }

        if (!$user->isConfirmed()) {
            $event->result = Logins\Result::UserIsNotConfirmed;

            dispatch($event);

            return new BadRequestResponse();
        }

        if (password_needs_rehash($user->passwordHash(), PASSWORD_DEFAULT)) {
            $user->setPasswordHash(password_hash($password, PASSWORD_DEFAULT));
        }

        $event->result = Logins\Result::Success;
        $event->user = $user;

        dispatch($event);

        $authenticationData = new Logins\AuthenticationData($user->id());

        // Dispatch so listeners can add additional data to the authentication data
        dispatch($authenticationData);

        $response = new EntityResponse([
            'authToken' => $this->tokenController->create($authenticationData),
            'user' => [
                'displayName' => $user->displayName(),
            ],
        ]);

        dispatch(new UserLoginResponse($user, $response));

        return $response;
    }
}
