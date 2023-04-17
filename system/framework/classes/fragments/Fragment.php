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
	 * Fragment de rendu
	 *
	 * Un fragment est un objet de l'application qui encapsule un template pour produire un rendu.
	 * Il peut être utilisé par exemple plusieurs fois par page ou sur plusieurs pages à la fois.
	 *
	 * @package    framework
	 * @subpackage classes/fragments
	 * @version    17/04/2023
	 * @since      16/12/2014
	 */
	abstract class Fragment {
		/*
		 * CHANGELOG:
		 * 17/04/2023: Suppression des méthodes « escape() » et « unescape() »
		 * 29/12/2015: Ajout des méthodes d'échappement de la classe « Template »
		 * 05/08/2015: Ajout des méthodes de cache, et suppression de l'interface « ICachedFragment »
		 * 16/12/2014: Version initiale
		 */

		/**
		 * Tableau des variables à associer au fragment
		 *
		 * @var array
		 */
		protected $binding = array();

		/**
		 * Tableau de l'échappement des variables à associer au fragment
		 *
		 * @var array
		 */
		protected $escaping = array();

		/**
		 * Association d'une variable dans le fragment
		 *
		 * @param  string   $name   Le nom de la variable
		 * @param  mixed    $value  La valeur de la variable
		 * @param  boolean  $escape Booléen indiquant si on doit échapper la valeur ou non [« TRUE » par défaut]
		 * @return Fragment         L'instance courante
		 */
		protected final function bind($name, $value, $escape = TRUE) {
			$this->binding[$name] = $value;
			$this->escaping[$name] = $escape;
			return $this;
		}

		/**
		 * Association de plusieurs variables dans fragment
		 *
		 * @param  array    $variables Les variables à associer
		 * @param  boolean  $escapeAll Booléen indiquant si on doit échapper les valeurs ou non [« TRUE » par défaut]
		 * @return Fragment            L'instance courante
		 */
		protected final function bindArray(array $variables, $escapeAll = TRUE) {
			foreach ($variables as $name => $value) {
				$this->bind($name, $value, $escapeAll);
			}
			return $this;
		}

		/**
		 * Génération du contenu du fragment depuis le template utilisé avec les éventuelles associations
		 *
		 * @return string Le contenu généré
		 */
		protected final function render() {
			$template = new Template($this->getTemplateName());
			$binding = $this->getBinding();
			$escaping = $this->getEscaping();
			foreach ($binding as $name => $value) {
				$template->bind($name, $value, $escaping[$name]);
			}
			return $template->render();
		}

		/**
		 * Retourne le contenu du fragment (par défaut le contenu généré)
		 *
		 * @return string Le contenu du fragment
		 */
		public function get() {
			return $this->render();
		}

		/**
		 * Retourne le tableau des variables à associer au fragment
		 *
		 * @return array Le tableau des variables
		 */
		public function getBinding() {
			return $this->binding;
		}

		/**
		 * Retourne le tableau de l'échappement des variables à associer au fragment
		 *
		 * @return array Le tableau de l'échappement des variables
		 */
		public function getEscaping() {
			return $this->escaping;
		}

		/**
		 * Retourne le nom de template utilisé par le fragment
		 *
		 * @return string Le nom du template
		 */
		public abstract function getTemplateName();

		/**
		 * Retourne le nom du fichier de cache
		 *
		 * @return ?string Le nom du fichier de cache
		 */
		public abstract function getCacheName();

		/**
		 * Retourne la durée de mise en cache
		 *
		 * @return ?integer La durée de mise en cache (en secondes)
		 */
		public abstract function getCacheDuration();
	}
?>