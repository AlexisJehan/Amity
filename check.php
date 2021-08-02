<?php
	/**
	 * ☺ Amity Framework
	 * © Copyright Alexis Jehan
	 */
	/**
	 * Vérification de la compatibilité
	 * 
	 * Ce script permet de vérifier si Amity peut fonctionner correctement sur l'environnement courant.
	 * 
	 * @author  Alexis Jehan <alexis.jehan2@gmail.com>
	 * @version 26/03/2016
	 * @since   01/07/2014
	 */
	/*
	 * DONNÉES:
	 * Les données telle que la version minimale PHP compatible ou les extensions utilisées ont été générées en utilisant PHP CompatInfo « https://github.com/llaville/php-compat-info ».
	 */

	// Version d'Amity à tester
	$amityVersion = '0.3.0';

	// Version minimale de PHP requise
	$phpMinVersion = '5.3.0';
	$phpAllVersion = '5.3.0';

	// Extensions obligatoires
	$requiredExtensions = array(
		'Core',
		'date',
		'pcre',
		'session',
		'spl',
		'standard',
		'xml',
		'zlib',
	);

	// Extensions facultatives
	$optionalExtensions = array(
		'PDO'       => 'PDODatabaseService',
		'SimpleXML' => 'Xml',
		'curl'      => 'WebRequest',
		'gd'        => 'Image',
		'json'      => 'Ajax',
		//'mbstring'  => 'Logger',               // Peut être exécuté sans
		//'mysql'     => 'MySQLDatabaseService', // Déprécié
		'mysqli'    => 'MySQLiDatabaseService',
	);

	// Apache et l'URL rewriting
	$serverSoftware = $_SERVER['SERVER_SOFTWARE'];
	$useApache = 0 === strpos($serverSoftware, 'Apache');
	$useModRewrite = function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());

	// Version de PHP
	$phpVersion = PHP_VERSION;
	$useValidPhpVersion = 0 <= version_compare($phpVersion, $phpMinVersion) && 0 <= version_compare($phpVersion, $phpAllVersion);

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
			<li><i>Amity</i> nécessite l'activation du module <b>mod_rewrite</b> d'<i>Apache</i> pour fonctionner.</li>
<?php
		endif;
		if (!$useValidPhpVersion):
?>
			<li><i>Amity</i> nécessite au minimum <b>PHP <?php echo $phpMinVersion; ?></b> pour fonctionner, vous utilisez <i>PHP <?php echo $phpVersion; ?></i>.</li>
<?php
		endif;
		if (!$useAllRequiredExtensions):
?>
			<li><i>Amity</i> nécessite l'activation  de la ou des extensions suivantes: <b><?php echo implode(', ', $missingRequiredExtensions); ?></b>.</li>
<?php
		endif;
?>
		</ul>
<?php
	endif;
?>
	</body>
</html>