<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Page d'erreur 503
	 * 
	 * L'erreur 503 se produit lorsque le serveur n'est pas disponible, se produit quand le site est en maintenance avec Amity.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/pages/errors
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/07/2015
	 * @since      20/03/2015
	 */
	class Error503Page extends ErrorPage {
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
			if(FALSE !== strpos($_SERVER['REQUEST_URI'], 'error503')) {
				$this->redirect('home');

			// Sinon on affiche l'erreur
			} else {

				// Code d'erreur
				$this->setErrorCode(503, TRUE);

				// Message d'erreur
				$this->setErrorMessage('The website is currently unavailable due to maintenance works, please try again later.');
			}
		}
	}
?>