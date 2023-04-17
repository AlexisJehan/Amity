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
	 * Service de base de données utilisant l'extension « PDO »
	 *
	 * Ce service permet de se connecter à une base de données en utilisant « PDO », c'est l'accès recommandé par défaut.
	 *
	 * @package    framework
	 * @subpackage classes/services/database
	 * @version    01/07/2020
	 * @since      23/09/2014
	 */
	final class PDODatabaseService extends DatabaseService {
		/*
		 * CHANGELOG:
		 * 01/07/2020: Ajout de la personnalisation d'options à la connexion à la base de données
		 * 06/06/2015: Gestion du charset personnalisé, compatible selon la version de PHP
		 * 23/09/2014: Version initiale
		 */

		/**
		 * Déclaration de la requête préparée
		 *
		 * @var PDOStatement
		 */
		protected $statement;

		/**
		 * {@inheritdoc}
		 */
		protected function __connect($host, $port, $database, $user, $password, $encoding, array $options, $driver = 'mysql') {

			// Paramètres par défaut
			$settings = array(
				PDO::ATTR_PERSISTENT       => TRUE,
				PDO::ATTR_EMULATE_PREPARES => TRUE,
				PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
			);

			// Pour les versions inférieures de MySQL, on définit le charset manuellement
			if ('mysql' === $driver && defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
				$settings[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $encoding;
			}

			$settings = $options + $settings;

			// Tentative de connexion via le DSN généré avec les paramètres
			try {
				$this->connection = new PDO($driver . ':host=' . $host . (!empty($port) ? ';port=' . $port : '') . ';dbname=' . $database . ';charset=' . $encoding, $user, $password, $settings);
			} catch (PDOException $exception) {

				// Impossible de se connecter à la base de données (serveur indisponible par exemple)
				// En mode de développement une exception sera lancée, autrement la connexion échouera et une page d'erreur d'affichera
				if (DEV_MODE) {
					throw $this->databaseException($exception);
				}

				return FALSE;
			}

			// Seconde tentative du forçage de charset avec les serveurs MySQL
			if ('mysql' === $driver && !defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
				$this->connection->exec('SET NAMES ' . $encoding);
			}

			// Bug: La désactivation de l'émulation des requêtes préparées produit une exception lors de l'utilisation d'un même placeholder nommé plusieurs fois
			// Exemple: SELECT * FROM users WHERE name = :login OR email = :login
			//                                           ------            ------
			/*if ('mysql' === $driver) {
				$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, version_compare($this->connection->getAttribute(PDO::ATTR_SERVER_VERSION), '5.1.17', '<'));
			}*/

			return TRUE;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function __disconnect() {
			$this->statement = NULL;
			$this->connection = NULL;
			return TRUE;
		}

		/**
		 * {@inheritdoc}
		 */
		public function query($query) {
			try {
				$this->statement = $this->connection->prepare($query);
			} catch (PDOException $exception) {
				throw $this->databaseException($exception);
			}
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function bind($key, $value, $type = NULL) {

			// Si le type n'est pas renseigné on le détermine
			if (NULL === $type) {
				switch (TRUE) {
					case is_int($value):
						$type = self::PARAM_INT;
						break;
					case is_bool($value):
						$type = self::PARAM_BOOL;
						break;
					case NULL === $value:
						$type = self::PARAM_NULL;
						break;
					default:
						$type = self::PARAM_STR;
				}
			}

			$this->statement->bindValue($key, $value, $type);
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function __execute() {
			try {
				$this->statement->execute();
			} catch (PDOException $exception) {
				throw $this->databaseException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function row($fetch = self::FETCH_ASSOC) {
			try {
				return $this->statement->fetch($fetch);
			} catch (PDOException $exception) {
				throw $this->databaseException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function rows($fetch = self::FETCH_ASSOC) {
			try {
				return $this->statement->fetchAll($fetch);
			} catch (PDOException $exception) {
				throw $this->databaseException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function column($number = 0) {
			try {
				return $this->statement->fetchColumn($number);
			} catch (PDOException $exception) {
				throw $this->databaseException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function columns($number = 0) {
			try {
				return $this->statement->fetchAll(PDO::FETCH_COLUMN, $number);
			} catch (PDOException $exception) {
				throw $this->databaseException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function count() {
			return $this->statement->rowCount();
		}

		/**
		 * {@inheritdoc}
		 */
		public function autoCommit($enabled = TRUE) {
			$this->connection->setAttribute(PDO::ATTR_AUTOCOMMIT , $enabled);
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function beginTransaction() {
			$this->connection->beginTransaction();
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function commit() {
			$this->connection->commit();
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function rollback() {
			$this->connection->rollBack();
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getAccessName() {
			return 'PDO [' . $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME) . ']';
		}

		/**
		 * Création d'une exception de base de données personnalisée
		 *
		 * @param  PDOException      $exception L'exception provoquée
		 * @return DatabaseException            L'exception personnalisée crée
		 */
		protected function databaseException(PDOException $exception) {
			$code = $exception->getCode();
			$message = $exception->getMessage();
			if (0 === strpos($message, 'SQLSTATE[')) {
				preg_match('/^SQLSTATE\[\w+\]:\s*(?:[^:]+:\s*(\d*)\s*)?(.*)/', $message, $matches) || preg_match('/^SQLSTATE\[\w+\]\s*\[(\d+)\]\s*(.*)/', $message, $matches);
				$code = !empty($matches[1]) ? $matches[1] : 0;
				$message = $matches[2];
			}
			return new DatabaseException($message, $code);
		}
	}

	// On vérifie que l'extension est disponible
	if (!extension_loaded('pdo')) {
		throw new SystemException('"%s" extension is not available', 'pdo');
	}
?>