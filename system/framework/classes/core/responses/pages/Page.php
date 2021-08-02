<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Page affichable du site
	 * 
	 * Une page est un rendu qui utilise le template principal du site pour encadrer son contenu.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/pages
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    11/11/2015
	 * @since      25/07/2014
	 */
	abstract class Page extends Response {
		/*
		 * CHANGELOG:
		 * 11/11/2015: Possibilité de personnaliser le nom du template de rendu à utiliser
		 * 26/06/2015: Meilleure implémentation
		 * 23/05/2015: Ajout de l'URL dans le template du squelette
		 * 05/05/2015: La classe Page étend désormais de la classe Document
		 * 05/05/2015: Les arguments d'URL ont été ajouté en tant qu'attribut, et implémentation de Content
		 * 21/02/2015: Ajout de la fonction de chargement depuis un template
		 * 17/10/2014: Ajout d'une méthode renseignant la liste des actions de la page
		 * 25/07/2014: Version initiale
		 */

		/**
		 * Titre de la page
		 * 
		 * @var string
		 */
		protected $title;

		/**
		 * Nom du template de rendu [« main » par défaut]
		 * 
		 * @var string
		 */
		protected $templateName = 'main';

		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// On génère simplement un rendu avec le titre, le contenu et le nom du template à utiliser
			$this->render($this->title, $this->content, $this->templateName);

			parent::send();
		}

		/**
		 * Retourne le titre de la page
		 * 
		 * @return string Le titre de la page
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Modifie le titre de la page
		 *
		 * @param  string $title Le nouveau titre de la page
		 * @return Page          L'instance courante
		 */
		public function setTitle($title) {
			$this->title = $title;
			return $this;
		}

		/**
		 * Retourne le nom du template de rendu
		 * 
		 * @return string Le nom du template de rendu
		 */
		public function getTemplateName() {
			return $this->templateName;
		}

		/**
		 * Modifie le nom du template de rendu
		 *
		 * @param  string $templateName Le nouveau nom du template de rendu
		 * @return Page                 L'instance courante
		 */
		public function setTemplateName($templateName) {
			$this->templateName = $templateName;
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public static function getTypeName() {
			return get_class();
		}
	}
?>