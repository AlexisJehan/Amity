<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Service de débogage
	 * 
	 * Ce service remplace le gestionnaire d'erreurs et d'exception par lui même. Il propose une interface et des informations plus poussées que XDebug.
	 * 
	 * @package    framework
	 * @subpackage classes/services
	 * @author     Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version    26/03/2016
	 * @since      24/07/2015
	 */
	final class DebugService implements IService {
		/*
		 * CHANGELOG:
		 * 26/03/2016: Compatibilité avec PHP 7, « ErrorException » dérivant non plus de « Exception » mais de « Throwable » le type du paramètre de « exceptionHandler() » n'est plus fixé
		 * 31/07/2015: Améliorations internes à la classe, avec notamment l'utilisation de « ErrorException » et des paramètres d'affichage et de journalisation
		 * 28/07/2015: Amélioration du style des fenêtres d'erreur, et...
		 * 24/07/2015: Version initiale
		 */

		/**
		 * Noms correspondant à chaque drapeau d'erreurs
		 * 
		 * @var array
		 */
		private static $errorCodes = array(
			E_ERROR             => 'E_ERROR',
			E_WARNING           => 'E_WARNING',
			E_PARSE             => 'E_PARSE',
			E_NOTICE            => 'E_NOTICE',
			E_CORE_ERROR        => 'E_CORE_ERROR',
			E_CORE_WARNING      => 'E_CORE_WARNING',
			E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
			E_USER_ERROR        => 'E_USER_ERROR',
			E_USER_WARNING      => 'E_USER_WARNING',
			E_USER_NOTICE       => 'E_USER_NOTICE',
			E_STRICT            => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED        => 'E_DEPRECATED',
			E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
			E_ALL               => 'E_ALL'
		);

		/**
		 * Indique si le débogueur est enregistré ou non
		 * 
		 * @var boolean
		 */
		protected $registered = FALSE;

		/**
		 * Indique si les erreurs doivent être affichées
		 * 
		 * @var boolean
		 */
		protected $display = FALSE;

		/**
		 * Indique si les erreurs doivent être journalisées
		 * 
		 * @var integer
		 */
		protected $log = FALSE;

		/**
		 * Indique si le style a déjà été envoyé
		 * 
		 * @var boolean
		 */
		protected $alreadyStylized = FALSE;

		/**
		 * Tableau contenant les chronos lancés
		 * 
		 * @var array
		 */
		protected $chronos = array();


		/**
		 * Constructeur du service de débogage
		 */
		public function __construct() {

			// On enregistre un handler à la fermeture pour reporter d'éventuelles erreurs
			register_shutdown_function(array($this, 'shutdownHandler'));
		}

		/**
		 * Enregistrement du service de débogage
		 *
		 * @param  boolean      $register Indique si on enregistre ou si on restaure [« TRUE » par défaut]
		 * @return DebugService           L'instance courante
		 */
		public function register($register = TRUE) {

			// Si la valeur est différente, on agit
			if($this->registered != $register) {

				// On inverse la valeur de l'enregistrement
				$this->registered = !$this->registered;

				// Si on active le débogueur
				if($this->registered) {

					// On enregistre les valeurs courantes dans les attributs
					$this->display = ini_get('display_errors');
					$this->log = ini_get('log_errors');

					// Avant de les désactiver pour les remplacer
					ini_set('display_errors', FALSE);
					ini_set('log_errors', FALSE);

					// On enregistre les handlers personnalisés
					set_error_handler(array($this, 'errorHandler'), -1);
					set_exception_handler(array($this, 'exceptionHandler'));

				// Sinon on restaure
				} else {

					// On restaure les précédentes valeurs
					ini_set('display_errors', $this->display);
					ini_set('log_errors', $this->log);

					// Avant de désactiver les valeurs des attributs
					$this->display = FALSE;
					$this->log = FALSE;

					// On restaure les précédents handlers
					restore_error_handler();
					restore_exception_handler();
				}
			}

			return $this;
		}

		/**
		 * Dump d'une ou de plusieurs variables
		 *
		 * @param  mixed        $variables Les variables à reporter dans le dump
		 * @return DebugService            L'instance courante
		 */
		public function dump($variables) {

			// On utilise la trace de débogage pour récupérer le fichier et la ligne d'appel
			$trace = debug_backtrace(FALSE);
			$call = array_shift($trace);

			// On récupère chaque variable pour les afficher textuellement
			$variables = func_get_args();
			foreach($variables as $variable) {
				$content[] = $this->variable($variable);
			}

			// Affichage de la fenêtre stylisée
			$this->printBox('dump', 'Dump', __('reported from %s on line %s.', '<b>'.path($call['file']).'</b>', '<b>'.$call['line'].'</b>'), implode(PHP_EOL, $content));

			return $this;
		}

		/**
		 * Enregistrement ou affichage d'un chrono déjà enregistré
		 *
		 * @param  string       $name      Le nom associé au chrono
		 * @param  integer      $precision Le nombre de chiffres après la virgule du temps [« 6 » par défaut]
		 * @return DebugService            L'instance courante
		 */
		public function chrono($name = '', $precision = 6) {

			// On utilise la trace de débogage pour récupérer le fichier et la ligne d'appel
			$trace = debug_backtrace(FALSE);
			$call = array_shift($trace);

			// Si le chrono n'est pas enregistré on le fait
			if(!isset($this->chronos[$name])) {
				$this->chronos[$name] = microtime(TRUE);
				$description = __('%s started from %s on line %s.', '<b>'.(!empty($name) ? '"'.$name.'"' : '[default]').'</b>', '<b>'.path($call['file']).'</b>', '<b>'.$call['line'].'</b>');

				// Pas de contenu à afficher dans la fenêtre
				$content = array();

			// Sinon on récupère le temps au moment de l'enregistrement pour calculer la durée
			} else {
				$duration = round(microtime(TRUE) - $this->chronos[$name], $precision);

				// Suppression du chrono
				unset($this->chronos[$name]);

				$description = __('%s stopped from %s on line %s.', '<b>'.(!empty($name) ? '"'.$name.'"' : '[default]').'</b>', '<b>'.path($call['file']).'</b>', '<b>'.$call['line'].'</b>');
				$content[] = '<i><u>'.__('Duration').'</u></i>:';
				$content[] = '<span>'.__('%s seconds', '<b>'.$duration.'</b>').'</span>';
			}

			// Affichage de la fenêtre du chrono
			$this->printBox('chrono', 'Chrono', $description, implode(PHP_EOL, $content));

			return $this;
		}

		/**
		 * Handler de fermeture personnalisé
		 */
		public function shutdownHandler() {

			// Si le débogueur n'est pas enregistré, on quitte directement
			if(!$this->registered) {
				return;
			}

			// On récupère l'éventuelle dernière erreur
			$error = error_get_last();

			// Si elle est fatale, on l'affiche car elle a provoqué la fermeture
			if($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_COMPILE_WARNING)) {
				$this->errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
			}
		}

		/**
		 * Méthode de gestion des erreurs
		 *
		 * @param integer $type    Le code de l'erreur
		 * @param string  $message Le message personnalisé décrivant l'erreur rencontrée
		 * @param string  $file    L'emplacement du fichier d'origine de l'erreur
		 * @param integer $line    La ligne du fichier à l'origine de l'erreur
		 * @param array   $scope   L'environnement de l'erreur [vide par défaut]
		 */
		public function errorHandler($type, $message, $file, $line, array $scope = array()) {

			// Si le débogueur n'est pas enregistré, ou si l'erreur ne doit pas être reportée on quitte directement
			if(!$this->registered || !($type & error_reporting())) {
				return;
			}

			// On attrape l'erreur qu'on génère via l'exception d'adaptation « ErrorException »
			try {
				throw new ErrorException($message, 0, $type, $file, $line);
			} catch(ErrorException $exception) {
				$this->exceptionHandler($exception, $scope);
			}

			// Si c'est une erreur fatale, on arrête
			if($type & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR)) {
				exit;
			}
		}

		/**
		 * Méthode de gestion des exceptions
		 *
		 * @param Exception|Throwable $throwable L'exception ou l'erreur à reporter
		 * @param array               $scope     L'environnement de l'exception [vide par défaut]
		 */
		public function exceptionHandler($throwable, array $scope = array()) {

			// Si le débogueur n'est pas enregistré, on quitte directement
			if(!$this->registered) {
				return;
			}

			// On récupère les attributs de l'exception
			$name = get_class($throwable);
			$code = $throwable->getCode();
			$message = $throwable->getMessage();
			$file = path($throwable->getFile());
			$line = $throwable->getLine();
			$trace = $throwable->getTrace();

			// Si c'est une erreur à la base
			if('ErrorException' === $name) {
				$isError = TRUE;
				$name = self::errorName($throwable->getSeverity());
				$code = self::errorCode($throwable->getSeverity());

				// On supprime les handlers de déboguage de la trace si elles sont présentes
				if(0 < count($trace) && 'errorHandler' === $trace[0]['function']) {
					array_shift($trace);
				}
				if(0 < count($trace) && 'shutdownHandler' === $trace[0]['function']) {
					array_shift($trace);
				}

				// La classe CSS est celle du nom de l'erreur en minuscule
				$class = strtolower($name);

			// Sinon c'est une exception
			} else {
				$isError = FALSE;

				// La classe CSS se compose du type de l'exception suivi de son nom de base
				$class = ('SystemException' !== $name && !is_subclass_of($name, 'SystemException') ? 'exception ' : 'systemexception ').strtolower($name);
			}

			// Si la journalisation est activée
			if($this->log) {
				$logger = new Logger($isError ? 'errors' : 'exceptions');
				$logger->setDate();
				$logger->setIPAddress();
				$logger->set($name, __('Name'), $isError ? 11 : 32);
				$logger->set(!empty($code) ? $code : '', __('Code'), $isError ? 19 : 5);
				$logger->set($message, __('Message'), 100);
				$logger->set($file.':'.$line, __('File'), 100);
				$logger->write();
			}

			// Si l'affichage est activé on affiche bien la fenêtre
			if($this->display) {

				// Ajout de contenu supplémentaire dans la fenêtre de l'erreur si la trace et l'environnement ne sont pas vides
				$content = array();
				if(!empty($trace)) {
					$content[] = $this->trace($trace);
				}
				if(!empty($scope)) {
					$content[] = $this->scope($scope);
				}

				$this->printBox($class, $name.(0 !== $code ? ' ['.$code.']' : ''), __('"%s" from %s on line %s.', $message, '<b>'.$file.'</b>', '<b>'.$line.'</b>'), implode(PHP_EOL, $content));
			}
		}

		/**
		 * Génération du contenu de la fenêtre pour la trace d'erreurs
		 *
		 * @param  array $trace La trace d'erreurs à afficher
		 * @return string       Le contenu à ajouter dans la fenêtre
		 */
		protected function trace(array $trace) {
			$content[] = '<div class="debug trace"><i><u>'.__('Trace').'</u></i>:';

			// Pour chaque appel de la trace, on ajoute le contenu spécifique stylisé
			foreach($trace as $index => $call) {
				$file  = isset($call['file'])  ? __('%s on line %s:', '<b>'.path($call['file']).'</b>', '<b>'.$call['line'].'</b>') : '<b>{internal function}</b>';
				$class = isset($call['class']) ? $call['class'] : '';
				$type  = isset($call['type'])  ? $call['type']  : '';
				$function = $call['function'];
				$args = isset($call['args']) ? implode(', ', array_map(array($this, 'variable'), $call['args'])) : '';

				$content[] = '<span>#'.$index.' '.$file;
				$content[] = '     <b>'.$class.$type.$function.'</b>('.$args.')';
				$content[] = '</span>';
			}

			$content[] = '<span>#'.($index + 1).' <b>{main}</b></span>';
			return implode(PHP_EOL, $content).'</div>';
		}

		/**
		 * Génération du contenu de la fenêtre pour l'environnement d'erreur
		 *
		 * @param  array $scope L'environnement d'erreur à afficher
		 * @return string       Le contenu à ajouter dans la fenêtre
		 */
		protected function scope(array $scope) {
			$content[] = '<div class="debug scope"><i><u>'.__('Scope').'</u></i>:';

			// Pour chaque variable de l'environnement, on ajoute le contenu spécifique stylisé
			foreach($scope as $name => $value) {
				$content[] = '<span><b>$'.$name.'</b> = '.$this->variable($value).'</span>';
			}

			return implode(PHP_EOL, $content).'</div>';
		}

		/**
		 * Génération d'un contenu affichable pour décrire une variable
		 *
		 * @param  string $variable La variable à décrire
		 * @param  string $depth    La profondeur courante, utile dans le cas d'un appel récursif
		 * @return string           Le contenu qui décrit la variable générée
		 */
		protected function variable($variable, $depth = 1) {

			// On récupère le type de la variable, et on génère le contenu selon celui-ci
			$content = '';
			$type = gettype($variable);
			switch($type) {
				case 'boolean':
					$content = $variable ? 'TRUE' : 'FALSE';
					break;

				case 'integer':
				case 'double':
					$content = $variable;
					break;

				case 'string':

					// On limite à 100 caractères de la chaîne, et on échappe les caractères HTML
					$content = '\''.htmlentities(utf8_encode((100 < strlen($variable) ? substr($variable, 0, 100).'...' : $variable)), ENT_QUOTES | ENT_IGNORE, 'UTF-8').'\'';
					break;

				case 'array';

					// On limite la profondeur à 3 pour les tableaux importants récursifs
					if(3 < $depth) {
						$content = '[...]';

					// Si le tableau n'est pas vide
					} else if(0 < count($variable)) {

						$content = array();

						// Tableau associatif, on affiche les clefs devant
						if($variable !== array_values($variable)) {
							foreach($variable as $key => $value) {
								$content[] = $this->variable($key).' => '.$this->variable($value, $depth + 1);
							}

						// Sinon on se contente des valeurs
						} else {
							foreach($variable as $value) {
								$content[] = $this->variable($value, $depth + 1);
							}
						}

						$content = '['.implode(', ', $content).']';

					} else {
						$content = '[empty]';
					}
					break;

				case 'object':
					$content = get_class($variable);
					break;

				case 'resource':
					$content = get_resource_type($variable);
					break;

				case 'NULL':
					break;

				default:
					$type = 'unknown';
					break;
			}

			// Si le contenu dépasse 300 caractères on le raccourci sans altérer les balises HTML
			if(300 < strlen($content)) {
				$content = substr($content, 0, 300);

				// Si la dernière balise fermante est avant la dernière balise ouvrante, on ajoute une fermante
				if(strrpos($content, '<i>') > strrpos($content, '</i>')) {
					$content .= '</i>';
				}

				$content .= '...';
			}

			return '<i>'.$type.'</i>'.(!empty($content) || 0 === $content ? ' '.$content : '');
		}

		/**
		 * Affichage d'une fenêtre de description
		 *
		 * @param string $class       La classe CSS associée au type de la fenêtre
		 * @param string $title       Le titre de la fenêtre
		 * @param string $description La description associée au titre
		 * @param string $message     Le message, qui s'affichera en tant que contenu dans la fenêtre [chaîne vide par défaut]
		 */
		protected function printBox($class, $title, $description, $message = '') {
			$this->style();
			$content = '<div class="debug box'.(!empty($class) ? ' '.$class : '').'">';
			$content .= '<div class="debug header"><span class="debug icon" title="'.$title.'"></span><b>'.$title.'</b>'.(!empty($description) ? ': '.$description : '').'<span class="debug close" title="Close" onclick="this.parentNode.parentNode.style.display = \'none\'">&#215;</span></div>';
			if(!empty($message)) {
				$content .= '<pre class="debug message">'.$message.'</pre>';
			} else {
				$content .= '<pre class="debug message empty"></pre>';
			}
			$content .= '</div>';

			// On affiche le contenu généré, qui affichera la fenêtre
			echo $content.PHP_EOL;
		}

		/**
		 * Génération du style de la fenêtre
		 */
		protected function style() {

			// S'il a déjà été envoyé, on ne le réécrit pas.
			if($this->alreadyStylized) {
				return;
			}
			$this->alreadyStylized = TRUE;

			// La référence, qui s'affiche en bas à droite du contenu de la fenêtre se compose de la description de l'environnement et du framework
			$reference = $_SERVER['SERVER_SOFTWARE'].' '.__NAME__.'/'.__VERSION__;

			echo '<style>div.debug.exception pre.debug.message,pre.debug.message{border-left:3px solid #2B2B2B;border-bottom:1px solid #2B2B2B}div.debug{padding:0;font-size:1em;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}div.debug.box{margin:15px 5px}div.debug.header{position:relative;min-height:34px;line-height:24px;padding:5px 34px;-webkit-border-top-left-radius:10px;-moz-border-radius-topleft:10px;border-top-left-radius:10px;-webkit-border-top-right-radius:10px;-moz-border-radius-topright:10px;border-top-right-radius:10px;overflow:hidden;background-color:#2B2B2B;color:#fff}div.debug.header:after{content:\'\';position:absolute;top:0;left:0;width:100%;height:17px;background:linear-gradient(rgba(255,255,255,.5),rgba(255,255,255,.1))}span.debug.close,span.debug.icon{position:absolute;width:24px;height:24px}span.debug.icon{top:5px;left:5px;background-image:url(data:image/png;base64,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);background-repeat:no-repeat}span.debug.close{z-index:1;top:5px;right:5px;cursor:pointer;font-size:30px;text-align:center}span.debug.close:hover{color:#ccc}pre.debug.message{position:relative;margin:0;padding:10px 0 25px 10px;white-space:pre-wrap;-webkit-border-bottom-left-radius:10px;-moz-border-radius-bottomleft:10px;border-bottom-left-radius:10px;border-right:1px solid #2B2B2B;background-color:#f5f5f5;color:#262626}pre.debug.message.empty{padding:10px}pre.debug.message:after{content:\''.$reference.'\';position:absolute;right:3px;bottom:3px;font-style:italic;color:#aaa}pre.debug.message span{padding-left:1em}div.debug.exception div.debug.header{background:repeating-linear-gradient(-50deg,transparent,transparent 10px,#262626 10px,#262626 15px);background-color:#2B2B2B;color:#fff}div.debug.exception span.debug.icon{background-position:0 -24px}div.debug.exception pre.debug.message{border-right:1px solid #2B2B2B;color:#262626}div.debug.systemexception div.debug.header{background:repeating-linear-gradient(-50deg,transparent,transparent 10px,#D9D9D9 10px,#D9D9D9 15px);background-color:#D4D4D4;color:#000}div.debug.systemexception span.debug.icon{background-position:-24px -24px}div.debug.systemexception pre.debug.message{border-left:3px solid #D4D4D4;border-bottom:1px solid #D4D4D4;border-right:1px solid #D4D4D4;color:#262626}div.debug.missingnoexception div.debug.header{background:#ECF0F1;font-family:\'Courier New\';color:#000}div.debug.missingnoexception span.debug.icon{background-position:-48px -24px}div.debug.missingnoexception pre.debug.message{border-left:3px solid #ECF0F1;border-bottom:1px solid #ECF0F1;border-right:1px solid #ECF0F1;color:#262626}div.debug.error div.debug.header{background:linear-gradient(to bottom,transparent,transparent 50%,#C0392B 50%,#C0392B);background-size:100% 2px;background-color:#E74C3C}div.debug.error span.debug.icon{background-position:0 0}div.debug.error pre.debug.message{border-left:3px solid #E74C3C;border-bottom:1px solid #E74C3C;border-right:1px solid #E74C3C;color:#C0392B}div.debug.warning div.debug.header{background:linear-gradient(to bottom,transparent,transparent 50%,#D35400 50%,#D35400);background-size:100% 2px;background-color:#E67E22}div.debug.warning span.debug.icon{background-position:-24px 0}div.debug.warning pre.debug.message{border-left:3px solid #E67E22;border-bottom:1px solid #E67E22;border-right:1px solid #E67E22;color:#D35400}div.debug.notice div.debug.header{background:linear-gradient(to bottom,transparent,transparent 50%,#394C81 50%,#394C81);background-size:100% 2px;background-color:#5065A1}div.debug.notice span.debug.icon{background-position:-48px 0}div.debug.notice pre.debug.message{border-left:3px solid #5065A1;border-bottom:1px solid #5065A1;border-right:1px solid #5065A1;color:#394C81}div.debug.deprecated div.debug.header{background:linear-gradient(to bottom,transparent,transparent 50%,#503B2C 50%,#503B2C);background-size:100% 2px;background-color:#5E4534}div.debug.deprecated span.debug.icon{background-position:-72px 0}div.debug.deprecated pre.debug.message{border-left:3px solid #5E4534;border-bottom:1px solid #5E4534;border-right:1px solid #5E4534;color:#503B2C}div.debug.parse div.debug.header{background:linear-gradient(to bottom,transparent,transparent 50%,#2C3E50 50%,#2C3E50);background-size:100% 2px;background-color:#34495E}div.debug.parse span.debug.icon{background-position:-96px 0}div.debug.parse pre.debug.message{border-left:3px solid #34495E;border-bottom:1px solid #34495E;border-right:1px solid #34495E;color:#2C3E50}div.debug.strict div.debug.header{background:linear-gradient(to bottom,transparent,transparent 50%,#8E44AD 50%,#8E44AD);background-size:100% 2px;background-color:#9B59B6}div.debug.strict span.debug.icon{background-position:-120px 0}div.debug.strict pre.debug.message{border-left:3px solid #9B59B6;border-bottom:1px solid #9B59B6;border-right:1px solid #9B59B6;color:#8E44AD}div.debug.recoverable div.debug.header{background:linear-gradient(to bottom,transparent,transparent 50%,#662621 50%,#662621);background-size:100% 2px;background-color:#79302A}div.debug.recoverable span.debug.icon{background-position:-144px 0}div.debug.recoverable pre.debug.message{border-left:3px solid #79302A;border-bottom:1px solid #79302A;border-right:1px solid #79302A;color:#662621}div.debug.unknown div.debug.header{background:linear-gradient(to bottom,transparent,transparent 50%,#8E725E 50%,#8E725E);background-size:100% 2px;background-color:#A38671}div.debug.unknown span.debug.icon{background-position:-168px 0}div.debug.unknown pre.debug.message{border-left:3px solid #A38671;border-bottom:1px solid #A38671;border-right:1px solid #A38671;color:#8E725E}div.debug.dump div.debug.header{background:repeating-linear-gradient(to right,transparent,transparent 3px,#2980B9 3px,#2980B9 9px);background-color:#3498DB}div.debug.dump span.debug.icon{background-position:-72px -24px}div.debug.dump pre.debug.message{border-left:3px solid #3498DB;border-bottom:1px solid #3498DB;border-right:1px solid #3498DB;color:#2980B9}div.debug.chrono div.debug.header{background:repeating-linear-gradient(to right,transparent,transparent 3px,#8EB021 3px,#8EB021 9px);background-color:#A5C63B}div.debug.chrono span.debug.icon{background-position:-96px -24px}div.debug.chrono pre.debug.message{border-left:3px solid #A5C63B;border-bottom:1px solid #A5C63B;border-right:1px solid #A5C63B;color:#8EB021}</style>';
		}

		/**
		 * Association d'un code au type de l'erreur
		 *
		 * @param  integer $type Le type de l'erreur
		 * @return string        Le code associé au type
		 */
		protected static function errorCode($type) {
			return isset(self::$errorCodes[$type]) ? self::$errorCodes[$type] : 'Unknown';
		}

		/**
		 * Association d'un nom au type de l'erreur
		 *
		 * @param  integer $type Le type de l'erreur
		 * @return string        Le nom associé au type
		 */
		protected static function errorName($type) {
			switch($type) {
				case E_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					$name = 'Error';
					break;

				case E_WARNING:
				case E_CORE_WARNING:
				case E_COMPILE_WARNING:
				case E_USER_WARNING:
					$name = 'Warning';
					break;

				case E_NOTICE:
				case E_USER_NOTICE:
					$name = 'Notice';
					break;

				case E_DEPRECATED:
				case E_USER_DEPRECATED:
					$name = 'Deprecated';
					break;

				case E_PARSE:
					$name = 'Parse';
					break;

				case E_STRICT:
					$name = 'Strict';
					break;
					
				case E_RECOVERABLE_ERROR:
					$name = 'Recoverable';
					break;

				default:
					$name = 'Unknown';
					break;
			}
			return $name;
		}
	}
?>