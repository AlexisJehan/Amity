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
	 * Exception du système
	 *
	 * Exception personnalisée pouvant être provoquée par le système du framework.
	 *
	 * @package    framework
	 * @subpackage classes/exceptions
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    31/07/2015
	 * @since      04/10/2014
	 */
	class SystemException extends Exception {
		/*
		 * CHANGELOG:
		 * 31/07/2015: Suppression de la journalisation désormais gérée par le débogueur
		 * 20/07/2015: Changement du fonctionnement de l'exception du système, avec gestion interne de la traduction et du binding
		 * 02/07/2015: Ajout du nom de l'exception
		 * 08/06/2015: Changement du fonctionnement des exceptions personnalisées avec une méthode « log() » isolée
		 * 04/10/2014: Version initiale
		 */

		/**
		 * Constructeur de l'exception système
		 *
		 * @param string $message Le message descrivant l'exception, peut être complété par d'autres arguments à la manière de « sprintf() »
		 */
		public function __construct($message) {

			// Tentative de traduction du message
			$message = call_user_func_array('__', func_get_args());

			// Appel au constructeur parent, avec le message éventuellement modifié
			parent::__construct($message);
		}
	}
?>