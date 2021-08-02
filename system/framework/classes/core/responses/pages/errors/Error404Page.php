<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Page d'erreur 404
	 * 
	 * L'erreur 404 se produit lorsqu'un utilisateur tente d'accès à un emplacement qui n'existe pas ou plus.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/pages/errors
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/07/2015
	 * @since      20/03/2015
	 */
	class Error404Page extends ErrorPage {
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
			if (FALSE !== strpos($_SERVER['REQUEST_URI'], 'error404')) {
				$this->redirect('home');

			// Sinon on affiche l'erreur
			} else {

				// Code d'erreur
				$this->setErrorCode(404, TRUE);

				// Message d'erreur
				$this->setErrorMessage('The page you requested was not found.');
			}
		}
	}
?>