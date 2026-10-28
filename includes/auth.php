<?php
/**
 * Kompatibilitäts-Einstiegspunkt.
 *
 * Bestehende Seiten binden weiterhin auth.php ein. Die Implementierung liegt
 * inzwischen zentral unter app/Core/Auth.php, damit bestehende URLs und
 * require_once-Aufrufe unverändert bleiben.
 */
require_once __DIR__ . '/app/Core/Auth.php';
