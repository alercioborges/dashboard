<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

class MailerService
{
    private PHPMailer $mail;
    private LoggerInterface $logger;
    private Twig $twig;

    public function __construct(array $config, LoggerInterface $logger, Twig $twig)
    {
        $this->logger = $logger;
        $this->twig = $twig;

        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host       = $config['host'];
        $this->mail->SMTPAuth   = $config['auth'];
        $this->mail->Username   = $config['username'];
        $this->mail->Password   = $config['password'];
        $this->mail->Port       = $config['port'];
        $this->mail->SMTPSecure = $config['encryption'];

        $this->mail->Timeout = 5;
        $this->mail->SMTPKeepAlive = false;

        $this->mail->CharSet = 'UTF-8';
        $this->mail->isHTML(true);

        $this->mail->setFrom(
            $config['from_email'],
            $config['from_name']
        );

        // 🔍 DEBUG (ATIVAR TEMPORARIAMENTE)
        $this->mail->SMTPDebug = 2;
        $this->mail->Debugoutput = 'error_log';
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

            return $this->mail->send();

        } catch (Exception $e) {
            
            $this->logger->error('[MAIL ERROR] ' . $e->getMessage());

            return false;
        }
    }
}
