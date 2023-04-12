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
	 * @version 12/04/2023
	 * @since   01/07/2014
	 */
	/*
	 * CHANGELOG:
	 * 12/04/2023: Refactorisation
	 * 25/01/2023: Mise à jour des extensions avec « bartlett/php-compatinfo^6.5 »
	 * 01/07/2014: Version initiale
	 */

	// Version d'Amity
	$amityVersion = '0.4.1';

	// Version de PHP minimum
	$phpMinimumVersion = '5.3';

	// Extensions de PHP requises
	$phpRequiredExtensions = array(
		'core',
		'date',
		'pcre',
		'reflection',
		'session',
		'spl',
		'standard',
		'zlib',
	);

	// Extensions de PHP optionnelles avec les classes dépendantes
	$phpOptionalExtensionsClasses = array(
		'curl' => array(
			'WebRequest',
		),
		'gd' => array(
			'Image',
		),
		'intl' => array(
			'LanguageService',
		),
		'json' => array(
			'Json',
		),

		// Optionnelle
		/*'mbstring' => array(
			'Logger', 
		),*/

		// Dépréciée depuis PHP 5.5, supprimée depuis PHP 7.0
		/*'mysql' => array( 
			'MySQLDatabaseService',
			'SpecificMySQLDatabaseService',
		),*/

		'mysqli' => array(
			'MySQLiDatabaseService',
			'SpecificMySQLiDatabaseService',
		),
		'pdo' => array(
			'PDODatabaseService',
		),
		'simplexml' => array(
			'Xml',
		),
	);

	// Apache
	$useApache = 0 === strpos($_SERVER['SERVER_SOFTWARE'], 'Apache');
	$useApacheModRewrite = function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());

	// PHP
	$phpMissingRequiredExtensions = array_diff(
		$phpRequiredExtensions,
		array_filter($phpRequiredExtensions, 'extension_loaded')
	);
	$phpMissingOptionalExtensions = array_diff(
		array_keys($phpOptionalExtensionsClasses),
		array_filter(array_keys($phpOptionalExtensionsClasses), 'extension_loaded')
	);
	$phpUnusableOptionalExtensionsClasses = array_intersect_key(
		$phpOptionalExtensionsClasses,
		array_flip($phpMissingOptionalExtensions)
	);
	$usePhpValidVersion = version_compare(PHP_VERSION, $phpMinimumVersion, '>=');
	$usePhpAllRequiredExtensions = 0 === count($phpMissingRequiredExtensions);
	$usePhpAllOptionalExtensions = 0 === count($phpMissingOptionalExtensions);

	if (
		$useApache
			&& $useApacheModRewrite
			&& $usePhpValidVersion
			&& $usePhpAllRequiredExtensions
			&& $usePhpAllOptionalExtensions
	) {
		$status = 'good';
	} else if (
		$useApache
			&& $useApacheModRewrite
			&& $usePhpValidVersion
			&& $usePhpAllRequiredExtensions
			&& !$usePhpAllOptionalExtensions
	) {
		$status = 'quite';
	} else {
		$status = 'bad';
	}
?>
<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8"/>
		<link rel="icon" type="image/png" href="assets/images/logo-amity.png"/>
		<link rel="stylesheet" href="assets/css/stylesheet.css"/>
		<style>
.status-good,
.status-quite,
.status-bad {
	font-weight: bold;
}

.status-good {
	color: green;
}

.status-quite {
	color: orange;
}

.status-bad {
	color: red;
}

.status-details {
	text-align: left;
}
		</style>
		<title>Check &ndash; Amity</title>
	</head>
	<body>
<?php
	if ('good' === $status):
?>
		<p class="status-good">
			Félicitations !<br/>
			Votre environnement est entièrement compatible avec <em>Amity <?php echo $amityVersion; ?></em>
		</p>
<?php
	elseif ('quite' === $status):
?>
		<p class="status-quite">
			Ouf&mldr;<br/>
			Votre environnement est partiellement compatible avec <em>Amity <?php echo $amityVersion; ?></em>
		</p>
		<div class="status-details">
			<p>Les extensions de <em>PHP</em> suivantes ne sont pas disponibles, certaines classes ne peuvent pas être utilisées :</p>
			<ul>
<?php
		foreach ($phpUnusableOptionalExtensionsClasses as $extension => $classes):
?>
				<li>L'extension <strong><code><?php echo $extension; ?></code></strong>, utilisée par les classes suivantes : <code><?php echo implode(', ', $classes); ?></code></li>
<?php
		endforeach;
?>
			</ul>
		</div>
<?php
	elseif ('bad' === $status):
?>
		<p class="status-bad">
			Dommage&mldr;<br/>
			Votre environnement n'est pas compatible avec <em>Amity <?php echo $amityVersion; ?></em>
		</p>
		<div class="status-details">
			<p>Les problèmes de compatibilité suivants ont été détectés :</p>
			<ul>
<?php
		if (!$useApache):
?>
				<li>Un serveur <strong>Apache</strong> est nécessaire, le serveur actuel est le suivant : <code><?php echo $_SERVER['SERVER_SOFTWARE']; ?></code></li>
<?php
		elseif (!$useApacheModRewrite):
?>
				<li>L'activation du module <strong><code>mod_rewrite</code></strong> du serveur <em>Apache</em> est nécessaire</li>
<?php
		endif;
		if (!$usePhpValidVersion):
?>
				<li>La <strong>version de PHP</strong> minimum nécessaire est <code><?php echo $phpMinimumVersion; ?></code>, la version actuelle est <code><?php echo PHP_VERSION; ?></code></li>
<?php
		endif;
		if (!$usePhpAllRequiredExtensions):
?>
				<li>Les <strong>extensions de PHP</strong> suivantes sont nécessaires : <code><?php echo implode(', ', $phpMissingRequiredExtensions); ?></code></li>
<?php
		endif;
?>
			</ul>
		</div>
<?php
	endif;
?>
	</body>
</html>