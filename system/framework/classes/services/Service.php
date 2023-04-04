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
	 * Fournisseur de services
	 *
	 * Cette classe permet d'enregistrer ou de récupérer un service.
	 *
	 * @package    framework
	 * @subpackage classes/services
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    27/07/2015
	 * @since      22/10/2014
	 */
	final class Service extends StaticClass {
		/*
		 * CHANGELOG:
		 * 27/07/2015: Changement de « Context » à « Service », et accessibilité désormais en statique plutôt qu'en singleton
		 * 22/10/2014: Version initiale
		 */

		/**
		 * Ensemble des services enregistrés
		 *
		 * @var array
		 */
		private static $services = array();

		/**
		 * Indique si un service est actuellement enregistré
		 *
		 * @param  string  $name Le nom du service
		 * @return boolean       Vrai si le service est enregistré
		 */
		public static function is($name) {
			return isset(self::$services[$name]);
		}

		/**
		 * Enregistre un service
		 *
		 * @param  string   $name    Le nom du service
		 * @param  IService $service Le service à enregistrer
		 * @return IService          Le service enregistré
		 */
		public static function set($name, IService $service) {

			// Si le service est déjà enregistré
			if (isset(self::$services[$name])) {
				throw new ServiceException('The service "%s" has already been initialised', $name);
			}

			return self::$services[$name] = $service;
		}

		/**
		 * Récupère un service
		 *
		 * @param  string   $name Le nom du service
		 * @return IService       Le service récupéré
		 */
		public static function get($name) {

			// Si aucun service n'est enregistré à ce nom
			if (!isset(self::$services[$name])) {
				throw new ServiceException('The service "%s" has not been created', $name);
			}

			return self::$services[$name];
		}

		/**
		 * Surcharge magique pour enregistrer / récupérer directement le service via une méthode à son nom selon si un paramètre est renseigné
		 *
		 * @param  string   $name      Le nom du service
		 * @param  array    $arguments Si renseigné, on enregistre ce service
		 * @return IService            Le service enregistré ou récupéré
		 */
		public static function __callStatic($name, $arguments) {

			// S'il n'y a pas d'argument, on tente de récupérer le service
			if (empty($arguments)) {
				return self::get($name);

			// Sinon on tente de l'enregistrer avec le premier argument
			} else {
				return self::set($name, $arguments[0]);
			}
		}
	}
?>