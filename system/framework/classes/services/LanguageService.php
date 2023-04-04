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
	 * Service multi-lingue
	 *
	 * Ce service permet de proposer des traductions lors de la génération du contenu envoyé à l'utilisateur selon les langues disponibles.
	 *
	 * @package    framework
	 * @subpackage classes/services
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    09/09/2015
	 * @since      03/08/2014
	 */
	final class LanguageService implements IService {
		/*
		 * CHANGELOG:
		 * 09/09/2015: Correction d'un bug de duplication se produisant lors du chargement de plusieurs fichiers de traductions de langues différentes (Oubli de réinitialisation de la variable locale)
		 * 03/08/2015: Nouvelle implémentation
		 * 05/10/2014: Création d'un constructeur privé pour interdire l'instanciation
		 * 07/08/2014: Emplacement des fichiers de traduction désormais dans une variable statique pour faciliter la flexibilité si utilisé dans un autre contexte
		 * 03/08/2014: Version initiale
		 */

		/**
		 * Chargeur qui donne le fichier de traductions selon son nom
		 *
		 * @var LanguageLoader
		 */
		private static $loader;

		/**
		 * Langue courante à utiliser pour la traduction [« DEFAULT_LANGUAGE » par défaut]
		 *
		 * @var string
		 */
		protected $language = DEFAULT_LANGUAGE;

		/**
		 * Dictionnaire contenant chaque équivalent de traductions chargés
		 *
		 * @var array
		 */
		protected $dictionary;

		/**
		 * Constructeur du service multi-lingue
		 */
		public function __construct() {

			// S'il n'a pas encore été instantié, on crée le chargeur statique, et on ajoute les emplacements des traductions
			if (NULL === self::$loader) {
				self::$loader = new LanguageLoader();
				self::$loader
					->addArray(
						array(
							FRAMEWORK_DIR . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . 'framework.lang.php',
							LANGUAGES_DIR,
						)
					)
					->load();
			}

			// On parse les languages acceptées par l'utilisateur (on retire les priorités)
			$userLanguages = self::parseAcceptLanguages();
			$userLanguages = array_keys($userLanguages);

			// On ajoute un tableau vide pour la langue courante, c'est-à-dire la langue par défaut
			$this->dictionary[$this->language] = array();

			// Puis on charge chacune des traductions
			$names = array_keys(self::$loader->getFiles());
			foreach ($names as $name) {
				$file = self::$loader->getFile($name);
				$dictionary = array();
				require ($file);
				$this->dictionary = array_merge_recursive($this->dictionary, $dictionary);
			}

			// Si dans les langues de l'utilisateur il y en a une proposée, on la choisit
			foreach ($userLanguages as $language) {
				if (isset($this->dictionary[$language])) {
					$this->language = $language;
					break;
				}
			}
		}

		/**
		 * Traduction d'une chaîne, si la traduction de la langue courante est disponible
		 *
		 * @param  string $string La chaîne à traduire
		 * @return string         La chaîne éventuellement traduite
		 */
		public function translate($string) {

			// Si la chaîne est traduisible, on le fait
			if (isset($this->dictionary[$this->language][$string])) {
				return $this->dictionary[$this->language][$string];

			// Sinon si la langue a un format du genre « fr-FR », on récupère la première partie et on regarde si il y a une traduction
			} else if (FALSE !== strpos($this->language, '-')) {
				$language = strstr($this->language, '-', TRUE);
				if (isset($this->dictionary[$language][$string])) {
					return $this->dictionary[$language][$string];
				}
			}

			// Sinon aucune traduction n'est disponible, on retourne la chaîne initiale dans sa langue par défaut
			return $string;
		}

		/**
		 * Retourne la liste des langues disponibles
		 *
		 * @return array La liste des langues
		 */
		public function getLanguages() {
			return array_keys($this->dictionary);
		}

		/**
		 * Retourne la langue courante à utiliser pour la traduction
		 *
		 * @return string La langue courante
		 */
		public function getLanguage() {
			return $this->language;
		}

		/**
		 * Modifie la langue courante à utiliser pour la traduction
		 *
		 * @param  string          $language La nouvelle langue courante
		 * @return LanguageService           L'instance courante
		 */
		public function setLanguage($language) {

			// Si la langue n'est pas valide
			if (!preg_match('/^[a-z]{1,8}(-[a-z]{1,8})?$/i', $language)) {
				throw new InvalidParameterException('The value "%s" is not valid for the language', $language);
			}

			$this->language = $language;
			return $this;
		}

		/**
		 * Parse et retourne les langues à partir de la chaîne utilisateur
		 *
		 * @param  string $acceptLanguages La chaîne utilisateur des langues [si non renseigné alors sera récupéré de la variable globale « $_SERVER »]
		 * @return array                   Le tableau associant chaque langue à leur priorité, par ordre décroissant
		 */
		protected static function parseAcceptLanguages($acceptLanguages = NULL) {

			// Si la chaîne utilisateur des langues n'est pas renseignée
			if (NULL === $acceptLanguages) {
				if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
					$acceptLanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
				} else {
					$acceptLanguages = '';
				}
			}

			// On extrait chaque langue et chaque priorité de la chaîne
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $acceptLanguages, $matches);

			// Si on a des résultats
			if (!empty($matches[1])) {

				// On récupère et enregistre correctement les valeurs
				$languages = array_combine($matches[1], $matches[4]);
				foreach ($languages as $language => $preference) {
					$languages[$language] = floatval($preference) ?: 1.0;
				}

				// On trie par ordre décroissant
				arsort($languages, SORT_NUMERIC);

				return $languages;

			// Sinon on a pas réussi à extraire des langues, on retourne une chaîne vide
			} else {
				return array();
			}
		}
	}

	// On vérifie que l'extension est disponible
	if (!extension_loaded('intl')) {
		throw new SystemException('"%s" extension is not available', 'intl');
	}
?>