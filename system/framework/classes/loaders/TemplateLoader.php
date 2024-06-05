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
	 * Classe de chargement des templates
	 *
	 * Permet de charger les templates de l'application dans les dossiers dédiés.
	 *
	 * @package    framework
	 * @subpackage classes/loaders
	 * @version    18/03/2016
	 * @since      12/01/2015
	 */
	final class TemplateLoader extends AbstractLoader {
		/*
		 * CHANGELOG:
		 * 18/03/2016: Changement de l'extension de « tpl.php » en « phtml »
		 * 29/07/2015: Compatibilité avec la nouvelle implémentation de « AbstractLoader »
		 * 27/03/2015: Changement de l'extension de « php » en « tpl.php »
		 * 12/01/2015: Version initiale
		 */

		/**
		 * {@inheritdoc}
		 */
		protected static $extension = '.phtml';

		/**
		 * {@inheritdoc}
		 */
		protected function getCacheName() {
			return 'templates';
		}
	}
?>