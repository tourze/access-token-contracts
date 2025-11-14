# AccessToken Contracts 访问令牌合约

[English](README.md) | [中文](README.zh-CN.md)

一个轻量级的 PHP 库，提供访问令牌管理的接口定义。此包为在应用程序中实现访问令牌服务定义了合约。

## 特性

- 🔐 **访问令牌接口**：为访问令牌实体提供标准化接口
- 🛠️ **服务合约**：为令牌管理提供定义明确的服务接口
- 🏗️ **框架无关**：适用于任何 PHP 8.2+ 应用程序
- 🧪 **可测试性**：设计用于简单的单元测试和模拟
- 📦 **零依赖**：只需要 symfony/security-core 用于用户接口

## 安装

```bash
composer require tourze/access-token-contracts
```

## 使用方法

### 基础实现

首先，为您的访问令牌实体实现 `AccessTokenInterface`：

```php
<?php

use Tourze\AccessTokenContracts\AccessTokenInterface;

class MyAccessToken implements AccessTokenInterface
{
    private string $token;
    private \DateTimeInterface $expiresAt;
    private UserInterface $user;

    // 实现您的访问令牌逻辑
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

然后为令牌管理实现 `TokenServiceInterface`：

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

### 与 Symfony 集成

在您的 `services.yaml` 中注册服务：

```yaml
services:
    App\Service\TokenService:
        arguments:
            - '@doctrine.orm.entity_manager'
            - '%env(default:3600:ACCESS_TOKEN_DEFAULT_TTL)%'
```

## API 参考

### AccessTokenInterface

所有访问令牌实现的基础接口。此接口定义了访问令牌类必须遵循的合约。

### TokenServiceInterface

访问令牌管理的服务接口：

```php
interface TokenServiceInterface
{
    /**
     * 为给定用户创建新的访问令牌
     *
     * @param UserInterface $user 创建令牌的用户
     * @param int|null $expiresIn 令牌过期时间（秒）（可选）
     * @param string|null $deviceInfo 用于跟踪的设备信息（可选）
     * @return AccessTokenInterface 创建的访问令牌
     */
    public function createToken(
        UserInterface $user,
        ?int $expiresIn = null,
        ?string $deviceInfo = null
    ): AccessTokenInterface;
}
```

## 测试

此包设计时考虑了可测试性。您可以在测试中轻松模拟接口：

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

## 系统要求

- PHP 8.2 或更高版本
- Symfony Security Core ^7.3

## 贡献

欢迎贡献！请随时提交 Pull Request。

## 许可证

此包在 MIT 许可证下发布。详情请参见 [LICENSE](LICENSE) 文件。

## 更新日志

查看 [CHANGELOG.md](CHANGELOG.md) 了解每个版本的更改列表。
