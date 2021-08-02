<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Décorateur de fragment avec traduction
	 * 
	 * Ce décorateur permet d'encapsuler un fragment et ainsi de le traduire si un template spécifique à la langue de l'utilisateur est disponible.
	 * Par exemple, si on a un fragment qui utilise le template « sample.tpl.php », et qu'un autre template « sample.en.tpl.php » existe, 
	 * alors si le service multi-lingue est activé le template « sample.en.tpl.php » sera choisi pour les visiteurs anglophones plutôt que celui par défaut.
	 * 
	 * @package    framework
	 * @subpackage classes/fragments/decorators
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    05/08/2015
	 * @since      05/08/2015
	 */
	final class TranslatedFragment extends AbstractFragmentDecorator {
		/*
		 * CHANGELOG:
		 * 05/08/2015: Version initiale
		 */

		/**
		 * Tentative de récupération d'un template spécifique à la langue de l'utilisateur
		 * 
		 * @return string Le nom du template
		 */
		public function getTemplateName() {

			// Si le service multi-lingue est activé, on tente de trouver un template correspondant à la langue
			if (USE_LANGUAGE) {

				// Tout d'abord on tente quelque soit la langue, aussi bien « fr » que « fr-FR »
				$language = Service::language()->getLanguage();
				$name = $this->fragment->getTemplateName() . '.' . $language;

				// Si le template existe, le nom correspond bien à un template traduit et on le retourne
				if (Template::is($name)) {
					return $name;

				// Sinon, si la langue est du style « fr-FR » on tente avec seulement la première partie, soit « fr »
				} else if (FALSE !== strpos($language, '-')) {
					$language = strstr($language, '-', TRUE);
					$name = $this->fragment->getTemplateName() . '.' . $language;

					// Si le template existe, le nom correspond bien à un template traduit avec la première partie de la langue et on le retourne
					if (Template::is($name)) {
						return $name;
					}
				}
			}

			// Sinon on utilise le template par défaut car aucun spécifique n'a été trouvé
			return $this->fragment->getTemplateName();
		}

		/**
		 * Redéfinition de la méthode d'obtention du nom du cache, pour ne pas confondre deux contenus de langues différentes
		 * 
		 * @return string Le nom du cache
		 */
		public function getCacheName() {

			// Si le service multi-lingue est activé, on ajoute la langue à la fin
			if (USE_LANGUAGE) {
				$language = Service::language()->getLanguage();
				return $this->fragment->getCacheName() . '.' . $language;
			}

			// Sinon on retourne le nom simple
			return $this->fragment->getCacheName();
		}
	}
?>