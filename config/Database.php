<?php
namespace Config;

use PDO;
use Exception;

/**
 * Classe Database - Gestion de la connexion à la base de données
 */
class Database
{
  private static $pdo = null;

  public static function getConnexion()
  {
    if (!isset(self::$pdo)) {
      try {
        self::$pdo = new PDO(
          'mysql:host=localhost;dbname=asteria',
          'root',
          '',
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ]
        );
      } catch (Exception $e) {
        die('Erreur de connexion DB: ' . $e->getMessage());
      }
    }
    return self::$pdo;
  }
}
