<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Contenu JSON
	 * 
	 * Contrôleur générant un contenu JSON à partir d'une valeur PHP.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
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
			if($this->isSpecific()) {

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
	if(!extension_loaded('json')) {
		throw new SystemException('"%s" extension is not available', 'json');
	}
?>