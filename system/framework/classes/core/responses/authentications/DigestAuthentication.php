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
	 * Contrôleur nécessitant une authentification avec la méthode « Digest »
	 *
	 * L'authentification avec la méthode « Digest » permet de sécuriser un peu plus l'authentification qu'avec la méthode basique, cependant cela ne guarantie pas une sécurité optimale.
	 *
	 * @package    framework
	 * @subpackage classes/core/responses/authentications
	 * @version    11/10/2015
	 * @since      29/09/2015
	 */
	abstract class DigestAuthentication extends Authentication {
		/*
		 * CHANGELOG:
		 * 11/10/2015: Compatibilité avec le préfixe « REDIRECT_ » pouvant être ajouté sous FastCGI
		 * 29/09/2015: Version initiale
		 */

		/**
		 * {@inheritdoc}
		 */
		protected function prepare() {

			// Contenu de l'entête à envoyer (différent de la méthode basique)
			$this->header = 'Digest realm="' . $this->realm . '",qop="auth",nonce="' . md5(uniqid()) . '",opaque="' . md5(uniqid()) . '"';

			// Permet une compatibilité avec les serveurs sous FastCGI
			if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
				$_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
				unset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
			}

			// Récupération via la valeur « PHP_AUTH_USER » [méthode 1]
			if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
				$this->auth = self::parseDigestAuthentication($_SERVER['PHP_AUTH_DIGEST']);

			// Récupération depuis « HTTP_AUTHORIZATION », cette valeur commence par la méthode suivie des identifiants et variables de sécurité [méthode 2]
			} else if (isset($_SERVER['HTTP_AUTHORIZATION']) && 0 === stripos($_SERVER['HTTP_AUTHORIZATION'], 'digest')) {
				$this->auth = self::parseDigestAuthentication(substr($_SERVER['HTTP_AUTHORIZATION'], 7));
			}
		}

		/**
		 * {@inheritdoc}
		 */
		protected function isValid() {

			// Si un nom d'utilisateur est renseigné on vérifie que son mot de passe correspond
			if (NULL !== $this->auth && NULL !== $password = $this->getPassword($this->auth['username'])) {
				$a1 = md5($this->auth['username'] . ':' . $this->realm . ':' . $password);
				$a2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $this->auth['uri']);
				return $this->auth['response'] === md5($a1 . ':' . $this->auth['nonce'] . ':' . $this->auth['nc'] . ':' . $this->auth['cnonce'] . ':' . $this->auth['qop'] . ':' . $a2);
			}

			// Sinon l'authentification a échouée
			return FALSE;
		}

		/**
		 * Création d'un tableau à partir de la chaîne d'authentification via la méthode digeste
		 *
		 * @param  string $username Le nom d'utilisateur à vérifier
		 * @return string           Le mot de passe de cet utilisateur s'il existe, sinon « NULL »
		 */
		protected static function parseDigestAuthentication($auth) {
			$data = array();
			$keys = array(
				'nonce'    => 1,
				'nc'       => 1,
				'cnonce'   => 1,
				'qop'      => 1,
				'username' => 1,
				'uri'      => 1,
				'response' => 1,
			);
			preg_match_all('@(' . implode('|', array_keys($keys)) . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $auth, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				$data[$match[1]] = $match[3] ? $match[3] : $match[4];
				unset($keys[$match[1]]);
			}
			return empty($keys) ? $data : FALSE;
		}
	}
?>