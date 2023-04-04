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
			if ($this->isSpecific()) {
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