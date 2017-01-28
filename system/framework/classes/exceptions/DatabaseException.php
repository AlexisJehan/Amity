<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Exception de base de données
	 * 
	 * Exception personnalisée du système pouvant être provoquée avec les services d'accès à la base de données.
	 * 
	 * @package    framework
	 * @subpackage classes/exceptions
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    31/07/2015
	 * @since      05/10/2014
	 */
	class DatabaseException extends ServiceException {
		/*
		 * CHANGELOG:
		 * 31/07/2015: Suppression de la journalisation désormais gérée par le débogueur
		 * 22/06/2015: Correction d'un bug se produisant avec les PDOException et les codes non numériques
		 * 08/06/2015: Changement du fonctionnement des exceptions personnalisées avec une méthode « log() » isolée
		 * 05/10/2014: Version initiale
		 */

		/**
		 * Constructeur de l'exception de base de données
		 *
		 * @param string $message Le message descrivant l'exception de base de données
		 * @param string $code    Le code d'erreur [« 0 » par défaut]
		 */
		public function __construct($message, $code = 0) {

			// Écrasement des valeurs des attributs de la classe « Exception » parent
			$this->message = $message;
			$this->code = $code;
		}
	}
?>