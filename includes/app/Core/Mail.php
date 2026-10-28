<?php
// mail_utils.php - Gemeinsame Mail-Funktionen für Einzel- und Bulk-Versand

/**
 * Sendet eine E-Mail mit Anhängen.
 * Wird von contact.php (Einzelversand) und send_bulk_mail.php (Bulk)
 * gemeinsam verwendet, damit Mails identisch aufgebaut sind.
 */
function sendEmailWithAttachments($to, $subject, $message, $attachments = []) {
    $from = "foerderverein@evkigarumeln.de";
    //$bcc  = "foerderverein@evkigarumeln.de";

    $boundary = md5(time());

    $headers  = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";
    //$headers .= "Bcc: $bcc\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $body  = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=utf-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $message . "\r\n";

    foreach ($attachments as $attachment) {
        if (file_exists($attachment)) {
            $file_content = file_get_contents($attachment);
            $file_encoded = chunk_split(base64_encode($file_content));

            $body .= "--$boundary\r\n";
            $body .= "Content-Type: application/pdf; name=\"$attachment\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n";
            $body .= "Content-Disposition: attachment; filename=\"$attachment\"\r\n\r\n";
            $body .= $file_encoded . "\r\n";
        }
    }

    $body .= "--$boundary--";

    $mail_result = mail($to, $subject, $body, $headers);

    if (!$mail_result) {
        error_log('E-Mail-Versand fehlgeschlagen.');
    }


    return $mail_result;
}