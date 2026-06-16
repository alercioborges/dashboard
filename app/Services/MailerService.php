<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class MailerService
{
    private array $config;
    private LoggerInterface $logger;
    private Twig $twig;
    private PHPMailer $mail;
    private bool $isDevelopment;

    public function __construct(
        array $config,
        LoggerInterface $logger,
        Twig $twig,
        PHPMailer $mail,
        bool $isDevelopment = false
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->twig = $twig;
        $this->mail = $mail;
        $this->isDevelopment = $isDevelopment;

        $this->configure(); // Self method configuration
    }

    /**
     * Setting injection instance PHPMailer.
     */
    private function configure(): void
    {
        $this->mail->isSMTP();

        $this->mail->Host       = $this->config['host'];
        $this->mail->SMTPAuth   = $this->config['auth'];
        $this->mail->Username   = $this->config['username'];
        $this->mail->Password   = $this->config['password'];
        $this->mail->Port       = $this->config['port'];
        $this->mail->SMTPSecure = $this->config['encryption'];

        $this->mail->Timeout = 5;
        $this->mail->SMTPKeepAlive = false;

        $this->mail->CharSet = 'UTF-8';
        $this->mail->isHTML(true);

        $this->mail->setFrom(
            $this->config['from_email'],
            $this->config['from_name']
        );

        // Debug SMTP only in development.
        if ($this->isDevelopment) {
            $this->mail->SMTPDebug   = SMTP::DEBUG_SERVER;
            $this->mail->Debugoutput = function (string $str, int $level): void {
                $this->logger->debug('[SMTP] ' . trim($str));
            };
        } else {
            $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
        }
    }

    public function send(
        string $to,
        string $subject,
        string $template,
        array $data = [],
        ?string $textBody = null
    ): bool {

        try {

            $this->mail->clearAddresses();
            $this->mail->clearAttachments();

            $html = $this->twig->fetch('emails/' . $template, $data);

            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $html;
            $this->mail->AltBody = $textBody ?? strip_tags($html);

            // Log without exposing body content or credentials.
            $this->logger->info('[MAIL] E-mail sent', [
                'to'       => $to,
                'subject'  => $subject,
                'template' => $template,
            ]);

            return $this->mail->send();
            
        } catch (Exception $e) {

            $this->logger->error('[MAIL ERROR] ' . $e->getMessage(), [
                'to'       => $to,
                'subject'  => $subject,
                'template' => $template,
            ]);

            return false;
        }
    }
}
