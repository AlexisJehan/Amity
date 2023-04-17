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
	 * Téléchargement limité d'un fichier
	 *
	 * Cette réponse encapsule le téléchargement d'un fichier, en y ajoutant une limite de vitesse de téléchargement.
	 *
	 * @package    framework
	 * @subpackage classes/core/responses/downloads
	 * @version    26/06/2015
	 * @since      26/06/2015
	 */
	abstract class LimitedFileDownload extends FileDownload {
		/*
		 * CHANGELOG:
		 * 26/06/2015: Version initiale
		 */

		/**
		 * Limite de vitesse de téléchargement (en kilo-bytes par secondes), « 50 » par défaut
		 *
		 * @var string
		 */
		protected $rate = 50;

		/**
		 * {@inheritdoc}
		 */
		protected function sendDownload() {
			flush();
			if ($handle = @fopen($this->file, 'r')) {
				while (!feof($handle)) {

					// On affiche seulement un certain nombre d'octets
					echo fread($handle, round($this->rate * 1024));

					flush();

					// Le système fait une pause d'une seconde
					sleep(1);
				}
				fclose($handle);
			}
		}

		/**
		 * Retourne la limite de vitesse de téléchargement
		 *
		 * @return integer La limite de vitesse de téléchargement
		 */
		public function getRate() {
			return $this->rate;
		}

		/**
		 * Modifie la limite de vitesse de téléchargement
		 *
		 * @param  integer  $rate      La nouvelle limite de vitesse de téléchargement
		 * @return LimitedFileDownload L'instance courante
		 */
		public function setRate($rate) {
			$this->rate = (int) $rate;
			return $this;
		}
	}
?>