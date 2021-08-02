<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Classe abstraite de chargement
	 * 
	 * Elle permet aux classes qui en hérite de pouvoir charger dynamiquement des fichiers selon les emplacements ajoutés.
	 * La map des fichiers correspondant est mise en cache pour être chargée plus rapidement les fois suivantes.
	 * 
	 * @package    framework
	 * @subpackage classes/loaders
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    03/09/2015
	 * @since      12/01/2015
	 */
	abstract class AbstractLoader {
		/*
		 * CHANGELOG:
		 * 03/09/2015: Suppression de la dépendance avec l'activation du cache
		 * 14/08/2015: Changements mineurs et possibilité d'enregistrer des fichiers en plus de dossiers
		 * 29/07/2015: - Utilisation de la classe « Cache » dédiée plutôt qu'une implémentation du cache interne
		 *             - Changement général dans le fonctionnement de la classe, pour un résultat similaire
		 * 28/03/2015: Suppression de la dépendance avec l'activation du cache
		 * 17/02/2015: Ajout de la méthode « load() »
		 * 12/01/2015: Version initiale
		 */

		/**
		 * Indique si on peut rafraîchir le cache automatiquement si on ne trouve pas un fichier après le chargement du cache courant
		 * 
		 * @var boolean
		 */
		private static $allowRefresh = DEV_MODE;

		/**
		 * Extension des fichiers à rechercher dans les emplacements
		 * 
		 * @var string
		 */
		protected static $extension = '.php';

		/**
		 * Objet de gestion du cache
		 *
		 * @var VariableCache
		 */
		protected $cache;

		/**
		 * Liste des emplacements à parcourir pour trouver les fichiers à charger
		 * 
		 * @var array
		 */
		protected $locations = array();

		/**
		 * Liste des fichiers trouvés correspondant à l'extension dans les emplacements
		 * 
		 * @var array
		 */
		protected $files = array();

		/**
		 * Indique si on a déjà rafraîchit le cache, si oui il est inutile de le refaire lors de la même exécution
		 * 
		 * @var boolean
		 */
		protected $alreadyRefreshed = FALSE;

		/**
		 * Constructeur d'un chargeur
		 */
		public function __construct() {

			// Instanciation de l'objet de gestion du cache avec le nom renseigné via la « template method »
			$this->cache = new VariableCache($this->getCacheName());
		}

		/**
		 * Ajout d'un emplacement où rechercher ou d'un fichier
		 * 
		 * @param  string         $location L'emplacement ou le fichier
		 * @return AbstractLoader           L'instance courante
		 */
		public final function add($location) {

			// Si l'emplacement ou le fichier n'existe pas
			if (!file_exists($location)) {
				throw new SystemException('"%s" location does not exist', path($location));
			}

			$this->locations[] = $location;
			return $this;
		}

		/**
		 * Ajout de plusieurs emplacements où rechercher ou de plusieurs fichiers
		 * 
		 * @param  array          $locations Le tableau d'emplacements ou de fichiers
		 * @return AbstractLoader            L'instance courante
		 */
		public final function addArray(array $locations) {
			foreach ($locations as $location) {
				$this->add($location);
			}
			return $this;
		}

		/**
		 * Chargement depuis le cache des fichiers ou génération si le cache n'est pas ou plus valide
		 */
		public final function load() {

			// Si les fichiers ne peuvent être lus depuis le cache
			if (NULL === $this->files = $this->cache->fetch()) {

				// On charge les fichiers depuis les emplacements ou fichiers enregistrés
				$files = array();
				foreach ($this->locations as $location) {

					// Si c'est un dossier, on le scan pour récupérer les fichiers qui le compose
					if (is_dir($location)) {
						$iterator = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($location)), '/^.+\\' . static::$extension . '$/i');
						foreach ($iterator as $file) {
							$files[] = $file->getPathname(); 
						}

					// Sinon c'est un fichier, on se contente de l'ajouter
					} else {
						$files[] = $location;
					}
				}

				// Pour chaque fichier trouvé, on les enregistre
				foreach ($files as $file) {

					// Le nom correspond à la base, à laquelle on retire l'extension et le point
					$name = basename($file, static::$extension);

					$this->files[$name] = $file;
				}

				// Écriture dans le cache pour charger rapidement les fois suivantes
				$this->cache->store($this->files);
			}
		}

		/**
		 * Récupération d'un fichier
		 * 
		 * @param  string $name Le nom du fichier à récupérer
		 * @return string       Le fichier récupéré
		 */
		public final function getFile($name) {

			// Si le nom correspond bien à un fichier toujours valide, on le retourne
			if (isset($this->files[$name]) && is_file($this->files[$name]) && is_readable($this->files[$name])) {
				return $this->files[$name];

			// Si on a jamais rafraîchit, on le fait avant de retenter une seconde fois
			} else if (self::$allowRefresh && !$this->alreadyRefreshed) {
				$this->cache->drop();
				$this->load();
				$this->alreadyRefreshed = TRUE;
				if (isset($this->files[$name]) && is_file($this->files[$name]) && is_readable($this->files[$name])) {
					return $this->files[$name];
				}
			}
		}

		/**
		 * Retourne l'ensemble des fichiers chargés
		 * 
		 * @return array L'ensemble des fichiers chargés
		 */
		public final function getFiles() {
			return $this->files;
		}

		/**
		 * Retourne l'extension des fichiers à charger
		 * 
		 * @return string L'extension des fichiers
		 */
		public final static function getExtension() {
			return static::$extension;
		}

		/**
		 * Récupération du nom du fichier de cache
		 * 
		 * @return string Le nom du fichier de cache
		 */
		protected abstract function getCacheName();
	}
?>