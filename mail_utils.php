<?php
/**
 * Kompatibilitäts-Einstiegspunkt für den bisherigen Mail-Helfer.
 *
 * Bestehende Seiten binden weiterhin mail_utils.php ein. Die Implementierung
 * liegt zentral unter app/Core/Mail.php.
 */
require_once __DIR__ . '/app/Core/Mail.php';
