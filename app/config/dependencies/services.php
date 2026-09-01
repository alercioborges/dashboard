<?php

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use Slim\Views\Twig;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

use PHPMailer\PHPMailer\PHPMailer;

use App\Services\{
    SetupService,
    QueryBuilderService,
    UserService,
    RoleService,
    AuthService,
    ForgotPasswordService,
    MailerService,
    PasswordService,
    PaginationService,
    TokenService,
    CookieService,
    RequestContext
};

use App\Interfaces\{
    SetupRepositoryInterface,
    UserRepositoryInterface,
    RoleRepositoryInterface,
    RememberMeRepositoryInterface,
    AuthServiceInterface,
    ForgotPasswordServiceInterface,
    PermissionRepositoryInterface
};

return [

    SetupService::class =>
    fn(ContainerInterface $c) => new SetupService(
        $c->get(SetupRepositoryInterface::class)
    ),

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

    TokenService::class => function (): TokenService {
        return new TokenService();
    },

    AuthServiceInterface::class =>
    fn(ContainerInterface $c) => new AuthService(
        $c->get(UserRepositoryInterface::class),
        $c->get(RememberMeRepositoryInterface::class),
        $c->get(PermissionRepositoryInterface::class),
        $c->get(TokenService::class),
        $c->get(CookieService::class),
        $c->get(PasswordService::class)
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

    RequestContext::class => function(): RequestContext {
        return new RequestContext();
    },

    // -------------------------------------------------------
    // TWIG DEDICADO A E-MAILS
    // -------------------------------------------------------
    // Aponta para a MESMA pasta de templates usada pela view web
    // (templates/emails/*.twig continua no mesmo lugar, nada muda
    // no send() do MailerService nem nos arquivos .twig existentes),
    // mas sem a ExtensionTwig — e-mails não usam CSRF, rotas nomeadas
    // ou o menu do dashboard, então não há motivo pra essa instância
    // depender do Guard/sessão.
    'mailer_twig' => function (ContainerInterface $c): Twig {
        $appConfig = $c->get('appConfig');

        $twig = Twig::create(__DIR__ . '/../../../templates/', [
            'cache'       => false,
            'debug'       => $appConfig['debug'],
            'auto_reload' => $appConfig['debug'],
        ]);

        // Só o que pode ser útil em e-mails (ex.: link de logo, "ver no navegador").
        // Nada de menu_items, session, cookies ou a extensão de CSRF/rotas aqui.
        $twig->getEnvironment()->addGlobal('base_path', $appConfig['url']);

        return $twig;
    },

    MailerService::class => function (ContainerInterface $c): MailerService {
        $appConfig = $c->get('appConfig');

        return new MailerService(
            $c->get('smtpConfig'),
            $c->get(LoggerInterface::class),
            $c->get('mailer_twig'),
            $c->get(PHPMailer::class),
            $appConfig['env'] === 'development'
        );
    },

];
