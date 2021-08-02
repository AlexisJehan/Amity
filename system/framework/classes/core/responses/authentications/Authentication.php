<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Contrôleur nécessitant une authentification avec la méthode « Basic »
	 * 
	 * Une authentification permet à ne rendre disponible le contenu seulement si l'utilisateur spécifie un nom d'utilisateur et un mot de passe correct.
	 * 
	 * @package    framework
	 * @subpackage classes/core/responses/authentications
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    02/11/2015
	 * @since      29/09/2015
	 */
	abstract class Authentication extends Response {
		/*
		 * CHANGELOG:
		 * 02/11/2015: Création de fichiers de journalisation avec historique des authentifications
		 * 11/10/2015: Compatibilité avec le préfixe « REDIRECT_ » pouvant être ajouté sous FastCGI
		 * 29/09/2015: Version initiale
		 */

		/**
		 * Realm (message d'authentification) [« Restricted area » par défaut]
		 * 
		 * @var string
		 */
		protected $realm = 'Restricted area';

		/**
		 * Contenu de l'entête d'authentification à envoyer
		 * 
		 * @var string
		 */
		protected $header;

		/**
		 * Contenu d'authentification récupéré
		 * 
		 * @var array
		 */
		protected $auth;

		/**
		 * Prépare l'authentification en récupérant les données de l'utilisateur
		 */
		protected function prepare() {

			// Contenu de l'entête à envoyer
			$this->header = 'Basic realm="' . $this->realm . '"';

			// Permet une compatibilité avec les serveurs sous FastCGI
			if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
				$_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
				unset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
			}

			// Récupération via les valeurs « PHP_AUTH_USER » et « PHP_AUTH_PW » [méthode 1]
			if (isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
				$this->auth['username'] = $_SERVER['PHP_AUTH_USER'];
				$this->auth['password'] = $_SERVER['PHP_AUTH_PW'];

			// Récupération depuis « HTTP_AUTHORIZATION », cette valeur commence par la méthode suivie des identifiants encodés en base 64 [méthode 2]
			} else if (isset($_SERVER['HTTP_AUTHORIZATION']) && 0 === stripos($_SERVER['HTTP_AUTHORIZATION'], 'basic')) {
				$authentication = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));

				// On récupère le nom d'utilisateur et le mot de passe selon la position du « : » dans la chaîne des identifiants
				if (FALSE !== strpos($authentication, ':')) {
					list($this->auth['username'], $this->auth['password']) = explode(':', $authentication, 2);

				} else {
					$this->auth['username'] = $authentication;
					$this->auth['password'] = '';
				}
			}
		}

		/**
		 * Méthode à redéfinir pour renseigner le mot de passe du nom d'utilisateur renseigné, ou « NULL » si le nom d'utilisateur est incorrect
		 * 
		 * @param  string $username Le nom d'utilisateur à vérifier
		 * @return string           Le mot de passe de cet utilisateur s'il existe, sinon « NULL »
		 */
		protected abstract function getPassword($username);

		/**
		 * Vérifie si l'authentification est correcte ou non selon les données récupérées
		 * 
		 * @return boolean Vrai si le nom d'utilisateur et le mot de passe sont corrects
		 */
		protected function isValid() {
			return NULL !== $this->auth && $this->auth['password'] === $this->getPassword($this->auth['username']);
		}

		/**
		 * {@inheritdoc}
		 */
		public function send() {

			// Préparation, écriture des variables
			$this->prepare();

			// Journalisation de l'authentification si activé
			if (ENABLE_LOGS && NULL !== $this->auth) {
				$logger = new Logger('http_authentications');
				$logger->setDate();
				$logger->setIPAddress();
				$logger->set($this->auth['username'], __('Username'), 32);
				$logger->set($this->isValid() ? __('Success') : __('Failure'), __('Status'), 10);
				$logger->setRequest();
				$logger->setReferer();
				$logger->write();
			}

			// Si l'authentification n'est pas valide on affiche une erreur
			if (!$this->isValid()) {

				// FIXME HTTP/1.0 uniquement ?
				$this->setProtocol('HTTP/1.0');

				$this->setStatus(401);
				$this->setHeader('WWW-Authenticate', $this->header);
				$title = __('Error') . ' ' . $this->getStatus() . ' &ndash; ' . __($this->getStatusMessage());
				$content = '<p>' . __('You need to provide a valid username and password.') . '</p>';
				$this->render($title, $content);
			}

			// Sinon on envoi les données du contrôleur personnalisées
			parent::send();
		}

		/**
		 * Retourne le realm (message d'authentification)
		 * 
		 * @return string Le realm
		 */
		public function getRealm() {
			return $this->realm;
		}

		/**
		 * Modifie le realm (message d'authentification)
		 *
		 * @param  string             $realm Le nouveau realm
		 * @return AuthenticationPage        L'instance courante
		 */
		public function setRealm($realm) {
			$this->realm = $realm;
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