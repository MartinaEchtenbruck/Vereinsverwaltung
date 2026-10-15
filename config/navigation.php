<?php
/**
 * Zentrale Definition der globalen Navigation.
 *
 * URLs bleiben in Phase A bewusst im bisherigen Projektverzeichnis.
 * Die Datenbank und bestehende Seitenpfade werden nicht verändert.
 */
return [
    [
        'label' => 'Dashboard',
        'url' => 'dashboard.php',
        'icon' => 'fas fa-gauge-high',
        // Für alle angemeldeten Benutzer sichtbar.
        'permission' => null,
    ],
    [
        'label' => 'Sponsorenübersicht',
        'url' => 'index.php',
        'icon' => 'fas fa-address-book',
        'permission' => 'view_contacts',
    ],
    [
        'label' => 'Spendenaktion',
        'url' => 'edit_fundraising.php',
        'icon' => 'fas fa-hand-holding-heart',
        'permission' => 'edit_fundraising',
    ],
    [
        'label' => 'Benutzerverwaltung',
        'url' => 'users.php',
        'icon' => 'fas fa-users',
        'permission' => 'manage_users',
    ],
    [
        'label' => 'Rollen & Rechte',
        'url' => 'role_permissions.php',
        'icon' => 'fas fa-user-shield',
        'permission' => 'manage_users',
    ],
];
