<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Fragment de rendu
	 * 
	 * Un fragment est un objet de l'application qui encapsule un template pour produire un rendu.
	 * Il peut être utilisé par exemple plusieurs fois par page ou sur plusieurs pages à la fois.
	 * 
	 * @package    framework
	 * @subpackage classes/fragments
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    29/12/2015
	 * @since      16/12/2014
	 */
	abstract class Fragment {
		/*
		 * CHANGELOG:
		 * 29/12/2015: Ajout des méthodes d'échappement de la classe « Template »
		 * 05/08/2015: Ajout des méthodes de cache, et suppression de l'interface « ICachedFragment »
		 * 16/12/2014: Version initiale
		 */

		/**
		 * Tableau d'association de valeurs au fragment
		 * 
		 * @var array
		 */
		protected $binding = array();

		/**
		 * Association d'une clé à une valeur
		 *
		 * @param  string   $key   La clé
		 * @param  mixed    $value La valeur associée
		 * @return Fragment        L'instance courante
		 */
		protected final function bind($key, $value) {
			$this->binding[$key] = $value;
			return $this;
		}

		/**
		 * Association de plusieurs clés à leurs valeurs respectives
		 *
		 * @param  array    $binding Tableau associant des clés à leurs valeurs
		 * @return Fragment          L'instance courante
		 */
		protected final function bindArray(array $binding) {
			foreach ($binding as $key => $value) {
				$this->binding[$key] = $value;
			}
			return $this;
		}

		/**
		 * Échappe les caractères HTML d'une variable
		 *
		 * @param  string $variable La variable à échapper
		 * @return string           La variable échappée
		 */
		public function escape($variable) {
			return htmlspecialchars($variable, ENT_COMPAT, 'UTF-8', FALSE);
		}

		/**
		 * Dé-échappement les caractères HTML d'une variable
		 *
		 * @param  string $variable La variable à dé-échapper
		 * @return string           La variable dé-échappée
		 */
		public function unescape($variable) {
			return htmlspecialchars_decode($variable, ENT_COMPAT);
		}

		/**
		 * Génération du contenu du fragment depuis le template utilisé avec les éventuelles associations
		 *
		 * @return string Le contenu généré
		 */
		protected final function render() {
			return Template::load(
				$this->getTemplateName(),
				$this->getBinding()
			);
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
		 * Retourne l'ensemble des associations
		 *
		 * @return array Les associations
		 */
		public function getBinding() {
			return $this->binding;
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
		 * @return string Le nom du fichier de cache
		 */
		public abstract function getCacheName();

		/**
		 * Retourne la durée de mise en cache
		 * 
		 * @return integer La durée de mise en cache (en secondes)
		 */
		public abstract function getCacheDuration();
	}
?>