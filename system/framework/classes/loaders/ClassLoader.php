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
	 * Classe d'auto-chargement des classes
	 *
	 * Permet de charger de manière automatique les classes et donc de se passer des « include » / « require » manuels. Attention, le framework n'utilise pas le système de « namespace » et n'est donc pas compatible avec.
	 *
	 * @package    framework
	 * @subpackage classes/loaders
	 * @version    29/07/2015
	 * @since      10/08/2014
	 */
	final class ClassLoader extends AbstractLoader {
		/*
		 * CHANGELOG:
		 * 29/07/2015: Compatibilité avec la nouvelle implémentation de « AbstractLoader »
		 * 12/01/2015: Correction d'une erreur se produisant si le cache est utilisé alors que le projet a changé d'emplacement, et utilisation d'une classe mère partagée avec TemplateLoader
		 * 16/10/2014: Changements majeurs dans le fonctionnement de la classe (Changement de l'ajout de chemins, arborescence dynamique, sauvegarde...)
		 * 12/09/2014: Séparation du dossier des interfaces de celui des classes
		 * 10/08/2014: Version initiale
		 */

		/**
		 * Enregistre de la fonction à appeler lors de l'auto-chargement
		 *
		 * @param  string      $register Enregistre si vrai, retire sinon [« TRUE » par défaut]
		 * @return ClassLoader           L'instance courante
		 */
		public function register($register = TRUE) {

			// Si vrai, on enregistre
			if ($register) {
				spl_autoload_register(array($this, 'loadClass'), TRUE, TRUE);

			// Sinon on retire
			} else {
				spl_autoload_unregister(array($this, 'loadClass'));
			}

			return $this;
		}

		/**
		 * Méthode pour charger une classe ou une interface, elle est appelée automatiquement par SPL
		 *
		 * @param string $name Le nom de la classe ou de l'interface dont nous recherchons le fichier
		 */
		public function loadClass($name) {

			// Tentative de récupération du fichier qui correspond au nom de classe
			if ($file = $this->getFile($name)) {
				require ($file);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		protected function getCacheName() {
			return 'classes';
		}
	}
?>