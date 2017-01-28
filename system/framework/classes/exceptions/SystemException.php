<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Exception du système
	 * 
	 * Exception personnalisée pouvant être provoquée par le système du framework.
	 * 
	 * @package    framework
	 * @subpackage classes/exceptions
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    31/07/2015
	 * @since      04/10/2014
	 */
	class SystemException extends Exception {
		/*
		 * CHANGELOG:
		 * 31/07/2015: Suppression de la journalisation désormais gérée par le débogueur
		 * 20/07/2015: Changement du fonctionnement de l'exception du système, avec gestion interne de la traduction et du binding
		 * 02/07/2015: Ajout du nom de l'exception
		 * 08/06/2015: Changement du fonctionnement des exceptions personnalisées avec une méthode « log() » isolée
		 * 04/10/2014: Version initiale
		 */

		/**
		 * Constructeur de l'exception système
		 *
		 * @param string $message Le message descrivant l'exception, peut être complété par d'autres arguments à la manière de « sprintf() »
		 */
		public function __construct($message) {

			// Tentative de traduction du message
			$message = call_user_func_array('__', func_get_args());

			// Appel au constructeur parent, avec le message éventuellement modifié
			parent::__construct($message);
		}
	}
?>