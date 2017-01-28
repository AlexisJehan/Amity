<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Détection de l'utilisation du réseau Tor
	 * 
	 * Cette classe tient à jour une liste des noeuds sortants du réseau Tor et permet de les comparer avec une adresse IP.
	 * 
	 * @package    framework
	 * @subpackage classes/tools
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    02/10/2015
	 * @since      02/10/2015
	 * @link       http://torstatus.blutmagie.de
	 * @uses       Cache, VariableCache et WebRequest
	 */
	final class TorDetector {
		/*
		 * DONNÉES:
		 * Les adresses IP sont téléchargées à partir du site « http://torstatus.blutmagie.de ».
		 */
		/*
		 * CHANGELOG:
		 * 02/10/2015: Version initiale
		 */

		/**
		 * Adresse de la page où récupérer les adresses IP des noeuds sortants
		 * 
		 * @var string
		 */
		private static $url = 'http://torstatus.blutmagie.de/ip_list_exit.php';

		/**
		 * Nom du fichier de cache
		 * 
		 * @var string
		 */
		private static $cacheName = 'tor';

		/**
		 * Durée de mise en cache avant re-téléchargement de la liste
		 * 
		 * @var integer
		 */
		private static $cacheDuration = 3600;

		/**
		 * Tableau qui contiendra l'ensemble des adresses IP des noeuds sortants
		 * 
		 * @var array
		 */
		private static $exitNodes;

		/**
		 * Adresse IP dont on souhaite vérifier si il s'agit d'un noeud sortant
		 * 
		 * @var string
		 */
		protected $ipAddress;

		/**
		 * Indique si l'adresse IP correspond à un noeud sortant du réseau Tor
		 * 
		 * @var boolean
		 */
		protected $useTor;


		/**
		 * Constructeur du détecteur
		 *
		 * @param string $ipAddress Adresse IP dont on souhaite vérifier si elle correspond à un noeud de sortie du réseau TOR [si non renseignée alors sera récupérée de la variable globale « $_SERVER »]
		 */
		public function __construct($ipAddress = NULL) {

			// Si ça n'a pas été fait auparavant dans l'exécution en cours, on génère le cache s'il n'est plus valide et on charge les adresses IP depuis la liste disponible sur Internet
			if(NULL === self::$exitNodes) {
				$url = self::$url;
				self::$exitNodes = Cache::variable(self::$cacheName, function() use($url) {
					$request = new WebRequest($url);
					$content = $request->get();

					// Si le premier caractère est « < », le contenu doit être du XML et donc une exception est apparue car cela ne correspond pas aux caractères d'une adresse IP
					if(FALSE === strpos($content, '<')) {
						$content = explode("\n", $request->get());
						array_pop($content);
						return $content;
					}
				}, self::$cacheDuration);
			}

			// Si l'adresse IP n'est pas renseignée on tente de la récupérer depuis la variable du serveur
			if(NULL === $ipAddress) {
				$ipAddress = $_SERVER['REMOTE_ADDR'];
			}

			$this->setIpAddress($ipAddress);
		}

		/**
		 * Retourne l'adresse IP
		 * 
		 * @return string L'adresse IP
		 */
		public function getIpAddress() {
			return $this->ipAddress;
		}

		/**
		 * Modifie l'adresse IP
		 * 
		 * @param string $ipAddress La nouvelle adresse IP
		 */
		public function setIpAddress($ipAddress) {
			$this->ipAddress = $ipAddress;
			$this->useTor = NULL !== self::$exitNodes && in_array($ipAddress, self::$exitNodes);
		}

		/**
		 * Indique si l'adresse IP est celle d'un noeud sortant du réseau Tor
		 * 
		 * @return boolean Vrai si l'adresse IP est celle d'un noeud sortant du réseau Tor
		 */
		public function useTor() {
			return $this->useTor;
		}
	}
?>