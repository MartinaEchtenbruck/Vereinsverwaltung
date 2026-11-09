<?php
/**
 * Zentrales Register und Renderer für Platzhalter in Mitglieder-E-Mails.
 * Erwartet für die Auflösung bereits entschlüsselte Mitgliedsdaten.
 */

function getAvailableMailPlaceholders(): array
{
    return [
        'vorname' => [
            'label' => 'Vorname',
            'description' => 'Vorname des Mitglieds',
            'source' => 'members.first_name',
        ],
        'nachname' => [
            'label' => 'Nachname',
            'description' => 'Nachname des Mitglieds',
            'source' => 'members.last_name',
        ],
        'email' => [
            'label' => 'E-Mail-Adresse',
            'description' => 'Entschlüsselte E-Mail-Adresse des Mitglieds',
            'source' => 'members.email_enc',
        ],
        'mitglied_seit' => [
            'label' => 'Mitglied seit',
            'description' => 'Eintrittsdatum, formatiert als TT.MM.JJJJ',
            'source' => 'members.start_date',
        ],
        'mitglied_bis' => [
            'label' => 'Mitglied bis',
            'description' => 'Enddatum, formatiert als TT.MM.JJJJ; leer, wenn kein Enddatum hinterlegt ist',
            'source' => 'members.end_date',
        ],
        'beitrag' => [
            'label' => 'Beitrag',
            'description' => 'Mitgliedsbeitrag im deutschen Zahlenformat, z. B. 12,00 €',
            'source' => 'members.monthly_fee',
        ],
    ];
}

/** Liefert alle Platzhalter-Tokens aus Betreff und Nachrichtentext. */
function findMailPlaceholders(string ...$texts): array
{
    $found = [];
    foreach ($texts as $text) {
        if (preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/u', $text, $matches)) {
            foreach ($matches[1] as $name) $found[$name] = true;
        }
    }
    return array_keys($found);
}

/** Prüft Platzhalter gegen das zentrale Register und liefert unbekannte Namen. */
function findUnknownMailPlaceholders(string ...$texts): array
{
    $known = getAvailableMailPlaceholders();
    return array_values(array_filter(findMailPlaceholders(...$texts), static fn($name) => !array_key_exists($name, $known)));
}

/** Erkennt Personalisierung automatisch anhand gültiger Platzhalter in Betreff oder Text. */
function isPersonalizedMemberMail(string $subject, string $body): bool
{
    $known = getAvailableMailPlaceholders();
    foreach (findMailPlaceholders($subject, $body) as $name) {
        if (isset($known[$name])) return true;
    }
    return false;
}

/** Ersetzt bekannte Platzhalter anhand eines bereits entschlüsselten Mitgliedsdatensatzes. */
function renderMemberMailText(string $text, array $member): string
{
    $values = [
        'vorname' => (string)($member['first_name'] ?? ''),
        'nachname' => (string)($member['last_name'] ?? ''),
        'email' => (string)($member['email'] ?? ''),
        'mitglied_seit' => formatMemberMailDate($member['start_date'] ?? null),
        'mitglied_bis' => formatMemberMailDate($member['end_date'] ?? null),
        'beitrag' => formatMemberMailFee($member['monthly_fee'] ?? null),
    ];
    return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/u', static function ($matches) use ($values) {
        $name = $matches[1];
        return array_key_exists($name, $values) ? $values[$name] : $matches[0];
    }, $text);
}

function formatMemberMailDate($value): string
{
    if ($value === null || $value === '') return '';
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', (string)$value);
    return $date ? $date->format('d.m.Y') : '';
}

/** Formatiert den monatlichen Mitgliedsbeitrag im deutschen Zahlenformat. */
function formatMemberMailFee($value): string
{
    if ($value === null || $value === '' || !is_numeric($value)) return '';
    return number_format((float)$value, 2, ',', '.') . ' €';
}


/**
 * Bereinigt den Nachrichtentext auf einfache, formatierte E-Mail-Elemente.
 * Es werden ausschließlich erlaubte Tags ohne Attribute übernommen.
 */
function sanitizeMemberMailHtml(string $html): string
{
    $html = preg_replace('~<(script|style|iframe|object|embed|svg|math)\b[^>]*>.*?</\1\s*>~is', '', $html) ?? '';
    $html = preg_replace('~<!--.*?-->~s', '', $html) ?? '';
    $allowed = '<p><div><br><strong><b><em><i><u><ul><ol><li>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace_callback('~</?([a-z0-9]+)\b[^>]*>~i', static function ($m) {
        $tag = strtolower($m[1]);
        $allowedTags = ['p','div','br','strong','b','em','i','u','ul','ol','li'];
        if (!in_array($tag, $allowedTags, true)) return '';
        return str_starts_with($m[0], '</') ? '</'.$tag.'>' : '<'.$tag.'>';
    }, $html) ?? '';
    return trim($html);
}

/** Rendert Platzhalter in bereinigtem HTML und escaped Mitgliedsdaten als HTML-Text. */
function renderMemberMailHtml(string $html, array $member): string
{
    $values = [
        'vorname' => (string)($member['first_name'] ?? ''),
        'nachname' => (string)($member['last_name'] ?? ''),
        'email' => (string)($member['email'] ?? ''),
        'mitglied_seit' => formatMemberMailDate($member['start_date'] ?? null),
        'mitglied_bis' => formatMemberMailDate($member['end_date'] ?? null),
        'beitrag' => formatMemberMailFee($member['monthly_fee'] ?? null),
    ];
    $html = sanitizeMemberMailHtml($html);
    return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/u', static function ($matches) use ($values) {
        $name = $matches[1];
        return array_key_exists($name, $values)
            ? htmlspecialchars($values[$name], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            : $matches[0];
    }, $html) ?? $html;
}
