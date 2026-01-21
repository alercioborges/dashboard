<?php

use PHPMailer\PHPMailer\PHPMailer;

return [
    'host'       => $_ENV['MAIL_HOST'],
    'port'       => (int) $_ENV['MAIL_PORT'],
    'auth'       => true,
    'username'   => $_ENV['MAIL_USERNAME'],
    'password'   => $_ENV['MAIL_PASSWORD'],
    'encryption' => match ($_ENV['MAIL_ENCRYPTION'] ?? 'tls') {
        'ssl' => PHPMailer::ENCRYPTION_SMTPS,
        default => PHPMailer::ENCRYPTION_STARTTLS
    },
    'from_email' => $_ENV['MAIL_FROM_ADDRESS'],
    'from_name'  => $_ENV['MAIL_FROM_NAME'],
];
