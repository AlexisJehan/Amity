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
	 * Requête de l'utilisateur
	 *
	 * La requête est générée selon une route qui peut être récupérée de l'URL ou renseignée manuellement.
	 * Cette route va être analysée afin d'en déduire le contrôleur et l'action associés.
	 *
	 * @package    framework
	 * @subpackage classes/core
	 * @version    12/04/2023
	 * @since      05/05/2015
	 */
	class Request {
		/*
		 * CHANGELOG:
		 * 12/04/2023: Amélioration de la compatibilité en mode « CLI »
		 * 02/08/2021: Possibilité de forcer l'usage de HTTPS
		 * 29/12/2015: Correction d'une exception pouvant se produire lors de la présence de deux slashs d'affilé dans l'URL
		 * 26/07/2015: Compatibilité avec la nouvelle méthode « getTypeName() » qui indique le nom public du type du contrôleur
		 * 23/07/2015: Décodage des arguments de l'URL
		 * 18/07/2015: Gestion de la requête « favicon.ico » plutôt que de produire une erreur 404
		 * 06/07/2015: Amélioration de la détection du contrôleur, notamment en ajoutant plus de possibilités d'URL
		 * 02/07/2015: Utilisation de méthodes statiques de la classe « Response » pour créer la classe et la méthode selon leurs noms, et de vérifier s'ils existent
		 * 22/06/2015: Possibilité de renseigner la requête en tant que paramètre plutôt que de la récupérer de l'URI, et ajout d'une méthode de retour d'informations
		 * 11/06/2015: Création de la classe « Request » plutôt que d'effectuer le traitement directement dans la classe « FrontController »
		 * 31/05/2015: Correction d'un bug se produisant avec un paramètre GET sans contrôleur spécifié (Erreur 404 au lieu de la page d'accueil attendue)
		 * 23/05/2015: Ajout de l'URL en tant que paramètre du document
		 * 18/05/2015: Mise à jour de la gestion de la langue
		 * 14/05/2015: Ajout de la supposition du type du contrôleur, qui assure la compatibilité avec n'importe quel type de document
		 * 05/05/2015: Version initiale
		 */

		/**
		 * L'URL de la requête
		 *
		 * @var string
		 */
		protected $url;

		/**
		 * Le type du contrôleur de la réponse [« page » par défaut]
		 *
		 * @var string
		 */
		protected $controllerType = 'page';

		/**
		 * Le nom du contrôleur de la réponse [« home » par défaut]
		 *
		 * @var string
		 */
		protected $controllerName = 'home';

		/**
		 * La classe du contrôleur de la réponse [« HomePage » par défaut]
		 *
		 * @var string
		 */
		protected $controllerClass = 'HomePage';

		/**
		 * Le nom de l'action de la réponse [« index » par défaut]
		 *
		 * @var string
		 */
		protected $actionName = 'index';

		/**
		 * La méthode de l'action de la réponse [« indexAction » par défaut]
		 *
		 * @var string
		 */
		protected $actionMethod = 'indexAction';

		/**
		 * Les arguments éventuels
		 *
		 * @var array
		 */
		protected $args;

		/**
		 * Constructeur de la requête
		 *
		 * @param string|array $route La route personnalisée, soit sous forme d'URL ou de pile [optionnelle]
		 */
		public function __construct($route = NULL) {

			// Si la route n'est pas une pile il faut la générer
			if (!is_array($route)) {

				// Si la route est vide, on la récupère de la variable globale du serveur
				if (NULL === $route) {

					// Si « REDIRECT_URL » est définie et valide on l'utilise pour récupérer les arguments
					if (isset($_SERVER['REDIRECT_URL']) && '/index.php' !== $_SERVER['REDIRECT_URL']) {
						$route = $_SERVER['REDIRECT_URL'];

					// Sinon on les récupère depuis le classique « REQUEST_URI »
					} else if (isset($_SERVER['REQUEST_URI'])) {
						$route = $_SERVER['REQUEST_URI'];

					} else {
						$route = '';
					}
				}

				// On retire les éventuels paramètres GET de l'URL (ils peuvent être présents dans « REQUEST_URI »)
				$route = strstr($route, '?', TRUE) ?: $route;

				// On retire l'éventuelle base du site
				$base = dirname($_SERVER['SCRIPT_NAME']);
				if (0 === strpos($route, $base)) {
					$route = substr($route, strlen($base));
				}

				// On enlève les slashs et anti-slashs au début et à la fin, et on met en minuscule
				$route = trim($route, '\\/');

				// Enfin on crée une pile puis en retire les étages vides avant de fixer les indices
				$route = array_values(array_filter(explode('/', $route)));
			}

			// On crée l'URL associée à la requête
			$this->url = BASE_URL . (!empty($route) ? '/' . implode('/', $route) : '');

			// Si on est en HTTP et qu'on veut forcer HTTPS, on redirige
			if (FORCE_HTTPS && 0 === strpos($this->url, 'http:')) {
				header('Location: https:' . substr($this->url, 5));
				exit;
			}

			// Si la route n'est pas vide
			if (!empty($route)) {

				// Si la requête est « favicon.ico », on arrête immédiatement
				if ('favicon.ico' === $route[0]) {
					header('Content-Type: image/vnd.microsoft.icon');
					header('Content-Length: 0');
					exit;
				}

				// Extraction de la langue si renseignée et disponible à la traduction
				if (USE_LANGUAGE) {
					$language = Service::language();
					if (in_array($route[0], $language->getLanguages())) {
						$language->setLanguage($route[0]);
						array_shift($route);
					}
				}
			}

			// Si la route n'est toujours pas vide on l'analyse pour rechercher le contrôleur
			if (!empty($route)) {
				$foundFlag = TRUE;

				$controllerName = strtolower($route[0]);
				$controllerClassWithType = Response::controllerNameToClass($controllerName, $this->controllerType);
				$controllerClass = Response::controllerNameToClass($controllerName);

				// Si le nom correspond à un contrôleur de type par défaut et que ce dernier n'est pas renseigné
				// Exemple: /home -> HomePage extends Page
				if (Response::isControllerClass($controllerClassWithType)) {
					$this->controllerName = $controllerName;
					$this->controllerClass = $controllerClassWithType;
					array_shift($route);

				// Sinon, si le nom correspond directement à la classe d'un contrôleur
				// Exemples: /home-page -> HomePage extends Page
				//           /move      -> Move     extends Redirect
				} else if (Response::isControllerClass($controllerClass)) {
					$this->controllerType = Response::controllerClassToName($controllerClass::getTypeName());

					// On retire le type du nom du contrôleur si on le trouve à la fin
					if ($this->controllerType === substr($controllerName, -strlen($this->controllerType))) {
						$this->controllerName = rtrim(substr($controllerName, 0, strlen($controllerName) - strlen($this->controllerType)), '-');
					} else {
						$this->controllerName = $controllerName;
					}

					$this->controllerClass = $controllerClass;
					array_shift($route);

				// Sinon s'il y a au moins deux arguments, c'est peut être un type suivi d'un nom
				} else if (2 <= count($route)) {
					$controllerType = strtolower($route[0]);
					$controllerName = strtolower($route[1]);
					$controllerClassWithType = Response::controllerNameToClass($controllerName, $controllerType);
					$controllerClass = Response::controllerNameToClass($controllerName);

					// Si c'est le type, suivi du nom sans le type à la fin
					// Exemple: /page/home -> HomePage extends Page
					if (Response::isControllerClass($controllerClassWithType)) {
						$this->controllerType = $controllerType;
						$this->controllerName = $controllerName;
						$this->controllerClass = $controllerClassWithType;
						array_shift($route);
						array_shift($route);

					// Sinon, si c'est le type suivi du nom, avec éventuellement le type à la fin du nom
					// Exemples: /page/home-page -> HomePage extends Page
					//           /redirect/move  -> Move     extends Redirect
					} else if (Response::isControllerClassWithType($controllerClass, Response::controllerNameToClass($controllerType))) {
						$this->controllerType = $controllerType;
						$this->controllerName = $controllerName;
						$this->controllerClass = $controllerClass;
						array_shift($route);
						array_shift($route);
					} else {
						$foundFlag = FALSE;
					}
				} else {
					$foundFlag = FALSE;
				}
			} else {
				$foundFlag = FALSE;
			}

			// S'il y a encore au moins un élément, on peut supposer qu'il s'agisse de l'action du contrôleur déterminé
			if (!empty($route)) {
				$actionName = strtolower($route[0]);
				$actionMethod = Response::actionNameToMethod($actionName);

				// Si c'est effectivement une action qui correspond au contrôleur trouvé plus haut
				if (Response::isActionMethod($actionMethod, $this->controllerClass)) {
					$this->actionName = $actionName;
					$this->actionMethod = $actionMethod;
					array_shift($route);

				// Si ce n'est pas une méthode
				} else {

					// Si aucun contrôleur n'avait été trouvé, on affichera l'erreur 404
					if (!$foundFlag) {
						$this->controllerType = 'page';
						$this->controllerName = 'error404';
						$this->controllerClass = 'Error404Page';
					}
				}
			}

			// Les éléments éventuels restants sont des arguments, on les décode
			$this->args = array_map('urldecode', $route);
		}

		/**
		 * Indique si la requête est de type GET
		 *
		 * @return boolean Vrai si la requête est de type GET
		 */
		public final function isGet() {
			return 'GET' === $_SERVER['REQUEST_METHOD'];
		}

		/**
		 * Indique si la requête est de type POST
		 *
		 * @return boolean Vrai si la requête est de type POST
		 */
		public final function isPost() {
			return 'POST' === $_SERVER['REQUEST_METHOD'];
		}

		/**
		 * Retourne un tableau d'informations sur la requête
		 *
		 * @return array Le tableau d'informations
		 */
		public final function getInfos() {
			return array(
				'url'        => $this->url,
				'type'       => $this->controllerType,
				'controller' => $this->controllerName,
				'class'      => $this->controllerClass,
				'action'     => $this->actionName,
				'method'     => $this->actionMethod,
				'args'       => $this->args,
			);
		}

		/**
		 * Retourne l'URL de la requête
		 *
		 * @return string L'URL de la requête
		 */
		public final function getUrl() {
			return $this->url;
		}

		/**
		 * Retourne le type du contrôleur de la requête
		 *
		 * @return string Le type du contrôleur de la requête
		 */
		public final function getControllerType() {
			return $this->controllerType;
		}

		/**
		 * Retourne le nom du contrôleur de la requête
		 *
		 * @return string Le nom du contrôleur de la requête
		 */
		public final function getControllerName() {
			return $this->controllerName;
		}

		/**
		 * Retourne le nom de la classe du contrôleur de la requête
		 *
		 * @return string Le nom de la classe du contrôleur de la requête
		 */
		public final function getControllerClass() {
			return $this->controllerClass;
		}

		/**
		 * Retourne le nom de l'action du contrôleur de la requête
		 *
		 * @return string Le nom de l'action du contrôleur de la requête
		 */
		public final function getActionName() {
			return $this->actionName;
		}

		/**
		 * Retourne le nom de la méthode de l'action du contrôleur de la requête
		 *
		 * @return string Le nom de la méthode de l'action du contrôleur de la requête
		 */
		public final function getActionMethod() {
			return $this->actionMethod;
		}

		/**
		 * Retourne les arguments éventuels de la requête
		 *
		 * @return string Les arguments éventuels de la requête
		 */
		public final function getArgs() {
			return $this->args;
		}
	}
?>