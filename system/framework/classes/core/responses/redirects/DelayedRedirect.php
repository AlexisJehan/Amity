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
	 * Redirection avec délai
	 * 
	 * La redirection avec délai affiche une page informant l'utilisateur avant de le rediriger après un certain temps.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/redirects
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    26/06/2015
	 * @since      26/06/2015
	 */
	abstract class DelayedRedirect extends Redirect {
		/*
		 * CHANGELOG:
		 * 26/06/2015: Version initiale
		 */

		/**
		 * Délai de la redirection (en secondes) [« 5 » par défaut]
		 * 
		 * @var integer
		 */
		protected $delay = 5;

		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// Si l'emplacement de redirection n'est pas vide, on ajoute les headers correspondants
			if (!empty($this->location)) {

				// Si ce n'est pas une URL, alors c'est un emplacement du site et on en crée une depuis la base
				if (!preg_match('/(http|https):\/\/(.*?)$/i', $this->location)) {
					$this->location = url($this->location);
				}

				$this->setStatus($this->isPermanent ? 301 : 302);
				$this->setHeader('Refresh', $this->delay . '; url=' . $this->location);

				// On crée un rendu affichant une page de délai avant la redirection
				$title = $this->getStatus() . ' &ndash; ' . __($this->getStatusMessage());
				$content = '<p>' . __('This location has moved. You will be automatically redirected to its new location in %s seconds. If you aren\'t forwarded to the new page, %sclick here%s.', '<b>' . $this->delay . '</b>', '<b><a href="' . $this->location . '">', '</a></b>') . '</p>';
				$this->render($title, $content);

				// On n'utilise pas la méthode par défaut car celle de la classe « Redirect » enverrait le header « Location » qui perturberai le bon fonctionnement du délai
				$this->sendHeaders();
				$this->sendContent();

			// Sinon on envoi le contenu par défaut
			} else {
				parent::send();
			}
		}

		/**
		 * Retourne le délai de la redirection
		 * 
		 * @return integer Le délai de la redirection
		 */
		public function getDelay() {
			return $this->delay;
		}

		/**
		 * Modifie le délai de la redirection
		 *
		 * @param  integer         $delay Le nouveau délai de la redirection
		 * @return DelayedRedirect        L'instance courante
		 */
		public function setDelay($delay) {
			$this->delay = (int) $delay;
			return $this;
		}
	}
?>