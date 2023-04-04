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
	 * Classe de génération de fichiers de journalisations
	 *
	 * Cette classe permet de créer des fichiers de journalisations personnalisés. Il est aussi possible de les archiver à partir d'une certaine taille.
	 *
	 * @package    framework
	 * @subpackage classes/utils
	 * @version    18/03/2016
	 * @since      08/08/2014
	 */
	class Logger {
		/*
		 * CHANGELOG:
		 * 18/03/2016: Utilisation de « file_put_contents » pour améliorer les performances
		 * 08/02/2016: Ajout d'une exception s'il est impossible de créer le dossier de journaux et changement des droits de création de dossiers en « 0777 »
		 * 17/09/2015: Compatibilité avec le préfixe de fichier
		 * 28/08/2015: Réécriture de la classe avec une meilleure utilisation, et la gestion de valeurs sur plusieurs lignes
		 * 23/03/2015: Correction de l'affichage quand le contenu d'un champ contient des retours à la ligne
		 * 03/09/2014: Création du dossier de stockage si inexistant
		 * 08/08/2014: Version initiale
		 */

		/**
		 * Alignement à gauche [identique à STR_PAD_RIGHT]
		 */
		const ALIGN_LEFT = STR_PAD_RIGHT;

		/**
		 * Alignement au milieu [identique à STR_PAD_BOTH]
		 */
		const ALIGN_MIDDLE = STR_PAD_BOTH;

		/**
		 * Alignement à droite [identique à STR_PAD_LEFT]
		 */
		const ALIGN_RIGHT = STR_PAD_LEFT;

		/**
		 * Nombre de caractères maximum de la date
		 */
		const DATE_MAX_SIZE = 19;

		/**
		 * Nombre de caractères maximum d'une adresse IP, IPv6 y-compris
		 */
		const IP_ADDRESS_MAX_SIZE = 45;

		/**
		 * Nombre de caractères maximum de l'agent utilisateur (valeur arbitraire)
		 */
		const USER_AGENT_MAX_SIZE = 150;

		/**
		 * Nombre de caractères maximum du référent (valeur arbitraire)
		 */
		const REFERER_MAX_SIZE = 100;

		/**
		 * Nombre de caractères maximum de la requête (valeur arbitraire)
		 */
		const REQUEST_MAX_SIZE = 100;

		/**
		 * Emplacement où stocker les fichiers de journalisation
		 *
		 * @var string
		 */
		private static $location = LOGS_DIR;

		/**
		 * Préfixe des fichiers de journalisation
		 *
		 * @var string
		 */
		private static $prefix = FILE_PREFIX;

		/**
		 * Extension des fichiers de journalisation
		 *
		 * @var string
		 */
		protected static $extension = '.log';

		/**
		 * Format de la date
		 */
		private static $dateFormat = 'Y-m-d H:i:s';

		/**
		 * Booléen indiquant si on doit utiliser les verrous lors de l'écriture
		 *
		 * @var boolean
		 */
		protected static $lockfile = TRUE;

		/**
		 * Booléen indiquant si on doit écrire les valeurs trop grandes sur plusieurs lignes ou les laisser élargies
		 *
		 * @var boolean
		 */
		private static $multiLines = TRUE;

		/**
		 * Emplacement du fichier de journalisation
		 *
		 * @var string
		 */
		protected $file;

		/**
		 * Nom du fichier de journalisation
		 *
		 * @var string
		 */
		protected $name;

		/**
		 * Taille limite d'un fichier de journalisation avant archivage (en octets) [« -1 » pour ne jamais archiver]
		 *
		 * @var integer
		 */
		protected $limit;

		/**
		 * Booléen indiquant si on doit afficher un entête lors de la création du fichier de journalisation
		 *
		 * @var boolean
		 */
		protected $displayHeaders;

		/**
		 * Liste des valeurs à écrire
		 *
		 * @var array
		 */
		protected $values = array();

		/**
		 * Liste des entêtes à écrire si on crée le fichier
		 *
		 * @var array
		 */
		protected $headers = array();

		/**
		 * Liste des tailles maximales des valeurs
		 *
		 * @var array
		 */
		protected $columnsSize = array();

		/**
		 * Liste des alignements des valeurs
		 *
		 * @var array
		 */
		protected $columnsAlignment = array();

		/**
		 * Initialisation de la classe
		 */
		public static function init() {

			// Utilisation ou non des fonctions multi-bytes
			if (extension_loaded('mbstring')) {
				mb_internal_encoding('UTF-8');
			}
		}

		/**
		 * Constructeur du journaliseur
		 *
		 * @param string  $filename       Le nom du fichier de journalisation
		 * @param integer $limit          La taille limite d'un fichier de journalisation avant archivage (en octets) [« -1 » pour ne jamais archiver]
		 * @param boolean $displayHeaders Booléen indiquant si on affiche les entêtes si on crée le fichier
		 */
		public function __construct($filename, $limit = 10485760, $displayHeaders = TRUE) {
			$filename = trim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $filename), DIRECTORY_SEPARATOR);
			$this->file = self::$location . DIRECTORY_SEPARATOR . ('.' !== dirname($filename) ? dirname($filename) . DIRECTORY_SEPARATOR : '') . self::$prefix . basename($filename) . static::$extension;
			$this->name = basename($filename);
			$this->limit = (int) $limit;
			$this->displayHeaders = $displayHeaders;

			// Si le répertoire de journalisation n'existe pas on le crée récursivement
			if (!is_dir(dirname($this->file))) {

				// Si on ne peut pas écrire le dossier, on lance une exception
				if (!mkdir(dirname($this->file), 0777, TRUE)) {
					throw new SystemException('Unable to create "%s" directory', dirname($this->file));
				}
			}
		}

		/**
		 * Ajoute une entrée au journaliseur
		 *
		 * @param  string  $value  La valeur de l'entrée
		 * @param  string  $header L'entête de l'entrée [vide par défaut]
		 * @param  integer $size   La taille maximale de la valeur de l'entrée
		 * @param  integer $align  L'alignement de l'entrée [« Logger::ALIGN_LEFT » par défaut pour un alignement à gauche]
		 * @return Logger          L'instance courante
		 */
		public function set($value, $header = '', $size = 0, $align = self::ALIGN_LEFT) {

			// On transforme les retours à la ligne en espaces
			$this->values[] = preg_replace('/(\r\n|\r|\n)+/', ' ', $value);

			// Si l'entête est trop grand par rapport à la taille maximale on le réduit
			$this->headers[] = 0 < $size ? substr($header, 0, $size) : $header;

			$this->columnsSize[] = (int) $size;
			$this->columnsAlignment[] = $align;
			return $this;
		}

		/**
		 * Ajoute la date au journaliseur
		 *
		 * @return Logger L'instance courante
		 */
		public final function setDate() {
			$this->set(date(self::$dateFormat), __('Date'), self::DATE_MAX_SIZE);
			return $this;
		}

		/**
		 * Ajoute l'adresse IP au journaliseur
		 *
		 * @return Logger L'instance courante
		 */
		public final function setIPAddress() {
			$this->set(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', __('IP address'), self::IP_ADDRESS_MAX_SIZE);
			return $this;
		}

		/**
		 * Ajoute l'agent utilisateur au journaliseur
		 *
		 * @return Logger L'instance courante
		 */
		public final function setUserAgent() {
			$this->set(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '', __('User agent'), self::USER_AGENT_MAX_SIZE);
			return $this;
		}

		/**
		 * Ajoute le référent au journaliseur
		 *
		 * @return Logger L'instance courante
		 */
		public final function setReferer() {
			$this->set(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', __('Referer'), self::REFERER_MAX_SIZE);
			return $this;
		}

		/**
		 * Ajoute la requête au journaliseur
		 *
		 * @return Logger L'instance courante
		 */
		public final function setRequest() {
			$this->set(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '', __('Request'), self::REQUEST_MAX_SIZE);
			return $this;
		}

		/**
		 * Écrit les entrées dans le fichier, et l'archive si besoin
		 */
		public final function write() {

			// Si le fichier existe
			$exists = is_file($this->file);

			// Si la limite est excédée
			$exceeds = $exists && 0 < $this->limit && filesize($this->file) > $this->limit;

			// Génération du contenu selon les deux valeurs ci-dessus
			$content = $this->generate($exists, $exceeds);

			// Si le fichier existe déjà on ajoute le contenu, sinon on le crée
			if ($exists) {
				file_put_contents($this->file, $content, FILE_APPEND | LOCK_EX);
			} else {
				file_put_contents($this->file, $content, LOCK_EX);
			}

			// Si la limite est dépassée, on archive le fichier courant avant de le supprimer
			if ($exceeds) {
				$this->archive();
			}
		}

		/**
		 * Archive le fichier de journalisation
		 */
		public final function archive() {
			if (!is_file($this->file)) {
				return;
			}

			// On tente de trouver un nom pas déjà crée
			$base = dirname($this->file) . DIRECTORY_SEPARATOR . basename($this->file, static::$extension);
			$i = 1;
			do {
				$archiveFile = $base . '_' . $i . static::$extension . '.gz';
				++$i;
			} while (is_file($archiveFile));

			// Écriture d'un fichier compressé
			$handle = gzopen($archiveFile, 'w9');

			// Verrou exclusif pour l'écriture (Si activé)
			if (static::$lockfile) {
				flock($handle, LOCK_EX);
			}

			gzwrite($handle, file_get_contents($this->file));

			// On lève le verrou (Si activé)
			if (static::$lockfile) {
				flock($handle, LOCK_UN);
			}

			gzclose($handle);

			// Suppression du fichier, il sera recrée la prochaine fois
			@unlink($this->file);
		}

		/**
		 * Génère le contenu à écrire selon l'état du fichier de journalisation
		 *
		 * @param  boolean $exists  Vrai si le fichier existe déjà
		 * @param  boolean $exceeds Vrai si la limite de taille du fichier est dépassée
		 * @return string           Le contenu à écrire
		 */
		protected function generate($exists, $exceeds) {
			$content = '';

			// Si le fichier n'existe pas, on ajoute la bordure et un entête si activé
			if (!$exists) {
				$content .= $this->generateBorder();
				if ($this->displayHeaders) {
					$content .= $this->generateHeaders();
					$content .= $this->generateBorder(TRUE);
				}
			}

			// On ajoute à présent le contenu généré par les valeurs
			$content .= $this->generateValues();

			// Si la limite est excédée, on ajoute une bordure de fin
			if ($exceeds) {
				$content .= $this->generateBorder();
			}

			// Réinitialisation des données de l'entrée
			$this->values = array();
			$this->headers = array();
			$this->columnsSize = array();
			$this->columnsAlignment = array();

			return $content;
		}

		/**
		 * Génère le contenu d'une bordure
		 *
		 * @param  boolean $headerBorder Indique si c'est une bordure d'entête ou non [« FALSE » par défaut]
		 * @return string                Le contenu de la bordure générée
		 */
		private function generateBorder($headerBorder = FALSE) {
			$content = '+';
			foreach ($this->columnsSize as $size) {
				$content .= str_repeat($headerBorder ? '=' : '-', $size + 2) . '+';
			}
			return $content . PHP_EOL;
		}

		/**
		 * Génère le contenu de l'entête
		 *
		 * @return string Le contenu de l'entête généré
		 */
		private function generateHeaders() {
			$content = '|';
			$count = count($this->headers);
			for ($i = 0; $i < $count; ++$i) {
				$content .= ' ' . self::str_pad(self::strtoupper($this->headers[$i]), $this->columnsSize[$i], ' ', STR_PAD_BOTH) . ' |';
			}
			return $content . PHP_EOL;
		}

		/**
		 * Génère le contenu des valeurs
		 *
		 * @return string Le contenu des valeurs généré
		 */
		private function generateValues() {

			// Si le mode multi-lignes est activé
			if (self::$multiLines) {

				// On commence par transformer chaque valeur en tableau de lignes, en conservant le nombre maximum de lignes d'une valeur
				$rowMax = 1;
				$columnCount = count($this->values);
				for ($i = 0; $i < $columnCount; ++$i) {
					if (0 < $this->columnsSize[$i] && self::strlen($this->values[$i]) > $this->columnsSize[$i]) {
						$this->values[$i] = self::str_split($this->values[$i], $this->columnsSize[$i]);
						$rowMax = max($rowMax, count($this->values[$i]));
					} else {
						$this->values[$i] = array($this->values[$i]);
					}
				}

				// À présent on écrit ces tableaux connaissant le maximum de ligne
				$content = '';
				for ($i = 0; $i < $rowMax; ++$i) {
					$content .= '|';
					for ($j = 0; $j < $columnCount; $j++) {
						$content .= ' ' . self::str_pad(isset($this->values[$j][$i]) ? $this->values[$j][$i] : '', $this->columnsSize[$j], ' ', $this->columnsAlignment[$j]) . ' |';
					}
					$content .= PHP_EOL;
				}

				return $content;

			// Sinon on écrit chaque valeur sur une seule ligne
			} else {
				$content = '|';
				$count = count($this->values);
				for ($i = 0; $i < $count; ++$i) {
					$content .= ' ' . self::str_pad($this->values[$i], $this->columnsSize[$i], ' ', $this->columnsAlignment[$i]) . ' |';
				}
				return $content . PHP_EOL;
			}
		}

		/**
		 * Retourne l'emplacement du fichier de journalisation
		 *
		 * @return string L'emplacement du fichier de journalisation
		 */
		public final function getFile() {
			return $this->file;
		}

		/**
		 * Retourne le nom du fichier de journalisation
		 *
		 * @return string Le nom du fichier de journalisation
		 */
		public final function getName() {
			return $this->name;
		}

		/**
		 * Retourne la limite de taille du fichier de journalisation avant archive
		 *
		 * @return integer La limite de taille (en octets)
		 */
		public final function getLimit() {
			return $this->limit;
		}

		/**
		 * Adaptation de la fonction « str_pad() » avec le multi-octets
		 *
		 * @param  string  $input      La chaîne dont on souhaite ajouter un décalage
		 * @param  boolean $pad_length La longueur du décalage
		 * @param  boolean $pad_string La chaîne de remplissage du décalage [espace par défaut]
		 * @param  boolean $pad_type   L'endroid où ajouter le décalage [« STR_PAD_RIGHT » par défaut]
		 * @return string              La chaîne avec le décalage ajouté
		 */
		private static function str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT) {
			if (extension_loaded('mbstring')) {
				return str_pad($input, $pad_length + abs(strlen($input) - mb_strlen($input)), $pad_string, $pad_type);
			}
			return str_pad($input, $pad_length, $pad_string, $pad_type);
		}

		/**
		 * Retourne une chaîne de caractères en majuscules
		 *
		 * @param  string $string La chaîne à mettre en majuscule
		 * @return string         La chaîne en majuscules
		 */
		private static function strtoupper($string) {
			if (extension_loaded('mbstring')) {
				return mb_strtoupper($string);
			}
			return strtoupper($string);
		}

		/**
		 * Adaptation de la fonction « str_split() » avec le multi-octets
		 *
		 * @param  boolean $string       La chaîne à découper
		 * @param  boolean $split_length Le nombre de caractères de chaque tronçon
		 * @return array                 Le tableau contenant chaque morceau généré à partir de la chaîne d'entrée
		 */
		private static function str_split($string, $split_length = 1) {
			if (extension_loaded('mbstring')) {
				if ($split_length < 1) {
					return FALSE;
				}
				$result = array();
				for ($i = 0; $i < mb_strlen($string); $i += $split_length) {
					$result[] = mb_substr($string, $i, $split_length);
				}
				return $result;
			}
			return str_split($string, $split_length);
		}

		/**
		 * Retourne le nombre de caractères d'une chaîne
		 * @param  string $string La chaîne
		 * @return integer         Le nombre de caractères
		 */
		private static function strlen($string) {
			if (extension_loaded('mbstring')) {
				return mb_strlen($string);
			}
			return strlen($string);
		}
	}

	// Initialisation de la classe au chargement de cette dernière
	Logger::init();
?>