<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Décorateur de fragment abstrait
	 * 
	 * Classe abstraite permettant de définir un décorateur pour ajouter des fonctionnalités à des fragments.
	 * 
	 * @package    framework
	 * @subpackage classes/fragments/decorators
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
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
		 * 
		 * @return array Les associations
		 */
		public function getBinding() {
			return $this->fragment->getBinding();
		}

		/**
		 * {@inheritdoc}
		 * 
		 * @return string Le nom du template
		 */
		public function getTemplateName() {
			return $this->fragment->getTemplateName();
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return string Le nom du fichier de cache
		 */
		public function getCacheName() {
			return $this->fragment->getCacheName();
		}

		/**
		 * {@inheritdoc}
		 *
		 * @return integer La durée de mise en cache
		 */
		public function getCacheDuration() {
			return $this->fragment->getCacheDuration();
		}
	}
?>