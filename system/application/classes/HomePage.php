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
	 * Contrôleur de la page d'accueil de l'application
	 *
	 * La page d'accueil du site est aussi la page par défaut, elle est entièrement personnalisable. Elle ne doit pas être retirée.
	 *
	 * @package    application
	 * @subpackage classes
	 * @version    05/08/2015
	 * @since      01/08/2014
	 */
	final class HomePage extends Page {

		/**
		 * Action principale (Le contenu doit être personnalisé)
		 */
		public function indexAction() {

			// Le titre de la page, il sera traduit si le service multi-lingue est activé et que la langue est disponible
			$this->setTitle(__('Home'));

			// Instanciation du fragment, que l'on traduit selon la disponibilité de la langue de l'utilisateur
			$fragment = new TranslatedFragment(new HomeFragment());

			// Contenu, composé du rendu du fragment
			$this->setContent($fragment->get());
		}
	}
?>