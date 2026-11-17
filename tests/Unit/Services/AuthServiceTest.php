<?php

namespace Tests\Unit\Services;

use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\RememberMeRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Services\AuthService;
use App\Services\CookieService;
use App\Services\PasswordService;
use App\Services\TokenService;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class AuthServiceTest extends TestCase
{
    private UserRepositoryInterface $userRepository;
    private RememberMeRepositoryInterface $rememberMeRepository;
    private PermissionRepositoryInterface $permissionRepository;
    private TokenService $tokenService;
    private CookieService $cookieService;
    private PasswordService $passwordService;
    private AuthService $authService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->rememberMeRepository = $this->createMock(RememberMeRepositoryInterface::class);
        $this->permissionRepository = $this->createMock(PermissionRepositoryInterface::class);
        $this->tokenService = $this->createMock(TokenService::class);
        $this->cookieService = $this->createMock(CookieService::class);
        $this->passwordService = $this->createMock(PasswordService::class);

        $this->authService = new AuthService(
            $this->userRepository,
            $this->rememberMeRepository,
            $this->permissionRepository,
            $this->tokenService,
            $this->cookieService,
            $this->passwordService
        );

        $_SESSION = [];
        $_COOKIE = [];

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_COOKIE = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    private function validUser(): array
    {
        return [
            'id'        => 1,
            'password'  => 'hashed_password',
            'is_active' => 1,
            'firstname' => 'Ana',
            'lastname'  => 'Silva',
            'role_id'   => 2,
        ];
    }

    public function testAuthenticateReturnsFalseWhenUserNotFound(): void
    {
        $this->userRepository->method('findByEmail')->willReturn(null);
        $this->passwordService->method('dummyHash')->willReturn('dummy');
        $this->passwordService->method('verify')->willReturn(false);

        $this->assertFalse($this->authService->authenticate ('nobody@example.com', 'secret'));
    }

    public function testAuthenticateReturnsFalseWhenPasswordIsInvalid(): void
    {
        $this->userRepository->method('findByEmail')->willReturn($this->validUser());
        $this->passwordService->method('verify')->willReturn(false);

        $this->assertFalse($this->authService->authenticate('ana@example.com', 'wrong'));
    }

    public function testAuthenticateReturnsFalseWhenUserIsInactive(): void
    {
        $user = $this->validUser();
        $user['is_active'] = 0;

        $this->userRepository->method('findByEmail')->willReturn($user);
        $this->passwordService->method('verify')->willReturn(true);

        $this->assertFalse($this->authService->authenticate('ana@example.com', 'secret'));
    }

    public function testAuthenticateSucceedsAndCreatesSession(): void
    {
        $this->userRepository->method('findByEmail')->willReturn($this->validUser());
        $this->passwordService->method('verify')->willReturn(true);
        $this->userRepository->expects($this->once())->method('updateLastLogin')->with(1);

        $this->assertTrue($this->authService->authenticate('ana@example.com', 'secret'));

        $this->assertSame(1, $_SESSION['user']['id']);
        $this->assertSame('Ana', $_SESSION['user']['firstname']);
        $this->assertSame('Silva', $_SESSION['user']['lastname']);
        $this->assertSame(2, $_SESSION['user']['role_id']);
        $this->assertTrue($_SESSION['user']['logged']);
    }

    public function testAuthenticateWithRememberStoresTokenAndCookie(): void
    {
        $this->userRepository->method('findByEmail')->willReturn($this->validUser());
        $this->passwordService->method('verify')->willReturn(true);
        $this->tokenService->method('generateToken')->willReturn('raw_token');
        $this->tokenService->method('hashToken')->willReturn('hashed_token');

        $this->rememberMeRepository->expects($this->once())
            ->method('store')
            ->with(1, 'hashed_token', $this->isInstanceOf(\DateTimeInterface::class));

        $this->cookieService->expects($this->once())
            ->method('setCookie')
            ->with('remember_me', 'raw_token', $this->anything(), '/');

        $this->assertTrue($this->authService->authenticate('ana@example.com', 'secret', true));
    }

    public function testIsAuthenticatedReturnsFalseWithoutSessionOrCookie(): void
    {
        $this->assertFalse($this->authService->isAuthenticated());
    }

    public function testIsAuthenticatedReturnsTrueWithValidSession(): void
    {
        $_SESSION['user'] = ['id' => 1, 'logged' => true];

        $this->assertTrue($this->authService->isAuthenticated());
    }

    public function testIsAuthenticatedReturnsFalseWhenLoggedFlagMissing(): void
    {
        $_SESSION['user'] = ['id' => 1];

        $this->assertFalse($this->authService->isAuthenticated());
    }

    public function testIsAuthenticatedViaRememberMeCookie(): void
    {
        $_COOKIE['remember_me'] = 'some_token';

        $this->tokenService->method('hashToken')->willReturn('hashed_token');
        $this->rememberMeRepository->method('findValidUserByToken')
            ->willReturn([
                'id'        => 1,
                'firstname' => 'Ana',
                'lastname'  => 'Silva',
                'role_id'   => 2,
            ]);

        $this->rememberMeRepository->expects($this->once())->method('delete')->with('hashed_token');
        $this->tokenService->method('generateToken')->willReturn('new_token');
        $this->rememberMeRepository->method('store')->willReturn(1);
        $this->cookieService->method('setCookie')->willReturn(true);

        $this->assertTrue($this->authService->isAuthenticated());
        $this->assertSame(1, $_SESSION['user']['id']);
    }

    public function testGetCurrentUserReturnsNullWhenNotAuthenticated(): void
    {
        $this->assertNull($this->authService->getCurrentUser());
    }

    public function testGetCurrentUserReturnsSessionUser(): void
    {
        $_SESSION['user'] = ['id' => 1, 'logged' => true];

        $this->assertSame(['id' => 1, 'logged' => true], $this->authService->getCurrentUser());
    }

    public function testLogoutDeletesRememberMeTokenAndCookie(): void
    {
        $_COOKIE['remember_me'] = 'some_token';

        $this->tokenService->method('hashToken')->willReturn('hashed_token');
        $this->rememberMeRepository->expects($this->once())->method('delete')->with('hashed_token');
        $this->cookieService->expects($this->once())->method('deleteCookie')->with('remember_me', '/');
        $this->rememberMeRepository->expects($this->once())->method('deleteOnLogout')->with(1);

        $this->authService->logout(1);
    }

    public function testLogoutWithoutCookieOnlyCallsDeleteOnLogout(): void
    {
        $this->rememberMeRepository->expects($this->once())->method('deleteOnLogout')->with(1);

        $this->authService->logout(1);
    }

    public function testHasPermissionReturnsFalseWhenNotAuthenticated(): void
    {
        $this->assertFalse($this->authService->hasPermission('users.view'));
    }

    public function testHasPermissionLoadsAndCachesPermissions(): void
    {
        $_SESSION['user'] = ['id' => 1, 'role_id' => 2, 'logged' => true];

        $this->permissionRepository->expects($this->once())
            ->method('getPermissionsByRoleId')
            ->with(2)
            ->willReturn(['users.view', 'users.create']);

        $this->assertTrue($this->authService->hasPermission('users.view'));
        $this->assertFalse($this->authService->hasPermission('nonexistent'));
    }
}
