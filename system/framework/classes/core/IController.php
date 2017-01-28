<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Interface d'un contrôleur
	 * 
	 * Un contrôleur peut proposer un ensemble d'actions via des méthodes dont le nom se termine par « Action ».
	 * Parmi ces méthodes, le contrôleur se doit d'implémenter au minimum l'action principale « indexAction() ».
	 * Il peut aussi proposer une méthode de forward vers un contrôleur différent.
	 * 
	 * @package    framework
	 * @subpackage classes/core
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    30/07/2015
	 * @since      05/05/2015
	 */
	interface IController {
		/*
		 * CHANGELOG:
		 * 30/07/2015: Changement de « Controller » à « IController »
		 * 02/07/2015: Ajout du forwarding
		 * 05/05/2015: Version initiale
		 */

		/**
		 * Action principale et par défaut du contrôleur
		 */
		public function indexAction();

		/**
		 * Forwarding vers l'action d'un autre contrôleur
		 * 
		 * @param string $controller Le nom du contrôleur vers lequel s'orienter
		 * @param string $action     Le nom de l'action du nouveau contrôleur
		 */
		public function forward($controller, $action);
	}
?>