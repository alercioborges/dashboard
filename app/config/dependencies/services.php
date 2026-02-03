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
    RememberMeService,
    ForgotPasswordService,
    MailerService,
    PasswordService
};

use App\Interfaces\{
    UserRepositoryInterface,
    RoleRepositoryInterface,
    RememberMeRepositoryInterface,
    AuthServiceInterface,
    ForgotPasswordServiceInterface
};

return [

    QueryBuilderService::class =>
        fn(ContainerInterface $c) => new QueryBuilderService(
            $c->get(Doctrine\DBAL\Connection::class)
        ),

    UserService::class =>
        fn(ContainerInterface $c) => new UserService(
            $c->get(UserRepositoryInterface::class)
        ),

    RoleService::class =>
        fn(ContainerInterface $c) => new RoleService(
            $c->get(RoleRepositoryInterface::class)
        ),

    AuthServiceInterface::class =>
        fn(ContainerInterface $c) => new AuthService(
            $c->get(UserRepositoryInterface::class),
            $c->get(RememberMeRepositoryInterface::class)
        ),

    ForgotPasswordServiceInterface::class =>
        fn(ContainerInterface $c) => new ForgotPasswordService(
            $c->get(UserRepositoryInterface::class),
            $c->get(MailerService::class),
            $c->get(LoggerInterface::class)
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
        return new MailerService(
            $c->get('smtpConfig'),
            $c->get(LoggerInterface::class),
            $c->get(Twig::class),
            $c->get(PHPMailer::class)
        );
    },

];
