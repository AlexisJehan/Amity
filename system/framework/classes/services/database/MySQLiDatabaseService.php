<?php
	/*
	 * MIT License
	 *
	 * Copyright (c) 2017-2024 Alexis Jehan
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
	 * Service de base de données utilisant l'extension « mysqli »
	 *
	 * Ce service permet de se connecter à une base de données en utilisant « mysqli ».
	 *
	 * @package    framework
	 * @subpackage classes/services/database
	 * @version    25/01/2023
	 * @since      06/06/2015
	 */
	class MySQLiDatabaseService extends DatabaseService {
		/*
		 * CHANGELOG:
		 * 25/01/2023: Compatibilité avec PHP 7.4, « get_magic_quotes_runtime() » est devenu déprécié
		 * 01/07/2020: Ajout de la personnalisation d'options à la connexion à la base de données
		 * 06/06/2015: Version initiale
		 */

		/**
		 * Requête en attente d'être exécutée
		 *
		 * @var string
		 */
		protected $query;

		/**
		 * Tableau des associations nommées (exemple: « WHERE :name = 'nom' »)
		 *
		 * @var array
		 */
		protected $namedBinding;

		/**
		 * Tableau des associations marquées (exemple: « WHERE ? = 'nom' »)
		 *
		 * @var array
		 */
		protected $markedBinding;

		/**
		 * Résultat de la requête
		 *
		 * @var mysqli_result
		 */
		protected $result;

		/**
		 * Indique si le résultat a été libéré de la mémoire
		 *
		 * @var boolean
		 */
		protected $free = FALSE;

		/**
		 * {@inheritdoc}
		 */
		protected final function __connect($host, $port, $database, $user, $password, $encoding, array $options) {
			$this->connection = mysqli_init();
			foreach ($options as $option => $value) {
				$this->connection->options($option, $value);
			}

			// Tentative de connexion au serveur
			$this->connection->real_connect($host, $user, $password, $database, empty($port) ? NULL : $port);

			if ($this->connection->connect_error) {

				// Impossible de se connecter à la base de données (serveur indisponible par exemple)
				// En mode de développement une exception sera lancée, autrement la connexion échouera et une page d'erreur d'affichera
				if (DEV_MODE) {
					throw $this->databaseException($this->connection->connect_error, $this->connection->connect_errno);
				}

				return FALSE;
			}

			// Sélection du charset
			if (!$this->connection->set_charset($encoding)) {
				throw $this->databaseException();
			}

			return TRUE;
		}

		/**
		 * {@inheritdoc}
		 */
		protected final function __disconnect() {
			if (!$this->connection->close()) {
				throw $this->databaseException();
			}
			return TRUE;
		}

		/**
		 * {@inheritdoc}
		 */
		public function query($query) {
			$this->query = $query;

			// Réinitialisation des attributs
			$this->namedBinding = array();
			$this->markedBinding = array();
			$this->result = NULL;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function bind($key, $value, $type = NULL) {

			// Transformation de la valeur selon son type renseigné ou déterminé
			if ((NULL === $type && is_int($value)) || $type === self::PARAM_INT) {
				$value = intval($value);

			} else if ((NULL === $type && is_bool($value)) || $type === self::PARAM_BOOL) {
				$value = $value ? 'TRUE' : 'FALSE';
			} else if ((NULL === $type && NULL === $value) || $type === self::PARAM_NULL) {

				// FIXME « = NULL / != NULL » devrait plutôt être « IS NULL / IS NOT NULL »
				$value = 'NULL';
			} else {

				// Déprécié depuis PHP 7.4, supprimé depuis PHP 8.0
				if (version_compare(PHP_VERSION, '7.4', '<') && get_magic_quotes_runtime()) {
					$value = stripslashes($value);
				}

				$value = '\'' . $this->connection->real_escape_string($value) . '\'';
			}

			// Selon la clé renseigné on ajoute dans l'association qui correspond
			if (is_int($key) && 0 < $key) {
				$this->markedBinding[$key] = $value;
			} else {
				$this->namedBinding[':' . ltrim($key, ':')] = $value;
			}

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function __execute() {
			//ini_set("pcre.recursion_limit", 524);

			// Si on doit associer des valeurs par nomination
			if (0 < count($this->namedBinding)) {
				$binding = $this->namedBinding;
				$this->query = preg_replace_callback('%(:\w+)|(?:\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|/*[^*]*\*+([^*/][^*]*\*+)*/|\#.*|--.*|[^"\':#])+%u', function($match) use($binding) {
					if (isset($match[1]) && isset($binding[$match[1]])) {
						return str_replace($match[1], $binding[$match[1]], $match[0]);
					} else {
						return $match[0];
					}
				}, $this->query, -1);
			}

			// Si on doit associer des valeurs par marquage
			if (0 < count($this->markedBinding)) {
				$binding = $this->markedBinding;
				$count = 0;
				$this->query = preg_replace_callback('%\?|(?:\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|/*[^*]*\*+([^*/][^*]*\*+)*/|\#.*|--.*|[^"\'?#])+%u', function($match) use($binding, &$count) {
					if ('?' === $match[0] && isset($binding[++$count])) {
						return $binding[$count];
					} else {
						return $match[0];
					}
				}, $this->query, -1, $count);
			}

			// Exécution de la ou des requêtes
			if ($this->connection->multi_query($this->query)) {
				do {
					$this->result = $this->connection->store_result();
				} while ($this->connection->more_results() && $this->connection->next_result());
				$this->free = FALSE;
			} else {
				throw $this->databaseException();
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public final function row($fetch = self::FETCH_ASSOC) {

			// Si le résultat a déjà été libéré on quitte
			if ($this->free) {
				return FALSE;
			}

			// Selon ma méthode de récupération on traite le résultat différemment
			switch ($fetch) {
				case self::FETCH_ASSOC:
					return $this->result->fetch_assoc();
					break;
				case self::FETCH_NUM:
					return $this->result->fetch_row();
					break;
				case self::FETCH_BOTH:
					return $this->result->fetch_array();
					break;
				case self::FETCH_OBJ:
					return $this->result->fetch_object();
					break;

				// TODO Implémenter les autres méthodes de récupération introduites par PDO
				/*case self::FETCH_KEY_PAIR:
					return self::mysql_fetch_key_pair($this->result);
					break;
				case self::FETCH_GROUP_UNIQUE_ASSOC:
					return self::mysql_fetch_group_unique_assoc($this->result);
					break;*/

				default:
					throw new ServiceException('Unavailable or unimplemented fetch method with flag "%s" using "%s" database service', $fetch, $this->getAccessName());
					break;
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public final function rows($fetch = self::FETCH_ASSOC) {
			$table = array();
			if (!$this->free) {
				while (($table[] = $this->row($fetch)) || array_pop($table));
				$this->result->free_result();
				$this->free = TRUE;
			}
			return $table;
		}

		/**
		 * {@inheritdoc}
		 */
		public final function column($number = 0) {

			// Si le résultat a déjà été libéré on quitte
			if ($this->free) {
				return FALSE;
			}

			$row = $this->row(self::FETCH_NUM);
			return isset($row[$number]) ? $row[$number] : NULL;
		}

		/**
		 * {@inheritdoc}
		 */
		public final function columns($number = 0) {
			$table = array();
			if (!$this->free) {
				while ($row = $this->row(self::FETCH_NUM)) {
					if (!isset($row[$number])) {
						throw $this->databaseException('Invalid column index');
					}
					$table[] = $row[$number];
				}
				$this->result->free_result();
				$this->free = TRUE;
			}
			return $table;
		}

		/**
		 * {@inheritdoc}
		 */
		public function count() {
			return $this->connection->affected_rows;
		}

		/**
		 * {@inheritdoc}
		 */
		public final function autoCommit($enabled = TRUE) {
			if (!$this->connection->autocommit($enabled)) {
				throw $this->databaseException();
			}
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public final function beginTransaction() {
			if (!$this->connection->begin_transaction()) {
				throw $this->databaseException();
			}
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public final function commit() {
			if (!$this->connection->commit()) {
				throw $this->databaseException();
			}
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public final function rollback() {
			if (!$this->connection->rollback()) {
				throw $this->databaseException();
			}
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getAccessName() {
			return 'MySQLi';
		}

		/**
		 * Création d'une exception de base de données personnalisée
		 *
		 * @param  string            $message Le message personnalisé [optionnel]
		 * @param  integer           $code    Le code personnalisé [optionnel, « 0 » par défaut]
		 * @return DatabaseException          L'exception personnalisée crée
		 */
		protected final function databaseException($message = NULL, $code = 0) {
			if (NULL === $message) {
				$message = $this->connection->error;
				$code = $this->connection->errno;
			}
			return new DatabaseException($message, $code);
		}
	}

	// On vérifie que l'extension est disponible
	if (!extension_loaded('mysqli')) {
		throw new SystemException('"%s" extension is not available', 'mysqli');
	}
?>