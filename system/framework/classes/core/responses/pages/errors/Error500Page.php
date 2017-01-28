<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Page d'erreur 500
	 * 
	 * L'erreur 500 se produit lorsque le serveur rencontre une erreur interne, se produit quand la base de données n'est pas accessible avec Amity.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/pages/errors
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/07/2015
	 * @since      20/03/2015
	 */
	class Error500Page extends ErrorPage {
		/*
		 * CHANGELOG:
		 * 05/07/2015: Meilleure implémentation
		 * 20/03/2015: Version initiale
		 */

		/**
		 * {@inheritdoc}
		 */
		public function indexAction() {

			// Si l'erreur a été spécifiée dans l'URL on redirige vers l'accueil
			if(FALSE !== strpos($_SERVER['REQUEST_URI'], 'error500')) {
				$this->redirect('home');

			// Sinon on affiche l'erreur
			} else {

				// Code d'erreur
				$this->setErrorCode(500, TRUE);

				// Message d'erreur
				$this->setErrorMessage('The server encountered an error or the database is not accessible.');
			}
		}
	}
?>