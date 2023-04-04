<?php
	/*
	 * MIT License
	 *
	 * Copyright (c) 2017-2023 Alexis Jehan
	 *
	 * Permission is hereby granted, free of charge, to any person obtaining a copy
	 * of this software and associated documentation files (the "Software"), to deal
	 * in the Software without restriction, including without limitation the rights
	 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	 * copies of the Software, and to permit persons to whom the Software is
	 * furnished to do so, subject to the following conditions:
	 *
	 * The above copyright notice and this permission notice shall be included in all
	 * copies or substantial portions of the Software.
	 *
	 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	 * SOFTWARE.
	 */

	/**
	 * Téléchargement d'un fichier
	 *
	 * Cette réponse encapsule le téléchargement d'un fichier.
	 *
	 * @package    framework
	 * @subpackage classes/core/responses/downloads
	 * @version    13/09/2015
	 * @since      26/06/2015
	 */
	abstract class FileDownload extends Download {
		/*
		 * CHANGELOG:
		 * 13/09/2015: Correction de l'oubli d'un point d'exclamation devant le « is_readable() » de la méthode « setFile() »
		 * 26/06/2015: Version initiale
		 */

		/**
		 * Emplacement du fichier à télécharger
		 *
		 * @var string
		 */
		protected $file;

		/**
		 * Vérifie si on envoi un fichier à télécharger ou un contenu de type parent
		 *
		 * @return boolean Vrai si on envoi un fichier à télécharger
		 */
		protected function isSpecific() {
			return !empty($this->file);
		}

		/**
		 * Retourne la taille du fichier à télécharger
		 *
		 * @return integer La taille du fichier à télécharger
		 */
		protected function getDownloadSize() {
			return filesize($this->file);
		}

		/**
		 * On affiche le contenu du fichier
		 */
		protected function sendDownload() {
			readfile($this->file);
		}

		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// Si c'est une réponse spécifique et si le nom du fichier est vide alors on met le même nom
			if ($this->isSpecific() && empty($this->filename)) {
				$this->filename = basename($this->file);
			}

			parent::send();
		}

		/**
		 * Retourne l'emplacement du fichier à télécharger
		 *
		 * @return string L'emplacement du fichier à télécharger
		 */
		public function getFile() {
			return $this->file;
		}

		/**
		 * Modifie l'emplacement du fichier à télécharger
		 *
		 * @param  string       $file Le nouvel emplacement du fichier à télécharger
		 * @return FileDownload       L'instance courante
		 */
		public function setFile($file) {

			// On vérifie si le fichier existe
			if (!is_file($file) || !is_readable($file)) {
				throw new InvalidParameterException('"%s" is not a valid file or it cannot be read', path($file));
			}

			$this->file = $file;
			return $this;
		}
	}
?>