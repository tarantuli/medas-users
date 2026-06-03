# medas-users

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

Provides a ready-to-use `User` entity and REST login/logout endpoints for `medas-http-request-handler` applications. It ships a concrete `User` entity, a `UserInterface` contract, `POST /login` and `POST /logout` route handlers, a `LoginVoteHandler` that grants public access to those two endpoints, and a `LoginEvent` dispatched on every login attempt.

**`User` entity fields:**

| Property       | Type              | Notes                                     |
|----------------|-------------------|-------------------------------------------|
| `id`           | `Uuid`            | Primary key                               |
| `logonName`    | `string`          | Unique; used for login                    |
| `passwordHash` | `string` (binary) | bcrypt; verified with `password_verify()` |
| `isConfirmed`  | `bool`            | Default `false`; must be `true` to log in |
| `isActive`     | `bool`            | Default `true`; must be `true` to log in  |
| `isBlocked`    | `bool`            | Default `false`; blocks login when `true` |
| `displayName`  | `string\|null`    | Optional display name                     |
| `createdAt`    | `DateTime`        | Via `Timestamps` trait                    |
| `modifiedAt`   | `DateTime`        | Via `Timestamps` trait                    |

**Login flow:**

1. `POST /login` with `{"userName": "…", "password": "…"}`
2. Fetches the user by `logonName` where `isActive = true`
3. Verifies the password with `password_verify()`
4. Checks `isBlocked` and `isConfirmed`
5. Rehashes the password if `password_needs_rehash()` returns `true`
6. Dispatches `LoginEvent` with the result and user
7. Creates `AuthenticationData` carrying the user id and dispatches it (listeners may add `$additionalData`)
8. Returns `{"authToken": "…", "user": {"displayName": "…"}}`

**Logout flow:** `POST /logout` extracts the bearer token from the `Authorization` header and calls `AuthenticationTokenController::invalidate()`.

`LoginVoteHandler` listens to `AuthorizationVote` and grants `AllowedAccess::Allowed` for `POST /login` and `POST /logout` unconditionally, so these endpoints work without authentication.

## Configuration options

| Option              | Default | Description                            |
|---------------------|---------|----------------------------------------|
| `users.users-store` | `users` | Store/table name for the `User` entity |

The `rest-request-handler.users-class` option (from `medas-rest-request-handler`) must also be set to `Medas\Users\Entities\User` (or your custom class) for bearer token authentication to work.

## Usage

### Package developer context

Register the package:

```php
use Medas\Users\UsersPackage;

UsersPackage::instance();
```

**Minimum configuration** — set the users class for bearer token auth and wire up an `AuthenticationTokenController` (e.g. `NamedTokenManager` from `medas-api-keys`):

```yaml
rest-request-handler:
  users-class: Medas\Users\Entities\User

users:
  users-store: users
```

**Creating a user:**

```php
use Medas\Users\Entities\User;
use Medas\EntityManager\EntityManager;
use Medas\Core\Attributes\Service;

#[Service]
readonly class UserRegistrationService
{
    public function __construct(
        private EntityManager $entityManager,
    ) {}

    public function register(string $logonName, string $password): User
    {
        /** @var User $user */
        $user = $this->entityManager->create(User::class, [
            'logonName'    => $logonName,
            'passwordHash' => password_hash($password, PASSWORD_DEFAULT),
            'isConfirmed'  => true,
            'isActive'     => true,
        ]);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
```

**Listening to the `LoginEvent`:**

```php
use Medas\Users\Events\LoginEvent;
use Medas\Users\RestControllers\Logins\Result;
use Medas\Core\Attributes\{EventListener, Service};

#[Service]
readonly class LoginAuditListener
{
    public function __construct(
        private AuditLogger $auditLogger,
    ) {}

    #[EventListener]
    public function onLogin(LoginEvent $event): void
    {
        $this->auditLogger->record(
            userName: $event->userName,
            result: $event->result->name,
            userId: $event->user?->id(),
        );
    }
}
```

`LoginEvent::$result` is one of: `Success`, `UserNotFound`, `WrongPassword`, `UserIsBlocked`, `UserIsNotConfirmed`.

**Adding extra data to the auth token** — listen to `AuthenticationData` and populate `$additionalData`:

```php
use Medas\Users\RestControllers\Logins\AuthenticationData;
use Medas\Core\Attributes\{EventListener, Service};

#[Service]
readonly class RoleDataListener
{
    public function __construct(
        private RoleRepository $roles,
    ) {}

    #[EventListener]
    public function onAuthData(AuthenticationData $data): void
    {
        // This data is embedded in the bearer token and available on every request
        $data->additionalData['roles'] = $this->roles->forUser($data->userId);
    }
}
```

**Using a custom user class** — implement `UserInterface` and annotate it with `#[Entity]`. Point `rest-request-handler.users-class` at your class:

```php
use Medas\Users\Entities\UserInterface;
use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\EntityManager\Traits\Timestamps;
use Medas\Core\Interfaces\Uuid;

#[Entity(store: 'app_users')]
class AppUser implements UserInterface
{
    use Timestamps;

    #[Id]
    public Uuid $id;

    public string $logonName;
    public string $passwordHash;
    public bool $isConfirmed = false;
    public bool $isActive    = true;
    public bool $isBlocked   = false;
    public string|null $displayName = null;

    public string $role = 'member'; // your own extra fields

    public function id(): Uuid { return $this->id; }
    public function logonName(): string { return $this->logonName; }
    public function passwordHash(): string { return $this->passwordHash; }
    public function setPasswordHash(string $hash): void { $this->passwordHash = $hash; }
    public function isConfirmed(): bool { return $this->isConfirmed; }
    public function isActive(): bool { return $this->isActive; }
    public function isBlocked(): bool { return $this->isBlocked; }
    public function displayName(): string|null { return $this->displayName; }
}
```

### Backend user context

**Logging in:**

```http
POST /login
Content-Type: application/json

{"userName": "alice@example.com", "password": "secret"}
```

Response:

```json
{
    "authToken": "…bearer token…",
    "user": {
        "displayName": "Alice"
    }
}
```

**Logging out:**

```http
POST /logout
Authorization: Bearer …token…
```

Response: `{"result": true}` on success, `{"result": false}` if no valid bearer token was present.

**Subsequent authenticated requests:**

```http
GET /invoices
Authorization: Bearer …token…
```

The bearer token is validated and the user entity is loaded automatically by `BearerTokenAuthenticator` (from `medas-rest-request-handler`) on every request.

**Blocking a user** — set `isBlocked = true` and flush. The user's existing tokens remain valid until they expire or are explicitly invalidated; to force immediate logout call `AuthenticationTokenController::invalidate($token)`.
