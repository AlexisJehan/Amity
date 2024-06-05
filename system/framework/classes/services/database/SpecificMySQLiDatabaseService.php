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
	 * Service spécifique de base de données utilisant l'extension « mysqli »
	 *
	 * Ce service permet de se connecter à une base de données en utilisant « mysqli ».
	 * L'adaptation à l'utilisation à la manière de « PDO » n'est pas disponible d'où la spécificité. L'utilisation de ce service est donc limitée mais plus stable.
	 *
	 * @package    framework
	 * @subpackage classes/services/database
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
		 */
		public function query($query) {

			// Préparation de la requête
			$this->statement = $this->connection->stmt_init();
			if (!$this->statement->prepare($query)) {
				throw $this->databaseException();
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
		 */
		public function bind($key, $value, $type = NULL) {

			// Si le type n'est pas indiqué on le détermine
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

			// Selon le type on ajoute le caractère dans la chaîne des types des associations
			switch ($type) {
				case self::PARAM_NULL:
					$this->bindingTypes .= 's';
					break;
				case self::PARAM_INT:
					$this->bindingTypes .= 'i';
					break;
				case self::PARAM_STR:
					$this->bindingTypes .= 's';
					break;
				case self::PARAM_BOOL:
					$this->bindingTypes .= 'i';
					break;
				default:
					$this->bindingTypes .= 's';
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
			if (0 < count($this->bindingValues)) {
				$binding = array();
				foreach ($this->bindingValues as $key => $value) {
					$binding[$key] = &$this->bindingValues[$key];
				}
				array_unshift($binding, $this->bindingTypes);
				call_user_func_array(array($this->statement, 'bind_param'), $binding);
			}

			// Exécution de la déclaration
			if ($this->statement->execute()) {
				$this->result = $this->statement->get_result();
				$this->count = $this->connection->affected_rows;
				$this->free = FALSE;
				$this->statement->close();
			} else {
				throw $this->databaseException();
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function count() {
			return $this->count;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getAccessName() {
			return 'Specific MySQLi';
		}
	}

	// On vérifie que l'extension est disponible
	if (!extension_loaded('mysqli')) {
		throw new SystemException('"%s" extension is not available', 'mysqli');
	}
?>