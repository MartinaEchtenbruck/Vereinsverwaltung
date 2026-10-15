<?php
// db_connection.php
class Database {
    private $host = '10.35.233.43:3306';
    private $db_name = 'k222795_fv_intern';
    private $username = 'k222795_fv_user_intern';  // Anpassen mit Ihren MySQL-Zugangsdaten
    private $password = 'h#JmS!@s8F@PQC^hd5Z@7wMfu';      // Anpassen mit Ihrem MySQL-Passwort
    public $conn;
	
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $exception) {
            // Fehler loggen aber keine sensiblen Daten anzeigen
            error_log("Datenbankverbindungsfehler: " . $exception->getMessage());
            
            // Benutzerfreundliche Fehlermeldung
            throw new Exception('Datenbankverbindung fehlgeschlagen. Bitte versuchen Sie es später erneut.');
        }
        return $this->conn;
    }
}
?>