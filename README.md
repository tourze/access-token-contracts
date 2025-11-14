# AccessToken Contracts

[English](README.md) | [中文](README.zh-CN.md)

A lightweight PHP library that provides interfaces for access token management. This package defines contracts for implementing access token services in your applications.

## Features

- 🔐 **Access Token Interface**: Standard interface for access token entities
- 🛠️ **Service Contract**: Well-defined service interface for token management
- 🏗️ **Framework Agnostic**: Works with any PHP 8.2+ application
- 🧪 **Testable**: Designed for easy unit testing and mocking
- 📦 **Zero Dependencies**: Only requires symfony/security-core for user interface

## Installation

```bash
composer require tourze/access-token-contracts
```

## Usage

### Basic Implementation

First, implement the `AccessTokenInterface` for your access token entity:

```php
<?php

use Tourze\AccessTokenContracts\AccessTokenInterface;

class MyAccessToken implements AccessTokenInterface
{
    private string $token;
    private \DateTimeInterface $expiresAt;
    private UserInterface $user;

    // Implement your access token logic
    public function getToken(): string
    {
        return $this->token;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTime();
    }
}
```

Then implement the `TokenServiceInterface` for token management:

```php
<?php

use Tourze\AccessTokenContracts\TokenServiceInterface;
use Tourze\AccessTokenContracts\AccessTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MyTokenService implements TokenServiceInterface
{
    public function createToken(
        UserInterface $user,
        ?int $expiresIn = null,
        ?string $deviceInfo = null
    ): AccessTokenInterface {
        $token = new MyAccessToken();
        $token->setUser($user);
        $token->setExpiresAt((new \DateTime())->add(new \DateInterval('PT' . ($expiresIn ?? 3600) . 'S')));
        $token->setDeviceInfo($deviceInfo);
        $token->generateToken();

        return $token;
    }
}
```

### Integration with Symfony

Register your service in your `services.yaml`:

```yaml
services:
    App\Service\TokenService:
        arguments:
            - '@doctrine.orm.entity_manager'
            - '%env(default:3600:ACCESS_TOKEN_DEFAULT_TTL)%'
```

## API Reference

### AccessTokenInterface

Base interface for all access token implementations. This interface defines the contract that access token classes must follow.

### TokenServiceInterface

Service interface for managing access tokens:

```php
interface TokenServiceInterface
{
    /**
     * Create a new access token for the given user
     *
     * @param UserInterface $user The user to create the token for
     * @param int|null $expiresIn Token expiration time in seconds (optional)
     * @param string|null $deviceInfo Device information for tracking (optional)
     * @return AccessTokenInterface The created access token
     */
    public function createToken(
        UserInterface $user,
        ?int $expiresIn = null,
        ?string $deviceInfo = null
    ): AccessTokenInterface;
}
```

## Testing

The package is designed with testability in mind. You can easily mock the interfaces in your tests:

```php
<?php

use PHPUnit\Framework\TestCase;
use Tourze\AccessTokenContracts\TokenServiceInterface;
use Tourze\AccessTokenContracts\AccessTokenInterface;

class TokenServiceTest extends TestCase
{
    public function testCreateToken()
    {
        $service = $this->createMock(TokenServiceInterface::class);
        $user = $this->createMock(UserInterface::class);
        $token = $this->createMock(AccessTokenInterface::class);

        $service->expects($this->once())
            ->method('createToken')
            ->with($user, 3600, 'mobile-device')
            ->willReturn($token);

        $result = $service->createToken($user, 3600, 'mobile-device');
        $this->assertInstanceOf(AccessTokenInterface::class, $result);
    }
}
```

## Requirements

- PHP 8.2 or higher
- Symfony Security Core ^7.3

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes in each version.