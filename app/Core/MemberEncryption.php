<?php
/** Verschlüsselung personenbezogener Mitgliederdaten mit AES-256-GCM. */
final class MemberEncryption
{
    private string $key;
    public function __construct(string $projectRoot)
    {
        $path = rtrim($projectRoot, '/\\') . '/private/member_encryption_key.php';
        if (!is_file($path)) {
            throw new RuntimeException('Der Verschlüsselungsschlüssel fehlt unter private/member_encryption_key.php.');
        }
        $config = require $path;
        $encoded = is_array($config) ? ($config['key'] ?? '') : '';
        $key = is_string($encoded) ? base64_decode($encoded, true) : false;
        if ($key === false || strlen($key) !== 32) {
            throw new RuntimeException('Der Mitgliederdaten-Schlüssel ist ungültig. Erwartet wird ein Base64-kodierter 32-Byte-Schlüssel.');
        }
        $this->key = $key;
    }
    public function encrypt(?string $plain): ?string
    {
        if ($plain === null || $plain === '') return $plain;
        $nonce = random_bytes(12); $tag = '';
        $cipher = openssl_encrypt($plain, 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, $nonce, $tag);
        if ($cipher === false) throw new RuntimeException('Mitgliederdaten konnten nicht verschlüsselt werden.');
        return 'v1:' . base64_encode($nonce . $tag . $cipher);
    }
    public function decrypt(?string $stored): ?string
    {
        if ($stored === null || $stored === '') return $stored;
        if (strncmp($stored, 'v1:', 3) !== 0) throw new RuntimeException('Unbekanntes Verschlüsselungsformat in Mitgliederdaten.');
        $raw = base64_decode(substr($stored, 3), true);
        if ($raw === false || strlen($raw) < 28) throw new RuntimeException('Beschädigte verschlüsselte Mitgliederdaten.');
        $plain = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
        if ($plain === false) throw new RuntimeException('Mitgliederdaten konnten nicht entschlüsselt werden. Schlüssel oder Daten sind möglicherweise beschädigt.');
        return $plain;
    }
}
