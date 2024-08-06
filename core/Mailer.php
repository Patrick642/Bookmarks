<?php
namespace core;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private Config $config;

    public function __construct()
    {
        $this->config = new Config();
    }

    public function send(string $to, string $subject, string $htmlTemplate, ?string $txtTemplate = null): bool
    {
        $this->config->get('mailer');

        try {
            $mail = new PHPMailer(true);

            $mail->IsSMTP();
            $mail->Host = MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Port = MAIL_PORT;
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;
            $mail->SetFrom(MAIL_SENDER_EMAIL, MAIL_SENDER_NAME);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->MsgHTML($htmlTemplate);
            if ($txtTemplate !== null)
                $mail->AltBody = $txtTemplate;

            return $mail->send();

        } catch (\Exception $e) {
            throw new \ErrorException($mail->ErrorInfo);
        }
    }
}