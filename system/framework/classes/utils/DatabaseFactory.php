<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Usine de base de données
	 * 
	 * Cette classe permet de créer une instance de service de base de données selon une partie de la configuration de l'application.
	 * 
	 * @package    framework
	 * @subpackage classes/utils
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    06/06/2015
	 * @since      16/12/2014
	 */
	final class DatabaseFactory extends StaticClass {
		/*
		 * CHANGELOG:
		 * 06/06/2015: Ajout des drivers PDO
		 * 16/12/2014: Version initiale
		 */

		/**
		 * Liste des services disponibles
		 * 
		 * @var array
		 */
		private static $databaseServices = array(
			'PDO'             =>            'PDODatabaseService',
			'MYSQL'           =>          'MySQLDatabaseService',
			'MYSQL_SPECIFIC'  =>  'SpecificMySQLDatabaseService',
			'MYSQLI'          =>         'MySQLiDatabaseService',
			'MYSQLI_SPECIFIC' => 'SpecificMySQLiDatabaseService'
		);

		/**
		 * Liste des drivers PDO disponibles
		 * 
		 * @var array
		 */
		private static $pdoDrivers = array(
			'PDO_CUBRID'   =>   'cubrid',		// Cubrid
			'PDO_DBLIB'    =>    'dblib',		// FreeTDS / Microsoft SQL Server / Sybase
			'PDO_FIREBIRD' => 'firebird',		// Firebird
			'PDO_IBM'      =>      'ibm',		// IBM DB2
			'PDO_INFORMIX' => 'informix',		// IBM Informix Dynamic Server
			'PDO_MYSQL'    =>    'mysql',		// MySQL 3.x/4.x/5.x
			'PDO_OCI'      =>      'oci',		// Oracle Call Interface
			'PDO_ODBC'     =>     'odbc',		// ODBC v3 (IBM DB2, unixODBC and win32 ODBC)
			'PDO_PGSQL'    =>    'pgsql',		// PostgreSQL
			'PDO_SQLITE'   =>   'sqlite',		// SQLite 3 and SQLite 2
			'PDO_SQLSRV'   =>   'sqlsrv',		// Microsoft SQL Server / SQL Azure
			'PDO_4D'       =>       '4D'		// 4D
		);

		/**
		 * Création d'un service de base de données
		 *
		 * @param  array           $config La configuration permettant de créer le service de base de données
		 * @return DatabaseService         L'instance du service de base de données crée
		 */
		public static function create(array $config) {

			// Hôte du serveur de base de données
			if(isset($config['DB_HOST'])) {
				$host = $config['DB_HOST'];
			} else {
				throw new SystemException('Unable to create the database service because "%s" is missing from the configuration', 'DB_HOST');
			}

			// Port de connection au serveur de base de données [vide par défaut]
			$port = isset($config['DB_PORT']) ? $config['DB_PORT'] : '';

			// Nom de la base de données
			if(isset($config['DB_DATABASE'])) {
				$database = $config['DB_DATABASE'];
			} else {
				throw new SystemException('Unable to create the database service because "%s" is missing from the configuration', 'DB_DATABASE');
			}

			// Utilisateur du schéma de la base de données
			if(isset($config['DB_USER'])) {
				$user = $config['DB_USER'];
			} else {
				throw new SystemException('Unable to create the database service because "%s" is missing from the configuration', 'DB_USER');
			}

			// Mot de passe du schéma de la base de données
			if(isset($config['DB_PASSWORD'])) {
				$password = $config['DB_PASSWORD'];
			} else {
				throw new SystemException('Unable to create the database service because "%s" is missing from the configuration', 'DB_PASSWORD');
			}

			// Encodage de connection au serveur de base de données [« utf8 » par défaut]
			$encoding = isset($config['DB_ENCODING']) ? $config['DB_ENCODING'] : 'utf8';

			// Accès à la base de données [« PDO » par défaut]
			$access = isset($config['DB_ACCESS']) ? strtoupper($config['DB_ACCESS']) : 'PDO';


			// Si l'accès renseigné est disponible
			if(in_array($access, array_keys(self::$databaseServices))) {

				// On l'instancie avec les paramètres et on le retourne
				$databaseService = self::$databaseServices[$access];
				return new $databaseService($host, $port, $database, $user, $password, $encoding);

			// Sinon si c'est un accès PDO avec le driver de renseigné
			} else if(in_array($access, array_keys(self::$pdoDrivers))) {
				return new PDODatabaseService($host, $port, $database, $user, $password, $encoding, self::$pdoDrivers[$access]);

			// Sinon le service n'est pas disponible à l'instanciation
			} else {
				throw new SystemException('Unable to create the database service because "%s" does not match any', $access);
			}
		}
	}
?>