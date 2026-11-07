<?php
/**
 * Zentrale Definition der globalen Navigation.
 *
 * URLs bleiben im bisherigen Projektverzeichnis.
 * Die Datenbank und bestehende Seitenpfade werden nicht verändert.
 */
return [
    [
        'label' => 'Dashboard',
        'url' => 'dashboard.php',
        'icon' => 'fas fa-gauge-high',
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
        'label' => 'Helferlisten',
        'url' => 'helferlisten.php',
        'icon' => 'fas fa-people-group',
        'permission' => 'manage_helpers',
    ],
    [
        'label' => 'Aufgabenübersicht',
        'url' => 'projects.php',
        'icon' => 'fas fa-list-check',
        'permission' => 'ProjectManagement',
    ],
    [
        'label' => 'Projekt anlegen',
        'url' => 'projects.php?view=new',
        'icon' => 'fas fa-plus',
        'permission' => 'ProjectManagement',
    ],
    [
        'label' => 'Einzelaufgaben',
        'url' => 'projects.php?view=standalone',
        'icon' => 'fas fa-list-check',
        'permission' => 'ProjectManagement',
    ],
    [
        'label' => 'Vorlagen',
        'url' => 'projects.php?view=templates',
        'icon' => 'fas fa-layer-group',
        'permission' => 'ProjectManagement',
    ],
    [
        'label' => 'Mitgliederübersicht',
        'url' => 'mitglieder.php',
        'icon' => 'fas fa-address-card',
        'permission' => 'manage_members',
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
