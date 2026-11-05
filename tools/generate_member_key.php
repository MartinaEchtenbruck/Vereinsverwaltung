<?php
// Einmalig lokal/auf dem Server per CLI ausführen: php generate_member_key.php
// Das Skript gibt einen Base64-kodierten AES-256-Schlüssel aus.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
echo base64_encode(random_bytes(32)) . PHP_EOL;
