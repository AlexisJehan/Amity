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
	 * Redirection d'emplacement
	 *
	 * La redirection permet de faire rediriger l'utilisateur vers un autre emplacement interne ou externe au site.
	 *
	 * @package    framework
	 * @subpackage classes/core/responses/redirects
	 * @version    26/06/2015
	 * @since      18/05/2015
	 */
	abstract class Redirect extends Response {
		/*
		 * CHANGELOG:
		 * 26/06/2015: Meilleure implémentation
		 * 18/05/2015: Version initiale
		 */

		/**
		 * Emplacement de redirection
		 *
		 * @var string
		 */
		protected $location;

		/**
		 * Redirection permanent si vrai, temporaire sinon [« TRUE » par défaut]
		 *
		 * @var boolean
		 */
		protected $isPermanent = TRUE;

		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// Si l'emplacement de redirection n'est pas vide, on ajoute les headers correspondants
			if (!empty($this->location)) {
				$this->redirect($this->location, $this->isPermanent ? 301 : 302);
			}

			parent::send();
		}

		/**
		 * Retourne l'emplacement de redirection
		 *
		 * @return string L'emplacement de redirection
		 */
		public function getLocation() {
			return $this->location;
		}

		/**
		 * Modifie l'emplacement de redirection
		 *
		 * @param  string   $location Le nouvel emplacement de redirection
		 * @return Redirect           L'instance courante
		 */
		public function setLocation($location) {
			$this->location = $location;
			return $this;
		}

		/**
		 * Retourne vrai si la redirection est permanente
		 *
		 * @return boolean Vrai si la redirection est permanente
		 */
		public function isPermanent() {
			return $this->isPermanent;
		}

		/**
		 * Rend la redirection permanente
		 *
		 * @return Redirect L'instance courante
		 */
		public function setPermanent() {
			$this->isPermanent = TRUE;
			return $this;
		}

		/**
		 * Retourne vrai si la redirection est temporaire
		 *
		 * @return boolean Vrai si la redirection est temporaire
		 */
		public function isTemporary() {
			return !$this->isPermanent;
		}

		/**
		 * Rend la redirection temporaire
		 *
		 * @return Redirect L'instance courante
		 */
		public function setTemporary() {
			$this->isPermanent = FALSE;
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public static function getTypeName() {
			return get_class();
		}
	}
?>