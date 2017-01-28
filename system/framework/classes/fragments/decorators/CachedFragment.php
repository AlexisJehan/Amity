<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
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
			if(ENABLE_CACHE) {

				// On récupère les attributs du fragment décoré
				$cacheName = $this->fragment->getCacheName();
				$cacheDuration = $this->fragment->getCacheDuration();

				// Création ou récupération du cache de contenu selon la date de création du fichier et la durée de mise en cache
				$cache = new ContentCache('fragments/'.$cacheName, $cacheDuration);
				if(NULL === $content = $cache->fetch()) {
					$cache->start();
					echo $this->fragment->get();
					$content = $cache->end();
				}

				// FIXME: À ajouter seulement si le contenu est du HTML, sinon ne pas mettre ou adapter le type du commentaire
				// Ajout d'une ligne de commentaire indiquant le temps restant avant la prochaine mise à jour du contenu
				$header = '<!-- Cached content, generated on '.date('d/m/Y H:i:s', filemtime($cache->getFile())).($cacheDuration > 0 ? ', next update in '.($cacheDuration - time() + filemtime($cache->getFile())).' seconds' : '').' -->';

				return PHP_EOL.$header.PHP_EOL.$content;
			}

			// Sinon, on retourne le contenu du fragment décoré sans rien faire
			return $this->fragment->get();
		}
	}
?>