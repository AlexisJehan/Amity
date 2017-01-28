<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Redirection d'emplacement
	 * 
	 * La redirection permet de faire rediriger l'utilisateur vers un autre emplacement interne ou externe au site.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/redirects
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    26/06/2015
	 * @since      18/05/2015
	 */
	abstract class Redirect extends Response {
		/*
		 * CHANGELOG:
		 * 26/06/2015: Meilleure implémentation
		 * 18/05/2015: Version initiale
		 */

		/**
		 * Emplacement de redirection
		 * 
		 * @var string
		 */
		protected $location;

		/**
		 * Redirection permanent si vrai, temporaire sinon [« TRUE » par défaut]
		 * 
		 * @var boolean
		 */
		protected $isPermanent = TRUE;


		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// Si l'emplacement de redirection n'est pas vide, on ajoute les headers correspondants
			if(!empty($this->location)) {
				$this->redirect($this->location, $this->isPermanent ? 301 : 302);
			}

			parent::send();
		}

		/**
		 * Retourne l'emplacement de redirection
		 * 
		 * @return string L'emplacement de redirection
		 */
		public function getLocation() {
			return $this->location;
		}

		/**
		 * Modifie l'emplacement de redirection
		 *
		 * @param  string   $location Le nouvel emplacement de redirection
		 * @return Redirect           L'instance courante
		 */
		public function setLocation($location) {
			$this->location = $location;

			return $this;
		}

		/**
		 * Retourne vrai si la redirection est permanente
		 * 
		 * @return boolean Vrai si la redirection est permanente
		 */
		public function isPermanent() {
			return $this->isPermanent;
		}

		/**
		 * Rend la redirection permanente
		 *
		 * @return Redirect L'instance courante
		 */
		public function setPermanent() {
			$this->isPermanent = TRUE;

			return $this;
		}

		/**
		 * Retourne vrai si la redirection est temporaire
		 * 
		 * @return boolean Vrai si la redirection est temporaire
		 */
		public function isTemporary() {
			return !$this->isPermanent;
		}

		/**
		 * Rend la redirection temporaire
		 *
		 * @return Redirect L'instance courante
		 */
		public function setTemporary() {
			$this->isPermanent = FALSE;

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