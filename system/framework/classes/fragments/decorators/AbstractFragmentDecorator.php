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
	 * Décorateur de fragment abstrait
	 *
	 * Classe abstraite permettant de définir un décorateur pour ajouter des fonctionnalités à des fragments.
	 *
	 * @package    framework
	 * @subpackage classes/fragments/decorators
	 * @version    05/08/2015
	 * @since      17/08/2014
	 */
	abstract class AbstractFragmentDecorator extends Fragment {
		/*
		 * CHANGELOG:
		 * 05/08/2015: Ajout des méthodes de cache, et suppression de l'interface « ICachedFragment »
		 * 17/08/2014: Version initiale
		 */

		/**
		 * Fragment à décorer
		 *
		 * @var Fragment
		 */
		protected $fragment;

		/**
		 * Constructeur du décorateur
		 *
		 * @param Fragment $fragment Le fragment à décorer
		 */
		public function __construct(Fragment $fragment) {
			$this->fragment = $fragment;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getBinding() {
			return $this->fragment->getBinding();
		}

		/**
		 * {@inheritdoc}
		 */
		public function getTemplateName() {
			return $this->fragment->getTemplateName();
		}

		/**
		 * {@inheritdoc}
		 */
		public function getCacheName() {
			return $this->fragment->getCacheName();
		}

		/**
		 * {@inheritdoc}
		 */
		public function getCacheDuration() {
			return $this->fragment->getCacheDuration();
		}
	}
?>