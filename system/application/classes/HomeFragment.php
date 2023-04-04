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
	 * Fragment du contenu de la page principale
	 *
	 * Ce fragment permet de définir le contenu de la page par défaut. Nous utilisons un fragment afin de pouvoir le décorer et ainsi le traduire selon la langue utilisateur.
	 *
	 * @package    application
	 * @subpackage classes
	 * @version    05/08/2015
	 * @since      05/08/2015
	 */
	final class HomeFragment extends Fragment {

		/**
		 * Retourne le nom de template utilisé par le fragment
		 *
		 * @return string Le nom du template
		 */
		public function getTemplateName() {
			return 'homeFragment';
		}

		/**
		 * Nous n'utilisons pas de cache, on se contente donc de redéfinir une méthode vide
		 */
		public function getCacheName() {
			// Vide
		}

		/**
		 * Nous n'utilisons pas de cache, on se contente donc de redéfinir une méthode vide
		 */
		public function getCacheDuration() {
			// Vide
		}
	}
?>