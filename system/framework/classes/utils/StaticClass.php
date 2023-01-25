<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Classe statique
	 * 
	 * Une classe statique ne peut pas être instanciée, « StaticClass » assure cela et doit donc être étendue par les classes dites statiques.
	 * Contrairement à Java, en PHP il n'est pas possible de créer une classe statique avec le mot clef associé, cette présente classe comble ainsi ce manque.
	 * 
	 * @package    framework
	 * @subpackage classes/utils
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    25/01/2023
	 * @since      27/07/2015
	 */
	abstract class StaticClass {
		/*
		 * CHANGELOG:
	 	 * 25/01/2023: Compatibilité avec PHP 8.0, « __clone() » et « __wakeup() » ne sont plus surchargés
		 * 27/07/2015: Version initiale
		 */

		/**
		 * Le constructeur n'est pas disponible
		 */
		private final function __construct() {
			// Vide
		}
	}
?>