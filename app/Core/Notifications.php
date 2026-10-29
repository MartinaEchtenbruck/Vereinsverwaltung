<?php
/** Zentrale Benachrichtigungslogik für Förderverein intern. */
if (!function_exists('fvNotificationCanSee')) {
    function fvNotificationCanSee(PDO $conn, int $userId, string $permission): bool {
        $q = $conn->prepare("SELECT 1 FROM users u JOIN user_roles ur ON ur.user_id=u.id JOIN role_permissions rp ON rp.role_id=ur.role_id JOIN permissions p ON p.id=rp.permission_id WHERE u.id=? AND u.is_active=1 AND p.permission_name=? LIMIT 1");
        $q->execute([$userId, $permission]);
        return (bool)$q->fetchColumn();
    }
    function fvNotificationRecipients(PDO $conn, array $permissions): array {
        if (!$permissions) return [];
        $marks = implode(',', array_fill(0, count($permissions), '?'));
        $q = $conn->prepare("SELECT DISTINCT u.id FROM users u JOIN user_roles ur ON ur.user_id=u.id JOIN role_permissions rp ON rp.role_id=ur.role_id JOIN permissions p ON p.id=rp.permission_id WHERE u.is_active=1 AND p.permission_name IN ($marks)");
        $q->execute($permissions);
        return array_map('intval', $q->fetchAll(PDO::FETCH_COLUMN));
    }
    function fvCreateNotification(PDO $conn, int $userId, string $type, string $entityType, int $entityId, string $eventKey, string $title, string $message, string $targetUrl): void {
        $q = $conn->prepare("INSERT IGNORE INTO notifications (user_id, notification_type, entity_type, entity_id, event_key, title, message, target_url, is_read, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 'open', NOW())");
        $q->execute([$userId, $type, $entityType, $entityId, $eventKey, $title, $message, $targetUrl]);
    }
    /** Läuft beim Rendern der Navigation; Einträge werden über eindeutige Schlüssel nicht dupliziert. */
    function fvSyncNotifications(PDO $conn): void {
        // Überfällige Aufgaben in aktiven Projekten sowie projektunabhängige Aufgaben.
        $taskRows = $conn->query("SELECT t.id, t.title, t.due_date, 'project' AS task_kind FROM tasks t JOIN projects p ON p.id=t.project_id WHERE p.status='active' AND t.status<>'done' AND t.due_date IS NOT NULL AND t.due_date < CURDATE() UNION ALL SELECT s.id, s.title, s.due_date, 'standalone' AS task_kind FROM standalone_tasks s WHERE s.status<>'done' AND s.due_date IS NOT NULL AND s.due_date < CURDATE()")->fetchAll(PDO::FETCH_ASSOC);
        $taskUsers = fvNotificationRecipients($conn, ['ProjectManagement']);
        foreach ($taskRows as $task) {
            $url = $task['task_kind'] === 'standalone' ? 'projects.php?view=standalone_task&id='.(int)$task['id'] : 'projects.php?view=task&id='.(int)$task['id'];
            foreach ($taskUsers as $userId) {
                fvCreateNotification($conn, $userId, 'task_overdue', $task['task_kind'], (int)$task['id'], (string)$task['due_date'], 'Aufgabe überfällig', '„'.$task['title'].'“ ist seit dem '.date('d.m.Y', strtotime($task['due_date'])).' überfällig.', $url);
            }
        }
        // Kontaktvorschläge bleiben offen, solange is_suggestion gesetzt ist.
        $conn->exec("UPDATE notifications n JOIN contacts c ON n.entity_type='contact' AND n.entity_id=c.primary_key SET n.status='done', n.completed_at=COALESCE(n.completed_at,NOW()) WHERE n.notification_type='contact_suggestion' AND n.status='open' AND COALESCE(c.is_suggestion,0)=0");
        // Überfällige Aufgaben nicht länger als offenen Handlungsbedarf führen, wenn erledigt oder nicht mehr überfällig.
        $conn->exec("UPDATE notifications n LEFT JOIN tasks t ON n.entity_type='project' AND t.id=n.entity_id LEFT JOIN projects p ON p.id=t.project_id LEFT JOIN standalone_tasks s ON n.entity_type='standalone' AND s.id=n.entity_id SET n.status='done', n.completed_at=COALESCE(n.completed_at,NOW()) WHERE n.notification_type='task_overdue' AND n.status='open' AND ((n.entity_type='project' AND (t.id IS NULL OR t.status='done' OR p.status<>'active' OR t.due_date IS NULL OR t.due_date>=CURDATE() OR t.due_date<>n.event_key)) OR (n.entity_type='standalone' AND (s.id IS NULL OR s.status='done' OR s.due_date IS NULL OR s.due_date>=CURDATE() OR s.due_date<>n.event_key)))");
    }
}
