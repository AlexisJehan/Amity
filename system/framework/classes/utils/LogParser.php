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
	 * Parseur de fichiers de journalisation
	 * 
	 * Classe utilitaire permettant de récupérer les entêtes et lignes d'un fichier de journalisation du framework.
	 * 
	 * @package    framework
	 * @subpackage classes/utils
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    09/11/2015
	 * @since      08/09/2015
	 */
	final class LogParser extends StaticClass {
		/*
		 * CHANGELOG:
		 * 09/11/2015: Ajout de la fermeture du pointeur vers le fichier, et compatibilité avec les archives
		 * 08/09/2015: Version initiale
		 */

		/**
		 * Parse le fichier passé en paramètre et retourne un tableau de son contenu
		 *
		 * @param  string $file Le fichier de journalisation à parser
		 * @return array        Les entêtes si présents, ainsi que les lignes du fichier de journalisation
		 */
		public static function parse($file) {

			// Si le fichier n'existe pas, on lance une exception
			if (!is_file($file) || !is_readable($file)) {
				throw new InvalidArgumentException('"' . $file . '" is not a valid file or it cannot be read.');
			}

			// Initialisation du tableau à retourner
			$log = array();
			$log['headers'] = array();
			$log['rows'] = array();

			// Lecture dans le fichier selon si c'est une archive ou non
			if ('.gz' !== substr($file, -3)) {
				$handle = fopen($file, 'r');
			} else {
				$handle = gzopen($file, 'r');
			}
			while (FALSE !== ($line = fgets($handle))) {

				// Si la ligne commence par un « + » c'est une bordure ou un séparateur d'entête
				if (0 === strpos($line, '+')) {

					// Si le « + » est suivi par des « = » alors c'est un séparateur, et la ligne parsée juste avant est les entêtes
					if (1 === strpos($line, '=')) {
						$log['headers'] = $log['rows'][0];
						$log['rows'] = array();
					}

					continue;
				}

				// On coupe les lignes par la barre verticale, avant de trimmer pour récupérer les colonnes correctement
				$columns = explode('|', $line);
				array_shift($columns);
				array_pop($columns);
				$columns = array_map('trim', $columns);

				// Si la première colonne est vide, alors c'est que la ligne courante est la suite de la précédente, qui a dû être coupée car une colonne devait être trop longue
				if ('' === $columns[0]) {
					$nbRows = count($log['rows']) - 1;
					$nbColumns = count($columns);
					for ($i = 0; $i < $nbColumns; ++$i) {
						if ('' !== $columns[$i]) {
							$log['rows'][$nbRows][$i] .= ' ' . $columns[$i];
						}
					}
					continue;
				}
				$log['rows'][] = $columns;
			}

			// Fermeture du pointeur vers le fichier selon si c'est une archive ou non
			if ('.gz' !== substr($file, -3)) {
				fclose($handle);
			} else {
				gzclose($handle);
			}

			// Si on a pas d'entêtes, on retourne seulement les lignes
			if (empty($log['headers'])) {
				return $log['rows'];
			}

			return $log;
		}
	}
?>