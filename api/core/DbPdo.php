<?php
class DbPdo {
    private $dbhost = 'localhost';
    private $dbport = '3306';
    private $dbname = 'armatumancha';
    private $dbuser = 'root';
    private $dbpass = '';
    private $dbcharset = 'utf8';

    function connect() {
        try {
            $cn = new PDO( "mysql:host={$this->dbhost}:{$this->dbport};dbname={$this->dbname}", $this->dbuser, $this->dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$this->dbcharset}'"));
            $cn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch(PDOException $e) {
            die("Error al conectarse a la Base de Datos." . $e->getMessage());
        }
        return $cn;
    }
}