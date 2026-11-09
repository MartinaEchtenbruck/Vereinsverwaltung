<?php
// mail_utils.php - Gemeinsame Mail-Funktionen für Einzel- und Bulk-Versand

/**
 * Sendet eine E-Mail mit Anhängen.
 * Wird von contact.php (Einzelversand) und send_bulk_mail.php (Bulk)
 * gemeinsam verwendet, damit Mails identisch aufgebaut sind.
 */
function sendEmailWithAttachments($to, $subject, $message, $attachments = [], $isHtml = false) {
    $from = "foerderverein@evkigarumeln.de";
    //$bcc  = "foerderverein@evkigarumeln.de";

    $boundary = 'mixed_' . bin2hex(random_bytes(12));
    $alternativeBoundary = 'alt_' . bin2hex(random_bytes(12));

    $headers  = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";
    //$headers .= "Bcc: $bcc\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $body = '';
    if ($isHtml) {
        $plainText = html_entity_decode(strip_tags(str_ireplace(['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'], "\n", $message)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: multipart/alternative; boundary=\"$alternativeBoundary\"\r\n\r\n";
        $body .= "--$alternativeBoundary\r\n";
        $body .= "Content-Type: text/plain; charset=utf-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= trim($plainText) . "\r\n";
        $body .= "--$alternativeBoundary\r\n";
        $body .= "Content-Type: text/html; charset=utf-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= '<!doctype html><html><body>' . $message . '</body></html>' . "\r\n";
        $body .= "--$alternativeBoundary--\r\n";
    } else {
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=utf-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $message . "\r\n";
    }

    foreach ($attachments as $attachment) {
        if (file_exists($attachment)) {
            $file_content = file_get_contents($attachment);
            $file_encoded = chunk_split(base64_encode($file_content));

            $body .= "--$boundary\r\n";
            $body .= "Content-Type: application/pdf; name=\"" . basename($attachment) . "\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n";
            $body .= "Content-Disposition: attachment; filename=\"" . basename($attachment) . "\"\r\n\r\n";
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