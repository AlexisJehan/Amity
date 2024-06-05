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
	 * Service de base de données
	 *
	 * Le service de base de données fourni une interface permettant d'exécuter des requêtes SQL vers un serveur dédié en se basant sur l'extension « PDO ».
	 * L'avantage majeur est que son utilisation reste la même qu'importe le serveur de base de données, le service s'adapte en effet lui même et votre code reste compatible lors d'une migration par exemple.
	 * De plus étant basé sur « PDO », certaines fonctionnalités non présentes pour les extensions comme « mysql » et « mysqli » ont été implémentées comme la sécurité ou la simulation des requêtes préparées.
	 *
	 * @package    framework
	 * @subpackage classes/services/database
	 * @version    01/07/2020
	 * @since      16/12/2014
	 */
	abstract class DatabaseService implements IService {
		/*
		 * CHANGELOG:
		 * 01/07/2020: Ajout de la personnalisation d'options à la connexion à la base de données
		 * 06/06/2015: - Ajout de l'encoding personnalisé
		 *             - Support des placeholders marqués par "?"
		 *             - Ajout de la configuration de l'auto-commit
		 * 16/12/2014: Version initiale
		 */

		/**
		 * Paramètre de type « NULL » [identique à PDO::PARAM_NULL]
		 */
		const PARAM_NULL = 0;

		/**
		 * Paramètre de type entier [identique à PDO::PARAM_INT]
		 */
		const PARAM_INT = 1;

		/**
		 * Paramètre de type chaîne de caractères [identique à PDO::PARAM_STR]
		 */
		const PARAM_STR = 2;

		/**
		 * Paramètre de type booléen [identique à PDO::PARAM_BOOL]
		 */
		const PARAM_BOOL = 5;

		/**
		 * Récupération par noms de colonnes [identique à PDO::FETCH_ASSOC]
		 */
		const FETCH_ASSOC = 2;

		/**
		 * Récupération par numéros de colonnes [identique à PDO::FETCH_NUM]
		 */
		const FETCH_NUM = 3;

		/**
		 * Récupération par noms et numéros de colonnes [identique à PDO::FETCH_BOTH]
		 */
		const FETCH_BOTH = 4;

		/**
		 * Récupération par objets [identique à PDO::FETCH_OBJ]
		 */
		const FETCH_OBJ = 5;

		/**
		 * Récupération par les variables qui ont comblé la requête préparée [identique à PDO::FETCH_BOUND]
		 */
		const FETCH_BOUND = 6;

		/**
		 * Récupération par une classe [identique à PDO::FETCH_CLASS]
		 */
		const FETCH_CLASS = 8;

		/**
		 * Récupération par une classe existante [identique à PDO::FETCH_INTO]
		 */
		const FETCH_INTO = 9;

		/**
		 * Récupération par les variables d'un objet [identique à PDO::FETCH_LAZY]
		 */
		const FETCH_LAZY = 1;

		/**
		 * Récupération par noms de colonnes, les valeurs multiples sont regroupées [identique à PDO::FETCH_NAMED]
		 */
		const FETCH_NAMED = 11;

		/**
		 * Hôte de la base de données
		 *
		 * @var string
		 */
		private $host;

		/**
		 * Port de connexion à la base de données
		 *
		 * @var string
		 */
		private $port;

		/**
		 * Nom de la base de données
		 *
		 * @var string
		 */
		private $database;

		/**
		 * Nom d'utilisateur de la base de données
		 *
		 * @var string
		 */
		private $user;

		/**
		 * Mot de passe de la base de données
		 *
		 * @var string
		 */
		private $password;

		/**
		 * Encodage de connexion à la base de données
		 *
		 * @var string
		 */
		private $encoding;

		/**
		 * Options de connexion à la base de données
		 *
		 * @var array
		 */
		private $options;

		/**
		 * Indique si la connexion a été effectuée avec succès à la base de données
		 *
		 * @var boolean
		 */
		private $isConnected = FALSE;

		/**
		 * Compte le nombre de déclarations effectuées
		 *
		 * @var string
		 */
		private $statementCount = 0;

		/**
		 * Variable contenant la connexion spécifique à la base de données
		 *
		 * @var mixed
		 */
		protected $connection;

		/**
		 * Constructeur du service de base de données
		 *
		 * @param string $host     Le nom de l'hôte
		 * @param string $port     Le port de connexion
		 * @param string $database Le nom de la base de données
		 * @param string $user     Le nom de l'utilisateur
		 * @param string $password Le mot de passe
		 * @param string $encoding L'encodage de connexion [« utf8 » par défaut]
		 * @param array  $options  Les options de connexion [vide par défaut]
		 */
		public function __construct($host, $port, $database, $user, $password, $encoding = 'utf8', array $options = array()) {
			$this->host = $host;
			$this->port = $port;
			$this->database = $database;
			$this->user = $user;
			$this->password = $password;
			$this->encoding = $encoding;
			$this->options = $options;
		}

		/**
		 * Méthode générale de connexion à la base de données
		 *
		 * @return DatabaseService L'instance courante
		 */
		public final function connect() {
			$this->isConnected = $this->__connect($this->host, $this->port, $this->database, $this->user, $this->password, $this->encoding, $this->options);
			return $this;
		}

		/**
		 * Méthode spécifique de connexion à la base de données
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
		protected abstract function __connect($host, $port, $database, $user, $password, $encoding, array $options);

		/**
		 * Méthode générale de déconnexion de la base de données
		 */
		public final function disconnect() {
			$this->isConnected = $this->__disconnect();
		}

		/**
		 * Méthode spécifique de déconnexion de la base de données
		 *
		 * @return boolean Vrai si la déconnexion a été effectuée
		 */
		protected abstract function __disconnect();

		/**
		 * Ajout d'une requête SQL à exécuter
		 *
		 * @param  string          $query La requête SQL à exécuter
		 * @return DatabaseService        L'instance courante
		 */
		public abstract function query($query);

		/**
		 * Association d'une variable à la requête
		 *
		 * @param  string          $key   L'identifiant de la variable dans la requête
		 * @param  string          $value La valeur à associer
		 * @param  integer         $type  Le drapeau du type de la variable [vide par défaut]
		 * @return DatabaseService        L'instance courante
		 */
		public abstract function bind($key, $value, $type = NULL);

		/**
		 * Association d'un tableau de variables à la requête
		 *
		 * @param  array           $array Le tableau de variables à associer
		 * @return DatabaseService        L'instance courante
		 */
		public final function bindArray(array $array) {

			// Tableau associatif
			if ($array !== array_values($array)) {
				foreach ($array as $key => $value) {
					$this->bind($key, $value);
				}

			// Tableau non associatif (Forçage du binding avec points d'interrogation)
			} else {
				foreach ($array as $key => $value) {
					$this->bind($key + 1, $value);
				}
			}

			return $this;
		}

		/**
		 * Méthode générale d'exécution de la requête précédemment rentrée et éventuellement associée avec des variables
		 *
		 * @param  array           $binding Un tableau de variables à éventuellement associer [vide par défaut]
		 * @return DatabaseService          L'instance courante
		 */
		public final function execute(array $binding = NULL) {

			// Si une association est renseignée on la fait avant d'exécuter la requête
			if (is_array($binding)) {
				$this->bindArray($binding);
			}

			$this->__execute();
			++$this->statementCount;
			return $this;
		}

		/**
		 * Méthode spécifique d'exécution de la requête précédemment rentrée
		 */
		protected abstract function __execute();

		/**
		 * Récupération d'une seule ligne de résultat après exécution
		 *
		 * @param  integer $fetch La méthode de récupération [« DatabaseService::FETCH_ASSOC » par défaut]
		 * @return array          La ligne de résultat
		 */
		public abstract function row($fetch = self::FETCH_ASSOC);

		/**
		 * Récupération de plusieurs lignes de résultats après exécution
		 *
		 * @param  integer $fetch La méthode de récupération [« DatabaseService::FETCH_ASSOC » par défaut]
		 * @return array          Les lignes de résultats
		 */
		public abstract function rows($fetch = self::FETCH_ASSOC);

		/**
		 * Récupération d'une seule cellule d'une colonne de résultat après exécution
		 *
		 * @param  integer $number Le numéro de la colonne
		 * @return mixed           La cellule
		 */
		public abstract function column($number = 0);

		/**
		 * Récupération de plusieurs cellules des colonnes de résultats après exécution
		 *
		 * @param  integer $number Le numéro de la colonne
		 * @return array           Les cellules
		 */
		public abstract function columns($number = 0);

		/**
		 * Indique le nombre de lignes retournées ou altérées par la dernière requête
		 *
		 * @return integer Le nombre de lignes retournées ou altérées
		 */
		public abstract function count();

		/**
		 * Paramètrage de la validation automatique d'une transaction
		 *
		 * @param  boolean         $enabled Activation si vrai [« TRUE » par défaut]
		 * @return DatabaseService          L'instance courante
		 */
		public abstract function autoCommit($enabled = TRUE);

		/**
		 * Lancement d'une transaction
		 *
		 * @return DatabaseService L'instance courante
		 */
		public abstract function beginTransaction();

		/**
		 * Validation d'une transaction
		 *
		 * @return DatabaseService L'instance courante
		 */
		public abstract function commit();

		/**
		 * Annulation d'une transaction
		 *
		 * @return DatabaseService L'instance courante
		 */
		public abstract function rollback();

		/**
		 * Indique si le service est connecté au serveur
		 *
		 * @return boolean Vrai si le service est connecté
		 */
		public function isConnected() {
			return $this->isConnected;
		}

		/**
		 * Indique le nombre de déclarations effectuées
		 *
		 * @return integer Le nombre de déclarations effectuées
		 */
		public function getStatementCount() {
			return $this->statementCount;
		}

		/**
		 * Méthode indiquant le nom spécifique du service utilisé pour accéder à la base de données
		 *
		 * @return string Le nom spécifique du service utilisé
		 */
		public abstract function getAccessName();
	}
?>