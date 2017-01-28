<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Fragment du contenu de la page principale
	 * 
	 * Ce fragment permet de définir le contenu de la page par défaut. Nous utilisons un fragment afin de pouvoir le décorer et ainsi le traduire selon la langue utilisateur.
	 * 
	 * @package    application
	 * @subpackage classes
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/08/2015
	 * @since      05/08/2015
	 */
	class HomeFragment extends Fragment {

		/**
		 * Retourne le nom de template utilisé par le fragment
		 *
		 * @return string Le nom du template
		 */
		public function getTemplateName() {
			return 'homeFragment';
		}

		/**
		 * Nous n'utilisons pas de cache, on se contente donc de redéfinir une méthode vide
		 */
		public function getCacheName() {
			
		}

		/**
		 * Nous n'utilisons pas de cache, on se contente donc de redéfinir une méthode vide
		 */
		public function getCacheDuration() {
			
		}
	}
?>