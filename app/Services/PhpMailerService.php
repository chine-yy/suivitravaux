<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerService
{
    /**
     * Envoie un email via PHPMailer en utilisant la configuration .env.
     *
     * Attendus dans $params :
     * - to (obligatoire), to_name
     * - subject, body, alt_body
     * - cc[], bcc[], reply_to
     * - attachments: [path => name]
     * - is_html (bool)
     * - from, from_name (fallback sur MAIL_FROM_*)
     */
    public function send(array $params): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Transport SMTP
            $mail->isSMTP();
            $mail->Host       = config('mail.mailers.smtp.host');
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.mailers.smtp.username');
            $mail->Password   = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption');
            $mail->Port       = config('mail.mailers.smtp.port');

            // Expéditeur / destinataires
            $mail->setFrom(
                $params['from'] ?? config('mail.from.address'),
                $params['from_name'] ?? config('mail.from.name')
            );
            $mail->addAddress($params['to'], $params['to_name'] ?? '');

            if (!empty($params['reply_to'])) {
                $mail->addReplyTo($params['reply_to']);
            }

            foreach ($params['cc'] ?? [] as $cc) {
                $mail->addCC($cc);
            }
            foreach ($params['bcc'] ?? [] as $bcc) {
                $mail->addBCC($bcc);
            }

            // Pièces jointes
            foreach ($params['attachments'] ?? [] as $path => $name) {
                $mail->addAttachment($path, $name ?: basename($path));
            }

            // Corps du message
            $mail->isHTML($params['is_html'] ?? true);
            $mail->Subject = $params['subject'] ?? '';
            $mail->Body    = $params['body'] ?? '';
            $mail->AltBody = $params['alt_body'] ?? strip_tags($params['body'] ?? '');

            $mail->send();
            return true;
        } catch (Exception $e) {
            Log::error('PHPMailer error: ' . $mail->ErrorInfo, ['exception' => $e]);
            return false;
        }
    }
}

