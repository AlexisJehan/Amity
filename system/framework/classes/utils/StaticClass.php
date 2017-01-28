<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Classe statique
	 * 
	 * Une classe statique ne peut pas être instanciée, sérialisée ou clonée. « StaticClass » assure toutes ces propriétés et doit donc être étendue par les classes dites statiques.
	 * Contrairement à Java, en PHP 5 il n'est pas possible de créer une classe statique avec le mot clef associé, cette présente classe comble ainsi ce manque.
	 * 
	 * @package    framework
	 * @subpackage classes/utils
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    27/07/2015
	 * @since      27/07/2015
	 */
	abstract class StaticClass {
		/*
		 * CHANGELOG:
		 * 27/07/2015: Version initiale
		 */

		/**
		 * Le constructeur n'est pas disponible
		 */
		private final function __construct() {
			
		}

		/**
		 * Le clonage n'est pas disponible
		 */
		private final function __clone() {
			
		}

		/**
		 * La désérialisation n'est pas disponible
		 */
		private final function __wakeup() {
			
		}
	}
?>