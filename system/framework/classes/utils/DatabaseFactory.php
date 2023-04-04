<?php
	/*
	 * MIT License
	 *
	 * Copyright (c) 2017-2023 Alexis Jehan
	 *
	 * Permission is hereby granted, free of charge, to any person obtaining a copy
	 * of this software and associated documentation files (the "Software"), to deal
	 * in the Software without restriction, including without limitation the rights
	 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	 * copies of the Software, and to permit persons to whom the Software is
	 * furnished to do so, subject to the following conditions:
	 *
	 * The above copyright notice and this permission notice shall be included in all
	 * copies or substantial portions of the Software.
	 *
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	 * SOFTWARE.
	 */

	/**
	 * Usine de base de données
	 * 
	 * Cette classe permet de créer une instance de service de base de données selon une partie de la configuration de l'application.
	 * 
	 * @package    framework
	 * @subpackage classes/utils
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    01/07/2020
	 * @since      16/12/2014
	 */
	final class DatabaseFactory extends StaticClass {
		/*
		 * CHANGELOG:
		 * 01/07/2020: Ajout de la personnalisation d'options à la connexion à la base de données
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
			'MYSQLI_SPECIFIC' => 'SpecificMySQLiDatabaseService',
		);

		/**
		 * Liste des drivers PDO disponibles
		 * 
		 * @var array
		 */
		private static $pdoDrivers = array(
			'PDO_CUBRID'   => 'cubrid',   // Cubrid
			'PDO_DBLIB'    => 'dblib',    // FreeTDS / Microsoft SQL Server / Sybase
			'PDO_FIREBIRD' => 'firebird', // Firebird
			'PDO_IBM'      => 'ibm',      // IBM DB2
			'PDO_INFORMIX' => 'informix', // IBM Informix Dynamic Server
			'PDO_MYSQL'    => 'mysql',    // MySQL 3.x/4.x/5.x
			'PDO_OCI'      => 'oci',      // Oracle Call Interface
			'PDO_ODBC'     => 'odbc',     // ODBC v3 (IBM DB2, unixODBC and win32 ODBC)
			'PDO_PGSQL'    => 'pgsql',    // PostgreSQL
			'PDO_SQLITE'   => 'sqlite',   // SQLite 3 and SQLite 2
			'PDO_SQLSRV'   => 'sqlsrv',   // Microsoft SQL Server / SQL Azure
			'PDO_4D'       => '4D',       // 4D
		);

		/**
		 * Création d'un service de base de données
		 *
		 * @param  array           $config La configuration permettant de créer le service de base de données
		 * @return DatabaseService         L'instance du service de base de données crée
		 */
		public static function create(array $config) {

			// Hôte du serveur de base de données
			if (isset($config['DB_HOST'])) {
				$host = $config['DB_HOST'];
			} else {
				throw new SystemException('Unable to create the database service because "%s" is missing from the configuration', 'DB_HOST');
			}

			// Port de connexion au serveur de base de données [vide par défaut]
			$port = isset($config['DB_PORT']) ? $config['DB_PORT'] : '';

			// Nom de la base de données
			if (isset($config['DB_DATABASE'])) {
				$database = $config['DB_DATABASE'];
			} else {
				throw new SystemException('Unable to create the database service because "%s" is missing from the configuration', 'DB_DATABASE');
			}

			// Utilisateur du schéma de la base de données
			if (isset($config['DB_USER'])) {
				$user = $config['DB_USER'];
			} else {
				throw new SystemException('Unable to create the database service because "%s" is missing from the configuration', 'DB_USER');
			}

			// Mot de passe du schéma de la base de données
			if (isset($config['DB_PASSWORD'])) {
				$password = $config['DB_PASSWORD'];
			} else {
				throw new SystemException('Unable to create the database service because "%s" is missing from the configuration', 'DB_PASSWORD');
			}

			// Encodage de connexion au serveur de base de données [« utf8 » par défaut]
			$encoding = isset($config['DB_ENCODING']) ? $config['DB_ENCODING'] : 'utf8';

			// Options de connexion au serveur de base de données [vide par défaut]
			$options = isset($config['DB_OPTIONS']) ? $config['DB_OPTIONS'] : array();

			// Accès à la base de données [« PDO » par défaut]
			$access = isset($config['DB_ACCESS']) ? strtoupper($config['DB_ACCESS']) : 'PDO';

			// Si l'accès renseigné est disponible
			if (in_array($access, array_keys(self::$databaseServices))) {

				// On l'instancie avec les paramètres et on le retourne
				$databaseService = self::$databaseServices[$access];
				return new $databaseService($host, $port, $database, $user, $password, $encoding, $options);

			// Sinon si c'est un accès PDO avec le driver de renseigné
			} else if (in_array($access, array_keys(self::$pdoDrivers))) {
				return new PDODatabaseService($host, $port, $database, $user, $password, $encoding, $options, self::$pdoDrivers[$access]);

			// Sinon le service n'est pas disponible à l'instanciation
			} else {
				throw new SystemException('Unable to create the database service because "%s" does not match any', $access);
			}
		}
	}
?>