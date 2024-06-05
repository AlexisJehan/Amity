<?php
	/*
	 * MIT License
	 *
	 * Copyright (c) 2017-2024 Alexis Jehan
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
	 * Service de crochets
	 *
	 * Les crochets permettent de greffer optionnellement des instructions personnalisées à certains emplacements. De plus dans notre cas ils sont cumulatifs.
	 *
	 * @package    framework
	 * @subpackage classes/services
	 * @version    26/07/2015
	 * @since      06/06/2015
	 */
	final class HookService implements IService {
		/*
		 * CHANGELOG:
		 * 26/07/2015: Possibilité d'enregistrer plusieurs crochets avec « registerArray() »
		 * 09/07/2015: Possibilité d'enregistrer plusieurs callbacks à un même crochet
		 * 06/06/2015: Version initiale
		 */

		/**
		 * Ensemble des différents crochets enregistrés
		 *
		 * @var array
		 */
		protected $hooks = array();

		/**
		 * Enregistrement d'un crochet
		 *
		 * @param  string      $name     Le nom associé au crochet
		 * @param  mixed       $callback La variable ou fonction qui permet d'accéder au callback [optionnel, égal au nom si non renseigné]
		 * @return HookService           L'instance courante
		 */
		public function register($name, $callback = NULL) {

			// Si le callback n'a pas été renseigné on suppose que son nom soit le même que celui du crochet
			if (NULL === $callback) {
				$callback = $name;
			}

			// Si le callable peut être appellé il est correct
			if (is_callable($callback)) {

				// S'il a déjà été enregistré
				if (isset($this->hooks[$name]) || array_key_exists($name, $this->hooks)) {

					// Si ce n'est pas un tableau on le crée pour contenir plusieurs callbacks
					if (!is_array($this->hooks[$name])) {
						$this->hooks[$name] = array($this->hooks[$name]);
					}

					// Puis on ajoute le nouveau callback
					$this->hooks[$name][] = $callback;

				// Sinon on ajoute simplement le callback
				} else {
					$this->hooks[$name] = $callback;
				}

			// Sinon on recherche sa nature pour lancer l'exception appropriée
			} else {

				// Si c'est le nom d'une fonction, mais qu'elle n'existe pas
				if (is_string($callback) && !function_exists($callback)) {
					throw new ServiceException('Unable to register hook "%s" because function "%s" does not exist', $name, $callback);

				// Sinon si c'est un tableau à deux éléments dont le deuxième est une chaîne, ça peut être une classe ou un objet avec le nom d'une méthode
				} else if (is_array($callback) && 2 === count($callback) && is_string($callback[1])) {

					// Si c'est une chaîne on suppose que c'est le nom d'une classe
					if (is_string($callback[0])) {

						// Si ce n'est pas le nom d'une classe
						if (!class_exists($callback[0])) {
							throw new ServiceException('Unable to register hook "%s" because class "%s" does not exist', $name, $callback[0]);

						// Sinon si la classe existe, mais que la méthode n'existe pas
						} else if (!method_exists($callback[0], $callback[1])) {
							throw new ServiceException('Unable to register hook "%s" because class "%s" does not have method "%s"', $name, $callback[0], $callback[1]);

						// Sinon la méthode existe bel et bien, mais n'est sûrement pas accessible
						} else {
							throw new ServiceException('Unable to register hook "%s" because class "%s" has method "%s" but it cannot be accessed', $name, $callback[0], $callback[1]);
						}

					// Sinon si c'est un objet
					} else if (is_object($callback[0])) {

						// Si la méthode de l'objet n'existe pas
						if (!method_exists($callback[0], $callback[1])) {
							throw new ServiceException('Unable to register hook "%s" because object "%s" does not have method "%s"', $name, get_class($callback[0]), $callback[1]);

						// Sinon la méthode existe bel et bien, mais n'est sûrement pas accessible
						} else {
							throw new ServiceException('Unable to register hook "%s" because object "%s" has method "%s" but it cannot be accessed', $name, get_class($callback[0]), $callback[1]);
						}

					// Sinon on ne sait pas ce que c'est
					} else {
						throw new ServiceException('Unable to register hook "%s" because "%s" does not refer to a class or an object', $name, $callback[0]);
					}

				// Sinon ce n'est pas un callable ou un nom de callable
				} else {
					throw new ServiceException('Unable to register hook "%s" because "%s" does not refer to a callable', $name, is_array($callback) ? '[' . implode(', ', $callback) . ']' : $callback);
				}
			}

			return $this;
		}

		/**
		 * Enregistrement de plusieurs crochets
		 *
		 * @param  array       $callbacks Les crochets à enregistrer
		 * @return HookService            L'instance courante
		 */
		public function registerArray(array $callbacks) {
			foreach ($callbacks as $name => $callback) {
				$this->register($name, $callback);
			}
			return $this;
		}

		/**
		 * Exécution d'un crochet
		 *
		 * @param  string $name Le nom associé au crochet
		 * @param  array  $args Les arguments à passer au callback associé [optionnel]
		 * @return mixed        Le résultat éventuel retourné par le (dernier) callback
		 */
		public function execute($name, array $args = array()) {

			// Si aucun crochet n'est enregistré on arrête directement
			if (!isset($this->hooks[$name])) {
				return;
			}

			// Si le callback n'est pas un tableau et est bien enregistré, on l'exécute
			if (!is_array($this->hooks[$name])) {
				return call_user_func_array($this->hooks[$name], $args);

			// Sinon on exécute chaque callback enregistré
			} else {

				// Pour le dernier, on retournera le résultat
				$last = end($this->hooks[$name]);

				// Pour chaque callback enregistré
				foreach ($this->hooks[$name] as $callback) {

					// Si ce n'est pas le dernier on l'exécute
					if ($callback !== $last) {
						call_user_func_array($callback, $args);

					// Sinon on l'exécute puis on retourne le résultat
					} else {
						return call_user_func_array($callback, $args);
					}
				}
			}
		}
	}
?>