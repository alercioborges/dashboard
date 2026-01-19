<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    private PHPMailer $mailer;

    public function __construct(array $config)
    {
        $this->mailer = new PHPMailer(true);

        // SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host       = $config['host'];
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $config['username'];
        $this->mailer->Password   = $config['password'];
        $this->mailer->Port       = $config['port'];
        $this->mailer->SMTPSecure = $config['encryption'];

        // Charset e HTML
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->isHTML(true);

        // From
        $this->mailer->setFrom(
            $config['from_email'],
            $config['from_name']
        );
    }

    public function send(
        string $to,
        string $subject,
        string $htmlBody,
        ?string $textBody = null
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $htmlBody;
            $this->mailer->AltBody = $textBody ?? strip_tags($htmlBody);

            return $this->mailer->send();

        } catch (Exception $e) {
            // Ideal: LoggerInterface
            // error_log($e->getMessage());
            return false;
        }
    }
}
