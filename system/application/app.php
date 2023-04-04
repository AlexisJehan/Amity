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
			'FORCE_HTTPS' => FALSE,

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
			'FORCE_HTTPS' => FALSE,

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
		'afterRun' => function() {
			// ...
		},
	);
?>