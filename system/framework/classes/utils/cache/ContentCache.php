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
	 * Classe de mise en cache d'un contenu
	 *
	 * Le cache permet de conserver un contenu périodiquement et de le recharger plutôt que de le regénérer à chaque fois. Ainsi, les performances sont augmentées.
	 * Ceci est particulièrement efficace lors de la génération de contenus nécessitant des données provenant d'une base de données, notamment quand ils ne nécessitent pas un rafraîchissement permanent.
	 *
	 * @package    framework
	 * @subpackage classes/utils/cache
	 * @version    12/05/2016
	 * @since      08/07/2014
	 */
	final class ContentCache extends Cache {
		/*
		 * CHANGELOG:
		 * 12/05/2016: Amélioration de la configuration via le constructeur
		 * 18/03/2016: Utilisation de « file_get_contents() » et « file_put_contents() » pour améliorer les performances
		 * 02/09/2015: Changement global du fonctionnement du cache
		 * 28/08/2015: Gestion des sous-dossiers
		 * 20/08/2015: Ajout de verrous en écriture sur le fichier de cache pour éviter des conflits de concurrence
		 * 22/07/2015: Amélioration conceptuelle avec la classe « VariableCache » réservée aux variables, et « ContentCache » aux contenus
		 * 08/07/2014: Version initiale
		 */

		/**
		 * Extension des fichiers de cache de contenu
		 *
		 * @var string
		 */
		protected static $extension = '.con.cache';

		/**
		 * Indique si on doit compresser le contenu mis en cache
		 *
		 * @var boolean
		 */
		protected $compress;

		/**
		 * Indique si la bufferisation de la sortie est activée
		 *
		 * @var boolean
		 */
		protected $buffering = FALSE;

		/**
		 * Constructeur du cache de contenu
		 *
		 * @param string  $filename Le nom du fichier de cache
		 * @param string  $duration La durée de conservation du cache, avant rafraîchissement (en secondes) [« -1 » par défaut, jamais rafraîchit]
		 * @param boolean $hashname Booléen indiquant si on doit hasher le nom des fichiers [« FALSE » par défaut]
		 * @param boolean $compress Booléen indiquant si on doit compresser le contenu pour un gain de place au détriment des performances [« FALSE » par défaut]
		 */
		public function __construct($filename, $duration = -1, $hashname = FALSE, $compress = FALSE) {
			parent::__construct($filename, $duration, $hashname);
			$this->compress = $compress;
		}

		/**
		 * Tentative de récupération du contenu depuis le cache
		 *
		 * @return mixed Le contenu si le cache existe et est frais, « NULL » sinon
		 */
		public function fetch() {

			// Si le fichier de cache est valide, alors on tente de récupérer le contenu
			if ($this->exists()) {
				$value = file_get_contents($this->file);

				// Si la compression est activée, alors on décompresse le contenu
				if ($this->compress) {
					$value = gzuncompress($value);
				}

				// On retourne le contenu récupéré
				return $value;
			}
		}

		/**
		 * Mise en cache du contenu
		 *
		 * @param  string $value Le contenu à mettre en cache
		 * @return string        Le contenu mis en cache
		 */
		public function store($value) {

			// Si la compression est activée, alors on compresse le contenu avant de l'écrire dans le fichier de cache
			if ($this->compress) {
				file_put_contents($this->file, gzcompress($value), LOCK_EX);
			} else {
				file_put_contents($this->file, $value, LOCK_EX);
			}

			// On retourne le contenu stocké
			return $value;
		}

		/**
		 * Lancement de la capture du contenu à mettre en cache
		 */
		public function start() {

			// Si la capture n'est pas activée on la lance
			if (!$this->buffering) {
				$this->buffering = TRUE;
				ob_start();
			}
		}

		/**
		 * Fin de la capture du contenu, et stockage de ce dernier
		 *
		 * @return string Le contenu mis en cache
		 */
		public function end() {

			// Si la capture est activée, on l'arrête avant de récupérer son contenu
			if ($this->buffering) {
				$content = ob_get_clean();
				$this->buffering = FALSE;
			} else {
				$content = NULL;
			}

			// On retourne le contenu capturé puis stocké
			return $this->store($content);
		}

		/**
		 * Méthode statique qui supprime le cache qui correspond à un nom
		 *
		 * @param mixed $filename Le nom du fichier de cache
		 */
		public static final function delete($filename) {
			$cache = new self($filename);
			$cache->drop();
		}
	}
?>