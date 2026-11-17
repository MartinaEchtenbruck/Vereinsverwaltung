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

        // Nur bei einem tatsächlich neu angelegten Datenbankeintrag Push senden.
        // INSERT IGNORE liefert bei einem bereits vorhandenen eindeutigen Eintrag rowCount() = 0.
        if ($q->rowCount() > 0) {
            require_once __DIR__ . '/WebPush.php';
            fvSendWebPushNotification($conn, $userId, $type, $title, $message, $targetUrl, (int)$conn->lastInsertId());
        }
    }

    /** Läuft beim Rendern der Navigation und im Cronjob; Einträge werden über eindeutige Schlüssel nicht dupliziert. */
    function fvSyncNotifications(PDO $conn): void {
        // Überfällige Aufgaben in aktiven Projekten sowie projektunabhängige Aufgaben.
        $taskRows = $conn->query("SELECT t.id, t.title, t.due_date FROM tasks t LEFT JOIN projects p ON p.id=t.project_id WHERE (t.project_id IS NULL OR p.status='active') AND t.status<>'done' AND t.due_date IS NOT NULL AND t.due_date < CURDATE()")->fetchAll(PDO::FETCH_ASSOC);
        $taskUsers = fvNotificationRecipients($conn, ['ProjectManagement']);
        foreach ($taskRows as $task) {
            $url = 'projects.php?view=task&id='.(int)$task['id'];
            foreach ($taskUsers as $userId) {
                fvCreateNotification($conn, $userId, 'task_overdue', 'task', (int)$task['id'], (string)$task['due_date'], 'Aufgabe überfällig', '„'.$task['title'].'“ ist seit dem '.date('d.m.Y', strtotime($task['due_date'])).' überfällig.', $url);
            }
        }

        // Mitgliedsjubiläen: Push-Erinnerungen sechs und zwei Monate vor dem Jubiläum.
        // Die event_keys unterscheiden beide Erinnerungen und verhindern Duplikate.
        $memberUsers = fvNotificationRecipients($conn, ['manage_members']);
        if ($memberUsers) {
            $members = $conn->query("SELECT id, first_name, last_name, start_date, end_date FROM members WHERE start_date IS NOT NULL")->fetchAll(PDO::FETCH_ASSOC);
            $today = new DateTimeImmutable('today');
            $shiftCalendarMonths = static function (DateTimeImmutable $date, int $months): DateTimeImmutable {
                $target = $date->modify('first day of this month')->modify(($months >= 0 ? '+' : '').$months.' months');
                $day = min((int)$date->format('j'), (int)$target->modify('last day of this month')->format('j'));
                return $target->setDate((int)$target->format('Y'), (int)$target->format('n'), $day);
            };
            foreach ($members as $member) {
                $entry = new DateTimeImmutable($member['start_date']);
                foreach ([10, 20, 25, 30, 40] as $years) {
                    $year = (int)$entry->format('Y') + $years;
                    $month = (int)$entry->format('n');
                    $day = min((int)$entry->format('j'), (int)(new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->modify('last day of this month')->format('j'));
                    $anniversary = $entry->setDate($year, $month, $day);
                    if (!empty($member['end_date']) && new DateTimeImmutable($member['end_date']) < $anniversary) continue;
                    if ($today >= $anniversary) continue;

                    $name = trim($member['first_name'].' '.$member['last_name']);
                    $dateText = $anniversary->format('d.m.Y');
                    foreach ([6, 2] as $monthsBefore) {
                        $reminderDate = $shiftCalendarMonths($anniversary, -$monthsBefore);
                        // Ein verpasster Cron-Lauf wird innerhalb des Folgemonats nachgeholt,
                        // aber nicht noch Monate später als verspätete Erinnerung verschickt.
                        $reminderUntil = $shiftCalendarMonths($reminderDate, 1);
                        if ($today < $reminderDate || $today > $reminderUntil) continue;

                        $label = $monthsBefore.' Monate';
                        $title = 'Mitgliedsjubiläum in '.$label;
                        $message = $name.' erreicht am '.$dateText.' '.$years.' Jahre Mitgliedschaft.';
                        $url = 'mitglieder.php?open='.(int)$member['id'];
                        $eventKey = $anniversary->format('Y-m-d').':'.$monthsBefore.'m';
                        foreach ($memberUsers as $userId) {
                            fvCreateNotification($conn, $userId, 'member_anniversary', 'member', (int)$member['id'], $eventKey, $title, $message, $url);
                        }
                    }
                }
            }
        }

        // Jubiläumserinnerungen nach dem Jubiläum schließen; eine vorher beendete
        // Mitgliedschaft macht die Erinnerung ebenfalls gegenstandslos.
        $conn->exec("UPDATE notifications n LEFT JOIN members m ON n.entity_type='member' AND n.entity_id=m.id SET n.status='done', n.completed_at=COALESCE(n.completed_at,NOW()) WHERE n.notification_type='member_anniversary' AND n.status='open' AND (m.id IS NULL OR STR_TO_DATE(LEFT(n.event_key,10),'%Y-%m-%d') < CURDATE() OR (m.end_date IS NOT NULL AND m.end_date < STR_TO_DATE(LEFT(n.event_key,10),'%Y-%m-%d')))");

        // Kontaktvorschläge bleiben offen, solange is_suggestion gesetzt ist.
        $conn->exec("UPDATE notifications n JOIN contacts c ON n.entity_type='contact' AND n.entity_id=c.primary_key SET n.status='done', n.completed_at=COALESCE(n.completed_at,NOW()) WHERE n.notification_type='contact_suggestion' AND n.status='open' AND COALESCE(c.is_suggestion,0)=0");

        // Überfällige Aufgaben-Benachrichtigungen löschen, sobald die Aufgabe erledigt
        // oder nicht mehr überfällig ist. Damit verschwinden sie auch aus der Nachrichtenliste.
        $conn->exec("DELETE n FROM notifications n LEFT JOIN tasks t ON n.entity_type='task' AND t.id=n.entity_id LEFT JOIN projects p ON p.id=t.project_id WHERE n.notification_type='task_overdue' AND n.entity_type='task' AND (t.id IS NULL OR t.status='done' OR (t.project_id IS NOT NULL AND p.status<>'active') OR t.due_date IS NULL OR t.due_date>=CURDATE() OR t.due_date<>n.event_key)");
    }
}
