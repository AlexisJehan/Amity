<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Contenu XML
	 * 
	 * Contrôleur générant un contenu XML à partir d'une valeur PHP.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    13/02/2016
	 * @since      13/02/2016
	 */
	abstract class Xml extends Response {
		/*
		 * CHANGELOG:
		 * 13/02/2016: Version initiale
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

			// Si c'est une réponse spécifique, c'est-à-dire un contenu à envoyer en XML
			if ($this->isSpecific()) {

				// Le contenu est du XML
				$this->setHeader('Content-type', 'application/xml');

				// On envoi les headers personnalisés
				$this->sendHeaders();

				// On encode la valeur en XML
				echo $this->encode($this->value);

			// Sinon on envoi le contenu par défaut
			} else {
				parent::send();
			}
		}

		/**
		 * Encode une valeur en XML
		 * @param  mixed  $value La valeur à encoder
		 * @return string        Le code XML généré
		 */
		protected function encode($value) {

			// Fonction anonyme récursive
			$toXml = function($value, &$xml) use(&$toXml) {
				if (!is_array($value)) {
					$value = array('item' => $value);
				}
				foreach ($value as $key => $child) {
					if (!preg_match('/\A(?!XML)[a-z][\w0-9-]*/i', $key)) {
						$key = 'item';
					}
					if (is_object($child)) {
						$child = get_object_vars($child);
					}
					if (is_array($child)) {
						$node = $xml->addChild($key);
						$toXml($child, $node);
					} else {
						if (is_bool($child)) {
							$child = $child ? 'true' : 'false';
						}
						$xml->addChild($key, htmlspecialchars($child));
					}
				}
			};

			$xmlElement = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><value></value>');
			$toXml($value, $xmlElement);
			return $xmlElement->asXML();
		}

		/**
		 * Retourne la valeur à écrire en XML
		 * 
		 * @return mixed La valeur
		 */
		public function getValue() {
			return $this->value;
		}

		/**
		 * Modifie la valeur à écrire en XML
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
?>