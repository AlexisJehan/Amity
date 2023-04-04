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