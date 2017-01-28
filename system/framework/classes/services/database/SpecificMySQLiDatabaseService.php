<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Service spécifique de base de données utilisant l'extension « mysqli »
	 * 
	 * Ce service permet de se connecter à une base de données en utilisant « mysqli ».
	 * L'adaptation à l'utilisation à la manière de « PDO » n'est pas disponible d'où la spécificité. L'utilisation de ce service est donc limitée mais plus stable.
	 * 
	 * @package    framework
	 * @subpackage classes/services/database
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    06/06/2015
	 * @since      06/06/2015
	 */
	final class SpecificMySQLiDatabaseService extends MySQLiDatabaseService {
		/*
		 * CHANGELOG:
		 * 06/06/2015: Version initiale
		 */

		/**
		 * Déclaration de la requête préparée
		 * 
		 * @var mysqli_stmt
		 */
		protected $statement;

		/**
		 * Chaîne des types des associations
		 * 
		 * @var string
		 */
		protected $bindingTypes;

		/**
		 * Valeurs des associations
		 * 
		 * @var array
		 */
		protected $bindingValues;

		/**
		 * Nombre de lignes retournées ou altérées par la dernière requête
		 * 
		 * @var integer
		 */
		protected $count;


		/**
		 * {@inheritdoc}
		 *
		 * @param  string          $query La requête SQL à exécuter
		 * @return DatabaseService        L'instance courante
		 */
		public function query($query) {

			// Préparation de la requête
			$this->statement = $this->connection->stmt_init();
			if(!$this->statement->prepare($query)) {
				$this->throwException();
			}

			// Réinitialisation des attributs
			$this->bindingTypes = '';
			$this->bindingValues = array();
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

			// Si le type n'est pas indiqué on le détermine
			if(NULL === $type) {
				switch(TRUE) {
					case is_int ($value): $type = self::PARAM_INT;  break;
					case is_bool($value): $type = self::PARAM_BOOL; break;
					case NULL === $value: $type = self::PARAM_NULL; break;
					default:              $type = self::PARAM_STR;
				}
			}

			// Selon le type on ajoute le caractère dans la chaîne des types des associations
			switch($type) {
				case self::PARAM_NULL: $this->bindingTypes .= 's'; break;
				case self::PARAM_INT:  $this->bindingTypes .= 'i'; break;
				case self::PARAM_STR:  $this->bindingTypes .= 's'; break;
				case self::PARAM_BOOL: $this->bindingTypes .= 'i'; break;
				default:               $this->bindingTypes .= 's';
			}

			// Puis on ajoute la valeur à associer
			$this->bindingValues[] = $value;

			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function __execute() {

			// Si on doit associer des valeurs par marquage
			if(0 < count($this->bindingValues)) {
				$binding = array();
				if(version_compare(PHP_VERSION, '5.3', '>=')) {
					foreach($this->bindingValues as $key => $value) {
						$binding[$key] = &$this->bindingValues[$key];
					}
				}
				array_unshift($binding, $this->bindingTypes);
				call_user_func_array(array($this->statement, 'bind_param'), $binding);
			}

			// Exécution de la déclaration
			if($this->statement->execute()) {
				$this->result = $this->statement->get_result();
				$this->count = $this->connection->affected_rows;
				$this->free = FALSE;
				$this->statement->close();
			} else {
				$this->throwException();
			}
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return integer Le nombre de lignes retournées ou altérées
		 */
		public function count() {
			return $this->count;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return string Le nom spécifique du service utilisé
		 */
		public function getAccessName() {
			return 'Specific MySQLi';
		}
	}
?>