<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Classe abstraite et utilitaire de mise en cache
	 * 
	 * Le cache permet de conserver une information périodiquement, plutôt que de la regénérer à chaque exécution.
	 * 
	 * @package    framework
	 * @subpackage classes/utils/cache
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    12/05/2016
	 * @since      08/07/2014
	 */
	abstract class Cache {
		/*
		 * CHANGELOG:
		 * 12/05/2016: Amélioration de la configuration via le constructeur
		 * 18/03/2016: Utilisation de « file_get_contents » et « file_put_contents » pour améliorer les performances
		 * 08/02/2016: Ajout d'une exception s'il est impossible de créer le dossier de cache et changement des droits de création de dossiers en « 0777 »
		 * 17/09/2015: Compatibilité avec le préfixe de fichier
		 * 02/09/2015: Changement global du fonctionnement du cache
		 * 20/08/2015: Ajout de verrous en écriture sur le fichier de cache pour éviter des conflits de concurrence
		 * 22/07/2015: Amélioration conceptuelle avec la classe « VariableCache » réservée aux variables, et « ContentCache » aux contenus
		 * 09/06/2015: Changement de « $_SERVER['REQUEST_TIME'] » en « time() »
		 * 12/05/2015: - Déplacement de l'ajout du commentaire d'information dans le décorateur du fragment
		 *             - Suppression de l'encodage en base64
		 * 20/03/2015: Ajout d'une option pour compresser ou non le contenu mis en cache
		 * 18/02/2015: - Meilleur gestion de la langue dans le nom du fichier
		 *             - Compatibilité avec le service de traductions
		 * 24/09/2014: Ajout d'un booléen permettant d'utiliser des noms de fichiers cache digérés (MD5)
		 * 03/09/2014: Création du dossier de stockage si inexistant
		 * 15/08/2014: - Une durée négative entraîne la désactivation du rafraîchissement périodique
		 *             - Ajout d'une méthode statique de suppression
		 * 13/08/2014: Ajout d'un booléen pour activer ou non le cache
		 * 07/08/2014: Emplacement des fichiers désormais dans une variable statique pour faciliter la flexibilité si utilisé dans un autre contexte
		 * 22/07/2014: Compression des fichiers temporaires pour une utilisation du disque réduite et amélioration de l'utilisation de la classe
		 * 08/07/2014: Version initiale
		 */

		/**
		 * Emplacement où stocker les fichiers de cache
		 * 
		 * @var string
		 */
		private static $location = CACHE_DIR;

		/**
		 * Préfixe des fichiers de cache
		 * 
		 * @var string
		 */
		private static $prefix = FILE_PREFIX;

		/**
		 * Extension des fichiers de cache
		 * 
		 * @var string
		 */
		protected static $extension = '.cache';

		/**
		 * Emplacement du fichier de cache
		 * 
		 * @var string
		 */
		protected $file;

		/**
		 * Nom du cache
		 * 
		 * @var string
		 */
		protected $name;

		/**
		 * Durée de conservation du cache, avant rafraîchissement (en secondes) [« -1 » pour ne jamais rafraîchir, « 0 » pour rafraîchir tout le temps]
		 * 
		 * @var integer
		 */
		protected $duration;


		/**
		 * Constructeur du cache
		 *
		 * @param string  $filename Le nom du fichier de cache
		 * @param string  $duration La durée de conservation du cache, avant rafraîchissement (en secondes) [« -1 » par défaut, jamais rafraîchit]
		 * @param boolean $hashname Booléen indiquant si on doit hasher le nom des fichiers [« FALSE » par défaut]
		 */
		public function __construct($filename, $duration = -1, $hashname = FALSE) {
			$filename = trim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $filename), DIRECTORY_SEPARATOR);
			$this->file = self::$location.DIRECTORY_SEPARATOR.('.' !== dirname($filename) ? dirname($filename).DIRECTORY_SEPARATOR : '').self::$prefix.($hashname ? md5(basename($filename)) : basename($filename)).static::$extension;
			$this->name = basename($filename);
			$this->duration = $duration;

			// Si le répertoire de cache n'existe pas on le crée récursivement
			if(!is_dir(dirname($this->file))) {

				// Si on ne peut pas écrire le dossier, on lance une exception
				if(!mkdir(dirname($this->file), 0777, TRUE)) {
					throw new SystemException('Unable to create "%s" directory', dirname($this->file));
				}
			}
		}

		/**
		 * Méthode indiquant si le cache est crée et encore frais
		 * 
		 * @return boolean Vrai si le cache est crée et encore frais
		 */
		public final function exists() {
			return is_file($this->file) && is_readable($this->file) && (0 > $this->duration || time() - $this->duration < filemtime($this->file));
		}

		/**
		 * Récupération d'un contenu stocké en cache
		 * 
		 * @return mixed Le contenu si le cache existe et est frais, « NULL » sinon
		 */
		public abstract function fetch();

		/**
		 * Mise en cache d'un contenu
		 * 
		 * @param  mixed $value Le contenu à mettre en cache
		 * @return mixed        Le contenu mis en cache
		 */
		public abstract function store($value);

		/**
		 * Suppression du cache
		 */
		public final function drop() {
			@unlink($this->file);
		}

		/**
		 * Retourne l'emplacement du fichier de cache
		 * 
		 * @return string L'emplacement du fichier de cache
		 */
		public final function getFile() {
			return $this->file;
		}

		/**
		 * Retourne le nom du cache
		 * 
		 * @return string Le nom du cache
		 */
		public final function getName() {
			return $this->name;
		}

		/**
		 * Retourne la durée de conservation du cache
		 * 
		 * @return integer La durée de conservation du cache
		 */
		public final function getDuration() {
			return $this->duration;
		}

		/**
		 * Méthode pratique de gestion du cache d'une variable
		 * 
		 * @param  string   $name     Le nom du cache
		 * @param  callable $callback Le callable qui retournera la variable à mettre en cache
		 * @param  string   $duration La durée de conservation du cache, avant rafraîchissement (en secondes) [« -1 » par défaut, jamais rafraîchit]
		 * @return mixed              La variable mise ou récupérée du cache
		 */
		public static final function variable($name, $callback, $duration = -1) {
			$cache = new VariableCache($name, $duration);
			if(NULL === $variable = $cache->fetch()) {
				$variable = $callback();
				$cache->store($variable);
			}
			return $variable;
		}

		/**
		 * Méthode pratique de gestion du cache d'un contenu
		 * 
		 * @param  string   $name     Le nom du cache
		 * @param  callable $callback Le callable qui écrira le contenu à mettre en cache
		 * @param  string   $duration La durée de conservation du cache, avant rafraîchissement (en secondes) [« -1 » par défaut, jamais rafraîchit]
		 * @return mixed              Le contenu mis ou récupéré du cache
		 */
		public static final function content($name, $callback, $duration = -1) {
			$cache = new ContentCache($name, $duration);
			if(NULL === $content = $cache->fetch()) {
				$cache->start();
				$callback();
				$content = $cache->end();
			}
			return $content;
		}

		/**
		 * Incrémente un nombre stocké en cache
		 * 
		 * @param  string  $name         Le nom du cache
		 * @param  integer $defaultValue La valeur par défaut si le cache n'existe pas
		 * @param  integer $step         Le pas lors de l'incrémentation
		 * @param  string  $duration     La durée de conservation du cache, avant rafraîchissement (en secondes) [« -1 » par défaut, jamais rafraîchit]
		 * @return integer               Le nombre incrémenté
		 */
		public static final function increase($name, $defaultValue = 0, $step = 1, $duration = -1) {
			$cache = new VariableCache($name, $duration);
			if(NULL !== $value = $cache->fetch()) {
				$value += $step;
			} else {
				$value = (int) $defaultValue;
			}
			return $cache->store($value);
		}

		/**
		 * Décrémente un nombre stocké en cache
		 * 
		 * @param  string  $name         Le nom du cache
		 * @param  integer $defaultValue La valeur par défaut si le cache n'existe pas
		 * @param  integer $step         Le pas lors de la décrémentation
		 * @param  string  $duration     La durée de conservation du cache, avant rafraîchissement (en secondes) [« -1 » par défaut, jamais rafraîchit]
		 * @return integer               Le nombre décrémenté
		 */
		public static final function decrease($name, $defaultValue = 0, $step = 1, $duration = -1) {
			$cache = new VariableCache($name, $duration);
			if(NULL !== $value = $cache->fetch()) {
				$value -= $step;
			} else {
				$value = (int) $defaultValue;
			}
			return $cache->store($value);
		}

		/**
		 * Mise à jour d'une valeur spécifique en cache (Compare-and-swap)
		 * 
		 * @param  string  $name     Le nom du cache
		 * @param  mixed   $oldValue L'ancienne valeur à remplacer
		 * @param  mixed   $newValue La nouvelle valeur remplaçante
		 * @return boolean           Vrai si la valeur a bien été remplacée
		 */
		public static final function cas($name, $oldValue, $newValue) {
			$cache = new VariableCache($name);
			if(NULL !== $value = $cache->fetch()) {
				if($oldValue === $value) {
					$cache->store($newValue);
					return TRUE;
				}
			}
			return FALSE;
		}

		/**
		 * Méthode statique qui supprime l'ensemble du cache
		 */
		public static final function deleteAll() {

			// On parcours d'abord les fichiers d'un dossier pour les supprimer avant celui-ci
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(self::$location, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

			foreach($files as $file) {

				// Si c'est un dossier ou un fichier on invoque la fonction associée
				if($file->isDir()) {
					rmdir($file->getRealPath());
				} else {
					unlink($file->getRealPath());
				}
			}
		}
	}
?>