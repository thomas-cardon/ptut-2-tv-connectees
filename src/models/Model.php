<?php

namespace Models;

use PDO;

/**
 * Class Model
 *
 * Generic class for Model
 * Contain basic function and connection to the database
 *
 * @package Models
 */
class Model
{

	/**
	 * @var PDO
	 */
	private static $database;

	/**
	 * Connect to the database
	 */
	private static function setDatabase()
    {
        self::$database = new PDO( 'mysql:host=' . DB_HOST . '; dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        self::$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

	/**
	 * Return the connection
	 *
	 * @return PDO
	 */
	protected function getDatabase()
    {
        if (self:: $database == null)
            self::setDatabase();
        return self::$database;
    }
}