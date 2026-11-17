<?php

namespace Tests\Unit\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Services\ForgotPasswordService;
use App\Services\MailerService;
use App\Services\TokenService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ForgotPasswordServiceTest extends TestCase
{
    private const EMAIL    = 'ana@example.com';
    private const TOKEN    = 'fixed-token-for-tests';
    private const BASE_URL = 'https://app.test';

    /** @var mixed */
    private $previousAppConfig;

    protected function setUp(): void
    {
        // getUrl() (helpers.php) lê $GLOBALS['app_config']['url']
        $this->previousAppConfig = $GLOBALS['app_config'] ?? null;
        $GLOBALS['app_config']['url'] = self::BASE_URL;
    }

    protected function tearDown(): void
    {
        if ($this->previousAppConfig === null) {
            unset($GLOBALS['app_config']);
        } else {
            $GLOBALS['app_config'] = $this->previousAppConfig;
        }
    }

    /**
     * Monta o serviço. Dependências não informadas viram stubs (sem expectativa),
     * e as que o teste quer verificar são passadas como mocks.
     */
    private function buildService(
        ?UserRepositoryInterface $repository = null,
        ?MailerService $mailer = null,
        ?LoggerInterface $logger = null
    ): ForgotPasswordService {
        $tokenService = $this->createStub(TokenService::class);
        $tokenService->method('generateToken')->willReturn(self::TOKEN);

        return new ForgotPasswordService(
            $repository ?? $this->createStub(UserRepositoryInterface::class),
            $mailer ?? $this->createStub(MailerService::class),
            $logger ?? $this->createStub(LoggerInterface::class),
            $tokenService
        );
    }

    private function user(array $overrides = []): array
    {
        return array_merge([
            'id'        => 7,
            'firstname' => 'Ana',
            'lastname'  => 'Souza',
            'email'     => self::EMAIL,
            'is_active' => 1,
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // process(): cenários em que NADA deve acontecer (sem vazar se o e-mail existe)
    // ------------------------------------------------------------------

    public function testProcessDoesNothingWhenUserDoesNotExist(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->expects($this->once())->method('findByEmail')->with(self::EMAIL)->willReturn(null);
        $repository->expects($this->never())->method('storePasswordReset');

        $mailer = $this->createMock(MailerService::class);
        $mailer->expects($this->never())->method('send');

        $this->buildService($repository, $mailer)->process(self::EMAIL);
    }

    #[DataProvider('inactiveValues')]
    public function testProcessDoesNothingWhenUserIsInactive(int|string $isActive): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->method('findByEmail')
            ->willReturn($this->user(['is_active' => $isActive]));
        $repository->expects($this->never())->method('storePasswordReset');

        $mailer = $this->createMock(MailerService::class);
        $mailer->expects($this->never())->method('send');

        $this->buildService($repository, $mailer)->process(self::EMAIL);
    }

    public static function inactiveValues(): array
    {
        return [
            'int zero'    => [0],
            'string zero' => ['0'],
        ];
    }

    // ------------------------------------------------------------------
    // process(): caminho feliz
    // ------------------------------------------------------------------

    #[DataProvider('activeValues')]
    public function testProcessStoresHashedTokenAndSendsEmail(int|string $isActive): void
    {
        $user = $this->user(['is_active' => $isActive]);

        $storedHash    = null;
        $storedExpires = null;

        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository->expects($this->once())->method('findByEmail')->with(self::EMAIL)->willReturn($user);
        $repository
            ->expects($this->once())
            ->method('storePasswordReset')
            ->willReturnCallback(
                function (int $userId, string $hash, \DateTimeImmutable $expiresAt) use (&$storedHash, &$storedExpires): int {
                    $this->assertSame(7, $userId);
                    $storedHash    = $hash;
                    $storedExpires = $expiresAt;

                    return 42;
                }
            );

        $expectedLink = self::BASE_URL . '/forgot/reset-password?id=42&token=' . self::TOKEN;

        $mailer = $this->createMock(MailerService::class);
        $mailer
            ->expects($this->once())
            ->method('send')
            ->with(
                self::EMAIL,
                'Redefinição de senha',
                'reset-password.twig',
                $this->callback(
                    fn(array $data): bool => $data['USER'] === $user
                        && $data['LINK'] === $expectedLink
                        && $data['EXPIRES_AT'] instanceof \DateTimeImmutable
                )
            )
            ->willReturn(true);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info')
            ->with('Password reset email sent', ['email' => self::EMAIL]);

        $this->buildService($repository, $mailer, $logger)->process(self::EMAIL);

        // O token em texto puro NÃO é gravado: só o hash, e ele valida o token.
        $this->assertNotSame(self::TOKEN, $storedHash);
        $this->assertTrue(password_verify(self::TOKEN, $storedHash));

        // Expira em ~1 hora.
        $this->assertEqualsWithDelta(time() + 3600, $storedExpires->getTimestamp(), 5);
    }

    public static function activeValues(): array
    {
        return [
            'int one'    => [1],
            'string one' => ['1'],
        ];
    }

    // ------------------------------------------------------------------
    // process(): falhas
    // ------------------------------------------------------------------

    public function testProcessLogsErrorAndDoesNotSendEmailWhenStoringTokenFails(): void
    {
        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findByEmail')->willReturn($this->user());
        $repository->method('storePasswordReset')
            ->willThrowException(new \RuntimeException('db down'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains('db down'));

        $mailer = $this->createMock(MailerService::class);
        $mailer->expects($this->never())->method('send');

        $this->buildService($repository, $mailer, $logger)->process(self::EMAIL);
    }

    public function testProcessDoesNotSendEmailWhenRepositoryReturnsNullId(): void
    {
        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findByEmail')->willReturn($this->user());
        $repository->method('storePasswordReset')->willReturn(null);

        $mailer = $this->createMock(MailerService::class);
        $mailer->expects($this->never())->method('send');

        $this->buildService($repository, $mailer)->process(self::EMAIL);
    }

    public function testProcessSwallowsMailerExceptionAndLogsIt(): void
    {
        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findByEmail')->willReturn($this->user());
        $repository->method('storePasswordReset')->willReturn(42);

        $mailer = $this->createStub(MailerService::class);
        $mailer->method('send')->willThrowException(new \RuntimeException('smtp down'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                'Error sending password reset email',
                ['email' => self::EMAIL, 'exception' => 'smtp down']
            );
        $logger->expects($this->never())->method('info');

        // Não pode propagar a exceção ao chamador (evita vazar se o e-mail existe)
        $this->buildService($repository, $mailer, $logger)->process(self::EMAIL);
    }

    // ------------------------------------------------------------------
    // Métodos de delegação
    // ------------------------------------------------------------------

    public function testValidateTokenReturnsRepositoryResult(): void
    {
        $row = ['id' => 42, 'user_id' => 7];

        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findValidPasswordReset')
            ->with(42, self::TOKEN)
            ->willReturn($row);

        $this->assertSame($row, $this->buildService($repository)->validateToken(42, self::TOKEN));
    }

    public function testValidateTokenReturnsNullWhenTokenIsInvalid(): void
    {
        $repository = $this->createStub(UserRepositoryInterface::class);
        $repository->method('findValidPasswordReset')->willReturn(null);

        $this->assertNull($this->buildService($repository)->validateToken(42, 'wrong'));
    }

    #[DataProvider('booleans')]
    public function testResetPasswordReturnsRepositoryResult(bool $result): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('updatePassword')
            ->with(42, 7, 'new-secret')
            ->willReturn($result);

        $this->assertSame($result, $this->buildService($repository)->resetPassword(42, 7, 'new-secret'));
    }

    public static function booleans(): array
    {
        return ['success' => [true], 'failure' => [false]];
    }

    public function testDeleteTokenDelegatesToRepository(): void
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('deleteExpiredToken')
            ->willReturn(true);

        $this->assertTrue($this->buildService($repository)->deleteToken());
    }
}
