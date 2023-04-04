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
	 * Script de lancement du système (bootstrap)
	 * 
	 * Ce script prépare le système à son exécution.
	 * Il se charge entre autres de définir les constantes du framework, de crée les liens symboliques, de charger la configuration
	 * de l'application, et instancie l'autoloader ainsi que les services optionnels ou obligatoires.
	 * 
	 * @package framework
	 * @author  Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version 29/08/2022
	 * @since   01/08/2014
	 */
	/*
	 * CHANGELOG:
	 * 29/08/2022: Ajout systématique de la langue dans l'URL lors de l'utilisation du service multi-lingue
	 * 02/08/2021: Possibilité de forcer l'usage de HTTPS
	 * 01/07/2020: Ajout de la personnalisation d'options à la connexion à la base de données
	 * 10/06/2020: Compatibilité avec PHP 7.4, « get_magic_quotes_gpc() » est devenu déprécié
	 * 04/04/2018: Amélioration de la fonction « path() »
	 * 19/07/2015: Meilleure gestion de la configuration de l'application, avec la définition des globales personnalisées
	 * 01/07/2015: - Ajout du service de débogage
	 *             - Ajout des liens vers le nouveau dossier « contents »
	 *             - Nouveau mode de fonctionnement pour la configuration de l'application
	 * 23/06/2015: Ajout des fonctions « path() » et « url() »
	 * 22/06/2015: Suppression des « magic quotes » si activées
	 * 01/08/2014: Version initiale
	 */


	/***************************************************************************
	 *                         VARIABLES DU FRAMEWORK                          *
	 **************************************************************************/

	/**
	 * Variable attestant du lancement du système
	 * 
	 * @package framework
	 */
	define('__SYSTEM__', TRUE);

	/**
	 * Nom du framework
	 * 
	 * @package framework
	 */
	define('__NAME__', 'Amity');

	/**
	 * Version du framework
	 * 
	 * @package framework
	 */
	define('__VERSION__', '0.4.1');

	/**
	 * Temps de lancement de la génération de la page (timestamp avec micro-secondes)
	 * 
	 * @package framework
	 */
	define('__START__', microtime(TRUE));


	/***************************************************************************
	 *                           DÉBUT DU LANCEMENT                            *
	 **************************************************************************/

	// Déprécié depuis PHP 7.4, supprimé depuis PHP 8.0
	if (version_compare(PHP_VERSION, '7.4', '<') && get_magic_quotes_gpc()) {
		$stripslashes = function(&$value) {
			$value = stripslashes($value);
		};
		array_walk_recursive($_GET, $stripslashes);
		array_walk_recursive($_POST, $stripslashes);
		array_walk_recursive($_COOKIE, $stripslashes);
		array_walk_recursive($_REQUEST, $stripslashes);
		unset($stripslashes);
	}

	// Activation des sessions
	session_start();

	// Charset en UTF-8 (« text/html » pour interpréter les éventuels messages du débugueur)
	header('Content-type: text/html; charset=utf-8');
	//header('Content-Type: text/plain; charset=utf-8');

	// Ajout d'un timezone si aucun n'est spécifié pour la fonction de date
	if (!ini_get('date.timezone')) {
		date_default_timezone_set('GMT');
	}


	/***************************************************************************
	 *                            LIENS SYMBOLIQUES                            *
	 **************************************************************************/

	/**
	 * Chemin vers la racine du site
	 * 
	 * @package framework
	 */
	define('BASE_DIR', realpath(__DIR__ . '/../..'));

	/**
	 * Lien vers la racine du site
	 * 
	 * @package framework
	 */
	define('BASE_URL', ((!empty($_SERVER['HTTPS']) && 'off' !== $_SERVER['HTTPS']) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), '\\/'));

	/**
	 * Chemin vers le dossier de l'application
	 * 
	 * @package framework
	 */
	define('APPLICATION_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'application');

	/**
	 * Chemin vers le dossier du framework
	 * 
	 * @package framework
	 */
	define('FRAMEWORK_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'framework');

	/**
	 * Chemin vers le dossier de cache de l'application
	 * 
	 * @package framework
	 */
	define('CACHE_DIR', APPLICATION_DIR . DIRECTORY_SEPARATOR . 'cache');

	/**
	 * Chemin vers le dossier des traductions de l'application
	 * 
	 * @package framework
	 */
	define('LANGUAGES_DIR', APPLICATION_DIR . DIRECTORY_SEPARATOR . 'languages');

	/**
	 * Chemin vers le dossier des fichiers de journalisation de l'application
	 * 
	 * @package framework
	 */
	define('LOGS_DIR', APPLICATION_DIR . DIRECTORY_SEPARATOR . 'logs');

	/**
	 * Chemin vers le dossier des templates de l'application
	 * 
	 * @package framework
	 */
	define('TEMPLATES_DIR', APPLICATION_DIR . DIRECTORY_SEPARATOR . 'templates');

	/**
	 * Chemin vers le dossier des composants
	 * 
	 * @package framework
	 */
	define('ASSETS_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'assets');

	/**
	 * Lien vers le dossier des composants
	 * 
	 * @package framework
	 */
	define('ASSETS_URL', BASE_URL . '/assets');

	/**
	 * Chemin vers le dossier du contenu
	 * 
	 * @package framework
	 */
	define('CONTENTS_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'contents');

	/**
	 * Lien vers le dossier du contenu
	 * 
	 * @package framework
	 */
	define('CONTENTS_URL', BASE_URL . '/contents');


	/***************************************************************************
	 *                       VARIABLES DE L'APPLICATION                        *
	 **************************************************************************/

	// On importe la configuration personnalisée
	require (APPLICATION_DIR . '/app.php');

	// Pour chaque entrée de la configuration
	foreach ($config as $key => $value) {

		// Si l'entrée est un tableau, alors c'est une configuration spécifique à un serveur
		if (is_array($value)) {

			// On supprime la configuration spécifique qu'elle soit celle du serveur courant ou non
			unset($config[$key]);

			// Un identifiant de serveur peut être un nom de serveur ou une adresse IP, s'ils sont plusieurs ils doivent être séparés
			// par un « | »
			$key = explode('|', $key);
			if (in_array($_SERVER['SERVER_NAME'], $key) || in_array($_SERVER['SERVER_ADDR'], $key)) {

				// On ajoute chaque entrée spécifique à la configuration globale
				foreach ($value as $key => $value) {
					$config[$key] = $value;
				}
			}
		}
	}

	// On fusionne les valeurs par défaut avec celles personnalisées
	$config = array_merge(
		array(
			'APP_NAME'         => 'Application',
			'DEFAULT_LANGUAGE' => 'en',
			'MAINTENANCE'      => FALSE,
			'ENABLE_CACHE'     => TRUE,
			'ENABLE_LOGS'      => TRUE,
			'USE_DATABASE'     => FALSE,
			'USE_DEBUG'        => TRUE,
			'USE_HOOK'         => TRUE,
			'USE_LANGUAGE'     => TRUE,
			'DEV_MODE'         => FALSE,
			'FILE_PREFIX'      => '',
			'FORCE_HTTPS'      => FALSE,
			'DB_ENCODING'      => 'utf8',
			'DB_OPTIONS'       => array(),
			'DB_ACCESS'        => 'PDO',
		),

		// On met les noms de clés en majuscule, pour les noms de constantes
		array_change_key_case($config, CASE_UPPER)
	);

	// Pour chaque valeur de la configuration on la définit
	foreach ($config as $key => $value) {

		// Si elle a le préfixe d'une constante de base de données, alors on ne la définit pas
		if (0 !== strpos($key, 'DB_')) {
			if (!defined($key)) {

				/**
				 * Déclaration dynamique des constantes de l'application
				 *
				 * @package 
				 */
				define($key, $value);
			}
			unset($config[$key]);
		}
	}


	/***************************************************************************
	 *                           REPORT DES ERREURS                            *
	 **************************************************************************/

	// On décide de reporter tous les types d'erreurs
	error_reporting(-1);

	// Selon le mode (développeur ou non), on affiche ou non les erreurs
	ini_set('display_errors', DEV_MODE);

	// Si la journalisation est activée alors on l'utilise pour les erreurs via le module PHP dédié
	ini_set('log_errors', ENABLE_LOGS);
	ini_set('error_log', LOGS_DIR . '/php_errors.log');


	/***************************************************************************
	 *                       AUTO-CHARGEMENT DES CLASSES                       *
	 **************************************************************************/

	// Instanciation et configuration de l'autoloader
	require (FRAMEWORK_DIR . '/classes/utils/cache/Cache.php');
	require (FRAMEWORK_DIR . '/classes/utils/cache/VariableCache.php');
	require (FRAMEWORK_DIR . '/classes/loaders/AbstractLoader.php');
	require (FRAMEWORK_DIR . '/classes/loaders/ClassLoader.php');
	$autoloader = new ClassLoader();
	$autoloader
		->addArray(
			array(
				FRAMEWORK_DIR . DIRECTORY_SEPARATOR . 'classes',
				APPLICATION_DIR . DIRECTORY_SEPARATOR . 'classes',
			)
		)
		->register()
		->load();


	/***************************************************************************
	 *                                SERVICES                                 *
	 **************************************************************************/

	// Création du service de débogage et enregistrement en mode de développement (si activé)
	if (USE_DEBUG) {
		Service::debug(new DebugService())->register();
	}

	// Instanciation du service multi-lingue (si activé)
	if (USE_LANGUAGE) {
		Service::language(new LanguageService());
	}

	// Création du service d'accès à la base de données (si activé)
	if (USE_DATABASE) {
		Service::database(DatabaseFactory::create($config))->connect();
	}

	// Création du service des crochets
	if (USE_HOOK) {
		Service::hook(new HookService())->registerArray($hooks);
	}


	/***************************************************************************
	 *                          FONCTIONS DU SYSTÈME                           *
	 **************************************************************************/

	/**
	 * Retourne un hash de la révision du framework courant
	 *
	 * @package framework
	 *
	 * @return string Le hash MD5 du framework courant
	 */
	function revision() {
		$files = array(md5_file(BASE_DIR . '/index.php'));
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(BASE_DIR . '/system/framework'));
		foreach ($iterator as $file) {
			if ($file->isDir()) {
				continue;
			}
			$files[] = md5_file($file->getPathname());
		}
		sort($files);
		return md5(implode('', $files));
	}

	/**
	 * Retourne le temps d'exécution depuis le lancement du système en secondes
	 *
	 * @package framework
	 *
	 * @param  integer $precision Le nombre de chiffres derrière la virgule du temps retourné
	 * @return double             Le temps d'exécution
	 */
	function chrono($precision = 3) {
		return round(microtime(TRUE) - __START__, $precision);
	}

	/**
	 * Retourne le chemin d'un emplacement depuis la racine du projet
	 *
	 * @package framework
	 *
	 * @param  string $locations Le chemin, avec éventuellement plusieurs sous-chemins [optionnel]
	 * @return string            Le chemin de l'emplacement depuis la racine du projet
	 */
	function path($locations = '') {
		$path = '';
		if (0 < func_num_args()) {
			foreach (func_get_args() as $i => $location) {
				$path .= (0 < $i ? DIRECTORY_SEPARATOR : '') . str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $location);
			}
		}
		if (0 === strpos($path, BASE_DIR)) {
			$path = substr($path, strlen(BASE_DIR) + 1);
		}
		return !empty($path) ? '.' . DIRECTORY_SEPARATOR . $path : '.';
	}

	/**
	 * Retourne l'URL d'un emplacement depuis la base du projet
	 *
	 * @package framework
	 *
	 * @param  string $locations L'emplacement, avec éventuellement plusieurs sous-emplacements [optionnel]
	 * @return string            L'URL de l'emplacement depuis la base du projet
	 */
	function url($locations = '') {
		$url = '';

		// Si le service multi-lingue est activé, on ajoute la langue dans l'URL
		if (USE_LANGUAGE) {
			$url .= Service::language()->getLanguage() . '/';
		}

		if (0 < func_num_args()) {
			foreach (func_get_args() as $i => $location) {
				$url .= (0 < $i ? '/' : '') . str_replace('\\', '/', trim($location, '/\\'));
			}
		}
		if (0 === strpos($url, BASE_URL)) {
			$url = substr($url, strlen(BASE_URL) + 1);
		}
		return !empty($url) ? BASE_URL . '/' . $url : BASE_URL;
	}

	/**
	 * Traduction d'une phrase selon si le service multi-lingue est activé ou non
	 *
	 * @package framework
	 *
	 * @param  string $message  Le message à traduire
	 * @param  mixed  $args,... unlimited OPTIONAL number of additional variables to display with var_dump()
	 * @return string           Le message éventuellement complété et rempli
	 */
	function __($message) {

		// On récupère les arguments de la fonction
		$args = func_get_args();

		// Si le service multi-lingue est activé, on tente de traduire le message (Premier argument)
		if (USE_LANGUAGE) {
			$message = $args[0] = Service::language()->translate($message);
		}

		// Si le message doit être complété on le fait avec les arguments supplémentaires
		if (1 < func_num_args()) {
			return call_user_func_array('sprintf', $args);
		}

		// Sinon on se contente de retourner le message
		return $message;
	}


	/***************************************************************************
	 *                                NETTOYAGE                                *
	 **************************************************************************/

	// Fonction qui s'exécute à la fin de l'exécution
	register_shutdown_function(function() {

		// Si le service de base de données est actif, on ferme la connexion si elle est encore active
		if (USE_DATABASE) {
			$database = Service::database();
			if ($database->isConnected()) {
				$database->disconnect();
			}
		}
	});


	/***************************************************************************
	 *                            FIN DU LANCEMENT                             *
	 **************************************************************************/

	// Suppression des variables de l'application par sécurité
	unset($config, $hooks, $key, $value);
?>