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
	 * Vérification de la compatibilité
	 *
	 * Ce script permet de vérifier si Amity peut fonctionner correctement sur l'environnement courant.
	 *
	 * @version 25/01/2023
	 * @since   01/07/2014
	 */
	/*
	 * CHANGELOG:
	 * 25/01/2023: Mise à jour des extensions avec « bartlett/php-compatinfo^6.5 »
	 * 01/07/2014: Version initiale
	 */

	// Version d'Amity à tester
	$amityVersion = '0.4.1';

	// Version minimale de PHP requise
	$phpMinVersion = '5.3';

	// Extensions obligatoires
	$requiredExtensions = array(
		'core',
		'date',
		'pcre',
		'reflection',
		'session',
		'spl',
		'standard',
		'zlib',
	);

	// Extensions facultatives
	$optionalExtensions = array(
		'curl'      => 'WebRequest',
		'gd'        => 'Image',
		'intl'      => 'LanguageService',
		'json'      => 'Json',
		//'mbstring'  => 'Logger',														// Optionnel
		//'mysql'     => array('MySQLDatabaseService', 'SpecificMySQLDatabaseService'),	// Déprécié depuis PHP 5.5, supprimé depuis PHP 7.0
		'mysqli'    => array('MySQLiDatabaseService', 'SpecificMySQLiDatabaseService'),
		'pdo'       => 'PDODatabaseService',
		'simplexml' => 'Xml',
	);

	// Apache et l'URL rewriting
	$serverSoftware = $_SERVER['SERVER_SOFTWARE'];
	$useApache = 0 === strpos($serverSoftware, 'Apache');
	$useModRewrite = function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());

	// Version de PHP
	$phpVersion = PHP_VERSION;
	$useValidPhpVersion = 0 <= version_compare($phpVersion, $phpMinVersion);

	// Extensions obligatoires
	$missingRequiredExtensions = array_diff($requiredExtensions, array_filter($requiredExtensions, 'extension_loaded'));
	$useAllRequiredExtensions = 0 === count($missingRequiredExtensions);

	// Extensions facultatives
	$missingOptionalExtensions = array_diff(array_keys($optionalExtensions), array_filter(array_keys($optionalExtensions), 'extension_loaded'));
	$unusableOptionalExtensions = array_intersect_key($optionalExtensions, array_flip($missingOptionalExtensions));
	$useAllOptionalExtensions = 0 === count($missingOptionalExtensions);

	// Status global
	if ((!$useApache || ($useApache && $useModRewrite)) && $useValidPhpVersion && $useAllRequiredExtensions && $useAllOptionalExtensions) {
		$status = 'good';
	} else if ((!$useApache || ($useApache && $useModRewrite)) && $useValidPhpVersion && $useAllRequiredExtensions && !$useAllOptionalExtensions) {
		$status = 'quite';
	} else if (!($useApache && $useModRewrite && $useValidPhpVersion && $useAllRequiredExtensions)) {
		$status = 'bad';
	}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<link rel="icon" type="image/png" href="assets/images/logo-amity.png"/>
		<link rel="stylesheet" href="assets/css/stylesheet.css"/>
		<style>
p.good {
	color: green;
	font-weight: bold;
}

p.quite {
	color: orange;
	font-weight: bold;
}

p.bad {
	color: red;
	font-weight: bold;
}

p.details,
ul.details li {
	text-align: left;
}
		</style>
		<title>Check &ndash; Amity</title>
	</head>
	<body>
<?php
	if ('good' === $status):
?>
		<p class="good">Félicitations ! Votre environnement est entièrement compatible avec <i>Amity <?php echo $amityVersion; ?></i>.</p>
<?php
	elseif ('quite' === $status):
?>
		<p class="quite">Votre environnement est partiellement compatible avec <i>Amity <?php echo $amityVersion; ?></i>.</p>
		<p class="details"><i>Amity</i> peut fonctionner correctement, mais la ou les extensions suivantes sont absentes et font que certaines classes ne peuvent être utilisées:</p>
		<ul class="details">
<?php
		foreach ($unusableOptionalExtensions as $extension => $class):
?>
			<li>L'extension <b><?php echo $extension; ?></b>, utilisée par la ou les classes suivantes: <i><?php echo is_array($class) ? implode(', ', $class) : $class; ?></i>.</li>
<?php
		endforeach;
?>
		</ul>
<?php
	elseif ('bad' === $status):
?>
		<p class="bad">Votre environnement n'est pas compatible avec <i>Amity <?php echo $amityVersion; ?></i>...</p>
		<p class="details">Voici le ou les problèmes de compatibilité détectés:</p>
		<ul class="details">
<?php
		if (!$useApache):
?>
			<li><i>Amity</i> nécessite un serveur <b>Apache</b> pour fonctionner, le serveur actuel est <i><?php echo $serverSoftware; ?></i>.</li>
<?php
		elseif (!$useModRewrite):
?>
			<li><i>Amity</i> nécessite l'activation du module <b>mod_rewrite</b> du serveur <i>Apache</i> pour fonctionner.</li>
<?php
		endif;
		if (!$useValidPhpVersion):
?>
			<li><i>Amity</i> nécessite au minimum <b>PHP <?php echo $phpMinVersion; ?></b> pour fonctionner, vous utilisez <i>PHP <?php echo $phpVersion; ?></i>.</li>
<?php
		endif;
		if (!$useAllRequiredExtensions):
?>
			<li><i>Amity</i> nécessite l'activation de la ou des extensions suivantes: <b><?php echo implode(', ', $missingRequiredExtensions); ?></b>.</li>
<?php
		endif;
?>
		</ul>
<?php
	endif;
?>
	</body>
</html>