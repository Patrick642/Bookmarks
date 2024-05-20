<?php
namespace core;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    public function send(string $to, string $subject, string $message, ?string $alt_message = null): bool
    {
        $this->getConfig();

        try {
            $mail = new PHPMailer(true);

            $mail->IsSMTP();
            $mail->Host = MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Port = MAIL_PORT;
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;
            $mail->SetFrom(MAIL_SENDER);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->MsgHTML($message);
            if ($alt_message !== null)
                $mail->AltBody = $alt_message;

            return $mail->send();

        } catch (\Exception $e) {
            throw new \ErrorException($mail->ErrorInfo);
        }
    }

    public function render(string $template_name, array $variables = []): string
    {
        $file = ROOT_DIR . '/src/View/email/' . $template_name;

        if (!file_exists($file)) {
            throw new \ErrorException('Html ' . $template_name . ' not found!');
        }

        $html = file_get_contents($file);

        foreach ($variables as $key => $value) {
            $html = str_replace($key, $value, $html);
        }

        return $html;
    }

    private function getConfig(): void
    {
        $config_file = null;

        switch (ENV) {
            case 'prod':
                $config_file = ROOT_DIR . '/config/mailer.php';
                break;

            case 'dev':
            default:
                $config_file = ROOT_DIR . '/config/mailer_local.php';
                break;
        }

        if (file_exists($config_file))
            include_once $config_file;
        else
            throw new \ErrorException($config_file . ' not found!');
    }

}