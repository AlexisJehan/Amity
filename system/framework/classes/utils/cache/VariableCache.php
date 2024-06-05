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
	 * Classe de mise en cache d'une variable
	 *
	 * Cette classe permet de sauvegarder une variable dans un fichier et la récupérer ultérieurement. Il est possible de spécifier une durée de validité pour regénérer la variable périodiquement.
	 *
	 * @package    framework
	 * @subpackage classes/utils/cache
	 * @version    18/03/2016
	 * @since      22/07/2015
	 */
	final class VariableCache extends Cache {
		/*
		 * CHANGELOG:
		 * 18/03/2016: Utilisation de « file_put_contents() » pour améliorer les performances
		 * 02/09/2015: Changement global du fonctionnement du cache
		 * 28/08/2015: Gestion des sous-dossiers
		 * 20/08/2015: Utilisation des verrous pour réduire les conflits qui peuvent se produire avec la concurrence
		 * 22/07/2015: Version initiale
		 */

		/**
		 * Extension des fichiers de cache de variable
		 *
		 * @var string
		 */
		protected static $extension = '.var.cache';

		/**
		 * Tentative de récupération de la variable depuis le cache
		 *
		 * @return mixed La variable si le cache existe et est frais, « NULL » sinon
		 */
		public function fetch() {

			// Si le fichier de cache existe, on récupère la variable
			if ($this->exists()) {
				return @require ($this->file);
			}
		}

		/**
		 * Mise en cache de la variable
		 *
		 * @param  mixed $value La variable à mettre en cache
		 * @return mixed        La variable mise en cache
		 */
		public function store($value) {

			// Écriture de la variable exportée dans le fichier de cache
			file_put_contents($this->file, '<?php return ' . var_export($value, TRUE) . '; ?>', LOCK_EX);

			// On retourne la variable stockée
			return $value;
		}

		/**
		 * Méthode statique qui supprime le cache qui correspond à un nom
		 *
		 * @param mixed $filename Le nom du fichier de cache
		 */
		public static final function delete($filename) {
			$cache = new self($filename);
			$cache->drop();
		}
	}
?>