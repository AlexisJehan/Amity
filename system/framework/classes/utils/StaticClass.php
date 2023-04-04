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
	 * Classe statique
	 *
	 * Une classe statique ne peut pas être instanciée, « StaticClass » assure cela et doit donc être étendue par les classes dites statiques.
	 * Contrairement à Java, en PHP il n'est pas possible de créer une classe statique avec le mot clef associé, cette présente classe comble ainsi ce manque.
	 *
	 * @package    framework
	 * @subpackage classes/utils
	 * @version    25/01/2023
	 * @since      27/07/2015
	 */
	abstract class StaticClass {
		/*
		 * CHANGELOG:
	 	 * 25/01/2023: Compatibilité avec PHP 8.0, « __clone() » et « __wakeup() » ne sont plus surchargés
		 * 27/07/2015: Version initiale
		 */

		/**
		 * Le constructeur n'est pas disponible
		 */
		private final function __construct() {
			// Vide
		}
	}
?>