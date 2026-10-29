<?php
declare(strict_types=1);

/**
 * Web-Push-Konfiguration
 *
 * Speicherort:
 * httpdocs/intern/private/webpush_config.php
 *
 * Diese Datei darf nicht direkt über HTTP erreichbar sein.
 */

return [
    // VAPID-Schlüssel: exakt aus der Schlüsselerzeugung übernehmen.
    'vapid' => [
        'subject' => 'mailto:foerderverein@evkigarumeln.de',
        'public_key' => 'BLgfd6SVlbokAjTi7RUxyr7Hhg8yUwe17yLmBFD0Cvt80xeQ0kbFtSU9bPdB2F7h2ksnChe8cgQ_pZvQG6mrghY',
        'private_key' => 'SJOd3dC58Js-zTlJJ3zMwPRZSRAqGFt5cOyqe6JW2fs',
    ],
];?>