<?php
	/*
	 * MIT License
	 *
	 * Copyright (c) 2017-2024 Alexis Jehan
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
	 * Interface d'un contrôleur
	 *
	 * Un contrôleur peut proposer un ensemble d'actions via des méthodes dont le nom se termine par « Action ».
	 * Parmi ces méthodes, le contrôleur se doit d'implémenter au minimum l'action principale « indexAction() ».
	 * Il peut aussi proposer une méthode de forward vers un contrôleur différent.
	 *
	 * @package    framework
	 * @subpackage classes/core
	 * @version    30/07/2015
	 * @since      05/05/2015
	 */
	interface IController {
		/*
		 * CHANGELOG:
		 * 30/07/2015: Changement de « Controller » à « IController »
		 * 02/07/2015: Ajout du forwarding
		 * 05/05/2015: Version initiale
		 */

		/**
		 * Action principale et par défaut du contrôleur
		 */
		public function indexAction();

		/**
		 * Forwarding vers l'action d'un autre contrôleur
		 *
		 * @param string $controllerClass La classe du contrôleur vers lequel s'orienter
		 * @param string $actionMethod    La méthode de l'action du nouveau contrôleur
		 */
		public function forward($controllerClass, $actionMethod);
	}
?>