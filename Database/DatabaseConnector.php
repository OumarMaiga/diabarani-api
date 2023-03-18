<?php 

    namespace Database;

    use PDO;
    use Exception;

    class DatabaseConnector {
        
        protected $dbConnection = null;
        
        public function __construct() {
            
            $user = "root";
            $password = '';
            $dbName = 'diabarani';
            $host = 'localhost';
            
            try {

                $this->dbConnection = new PDO("mysql:host=$host;dbname=$dbName",$user,$password);

            } catch (Exception $e) {
                die('Erreur: '.$e->getMessage());
            }
        }

        public function getConnection() {
            return $this->dbConnection;
        }
    }