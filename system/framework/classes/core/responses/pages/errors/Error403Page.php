<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Page d'erreur 403
	 * 
	 * L'erreur 403 se produit lorsqu'un utilisateur tente d'accès à un emplacement sans l'authorisation requise.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/pages/errors
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/07/2015
	 * @since      20/03/2015
	 */
	class Error403Page extends ErrorPage {
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
			if (FALSE !== strpos($_SERVER['REQUEST_URI'], 'error403')) {
				$this->redirect('home');

			// Sinon on affiche l'erreur
			} else {

				// Code d'erreur
				$this->setErrorCode(403, TRUE);

				// Message d'erreur
				$this->setErrorMessage('You are not allowed to access this page.');
			}
		}
	}
?>