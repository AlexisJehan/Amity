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
	 * Service de base de données utilisant l'extension « mysql »
	 *
	 * Ce service permet de se connecter à une base de données en utilisant « mysql », il n'est pas recommandé à l'utilisation par sécurité et est proposé uniquement par compatibilité.
	 *
	 * @package    framework
	 * @subpackage classes/services/database
	 * @version    25/01/2023
	 * @since      24/09/2014
	 */
	class MySQLDatabaseService extends DatabaseService {
		/*
		 * CHANGELOG:
		 * 25/01/2023: Compatibilité avec PHP 7.4, « get_magic_quotes_runtime() » est devenu déprécié
		 * 01/07/2020: Ajout de la personnalisation d'options à la connexion à la base de données
		 * 22/06/2015: Utilisation de « mysql_set_charset() »
		 * 05/06/2015: Amélioration des différentes expressions régulières pour ne pas faire correspondre les jetons entre apostrophes ou guillemets
		 * 02/06/2015: Correction d'un bug se produisant avec le binding quand le nom d'une clef était inclus dans celui d'une autre (str_replace, gotcha)
		 * 04/04/2015: Amélioration de l'exécution de plusieurs requêtes
		 * 24/09/2014: Version initiale
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
		 * @var resource
		 */
		protected $result;

		/**
		 * Nombre de lignes retournées ou altérées par la dernière requête
		 *
		 * @var integer
		 */
		protected $count;

		/**
		 * Indique si le résultat a été libéré de la mémoire
		 *
		 * @var boolean
		 */
		protected $free = FALSE;

		/**
		 * {@inheritdoc}
		 *
		 * @param  string  $host     Le nom de l'hôte
		 * @param  string  $port     Le port de connexion
		 * @param  string  $database Le nom de la base de données
		 * @param  string  $user     Le nom de l'utilisateur
		 * @param  string  $password Le mot de passe
		 * @param  string  $encoding L'encodage de connexion
		 * @param  array   $options  Les options de connexion
		 * @return boolean           Vrai si la connexion a été effectuée
		 */
		protected final function __connect($host, $port, $database, $user, $password, $encoding, array $options) {

			// Tentative de connexion au serveur
			if (!$this->connection = @mysql_pconnect($host . (!empty($port) ? ':' . $port : ''), $user, $password)) {

				// Impossible de se connecter à la base de données (serveur indisponible par exemple)
				// En mode de développement une exception sera lancée, autrement la connexion échouera et une page d'erreur d'affichera
				if (DEV_MODE) {
					throw $this->databaseException();
				}

				return FALSE;
			}

			// Tentative de connexion à la base de données
			if (!mysql_select_db($database, $this->connection)) {
				throw $this->databaseException();
			}

			// Sélection du charset (nécessite MySQL 5.0.7)
			if (!mysql_set_charset($encoding, $this->connection)) {
				throw $this->databaseException();
			}

			//mysql_query('SET NAMES ' . $encoding);

			return TRUE;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return boolean Vrai si la déconnexion a été effectuée
		 */
		protected final function __disconnect() {
			if (!mysql_close($this->connection)) {
				throw $this->databaseException();
			}
			return TRUE;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @param  string          $query La requête SQL à exécuter
		 * @return DatabaseService        L'instance courante
		 */
		public function query($query) {
			$this->query = $query;

			// Réinitialisation des attributs
			$this->namedBinding = array();
			$this->markedBinding = array();
			$this->result = NULL;
			$this->count = -1;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @param  string          $key   L'identifiant de la variable dans la requête
		 * @param  string          $value La valeur à associer
		 * @param  integer         $type  Le drapeau du type de la variable [vide par défaut]
		 * @return DatabaseService        L'instance courante
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

				$value = '\'' . mysql_real_escape_string($value, $this->connection) . '\'';
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

			// S'il y a plusieurs requêtes on les sépare pour les exécuter chacune à leur tour
			// Source: http://stackoverflow.com/a/5610067/4965547
			preg_match_all('%(?:\s|;)*((?:\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|/*[^*]*\*+([^*/][^*]*\*+)*/|\#.*|--.*|[^"\';#])+(?=;)*)%u', $this->query, $matches);
			$queries = $matches[1];

			// On exécute chaque requête
			foreach ($queries as $query) {
				$this->result = mysql_query($query, $this->connection);
				if (!$this->result) {
					throw $this->databaseException();
				}
			}

			// Si le résultat vaut « TRUE », ce n'était pas un « SELECT » donc on retourne le nombre de lignes affectées
			$this->count = TRUE === $this->result ? mysql_affected_rows($this->connection) : mysql_num_rows($this->result);
			$this->free = FALSE;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @param  integer $fetch La méthode de récupération [« DatabaseService::FETCH_ASSOC » par défaut]
		 * @return array          La ligne de résultat
		 */
		public final function row($fetch = self::FETCH_ASSOC) {

			// Si le résultat a déjà été libéré on quitte
			if ($this->free) {
				return FALSE;
			}

			// Selon ma méthode de récupération on traite le résultat différemment
			switch ($fetch) {
				case self::FETCH_ASSOC:
					return mysql_fetch_assoc($this->result);
					break;
				case self::FETCH_NUM:
					return mysql_fetch_row($this->result);
					break;
				case self::FETCH_BOTH:
					return mysql_fetch_array($this->result);
					break;
				case self::FETCH_OBJ:
					return mysql_fetch_object($this->result);
					break;
				// mysql_fetch_field ???

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
		 *
		 * @param  integer $fetch La méthode de récupération [« DatabaseService::FETCH_ASSOC » par défaut]
		 * @return array          Les lignes de résultats
		 */
		public final function rows($fetch = self::FETCH_ASSOC) {
			$table = array();
			if (!$this->free) {
				while (($table[] = $this->row($fetch)) || array_pop($table));
				$this->free = mysql_free_result($this->result);
			}
			return $table;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @param  integer $number Le numéro de la colonne
		 * @return mixed           La cellule
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
		 *
		 * @param  integer $number Le numéro de la colonne
		 * @return array           Les cellules
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
				$this->free = mysql_free_result($this->result);
			}
			return $table;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return integer Le nombre de lignes retournées ou altérées
		 */
		public final function count() {
			return $this->count;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @param  boolean         $enabled Activation si vrai [« TRUE » par défaut]
		 * @return DatabaseService          L'instance courante
		 */
		public final function autoCommit($enabled = TRUE) {
			mysql_query('SET AUTOCOMMIT = ' . ($enabled ? 1 : 0));
			return $this;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return DatabaseService L'instance courante
		 */
		public final function beginTransaction() {
			mysql_query('START TRANSACTION');
			return $this;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return DatabaseService L'instance courante
		 */
		public final function commit() {
			mysql_query('COMMIT');
			return $this;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return DatabaseService L'instance courante
		 */
		public final function rollback() {
			mysql_query('ROLLBACK');
			return $this;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return string Le nom spécifique du service utilisé
		 */
		public function getAccessName() {
			return 'MySQL';
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
				if (FALSE !== $this->connection) {
					$message = mysql_error($this->connection);
					$code = mysql_errno($this->connection);
				} else {
					$message = mysql_error();
					$code = mysql_errno();
				}
			}
			return new DatabaseException($message, $code);
		}
	}

	// On vérifie que l'extension est disponible
	if (!extension_loaded('mysql')) {
		throw new SystemException('"%s" extension is not available', 'mysql');
	}
?>