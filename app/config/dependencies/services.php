<?php

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use Slim\Views\Twig;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

use PHPMailer\PHPMailer\PHPMailer;

use App\Services\{
    QueryBuilderService,
    UserService,
    RoleService,
    AuthService,
    ForgotPasswordService,
    MailerService,
    PasswordService,
    PaginationService,
    TokenService,
    CookieService
};

use App\Interfaces\{
    UserRepositoryInterface,
    RoleRepositoryInterface,
    RememberMeRepositoryInterface,
    AuthServiceInterface,
    ForgotPasswordServiceInterface,
    PermissionRepositoryInterface
};

return [

    QueryBuilderService::class =>
    fn(ContainerInterface $c) => new QueryBuilderService(
        $c->get(Doctrine\DBAL\Connection::class)
    ),

    PaginationService::class => function (): PaginationService {
        return new PaginationService();
    },

    UserService::class =>
    fn(ContainerInterface $c) => new UserService(
        $c->get(UserRepositoryInterface::class),
        $c->get(PaginationService::class)
    ),    

    RoleService::class =>
    fn(ContainerInterface $c) => new RoleService(
        $c->get(RoleRepositoryInterface::class),
        $c->get(PaginationService::class)
    ),

    TokenService::class => function(): TokenService {
        return new TokenService();
    },

    AuthServiceInterface::class =>
    fn(ContainerInterface $c) => new AuthService(
        $c->get(UserRepositoryInterface::class),
        $c->get(RememberMeRepositoryInterface::class),
        $c->get(PermissionRepositoryInterface::class),
        $c->get(TokenService::class),
        $c->get(CookieService::class)

    ),

    ForgotPasswordServiceInterface::class =>
    fn(ContainerInterface $c) => new ForgotPasswordService(
        $c->get(UserRepositoryInterface::class),
        $c->get(MailerService::class),
        $c->get(LoggerInterface::class),
        $c->get(TokenService::class)
    ),

    PasswordService::class =>
    fn() => new PasswordService(12),

    LoggerInterface::class => function (ContainerInterface $c): LoggerInterface {
        $logger = new Logger('app');
        $logPath = __DIR__ . '/../../../storage/logs/app.log';
        $handler = new StreamHandler($logPath, Level::Debug);
        $logger->pushHandler($handler);
        return $logger;
    },

    PHPMailer::class => function (): PHPMailer {
        return new PHPMailer(true);
    },

    MailerService::class => function (ContainerInterface $c): MailerService {

        $appConfig = $c->get('appConfig');

        return new MailerService(
            $c->get('smtpConfig'),
            $c->get(LoggerInterface::class),
            $c->get(Twig::class),
            $c->get(PHPMailer::class),
            $appConfig['env'] === 'development'
        );
    },

];
