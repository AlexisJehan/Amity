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
	 * Décorateur de fragment avec mise en cache
	 *
	 * Ce décorateur permet d'encapsuler un fragment en le mettant en cache pendant une durée déterminée et ainsi ne pas le regénérer à chaque nouveau rendu.
	 *
	 * @package    framework
	 * @subpackage classes/fragments/decorators
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    22/07/2015
	 * @since      17/08/2014
	 */
	final class CachedFragment extends AbstractFragmentDecorator {
		/*
		 * CHANGELOG:
		 * 22/07/2015: Compatibilité avec le nouveau cache de contenu
		 * 09/06/2015: Changement de « $_SERVER['REQUEST_TIME'] » en « time() »
		 * 17/08/2014: Version initiale
		 */

		/**
		 * Redéfinition de la méthode d'obtention du contenu avec prise en compte du cache
		 *
		 * @return string Contenu du fragment crée ou récupéré du cache
		 */
		public function get() {

			// Si le cache est activé
			if (ENABLE_CACHE) {

				// On récupère les attributs du fragment décoré
				$cacheName = $this->fragment->getCacheName();
				$cacheDuration = $this->fragment->getCacheDuration();

				// Création ou récupération du cache de contenu selon la date de création du fichier et la durée de mise en cache
				$cache = new ContentCache('fragments/' . $cacheName, $cacheDuration);
				if (NULL === $content = $cache->fetch()) {
					$cache->start();
					echo $this->fragment->get();
					$content = $cache->end();
				}

				// FIXME À ajouter seulement si le contenu est du HTML, sinon ne pas mettre ou adapter le type du commentaire
				// Ajout d'une ligne de commentaire indiquant le temps restant avant la prochaine mise à jour du contenu
				$header = '<!-- Cached content, generated on ' . date('d/m/Y H:i:s', filemtime($cache->getFile())) . ($cacheDuration > 0 ? ', next update in ' . ($cacheDuration - time() + filemtime($cache->getFile())) . ' seconds' : '') . ' -->';

				return PHP_EOL . $header . PHP_EOL . $content;
			}

			// Sinon, on retourne le contenu du fragment décoré sans rien faire
			return $this->fragment->get();
		}
	}
?>