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
	 * Contenu JSON
	 *
	 * Contrôleur générant un contenu JSON à partir d'une valeur PHP.
	 *
	 * @package    framework
	 * @subpackage classes/core/responses
	 * @version    13/02/2016
	 * @since      14/05/2015
	 */
	abstract class Json extends Response {
		/*
		 * CHANGELOG:
		 * 13/02/2016: Renommage de « Ajax » en « Json »
		 * 09/08/2015: Implémentation de l'envoi de JSON
		 * 14/05/2015: Version initiale
		 */

		/**
		 * Valeur à envoyer
		 *
		 * @var mixed
		 */
		protected $value;

		/**
		 * Vérifie si on envoit une valeur
		 *
		 * @return boolean Vrai si on envoi une valeur
		 */
		protected function isSpecific() {
			return !empty($this->value);
		}

		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// Si c'est une réponse spécifique, c'est-à-dire un contenu à envoyer en JSON
			if ($this->isSpecific()) {

				// Le contenu est du JSON
				$this->setHeader('Content-type', 'application/json');

				// On envoi les headers personnalisés
				$this->sendHeaders();

				// On encode la valeur en JSON
				echo json_encode($this->value);

			// Sinon on envoi le contenu par défaut
			} else {
				parent::send();
			}
		}

		/**
		 * Retourne la valeur à écrire en JSON
		 *
		 * @return mixed La valeur
		 */
		public function getValue() {
			return $this->value;
		}

		/**
		 * Modifie la valeur à écrire en JSON
		 *
		 * @param  mixed $value La nouvelle valeur
		 * @return Ajax         L'instance courante
		 */
		public function setValue($value) {
			$this->value = $value;
			return $this;
		}

		/**
		 * {@inheritdoc}
		 */
		public static function getTypeName() {
			return get_class();
		}
	}

	// On vérifie que l'extension est disponible
	if (!extension_loaded('json')) {
		throw new SystemException('"%s" extension is not available', 'json');
	}
?>