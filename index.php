<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Script principal
	 * 
	 * Ce script s'exécute sur chaque page du site, il instancie le contrôleur frontal de l'application.
	 * 
	 * @author  Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version 03/09/2015
	 * @since   01/07/2014
	 */
	require ('system/framework/boot.php');

	// Hook s'exécutant avant l'instanciation du contrôleur frontal
	if (USE_HOOK) {
		Service::hook()->execute('beforeRun');
	}

	// Instanciation et lancement de l'application
	$application = new FrontController();
	$application->run();

	// Hook s'exécutant après l'instanciation du contrôleur frontal
	if (USE_HOOK) {
		Service::hook()->execute('afterRun');
	}
?>