<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Téléchargement d'un contenu
	 * 
	 * Cette réponse indique au navigateur d'enregistrer le contenu dans un fichier.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/downloads
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    26/06/2015
	 * @since      26/06/2015
	 */
	abstract class Download extends Response {
		/*
		 * CHANGELOG:
		 * 26/06/2015: Version initiale
		 */

		/**
		 * Contenu à télécharger
		 * 
		 * @var string
		 */
		protected $download;

		/**
		 * Nom du fichier à télécharger
		 * 
		 * @var string
		 */
		protected $filename;

		/**
		 * Vérifie si on envoi un contenu à télécharger ou un contenu de type parent
		 * 
		 * @return boolean Vrai si on envoi un contenu à télécharger
		 */
		protected function isSpecific() {
			return !empty($this->download);
		}

		/**
		 * Retourne la taille du contenu à télécharger
		 * 
		 * @return integer La taille du contenu à télécharger
		 */
		protected function getDownloadSize() {
			return strlen($this->download);
		}

		/**
		 * Envoi du contenu à télécharger
		 */
		protected function sendDownload() {
			echo $this->download;
		}

		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// Si c'est une réponse spécifique, c'est-à-dire un contenu à télécharger
			if ($this->isSpecific()) {

				// On n'interrompt pas le script au bout d'un certain temps
				// Utile si la connexion est lente ou si le contenu est important
				set_time_limit(0);

				// Si l'utilisateur annule le téléchargement, on arrête l'exécution
				ignore_user_abort(FALSE);

				// On désactive la bufferisation du contenu à l'envoi
				ini_set('output_buffering', 0);
				ini_set('zlib.output_compression', 0);

				// Headers spécifiques au téléchargement
				$this->setHeader('Content-Description', 'File Transfer');
				$this->setHeader('Content-Type', 'application/octet-stream');
				$this->setHeader('Content-Disposition', 'attachment; filename="' . $this->filename . '"');
				$this->setHeader('Content-Transfer-Encoding', 'binary');
				$this->setHeader('Content-Length', $this->getDownloadSize());
				$this->setHeader('Connection', 'Keep-Alive');
				$this->setHeader('Pragma', 'public');
				$this->setHeader('Expires', '0');
				$this->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
				$this->setHeader('Cache-Control', 'public');

				// On envoi les headers puis le contenu à télécharger
				$this->sendHeaders();
				$this->sendDownload();

			// Sinon on envoi le contenu par défaut
			} else {
				parent::send();
			}
		}

		/**
		 * Retourne le contenu à télécharger
		 * 
		 * @return string Le contenu à télécharger
		 */
		public function getDownload() {
			return $this->download;
		}

		/**
		 * Modifie le contenu à télécharger
		 *
		 * @param  string   $download Le nouveau contenu à télécharger
		 * @return Download           L'instance courante
		 */
		public function setDownload($download) {
			$this->download = (string) $download;
			return $this;
		}

		/**
		 * Ajoute du contenu à télécharger
		 * 
		 * @param string $download Le contenu à ajouter à télécharger
		 */
		public function addDownload($download) {
			$this->download .= (string) $download;
			return $this;
		}

		/**
		 * Retourne le nom du fichier à télécharger
		 * 
		 * @return string Le nom du fichier à télécharger
		 */
		public function getFilename() {
			return $this->filename;
		}

		/**
		 * Modifie le nom du fichier à télécharger
		 *
		 * @param  string   $filename Le nouveau nom du fichier à télécharger
		 * @return Download           L'instance courante
		 */
		public function setFilename($filename) {
			$this->filename = $filename;
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