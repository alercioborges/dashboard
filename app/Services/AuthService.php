<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;
use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\RememberMeRepositoryInterface;

use App\Services\TokenService;
use App\Services\CookieService;
use App\Services\PasswordService;

/**
 * Authentication Service
 * 
 * Handles user authentication logic
 * Implements business rules for login/logout
 */
class AuthService implements AuthServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private RememberMeRepositoryInterface $rememberMeRepository;
    private PermissionRepositoryInterface $permissionRepository;
    private TokenService $tokenService;
    private CookieService $cookieService;
    private PasswordService  $passwordService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RememberMeRepositoryInterface $rememberMeRepository,
        PermissionRepositoryInterface $permissionRepository,
        TokenService $tokenService,
        CookieService $cookieService,
        PasswordService $passwordService
    ) {
        $this->userRepository = $userRepository;
        $this->rememberMeRepository = $rememberMeRepository;
        $this->permissionRepository = $permissionRepository;
        $this->tokenService = $tokenService;
        $this->cookieService = $cookieService;
        $this->passwordService = $passwordService;
    }

    /**
     * Authenticate user with email and password
     */
    public function authenticate(string $email, string $password, bool $remember = false): bool
    {
        // Find user by email
        $user = $this->userRepository->findByEmail($email);

        $hashToVerify = $user['password'] ?? $this->passwordService->dummyHash();
        $passwordIsValid = $this->passwordService->verify($password, $hashToVerify);

        if (!$user || $user['is_active'] === 0 || !$passwordIsValid) {
            return false;
        }

        $this->createSessionUser($user['id'], $user['firstname'], $user['lastname'], $user['role_id']);

        if ($remember) {
            $this->createRememberMeToken($user['id']);
        }

        return true;
    }


    private function createRememberMeToken(int $userId): void
    {
        $token = $this->tokenService->generateToken();
        $hash  = $this->tokenService->hashToken($token);

        $expiresAt = new \DateTime('7 days');

        $this->rememberMeRepository->store($userId, $hash, $expiresAt);

        $this->cookieService->setCookie(
            'remember_me',
            $token,
            $expiresAt->getTimestamp(),
            '/'
        );
    }

    private function rotateRememberMeToken(string $oldHash, int $userId): void
    {
        $this->rememberMeRepository->delete($oldHash);
        $this->createRememberMeToken($userId);
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        if (
            isset($_SESSION['user']) &&
            !empty($_SESSION['user']) &&
            ($_SESSION['user']['logged'] ?? false) === true &&
            (int)($_SESSION['user']['id'] ?? 0) > 0
        ) {
            return true;
        }

        if (!empty($_COOKIE['remember_me'])) {
            return $this->loginViaRememberMe($_COOKIE['remember_me']);
        }

        return false;
    }


    private function loginViaRememberMe(string $token): bool
    {
        $hash = $this->tokenService->hashToken($token);

        $user = $this->rememberMeRepository->findValidUserByToken($hash);

        if (!$user) {
            return false;
        }

        $this->rotateRememberMeToken($hash, $user['id']);

        $this->createSessionUser($user['id'], $user['firstname'], $user['lastname'], $user['role_id']);

        return true;
    }

    private function createSessionUser(int $id, string $firstname, string $lastname, int $roleId): void
    {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'        => $id,
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'role_id'   => $roleId,
            'logged'    => true
        ];
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Logout user
     */
    public function logout(int $userId): void
    {
        if (!empty($_COOKIE['remember_me'])) {

            $hash = $this->tokenService->hashToken($_COOKIE['remember_me']);
            $this->rememberMeRepository->delete($hash);

            $this->cookieService->deleteCookie('remember_me', '/');
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 3600,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            session_destroy();
        }

        $this->rememberMeRepository->deleteOnLogout($userId);
    }

    /**
     * Check if current user has permission
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        if (!isset($_SESSION['user']['permissions'])) {

            $permissions = $this->permissionRepository
                ->getPermissionsByRoleId($user['role_id']);

            $_SESSION['user']['permissions'] = $permissions;
        }

        return in_array($permission, $_SESSION['user']['permissions']);
    }
}
