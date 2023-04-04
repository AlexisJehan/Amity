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
	 * Contrôleur frontal
	 *
	 * Le contrôleur frontal se charge d'instancier la réponse à une requête d'un utilisateur.
	 *
	 * @package    framework
	 * @subpackage classes/core
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    02/07/2015
	 * @since      05/05/2015
	 */
	class FrontController implements IController {
		/*
		 * CHANGELOG:
		 * 02/07/2015: Implémentation de l'interface « IController », et support du forwarding
		 * 26/06/2015: Les arguments d'URL sont désormais envoyés à l'action du contrôleur
		 * 11/06/2015: L'analyse de la requête se fait désormais dans une classe spécifique
		 * 05/05/2015: Version initiale
		 */

		/**
		 * La requête, générée à partir des données envoyées d'un utilisateur
		 *
		 * @var Request
		 */
		protected $request;

		/**
		 * La réponse, spécifique à la requête et qui génère le contenu renvoyé à l'utilisateur
		 *
		 * @var Response
		 */
		protected $response;

		/**
		 * Pile de l'ensemble des forwarding effectués, pour prévenir d'un éventuel forwarding mutuel ou d'une boucle
		 *
		 * @var array
		 */
		protected $forwards = array();

		/**
		 * Constructeur du contrôleur frontal
		 *
		 * @param string|array $route La route personnalisée, soit sous forme d'URL ou de pile [optionnelle]
		 */
		public function __construct($route = NULL) {

			// Instanciation de la requête, cette dernière n'est pas amène de changer
			$this->request = new Request($route);
		}

		/**
		 * Envoi de la réponse au client
		 */
		public function run() {
			$this->indexAction();
			$this->response->send();
		}

		/**
		 * Action par défaut, il s'agit du comportement normal de l'application
		 */
		public function indexAction() {

			// Si la maintenance est activée, on forward vers l'erreur 503
			if (MAINTENANCE) {
				$this->forward('Error503Page');

			// Sinon si on utilise la base de données mais qu'elle n'est pas accessible, on affiche l'erreur 500
			} else if (USE_DATABASE && !Service::database()->isConnected()) {
				$this->forward('Error500Page');

			// Sinon on utilise le contrôleur normalement
			} else {
				$this->forward($this->request->getControllerClass(), $this->request->getActionMethod());
			}
		}

		/**
		 * {@inheritdoc}
		 *
		 * @param string $controllerClass La classe du contrôleur vers lequel s'orienter
		 * @param string $actionMethod    La méthode de l'action du nouveau contrôleur
		 * @param array  $args            Les arguments de l'action [si non renseignés alors correspond aux arguments de la requête]
		 */
		public final function forward($controllerClass = 'HomePage', $actionMethod = 'indexAction', array $args = NULL) {

			// Si des arguments spécifiques ne sont pas renseignés, on récupère ceux de la requête
			if (NULL === $args) {
				$args = $this->request->getArgs();
			}

			// On crée une référence à partir du contrôleur et de l'action
			$reference = $controllerClass . ':' . $actionMethod;

			// Si celle-ci est dans notre pile, alors c'est qu'un forward a déjà été effectué vers cette action de ce contrôleur
			if (in_array($reference, $this->forwards)) {
				throw new CoreException('Responses forwards loop detected, unable to handle the request');
			}

			// Sinon on l'ajoute à la pile pour une éventuelle future vérification
			$this->forwards[] = $reference;

			// Instanciation de la réponse selon le contrôleur, puis exécution de l'action
			$this->response = new $controllerClass($this);
			$this->response->$actionMethod($args);
		}

		/**
		 * Retourne la requête de l'application
		 *
		 * @return Request La requête de l'application
		 */
		public final function getRequest() {
			return $this->request;
		}

		/**
		 * Retourne la réponse de l'application
		 *
		 * @return Response La réponse de l'application
		 */
		public final function getResponse() {
			return $this->response;
		}
	}
?>