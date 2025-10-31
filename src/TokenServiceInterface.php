<?php

declare(strict_types=1);

namespace Tourze\AccessTokenContracts;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 异步队列
 */
interface TokenServiceInterface
{
    /**
     * 创建新的访问令牌
     */
    public function createToken(UserInterface $user, ?int $expiresIn = null, ?string $deviceInfo = null): AccessTokenInterface;
}
