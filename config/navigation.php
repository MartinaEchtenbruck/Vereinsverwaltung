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
        'label' => 'Verkäufe & Bestellungen',
        'url' => 'verkauf.php',
        'icon' => 'fas fa-cart-shopping',
        'permission' => 'Verkauf',
    ],
    [
        'label' => 'Projektmanagement',
        'url' => 'projects.php',
        'icon' => 'fas fa-list-check',
        'permission' => 'ProjectManagement',
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
