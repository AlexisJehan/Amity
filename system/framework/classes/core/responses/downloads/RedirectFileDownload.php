<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Téléchargement d'un fichier avec redirection
	 * 
	 * Cette réponse redirige vers l'emplacement d'un fichier pour le télécharger, plutôt que d'encapsuler le téléchargement.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/downloads
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/07/2015
	 * @since      05/07/2015
	 */
	abstract class RedirectFileDownload extends FileDownload {
		/*
		 * CHANGELOG:
		 * 05/07/2015: Version initiale
		 */

		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// Si c'est une réponse spécifique, on redirige vers le fichier
			if($this->isSpecific()) {
				$this->redirect($this->file, 301);

				$this->sendHeaders();

			} else {
				parent::send();
			}
		}

		/**
		 * Modifie l'URL du fichier à télécharger
		 *
		 * @param  string   $file       La nouvelle URL du fichier à télécharger
		 * @return RedirectFileDownload L'instance courante
		 */
		public function setFile($file) {
			$this->file = $file;

			return $this;
		}
	}
?>