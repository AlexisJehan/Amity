<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Classe de chargement des templates
	 * 
	 * Permet de charger les templates de l'application dans les dossiers dédiés.
	 * 
	 * @package    framework
	 * @subpackage classes/loaders
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    18/03/2016
	 * @since      12/01/2015
	 */
	final class TemplateLoader extends AbstractLoader {
		/*
		 * CHANGELOG:
		 * 18/03/2016: Changement de l'extension de « tpl.php » en « phtml »
		 * 29/07/2015: Compatibilité avec la nouvelle implémentation de « AbstractLoader »
		 * 27/03/2015: Changement de l'extension de « php » en « tpl.php »
		 * 12/01/2015: Version initiale
		 */

		/**
		 * {@inheritdoc}
		 *
		 * @var string
		 */
		protected static $extension = '.phtml';


		/**
		 * {@inheritdoc}
		 * 
		 * @return string Le nom du fichier de cache
		 */
		protected function getCacheName() {
			return 'templates';
		}
	}
?>