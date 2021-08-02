<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Contrôleur de la page d'accueil de l'application
	 * 
	 * La page d'accueil du site est aussi la page par défaut, elle est entièrement personnalisable. Elle ne doit pas être retirée.
	 * 
	 * @package    application
	 * @subpackage classes
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/08/2015
	 * @since      01/08/2014
	 */
	final class HomePage extends Page {

		/**
		 * Action principale (Le contenu doit être personnalisé)
		 */
		public function indexAction() {

			// Le titre de la page, il sera traduit si le service multi-lingue est activé et que la langue est disponible
			$this->setTitle(__('Home'));

			// Instanciation du fragment, que l'on traduit selon la disponibilité de la langue de l'utilisateur
			$fragment = new TranslatedFragment(new HomeFragment());

			// Contenu, composé du rendu du fragment
			$this->setContent($fragment->get());
		}
	}
?>