<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Téléchargement limité d'un fichier
	 * 
	 * Cette réponse encapsule le téléchargement d'un fichier, en y ajoutant une limite de vitesse de téléchargement.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/downloads
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
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
		 * {@inheritdoc}, on affiche le contenu du fichier de façon ralentie
		 */
		protected function sendDownload() {
			flush();
			if($handle = @fopen($this->file, 'r')) {
				while(!feof($handle)) {

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