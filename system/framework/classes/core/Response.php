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
	 * Réponse d'une requête utilisateur
	 *
	 * La réponse est un contrôleur qui effectue une action selon la requête de l'utilisateur avant d'être envoyée.
	 *
	 * @package    framework
	 * @subpackage classes/core
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    25/01/2023
	 * @since      11/06/2015
	 */
	abstract class Response implements IController {
		/*
		 * CHANGELOG:
		 * 25/01/2023: Compatibilité avec PHP 8.0, « is_callable() » ne fonctionne plus avec une méthode non-statique
		 * 26/02/2016: Changement du rendu en adéquation avec le nouveau fonctionnement de la classe « Template »
		 * 11/11/2015: Possibilité de personnaliser le nom du template de rendu à utiliser
		 * 02/07/2015: Implémentation du forwarding
		 * 26/06/2015: Amélioration d'un point de vue conceptuel, et ajout de la gestion des headers HTTP
		 * 11/06/2015: Version initiale
		 */

		/**
		 * Tableau associant un message à un code d'erreur
		 *
		 * @var array
		 */
		private static $statusMessages = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Moved Temporarily',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-out',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Large',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Unsatifiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Time-out',
			505 => 'HTTP Version not supported',
		);

		/**
		 * Le contrôleur frontal, pour le forwarding
		 *
		 * @var FrontController
		 */
		private $frontController;

		/**
		 * L'objet représentant la requête
		 *
		 * @var Request
		 */
		private $request;

		/**
		 * Protocôle de la réponse [« HTTP/1.0 » par défaut]
		 *
		 * @var string
		 */
		protected $protocol = 'HTTP/1.0';

		/**
		 * Statut de la réponse [« 200 » par défaut]
		 *
		 * @var integer
		 */
		protected $status = 200;

		/**
		 * Message du statut de la réponse [« OK » par défaut]
		 *
		 * @var string
		 */
		protected $statusMessage = 'OK';

		/**
		 * Liste des headers de la réponse
		 *
		 * @var array
		 */
		protected $headers = array();

		/**
		 * Contenu de la réponse
		 *
		 * @var mixed
		 */
		protected $content = array();

		/**
		 * Constructeur de la réponse
		 *
		 * @param FrontController $frontController Le contrôleur frontal
		 */
		public function __construct(FrontController $frontController) {
			$this->frontController = $frontController;
			$this->request = $frontController->getRequest();
			$this->protocol = $_SERVER['SERVER_PROTOCOL'];
		}

		/**
		 * Envoi des headers HTTP
		 */
		protected final function sendHeaders() {

			// Si du contenu a déjà été envoyé, on ne peut plus envoyer les headers et on affiche une erreur
			if (headers_sent($file, $line)) {
				throw new CoreException('Unable to send HTTP headers because some content has already been sent from "%s" on line %s', path($file), $line);
			}

			// Header du protocôle, du status et du message HTTP
			header($this->protocol . ' ' . $this->status . ' ' . $this->statusMessage);

			// Ces deux headers ne sont pas compatibles
			if (NULL !== $this->getHeader('Transfer-Encoding')) {
				$this->removeHeader('Content-Length');
			}

			// Si on est en HTTP 1.0, le header « Cache-Control » n'existe pas et on utilise l'équivalent
			if ('HTTP/1.0' === $this->protocol && 'no-cache' === $this->getHeader('Cache-Control')) {
				$this->setHeader('Pragma', 'no-cache');
				$this->setHeader('Expires', 0);
			}

			// Si on a au moins un header personnalisé on les ajoute
			if (0 < count($this->headers)) {

				// Drapeau renseignant si on stop après l'envoi des headers ou non
				$exitFlag = FALSE;
				foreach ($this->headers as $key => $value) {

					// On convertit les clés en les affichant proprement, avec une majuscule
					$key = implode('-', array_map('ucfirst', explode('-', $key)));

					// Si ce n'est pas un tableau, on n'a qu'une seule ligne à ajouter
					if (!is_array($value)) {
						header($key . ': ' . $value, TRUE, $this->status);

					// Sinon on ajoute chacune des lignes avec la même clé
					} else {
						foreach ($value as $element) {
							header($key . ': ' . $element, FALSE, $this->status);
						}
					}

					// Si c'est une redirection on change le drapeau
					if (0 === strcasecmp($key, 'Location')) {
						$exitFlag = TRUE;
					}
				}

				// Si le drapeau est vrai, on arrête l'exécution pour envoyer la réponse
				if ($exitFlag) {
					exit;
				}
			}
		}

		/**
		 * Envoi du contenu
		 */
		protected final function sendContent() {

			// On affiche simplement le contenu s'il n'est pas vide, ce dernier va alors être envoyé au client
			if (!empty($this->content)) {
				echo implode(PHP_EOL, $this->content);
			}
		}

		/**
		 * Envoi de la réponse à l'utilisateur
		 */
		public function send() {

			// Par défaut, on envoi à la fin les headers et le contenu
			$this->sendHeaders();
			$this->sendContent();
		}

		/**
		 * Forwarding, redirection interne vers un autre contrôleur sans que l'utilisateur ne reformule une autre requête
		 *
		 * @param string $controllerName Le nom du nouveau contrôleur [« home » par défaut]
		 * @param string $actionName     Le nom de l'action du nouveau contrôleur [« index » par défaut]
		 * @param string $controllerType Le type du nouveau contrôleur [« page » par défaut]
		 */
		public final function forward($controllerName = 'home', $actionName = 'index', $controllerType = 'page') {
			$this->frontController->forward(self::controllerNameToClass($controllerName, $controllerType), self::actionNameToMethod($actionName));
		}

		/**
		 * Redirection, on informe le client qui va reformer une nouvelle requête
		 *
		 * @param string  $location Le nouvel emplacement de la requête, peut être une URL ou un emplacement de l'application
		 * @param integer $status   Le statut de la requête courante, « 301 » correspond à une redirection permanent et « 302 » à une redirection temporaire [« 301 » par défaut]
		 */
		public final function redirect($location, $status = 301) {

			// Si ce n'est pas une URL, on ajoute la base du site devant l'emplacement
			if (!preg_match('/(http|https):\/\/(.*?)$/i', $location)) {
				$location = url($location);
			}

			// On ajoute un header pour l'emplacement, et pour le statut HTTP
			$this->setHeader('Location', $location);
			$this->setStatus($status);
		}

		/**
		 * Génération d'un rendu comme une page, le contenu de la requête contiendra le rendu du template principal avec un titre et un contenu personnalisé
		 *
		 * @param string       $title        Le titre du rendu
		 * @param array|string $content      Le contenu du rendu
		 * @param string       $templateName Le nom du template du rendu [« main » par défaut]
		 */
		public final function render($title, $content, $templateName = 'main') {

			// Le contenu est du texte, on l'encode en UTF-8
			$this->setHeader('Content-Type', 'text/html; charset=utf-8');

			// Création du template
			$template = new Template($templateName);

			// Variables de la requête accessibles dans le template de la page
			$template->bindArray($this->request->getInfos());

			// Si on utilise le module multilingue, on ajoute la langue utilisée ainsi qu'un tableau de l'ensemble des langues proposées
			if (USE_LANGUAGE) {
				$language = Service::language();
				$template->bindArray(
					array(
						'language'  => $language->getLanguage(),
						'languages' => $language->getLanguages(),
					)
				);

			// Sinon on se contente de la langue par défaut du site
			} else {
				$template->bind('language', DEFAULT_LANGUAGE);
			}

			// On ajoute enfin le titre de la page ainsi que son contenu, ce dernier n'étant pas échappé
			$template
				->bind('title', $title)
				->bindHtml('content', implode(PHP_EOL, (array) $content));

			// On retourne le rendu généré par le template
			$this->setContent($template->render());
		}

		/**
		 * Retourne la requête
		 *
		 * @return Request La requête
		 */
		public function getRequest() {
			return $this->request;
		}

		/**
		 * Retourne le protocôle de la réponse
		 *
		 * @return string Le protocôle de la réponse
		 */
		public function getProtocol() {
			return $this->protocol;
		}

		/**
		 * Modifie le protocôle de la réponse
		 *
		 * @param  string   $protocol Le nouveau protocôle de la réponse
		 * @return Response           L'instance courante
		 */
		public function setProtocol($protocol) {

			// Le protocôle doit être une chaîne de la forme « HTTP/x.x »
			if (!preg_match('/^HTTP\/\d\.\d$/', $protocol)) {
				throw new InvalidParameterException('The value "%s" is not valid for the HTTP protocol, it must match "HTTP/x.x"', $protocol);
			}

			$this->protocol = $protocol;
			return $this;
		}

		/**
		 * Retourne le statut de la réponse
		 *
		 * @return integer Le statut de la réponse
		 */
		public function getStatus() {
			return $this->status;
		}

		/**
		 * Retourne le message du statut de la réponse
		 *
		 * @return string Le message du statut de la réponse
		 */
		public function getStatusMessage() {
			return $this->statusMessage;
		}

		/**
		 * Modifie le statut de la réponse
		 *
		 * @param  integer  $status        Le nouveau statut de la réponse
		 * @param  string   $statusMessage Le nouveau message du statut de la réponse
		 * @return Response                L'instance courante
		 */
		public function setStatus($status, $statusMessage = NULL) {
			$status = (int) $status;

			// Pour être valide il doit être entre 100 et 599 inclus
			if (100 > $status || 600 <= $status) {
				throw new InvalidParameterException('The value "%s" is not valid for the HTTP status, it must be between 100 and 599', $status);
			}

			$this->status = $status;

			// Si le message du contenu n'est pas renseigné
			if (NULL === $statusMessage) {

				// Si le statut n'est pas dans le tableau de la classe, alors on lance une erreur demandant à en mettre un personnalisé
				if (!isset(self::$statusMessages[$status])) {
					throw new InvalidParameterException('A custom status message is expected for the "%s" HTTP status', $status);
				}

				$this->statusMessage = self::$statusMessages[$status];

			// Sinon si le message du statut est renseigné
			} else {
				$this->statusMessage = $statusMessage;
			}

			return $this;
		}

		/**
		 * Retourne le header correspondant à la clé renseignée
		 *
		 * @param  string $key La clé du header à récupérer
		 * @return string      Le contenu du header qui correspond à la clé
		 */
		public function getHeader($key) {

			// Les clés sont stockées en minuscules
			$key = strtolower($key);

			// Si elle existe bel et bien, on retourne le contenu associé
			if (isset($this->headers[$key]) || array_key_exists($key, $this->headers)) {
				return $this->headers[$key];
			}
		}

		/**
		 * Modifie ou ajoute un header
		 *
		 * @param  string   $key   La clé du header à modifier ou ajouter
		 * @param  string   $value Le contenu du header associé à la clé
		 * @return Response        L'instance courante
		 */
		public function setHeader($key, $value) {

			// On enregistre la clé en miniscules, pour éviter les doublons
			$key = strtolower($key);

			// Si un header a déjà été ajouté avec cette clé
			if (isset($this->headers[$key]) || array_key_exists($key, $this->headers)) {

				// Si la clé n'est pas associée à un tableau, c'est qu'on a rentré qu'un seul élément et donc on le transforme en liste pour contenir plusieurs éléments
				if (!is_array($this->headers[$key])) {
					$this->headers[$key] = array($this->headers[$key]);
				}

				// Puis on ajoute le nouvel élément dans la liste
				$this->headers[$key][] = $value;

			// Sinon on ajoute simplement l'entrée
			} else {
				$this->headers[$key] = $value;
			}

			return $this;
		}

		/**
		 * Retire un header
		 *
		 * @param  string   $key      La clé du header à retirer
		 * @param  boolean  $onlyLast Si vrai, on ne retire que la dernière entrée ajoutée, sinon on les retire toutes [« FALSE » par défaut]
		 * @return Response           L'instance courante
		 */
		public function removeHeader($key, $onlyLast = FALSE) {

			// Les clés sont stockées en minuscules
			$key = strtolower($key);

			// Si on ne retire que la dernière entrée, et qu'au moins une a été ajouté, et si c'est une liste à plusieurs éléments
			if ($onlyLast && (isset($this->headers[$key]) || array_key_exists($key, $this->headers)) && is_array($this->headers[$key]) && 1 < count($this->headers[$key])) {

				// On dépile la liste
				array_pop($this->headers[$key]);

			// Sinon on supprime le ou les headers qu'il correspondent, que se soit une liste ou non
			} else {
				unset($this->headers[$key]);
			}

			return $this;
		}

		/**
		 * Retourne le contenu de la réponse
		 *
		 * @return array Le contenu de la réponse
		 */
		public function getContent() {
			return $this->content;
		}

		/**
		 * Modifie le contenu de la réponse
		 *
		 * @param  array|string $content Le nouveau contenu de la réponse
		 * @return Response              L'instance courante
		 */
		public function setContent($content) {
			$this->content = (array) $content;
			return $this;
		}

		/**
		 * Ajoute du contenu à la réponse
		 *
		 * @param  string   $content Le contenu à ajouter à la réponse
		 * @return Response          L'instance courante
		 */
		public function addContent($content = '') {
			$this->content[] = $content;
			return $this;
		}

		/**
		 * Convertit un nom et un type de contrôleur en son nom de classe logique
		 *
		 * @param  string $name Le nom du contrôleur
		 * @param  string $type Le type du contrôleur [optionnel]
		 * @return string       Le nom de la classe du contrôleur
		 */
		public static final function controllerNameToClass($name, $type = '') {

			// Conversion en « StudlyCaps »
			//return preg_replace('/(?:^|-)(.?)/e', 'strtoupper("$1")', trim($name . '-' . $type, '-'));
			return preg_replace_callback('/(?:^|-)(.?)/', function($matches) {
				return strtoupper($matches[1]);
			}, trim($name . '-' . $type, '-'));
		}

		/**
		 * Convertit une classe de contrôleur en son nom
		 *
		 * @param  string $class Le nom de la classe du contrôleur
		 * @return string        Le nom du contrôleur
		 */
		public static final function controllerClassToName($class) {

			// Conversion depuis du « StudlyCaps »
			return strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1-$2', $class));
		}

		/**
		 * Convertit un nom d'action d'un contrôleur en son nom de méthode logique
		 *
		 * @param  string $name Le nom de l'action du contrôleur
		 * @return string       Le nom de la méthode de l'action du contrôleur
		 */
		public static final function actionNameToMethod($name) {

			// Conversion en « camelCase »
			//return preg_replace('/-(.?)/e', 'strtoupper("$1")', trim($name, '-')) . 'Action';
			return preg_replace_callback('/-(.?)/', function($matches) {
				return strtoupper($matches[1]);
			}, trim($name, '-')) . 'Action';
		}

		/**
		 * Indique si le contrôleur d'une réponse est correct selon son nom de classe
		 *
		 * @param  string  $class Le nom de la classe du contrôleur
		 * @return boolean        Vrai si le contrôleur de la réponse est correct de par son nom
		 */
		public static final function isControllerClass($class) {
			if (!class_exists($class)) {
				return FALSE;
			}
			$reflectionClass = new ReflectionClass($class);
			return $reflectionClass->isSubclassOf('Response') && $reflectionClass->isInstantiable();
		}

		/**
		 * Indique si le contrôleur d'une réponse est correct selon son nom de classe et son type
		 *
		 * @param  string  $class     Le nom de la classe du contrôleur
		 * @param  string  $typeClass Le nom de la classe du type du contrôleur
		 * @return boolean            Vrai si le contrôleur de la réponse est correct de par son nom et son type
		 */
		public static final function isControllerClassWithType($class, $typeClass = NULL) {
			return self::isControllerClass($class) && $typeClass === $class::getTypeName();
		}

		/**
		 * Indique si l'action d'un contrôleur d'une réponse est correcte selon son nom de méthode et le nom de la classe du contrôleur
		 *
		 * @param  string  $method          Le nom de la méthode de l'action du contrôleur
		 * @param  string  $controllerClass Le nom de la classe du contrôleur
		 * @return boolean                  Vrai si l'action du contrôleur de la réponse est correcte
		 */
		public static final function isActionMethod($method, $controllerClass) {
			return method_exists($controllerClass, $method);
		}

		/**
		 * Indique le nom public du type de réponse
		 *
		 * @return string Le nom public du type de réponse
		 */
		public static function getTypeName() {
			return get_called_class();
		}
	}
?>