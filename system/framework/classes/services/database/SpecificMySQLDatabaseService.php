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
	 * Service spécifique de base de données utilisant l'extension « mysql »
	 *
	 * Ce service permet de se connecter à une base de données en utilisant « mysql ».
	 * L'adaptation à l'utilisation à la manière de « PDO » n'est pas disponible d'où la spécificité. L'utilisation de ce service est donc limitée mais plus stable.
	 *
	 * @package    framework
	 * @subpackage classes/services/database
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/06/2015
	 * @since      05/06/2015
	 */
	final class SpecificMySQLDatabaseService extends MySQLDatabaseService {
		/*
		 * CHANGELOG:
		 * 05/06/2015: Version initiale
		 */

		/**
		 * {@inheritdoc}
		 *
		 * @param  string          $query La requête SQL à exécuter
		 * @return DatabaseService        L'instance courante
		 */
		public function query($query) {
			$this->query = $query;
			$this->result = NULL;
			return $this;
		}

		/**
		 * Non disponible nativement dans « mysql » et de ce fait ne fait rien dans ce service spécifique
		 *
		 * @param  string          $key   L'identifiant de la variable dans la requête
		 * @param  string          $value La valeur à associer
		 * @param  integer         $type  Le drapeau du type de la variable [vide par défaut]
		 * @return DatabaseService        L'instance courante
		 */
		public function bind($key, $value, $type = NULL) {
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function __execute() {

			// Exécution de la requête
			$this->result = mysql_query($this->query, $this->connection);
			if (!$this->result) {
				$this->throwException();
			}

			// Si le résultat vaut « TRUE », ce n'était pas un « SELECT » donc on retourne le nombre de lignes affectées
			$this->count = TRUE === $this->result ? mysql_affected_rows($this->connection) : mysql_num_rows($this->result);
			$this->free = FALSE;
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return string Le nom spécifique du service utilisé
		 */
		public function getAccessName() {
			return 'Specific MySQL';
		}
	}

	// On vérifie que l'extension est disponible
	if (!extension_loaded('mysql')) {
		throw new SystemException('"%s" extension is not available', 'mysql');
	}
?>