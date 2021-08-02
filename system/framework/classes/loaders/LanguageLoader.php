<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Classe de chargement des fichiers de traduction
	 * 
	 * Permet de charger les fichiers de traduction dans les dossiers dédiés.
	 * 
	 * @package    framework
	 * @subpackage classes/loaders
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    29/07/2015
	 * @since      17/02/2015
	 */
	final class LanguageLoader extends AbstractLoader {
		/*
		 * CHANGELOG:
		 * 29/07/2015: Compatibilité avec la nouvelle implémentation de « AbstractLoader »
		 * 17/02/2015: Version initiale
		 */

		/**
		 * {@inheritdoc}
		 *
		 * @var string
		 */
		protected static $extension = '.lang.php';

		/**
		 * {@inheritdoc}
		 * 
		 * @return string Le nom du fichier de cache
		 */
		protected function getCacheName() {
			return 'languages';
		}
	}
?>