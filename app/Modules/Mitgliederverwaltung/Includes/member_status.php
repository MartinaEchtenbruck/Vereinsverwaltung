<?php
/**
 * Zentrale Statuspflege für Mitgliedschaften.
 * Eine gekündigte Mitgliedschaft wird ab dem Tag nach dem Enddatum inaktiv.
 */
function updateExpiredMemberStatuses(PDO $conn): int
{
    $stmt = $conn->prepare("\n        UPDATE members\n        SET status = 'Inaktiv'\n        WHERE status = 'Gekündigt'\n          AND end_date IS NOT NULL\n          AND end_date < CURRENT_DATE()\n    ");
    $stmt->execute();
    return $stmt->rowCount();
}
