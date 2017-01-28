<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Requête web
	 * 
	 * « WebRequest » est un outil adaptant l'utilitaire cURL et permettant de faire toutes sortes de requêtes vers un serveur distant, et particulièrement des requêtes HTTP.
	 * 
	 * @package    framework
	 * @subpackage classes/tools
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    30/05/2016
	 * @since      22/08/2015
	 */
	final class WebRequest {
		/*
		 * CHANGELOG:
		 * 30/05/2016: Ajout d'accesseurs manquant ainsi qu'une méthode permettant de falsifier l'agent utilisateur plutôt qu'utiliser celui de la classe par défaut
		 * 28/09/2015: Changement mineur de l'utilisation complémentaire de « list() » et « explode() » en renseignant la dimension attendue en troisième paramètre de « explode() »
		 * 22/08/2015: Version initiale
		 */

		/**
		 * Options cURL à ajouter pour la requête à envoyer, contient à l'instanciation des options avec des valeurs par défaut
		 * 
		 * @var array
		 */
		protected $options = array(
			CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; WebRequest/1.0)',

			CURLOPT_AUTOREFERER    => TRUE,

			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_MAXREDIRS      => 10,

			CURLOPT_CONNECTTIMEOUT => 30,
			CURLOPT_TIMEOUT        => 60,

			CURLOPT_HEADER         => FALSE,
			CURLOPT_COOKIESESSION  => FALSE,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_RETURNTRANSFER => TRUE
		);

		/**
		 * L'URL du site à requêter (« http://example.com/ » par défaut)
		 * 
		 * @var string
		 */
		protected $url = 'http://example.com/';

		/**
		 * Des données à ajouter facultativement à la requête selon son type
		 * 
		 * @var array
		 */
		protected $datas;

		/**
		 * Des entêtes HTTP personnalisés
		 * 
		 * @var array
		 */
		protected $headers;

		/**
		 * Des cookies à envoyer avec la requête, ils ne sont pas conservés
		 * 
		 * @var array
		 */
		protected $cookies;

		/**
		 * Le code d'erreur de la dernière requête (« 0 » pour une requête réussie, peut être un tableau pour des requêtes multiples)
		 * 
		 * @var integer|array
		 */
		protected $errorCode;

		/**
		 * Le message d'erreur de la dernière requête (vide pour une requête réussie, peut être un tableau pour des requêtes multiples)
		 * 
		 * @var string|array
		 */
		protected $errorMessage;

		/**
		 * Le contenu de la réponse, si retourné (peut être un tableau pour des requêtes multiples)
		 * 
		 * @var string|array
		 */
		protected $response;

		/**
		 * Le protocôle HTTP de la réponse (peut être un tableau pour des requêtes multiples)
		 * 
		 * @var string|array
		 */
		protected $responseProtocol;

		/**
		 * Le status HTTP de la réponse (peut être un tableau pour des requêtes multiples)
		 * 
		 * @var integer|array
		 */
		protected $responseStatus;

		/**
		 * Le message du status HTTP de la réponse (peut être un tableau pour des requêtes multiples)
		 * 
		 * @var string|array
		 */
		protected $responseStatusMessage;

		/**
		 * Les entêtes HTTP de la réponse sous forme de tableau (peut être un tableau de tableaux pour des requêtes multiples)
		 * 
		 * @var array
		 */
		protected $responseHeaders;

		/**
		 * Les chaînes courantes, seulement utilisé pour les requêtes multiples
		 * 
		 * @var array
		 */
		protected $channels;

		/**
		 * Constructeur de la requête web
		 *
		 * @param string $url     L'URL de la requête (optionnelle)
		 * @param array  $datas   Les données de la requête (optionnelles)
		 * @param array  $headers Les entêtes de la requête (optionnelles)
		 * @param array  $cookies Les cookies de la requête (optionnels)
		 */
		public function __construct($url = NULL, array $datas = array(), array $headers = array(), array $cookies = array()) {

			// Si l'URL n'est pas vide on la remplace, sinon on conserve celle par défaut
			if(NULL !== $url) {
				$this->url = $url;
			}

			$this->datas = $datas;
			$this->headers = $headers;
			$this->cookies = $cookies;
		}

		/**
		 * Change l'URL de la requête
		 *
		 * @param  string     $url La nouvelle URL
		 * @return WebRequest      L'instance courante
		 */
		public function setUrl($url) {
			$this->url = $url;

			return $this;
		}

		/**
		 * Ajoute une donnée à la requête
		 *
		 * @param  string     $key   La clé de la donnée à ajouter
		 * @param  string     $value La valeur de la donnée à ajouter (optionnelle, vide par défaut)
		 * @return WebRequest        L'instance courante
		 */
		public function setData($key, $value = '') {
			$this->datas[$key] = $value;

			return $this;
		}

		/**
		 * Change les données de la requête
		 *
		 * @param  array      $datas Les nouvelles données de la requête
		 * @return WebRequest        L'instance courante
		 */
		public function setDatas(array $datas) {
			$this->datas = $datas;

			return $this;
		}

		/**
		 * Ajoute un entête à la requête
		 *
		 * @param  string     $key   La clé de l'entête à ajouter
		 * @param  string     $value La valeur de l'entête à ajouter (optionnelle, vide par défaut)
		 * @return WebRequest        L'instance courante
		 */
		public function setHeader($key, $value = '') {
			$this->headers[$key] = $value;

			return $this;
		}

		/**
		 * Change les entêtes de la requête
		 *
		 * @param  array      $headers Les nouveaux entêtes de la requête
		 * @return WebRequest          L'instance courante
		 */
		public function setHeaders(array $headers) {
			$this->headers = $headers;

			return $this;
		}

		/**
		 * Ajoute un cookie à la requête
		 *
		 * @param  string     $key   La clé du cookie à ajouter
		 * @param  string     $value La valeur du cookie à ajouter (optionnelle, vide par défaut)
		 * @return WebRequest        L'instance courante
		 */
		public function setCookie($key, $value = '') {
			$this->cookies[$key] = $value;

			return $this;
		}

		/**
		 * Change les cookies de la requête
		 *
		 * @param  array      $cookies Les nouveaux cookies de la requête
		 * @return WebRequest          L'instance courante
		 */
		public function setCookies(array $cookies) {
			$this->cookies = $cookies;

			return $this;
		}

		/**
		 * Ajoute une option cURL à la requête
		 *
		 * @param  string     $key   La valeur du nom de l'option à ajouter
		 * @param  string     $value La valeur de l'option à ajouter (optionnelle, vrai par défaut)
		 * @return WebRequest        L'instance courante
		 */
		public function setOption($key, $value = TRUE) {
			$this->options[$key] = $value;

			return $this;
		}

		/**
		 * Ajoute plusieurs options à la requête
		 *
		 * @param  array      $options Les options à ajouter
		 * @return WebRequest          L'instance courante
		 */
		public function setOptions(array $options) {
			$this->options = $this->options + $options;

			return $this;
		}

		/**
		 * Change l'agent utilisateur de la requête
		 *
		 * @param  string     $userAgent Le nouvel agent utilisateur
		 * @return WebRequest            L'instance courante
		 */
		public function setUserAgent($userAgent) {
			$this->options[CURLOPT_USERAGENT] = $userAgent;

			return $this;
		}

		/**
		 * Change l'agent utilisateur en imitant celui d'un utilisateur lambda
		 *
		 * @return WebRequest L'instance courante
		 */
		public function setFakeUserAgent() {
			$this->options[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 5.1; rv:13.0) Gecko/20100101 Firefox/13.0.1';

			return $this;
		}

		/**
		 * Change le référent de la requête
		 *
		 * @param  string     $referer Le nouveau référent
		 * @return WebRequest          L'instance courante
		 */
		public function setReferer($referer) {
			$this->options[CURLOPT_AUTOREFERER] = FALSE;
			$this->options[CURLOPT_REFERER] = $referer;

			return $this;
		}

		/**
		 * Indique qu'on doit suivre les redirections de la requête
		 *
		 * @param  boolean    $followLocation Vrai si on suit les redirections (vrai par défaut)
		 * @param  integer    $maxRedirs      Le nombre maximum de redirections autorisées (« 0 » par défaut)
		 * @return WebRequest                 L'instance courante
		 */
		public function setFollow($followLocation = TRUE, $maxRedirs = 0) {
			$this->options[CURLOPT_FOLLOWLOCATION] = $followLocation;
			$this->options[CURLOPT_MAXREDIRS] = (int) $maxRedirs;

			return $this;
		}

		/**
		 * Change le port de la requête
		 *
		 * @param  integer    $port Le nouveau port (« 80 » par défaut)
		 * @return WebRequest       L'instance courante
		 */
		public function setPort($port = 80) {
			$this->options[CURLOPT_PORT] = (int) $port;
		}

		/**
		 * Change le temps maximal d'exécution d'une requête avant interruption
		 *
		 * @param  integer    $timeout        Le temps maximal d'exécution (« 0 » par défaut)
		 * @param  integer    $connectTimeout Le temps maximal de connexion (« 0 » par défaut)
		 * @return WebRequest                 L'instance courante
		 */
		public function setTimeout($timeout = 0, $connectTimeout = 0) {
			$this->options[CURLOPT_TIMEOUT] = (int) $timeout;
			$this->options[CURLOPT_CONNECTTIMEOUT] = (int) $connectTimeout;

			return $this;
		}

		/**
		 * Indique qu'on doit rafraîchir la connexion à chaque requête
		 *
		 * @param  boolean    $fresh Vrai pour rafraîchir la connexion (vrai par défaut)
		 * @return WebRequest        L'instance courante
		 */
		public function setFresh($fresh = TRUE) {
			$this->options[CURLOPT_FORBID_REUSE] = $fresh;
			$this->options[CURLOPT_FRESH_CONNECT] = $fresh;

			return $this;
		}

		/**
		 * Indique le fichier dans lequel conserver les cookies générés par le serveur, le fichier peut ne pas exister
		 *
		 * @param  string     $file L'emplacement du fichier (« cookies.txt » par défaut à l'emplacement de la classe)
		 * @return WebRequest       L'instance courante
		 */
		public function setCookieFile($file = NULL) {

			// Si aucun fichier n'est spécifié, on utilise celui par défaut à l'emplacement de la classe
			if(NULL === $file) {
				$file = __DIR__.'/cookies.txt'; 
			}

			$this->options[CURLOPT_COOKIESESSION] = TRUE;
			$this->options[CURLOPT_COOKIEFILE] = $file;
			$this->options[CURLOPT_COOKIEJAR] = $file;

			return $this;
		}

		/**
		 * Indique les identifiants d'une authentification par méthode « basique »
		 *
		 * @param  string     $username Le nom d'utilisateur de l'authentification
		 * @param  string     $password Le mot de passe de l'authentification (vide par défaut)
		 * @return WebRequest           L'instance courante
		 */
		public function setBasicAuthentication($username, $password = '') {
			$this->options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
			$this->options[CURLOPT_USERPWD] = $username.':'.$password;

			return $this;
		}

		/**
		 * Indique les identifiants d'une authentification par méthode « digest »
		 *
		 * @param  string     $username Le nom d'utilisateur de l'authentification
		 * @param  string     $password Le mot de passe de l'authentification (vide par défaut)
		 * @return WebRequest           L'instance courante
		 */
		public function setDigestAuthentication($username, $password = '') {
			$this->options[CURLOPT_HTTPAUTH] = CURLAUTH_DIGEST;
			$this->options[CURLOPT_USERPWD] = $username.':'.$password;

			return $this;
		}

		/**
		 * Effectue une requête GET vers l'URL configurée avec les éventuelles données
		 *
		 * @return string Le contenu renvoyé par la requête
		 */
		public function get() {
			$url = $this->url;

			// Si on a des données, on les ajoute en paramètres à la fin de l'URL
			if(count($this->datas) > 0) {
				$parseUrl = parse_url($url);
				$url .= (empty($parseUrl['query']) ? '?' : '&').http_build_query($this->datas);
			}

			$this->send(
				$url,
				array(
					//CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPGET => TRUE
				)
			);
			return $this->response;
		}

		/**
		 * Effectue une requête HEAD vers l'URL configurée
		 *
		 * @return array Les entêtes renvoyés par la requête
		 */
		public function head() {
			$this->send(
				$this->url,
				array(
					//CURLOPT_CUSTOMREQUEST => 'HEAD',
					CURLOPT_NOBODY => TRUE
				)
			);
			return $this->responseHeaders;
		}

		/**
		 * Effectue une requête POST vers l'URL configurée avec les éventuelles données
		 *
		 * @return string Le contenu renvoyé par la requête
		 */
		public function post() {
			$options = array(
				//CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POST => TRUE
			);

			// Si on a des données on les ajoute en champs POST
			if(count($this->datas) > 0) {
				$options[CURLOPT_POSTFIELDS] = http_build_query($this->datas);

			// Sinon on retire l'entête de taille du contenu
			} else {
				unset($this->headers['Content-Length']);
			}

			$this->send($this->url, $options);
			return $this->response;
		}

		/**
		 * Effectue une requête PUT vers l'URL configurée avec les éventuelles données
		 *
		 * @return string Le contenu renvoyé par la requête
		 */
		public function put() {
			$options = array(
				CURLOPT_CUSTOMREQUEST => 'PUT',
				//CURLOPT_PUT => TRUE
			);

			// Si on a des données on les ajoute en champs POST
			if(count($this->datas) > 0) {
				$options[CURLOPT_POSTFIELDS] = http_build_query($this->datas);
			}

			// On ajoute un entête sur la taille des données
			$this->headers['Content-Length'] = strlen(http_build_query($this->datas));

			// On ajoute un entête pour simuler une requête PUT avec les serveurs qui ne l'acceptent pas
			$this->headers['X-HTTP-Method-Override'] = 'PUT';

			$this->send($this->url, $options);
			return $this->response;
		}

		/**
		 * Effectue une requête PUT vers l'URL configurée avec le fichier renseigné
		 *
		 * @param  string $file L'emplacement du fichier à envoyer
		 * @return string       Le contenu renvoyé par la requête
		 */
		public function putFile($file) {

			// Si le fichier n'existe pas, on lance une exception
			if(!is_file($file) || !is_readable($file)) {
				throw new InvalidArgumentException('"'.$file.'" is not a valid file or it cannot be read.');
			}

			// On ajoute un entête pour simuler une requête PUT avec les serveurs qui ne l'acceptent pas
			$this->headers['X-HTTP-Method-Override'] = 'PUT';

			$this->send(
				$this->url,
				array(
					//CURLOPT_CUSTOMREQUEST => 'PUT',
					CURLOPT_PUT => TRUE,
					CURLOPT_INFILE => fopen($file, 'r'),
					CURLOPT_INFILESIZE => filesize($file)
				)
			);
			return $this->response;
		}

		/**
		 * Effectue une requête DELETE vers l'URL configurée avec les éventuelles données
		 *
		 * @return string Le contenu renvoyé par la requête
		 */
		public function delete() {
			$options = array(
				CURLOPT_CUSTOMREQUEST => 'DELETE'
			);

			// Si on a des données on les ajoute en champs POST
			if(count($this->datas) > 0) {
				$options[CURLOPT_POSTFIELDS] = http_build_query($this->datas);
			}

			// On ajoute un entête pour simuler une requête DELETE avec les serveurs qui ne l'acceptent pas
			$this->headers['X-HTTP-Method-Override'] = 'DELETE';

			$this->send($this->url, $options);
			return $this->response;
		}

		/**
		 * Effectue une requête TRACE vers l'URL configurée
		 *
		 * @return string Le contenu renvoyé par la requête
		 */
		public function trace() {
			$this->send(
				$this->url,
				array(
					CURLOPT_CUSTOMREQUEST => 'TRACE'
				)
			);
			return $this->response;
		}

		/**
		 * Effectue une requête OPTIONS vers l'URL configurée
		 *
		 * @return array Les entêtes renvoyés par la requête
		 */
		public function options() {

			// On retire l'entête de taille du contenu
			unset($this->headers['Content-Length']);

			$this->send(
				$this->url,
				array(
					CURLOPT_CUSTOMREQUEST => 'OPTIONS'
				)
			);
			return $this->responseHeaders;
		}

		/**
		 * Effectue une requête CONNECT vers l'URL configurée
		 *
		 * @return boolean Vrai si la requête a réussie
		 */
		public function connect() {
			$options = array(
				CURLOPT_CUSTOMREQUEST => 'CONNECT'
			);

			// Seulement disponible avec PHP 5.5.0
			if(defined('CURLOPT_CONNECT_ONLY')) {
				$options[CURLOPT_CONNECT_ONLY] = TRUE;
			}

			return $this->send($this->url, $options);
		}

		/**
		 * Effectue une requête PATCH vers l'URL configurée
		 *
		 * @return string Le contenu renvoyé par la requête
		 */
		public function patch() {

			// On retire l'entête de taille du contenu
			unset($this->headers['Content-Length']);

			$this->send(
				$this->url,
				array(
					CURLOPT_CUSTOMREQUEST => 'PATCH'
				)
			);
			return $this->response;
		}

		/**
		 * Envoi la requête vers le serveur de l'URL
		 *
		 * @param  string $url     L'URL de la requête
		 * @param  array  $options Les options cURL personnalisées
		 * @return boolean         Vrai si la requête a réussie
		 */
		public function send($url, array $options = array()) {
			$options += $this->options;

			// On enregistre une méthode qui s'occupera de récupérer les entêtes
			$options[CURLOPT_HEADERFUNCTION] = array($this, 'headerHandler');

			$options[CURLOPT_URL] = $url;

			// Si on a des entêtes on les ajoute
			if(count($this->headers) > 0) {
				$options[CURLOPT_HTTPHEADER] = array_map(function($key, $value) {
					return $key.': '.$value;
				}, array_keys($this->headers), $this->headers);
			}

			// Si on a des cookies on les ajoute
			if(count($this->cookies) > 0) {
				$options[CURLOPT_COOKIE] = http_build_query($this->cookies, NULL, '; ');
			}

			// Préparation puis envoi de la requête
			$handler = curl_init();
			curl_setopt_array($handler, $options);
			$this->response = curl_exec($handler);
			$this->errorCode = curl_errno($handler);
			$this->errorMessage = curl_error($handler);

			// On supprime les données de la requête
			$this->datas = array();

			return 0 === $this->errorCode;
		}

		/**
		 * Envoi de plusieurs requêtes
		 *
		 * @param  array   $urls    Les URL des requêtes
		 * @param  array   $options Les options cURL personnalisées
		 * @return boolean          Vrai si toutes les requêtes ont réussis
		 */
		public function sendMulti(array $urls, array $options = array()) {
			$options = $this->options + $options;

			// On enregistre une méthode qui s'occupera de récupérer les entêtes
			$options[CURLOPT_HEADERFUNCTION] = array($this, 'headerMultiHandler');

			// Si on a des entêtes on les ajoute
			if(count($this->headers) > 0) {
				$options[CURLOPT_HTTPHEADER] = array_map(function($key, $value) {
					return $key.': '.$value;
				}, array_keys($this->headers), $this->headers);
			}

			// Si on a des cookies on les ajoute
			if(count($this->cookies) > 0) {
				$options[CURLOPT_COOKIE] = http_build_query($this->cookies, NULL, '; ');
			}

			// Préparation des chaînes selon les URLs et les options
			$handler = curl_multi_init();
			$this->channels = array();
			foreach($urls as $i => $url) {
				$this->channels[$i] = curl_init();
				$options[CURLOPT_URL] = $url;
				curl_setopt_array($this->channels[$i], $options);
				curl_multi_add_handle($handler, $this->channels[$i]);
			}

			// Exécution des requêtes
			$running = NULL;
			do {
				curl_multi_select($handler);
				curl_multi_exec($handler, $running);

				// Si une requête est achevée
				if(FALSE !== ($infos = curl_multi_info_read($handler))) {
					//if(CURLMSG_DONE === $infos['msg']) {

						// On cherche son numéro de chaîne pour enregistrer les données de la réponse
						foreach($this->channels as $i => $channel) {
							if($channel === $infos['handle']) {
								$this->response[$i] = curl_multi_getcontent($channel);
								$this->errorCode[$i] = $infos['result'];

								// « curl_multi_strerror » n'est disponible que depuis PHP 5.5.0
								if(version_compare(PHP_VERSION, '5.5.0', '>=')) {
									$this->errorMessage[$i] = 0 < $infos['result'] ? curl_multi_strerror($infos['result']) : '';
								} else {
									$this->errorMessage[$i] = 0 < $infos['result'] ? 'Error' : '';
								}

								curl_multi_remove_handle($handler, $channel);
								curl_close($channel);
							}
						}
					//}
				}

			// Tant qu'il en reste
			} while($running);

			curl_multi_close($handler);

			// On ordonne les tableaux de résultats
			ksort($this->errorCode);
			ksort($this->errorMessage);
			ksort($this->response);
			ksort($this->responseProtocol);
			ksort($this->responseStatus);
			ksort($this->responseStatusMessage);
			ksort($this->responseHeaders);

			// On supprime les données de la requête
			$this->datas = array();

			return 0 === array_sum($this->errorCode);
		}

		/**
		 * Méthode s'occupant de générer le tableau des entêtes, selon chaque ligne de l'entête
		 *
		 * @param  resource $handler La ressource cURL de la requête
		 * @param  string   $header  La chaîne de la ligne courante
		 * @return integer           La taille de la ligne courante
		 */
		protected function headerHandler($handler, $header) {

			// On trim pour retirer le retour à la ligne à la fin
			$line = trim($header);

			// Si la ligne commence par « HTTP/ » c'est celle indiquant le protocôle, le status et le message
			if(0 === strpos($line, 'HTTP/')) {
				list($this->responseProtocol, $this->responseStatus, $this->responseStatusMessage) = explode(' ', $line, 3);

			// Sinon c'est une ligne d'entête classique
			} else if(!empty($line)) {
				if(FALSE !== strpos($line, ': ')) {
					list($key, $value) = explode(': ', $line, 2);
				} else {
					$key = trim($line, ':');
					$value = '';
				}
				$this->responseHeaders[$key] = $value;
			}

			return strlen($header);
		}

		/**
		 * Méthode s'occupant de générer le tableau des entêtes à partir de requêtes multiples, selon chaque ligne de l'entête
		 *
		 * @param  resource $handler La ressource cURL de la requête courante
		 * @param  string   $header  La chaîne de la ligne courante
		 * @return integer           La taille de la ligne courante
		 */
		protected function headerMultiHandler($handler, $header) {

			// On détermine le numéro de la chaîne
			foreach($this->channels as $i => $channel) {
				if($channel === $handler) {

					// On trim pour retirer le retour à la ligne à la fin
					$line = trim($header);

					// Si la ligne commence par « HTTP/ » c'est celle indiquant le protocôle, le status et le message
					if(0 === strpos($line, 'HTTP/')) {
						list($this->responseProtocol[$i], $this->responseStatus[$i], $this->responseStatusMessage[$i]) = explode(' ', $line, 3);

					// Sinon c'est une ligne d'entête classique
					} else if(!empty($line)) {
						if(FALSE !== strpos($line, ': ')) {
							list($key, $value) = explode(': ', $line, 2);
						} else {
							$key = trim($line, ':');
							$value = '';
						}
						$this->responseHeaders[$i][$key] = $value;
					}
				}
			}

			return strlen($header);
		}

		/**
		 * Retourne l'URL de la requête
		 * 
		 * @return string L'URL de la requête
		 */
		public function getUrl() {
			return $this->url;
		}

		/**
		 * Retourne les données optionnelles de la requête
		 * 
		 * @return string Les données optionnelles de la requête
		 */
		public function getDatas() {
			return $this->datas;
		}

		/**
		 * Retourne les entêtes de la requête
		 * 
		 * @return string Les entêtes de la requête
		 */
		public function getHeaders() {
			return $this->headers;
		}

		/**
		 * Retourne les cookies de la requête
		 * 
		 * @return string Les cookies de la requête
		 */
		public function getCookies() {
			return $this->cookies;
		}

		/**
		 * Retourne le code d'erreur de la requête (« 0 » pour une requête réussie, peut être un tableau pour des requêtes multiples)
		 * 
		 * @return integer|array Le ou les codes d'erreur de requêtes
		 */
		public function getErrorCode() {
			return $this->errorCode;
		}

		/**
		 * Retourne le message d'erreur de la requête (vide pour une requête réussie, peut être un tableau pour des requêtes multiples)
		 * 
		 * @return string|array Le ou les messages d'erreur de requêtes
		 */
		public function getErrorMessage() {
			return $this->errorMessage;
		}

		/**
		 * Retourne le contenu de la réponse (peut être un tableau pour des requêtes multiples)
		 * 
		 * @return string|array Le ou les contenus de la réponse
		 */
		public function getResponse() {
			return $this->response;
		}

		/**
		 * Retourne le protocôle HTTP de la réponse (peut être un tableau pour des requêtes multiples)
		 * 
		 * @return string|array Le ou les protocoles HTTP de la réponse
		 */
		public function getResponseProtocol() {
			return $this->responseProtocol;
		}

		/**
		 * Retourne le status HTTP de la réponse (peut être un tableau pour des requêtes multiples)
		 * 
		 * @return integer|array Le ou les status HTTP de la réponse
		 */
		public function getResponseStatus() {
			return $this->responseStatus;
		}

		/**
		 * Retourne le message du status HTTP de la réponse (peut être un tableau pour des requêtes multiples)
		 * 
		 * @return string|array Le ou les messages des status HTTP de la réponse
		 */
		public function getResponseStatusMessage() {
			return $this->responseStatusMessage;
		}

		/**
		 * Retourne la valeur d'un entête de la réponse selon sa clé (requête seule uniquement)
		 * 
		 * @param  string $key La clé de l'entête de la réponse
		 * @return string      La valeur de l'entête de la réponse
		 */
		public function getResponseHeader($key) {
			if(isset($this->responseHeaders[$key]) || array_key_exists($key, $this->responseHeaders)) {
				return $this->responseHeaders[$key];
			}
		}

		/**
		 * Retourne les entêtes de la réponse (peut être un tableau pour des requêtes multiples)
		 * 
		 * @return array Les entêtes de la réponse
		 */
		public function getResponseHeaders() {
			return $this->responseHeaders;
		}
	}

	// On vérifie que l'extension est disponible
	if(!extension_loaded('curl')) {
		throw new SystemException('"%s" extension is not available', 'curl');
	}
?>