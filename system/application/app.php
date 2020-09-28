<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Script de configuration de l'application
	 * 
	 * Ce script permet de paramétrer l'application et les services. Il est possible de choisir une configuration différente selon l'environnement utilisé.
	 * 
	 * @package application
	 * @author  Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version 28/09/2020
	 * @since   01/08/2014
	 */
	if (!defined('__SYSTEM__')) exit('<h2>Error</h2><p>You cannot directly access this file.</p>');


	/***************************************************************************
	 *                              CONFIGURATION                              *
	 **************************************************************************/
	$config = array(
		'APP_NAME'         => 'Amity',
		'DEFAULT_LANGUAGE' => 'en',

		'MAINTENANCE'      => FALSE,

		'ENABLE_CACHE'     => TRUE,
		'ENABLE_LOGS'      => TRUE,

		'USE_DATABASE'     => FALSE,
		'USE_DEBUG'        => TRUE,
		'USE_HOOK'         => TRUE,
		'USE_LANGUAGE'     => TRUE,

		// Environnement de développement [« localhost »]
		'localhost|127.0.0.1|::1' => array(
			'DEV_MODE'    => TRUE,
			'FILE_PREFIX' => 'dev_',

			'DB_HOST'     => 'localhost',
			'DB_PORT'     => '',
			'DB_DATABASE' => '',
			'DB_USER'     => '',
			'DB_PASSWORD' => '',
			'DB_ENCODING' => 'utf8mb4',
			'DB_OPTIONS'  => array(),
			'DB_ACCESS'   => 'PDO',
		),

		// Environnement de production [« example.com »]
		'my-online-website.com' => array(
			'DEV_MODE'    => FALSE,
			'FILE_PREFIX' => '',

			'DB_HOST'     => '',
			'DB_PORT'     => '',
			'DB_DATABASE' => '',
			'DB_USER'     => '',
			'DB_PASSWORD' => '',
			'DB_ENCODING' => 'utf8mb4',
			'DB_OPTIONS'  => array(),
			'DB_ACCESS'   => 'PDO',
		),
	);


	/***************************************************************************
	 *                                CROCHETS                                 *
	 **************************************************************************/
	$hooks = array(
		// Crochet s'exécutant avant l'instanciation du contrôleur frontal
		'beforeRun' => function() {
			// ...
		},

		// Crochet s'exécutant après l'instanciation du contrôleur frontal
		'afterRun'  => function() {
			// ...
		},
	);
?>