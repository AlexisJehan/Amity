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
	 * Traduction du framework
	 *
	 * Ce fichier contient les traductions des contenus du framework.
	 *
	 * @package    framework
	 * @subpackage languages
	 * @version    26/03/2016
	 * @since      05/07/2015
	 */
	if (!defined('__SYSTEM__')) exit('<h2>Error</h2><p>You cannot directly access this file.</p>');

	$dictionary['fr'] = array(

		// Traduction dans les classes :
		// - « core/responses/Image »
		// - « core/responses/Json »
		// - « core/responses/Xml »
		// - « services/LanguageService »
		// - « services/database/MySQLDatabaseService »
		// - « services/database/MySQLiDatabaseService »
		// - « services/database/PDODatabaseService »
		// - « services/database/SpecificMySQLDatabaseService »
		// - « services/database/SpecificMySQLiDatabaseService »
		// - « utils/WebRequest »
		'"%s" extension is not available' => 'L\'extension « %s » n\'est pas disponible',

		// Traduction dans les classes :
		// - « core/responses/authentications/Authentication »
		// - « core/responses/pages/errors/ErrorPage »
		'Error' => 'Erreur',

		// Traduction dans les classes :
		// - « services/database/MySQLDatabaseService »
		// - « services/database/MySQLiDatabaseService »
		'Unavailable or unimplemented fetch method with flag "%s" using "%s" database service' => 'Méthode de récupération indisponible ou non implémentée pour la valeur « %s » en utilisant le service de base de données « %s »',

		// Traduction dans les classes :
		// - « utils/Logger »
		// - « utils/cache/Cache »
		'Unable to create "%s" directory' => 'Impossible de créer le dossier « %s »',

		// Traduction dans la classe « core/FrontController »
		'Responses forwards loop detected, unable to handle the request' => 'Forwards mutuels de réponses, impossible de traiter la requête',

		// Traduction dans la classe « core/Response »
		'Continue' => 'Continuer',
		'Switching Protocols' => 'Changement de protocole',
		'Processing' => 'Traitement en cours',
		'OK' => 'OK',
		'Created' => 'Créé',
		'Accepted' => 'Accepté',
		'Non-Authoritative Information' => 'Information non certifiée',
		'No Content' => 'Pas de contenu',
		'Reset Content' => 'Contenu réinitialisé',
		'Partial Content' => 'Contenu partiel',
		'Multiple Choices' => 'Choix multiples',
		'Moved Permanently' => 'Changement d\'adresse définitif',
		'Moved Temporarily' => 'Changement d\'adresse temporaire',
		'See Other' => 'Voir ailleurs',
		'Not Modified' => 'Non modifié',
		'Use Proxy' => 'Utiliser le proxy',
		'Bad Request' => 'Mauvaise requête',
		'Unauthorized' => 'Non autorisé',
		'Payment Required' => 'Paiement exigé',
		'Forbidden' => 'Interdit',
		'Not Found' => 'Non trouvé',
		'Method Not Allowed' => 'Méthode non autorisée',
		'Not Acceptable' => 'Aucun disponible',
		'Proxy Authentication Required' => 'Authentification proxy exigée',
		'Request Time-out' => 'Requête hors-délai',
		'Conflict' => 'Conflit',
		'Gone' => 'Parti',
		'Length Required' => 'Longueur exigée',
		'Precondition Failed' => 'Précondition échouée',
		'Request Entity Too Large' => 'Corps de requête trop grand',
		'Request-URI Too Large' => 'URI trop long',
		'Unsupported Media Type' => 'Format non supporté',
		'Requested Range Unsatifiable' => 'Plage demandée invalide',
		'Expectation Failed' => 'Comportement erroné',
		'Internal Server Error' => 'Erreur interne du serveur',
		'Not Implemented' => 'Non implémenté',
		'Bad Gateway' => 'Mauvais intermédiaire',
		'Service Unavailable' => 'Service indisponible',
		'Gateway Time-out' => 'Intermédiaire hors-délai',
		'HTTP Version not supported' => 'Version HTTP non supportée',
		'Unable to send HTTP headers because some content has already been sent from "%s" on line %s' => 'Impossible d\'envoyer les entêtes HTTP car du contenu a déjà été retourné depuis « %s » à la ligne %s',
		'The value "%s" is not valid for the HTTP protocol, it must match "HTTP/x.x"' => 'La valeur « %s » est invalide pour le protocole HTTP, elle doit correspondre à « HTTP/x.x »',
		'The value "%s" is not valid for the HTTP status, it must be between 100 and 599' => 'La valeur « %s » est invalide pour le status HTTP, elle doit être comprise entre 100 et 599',
		'A custom status message is expected for the "%s" HTTP status' => 'Un message personnalisé est attendu pour le status HTTP « %s »',

		// Traduction dans la classe « core/responses/Image »
		'The image "%s" is not a valid resource, it must be a "gd" resource' => 'L\'image « %s » n\'est pas une ressource valide, elle doit être une ressource de type « gd »',

		// Traduction dans la classe « core/responses/authentications/Authentication »
		'Username' => 'Nom d\'utilisateur',
		'Success' => 'Succès',
		'Failure' => 'Échec',
		'Status' => 'Status',
		'You need to provide a valid username and password.' => 'Un nom d\'utilisateur et un mot de passe valides sont attendus.',

		// Traduction dans la classe « core/responses/downloads/FileDownload »
		'"%s" is not a valid file or it cannot be read' => '« %s » n\'est pas un fichier valide ou il ne peut être lu',

		// Traduction dans la classe « core/responses/pages/errors/Error403Page »
		'You are not allowed to access this page.' => 'Vous ne pouvez pas accéder à cet emplacement.',

		// Traduction dans la classe « core/responses/pages/errors/Error404Page »
		'The page you requested was not found.' => 'Cet emplacement n\'existe pas ou plus.',

		// Traduction dans la classe « core/responses/pages/errors/Error500Page »
		'The server encountered an error or the database is not accessible.' => 'Le serveur a rencontré un problème ou la base de données n\'est pas accessible.',

		// Traduction dans la classe « core/responses/pages/errors/Error503Page »
		'The website is currently unavailable due to maintenance works, please try again later.' => 'Le site n\'est pas disponible actuellement pour cause de maintenance, merci de retenter ultérieurement.',

		// Traduction dans la classe « core/responses/pages/errors/ErrorPage »
		'An error has occurred.' => 'Une erreur est survenue.',

		// Traduction dans la classe « core/responses/redirects/DelayedRedirect »
		'This location has moved. You will be automatically redirected to its new location in %s seconds. If you aren\'t forwarded to the new page, %sclick here%s.' => 'Cet emplacement a été déplacé. Vous allez être redirigé de manière automatique dans %s secondes. Si vous n\'êtes pas redirigé automatiquement, %scliquez ici%s.',

		// Traduction dans la classe « loaders/AbstractLoader »
		'"%s" location does not exist' => 'L\'emplacement « %s » n\'existe pas',

		// Traduction dans la classe « services/DebugService »
		'reported from %s on line %s.' => 'reporté depuis %s à la ligne %s.',
		'%s started from %s on line %s.' => '%s débuté depuis %s à la ligne %s.',
		'%s stopped from %s on line %s.' => '%s stoppé depuis %s à la ligne %s.',
		'Duration' => 'Durée',
		'%s seconds' => '%s secondes',
		'Name' => 'Nom',
		'Code' => 'Code',
		'Message' => 'Message',
		'File' => 'Fichier',
		'"%s" from %s on line %s.' => '"%s" depuis %s à la ligne %s.',
		'Trace' => 'Trace',
		'%s on line %s:' => '%s à la ligne %s:',
		'Scope' => 'Environnement',

		// Traduction dans la classe « services/HookService »
		'Unable to register hook "%s" because function "%s" does not exist' => 'Impossible d\'enregistrer le crochet « %s » car la fonction « %s » n\'existe pas',
		'Unable to register hook "%s" because class "%s" does not exist' => 'Impossible d\'enregistrer le crochet « %s » car la classe « %s » n\'existe pas',
		'Unable to register hook "%s" because class "%s" does not have method "%s"' => 'Impossible d\'enregistrer le crochet « %s » car la classe « %s » ne possède pas de méthode « %s »',
		'Unable to register hook "%s" because class "%s" has method "%s" but it cannot be accessed' => 'Impossible d\'enregistrer le crochet « %s » car la classe « %s » possède bien la méthode « %s », mais cette dernière n\'est pas accessible',
		'Unable to register hook "%s" because object "%s" does not have method "%s"' => 'Impossible d\'enregistrer le crochet « %s » car l\'objet « %s » ne possède pas de méthode « %s »',
		'Unable to register hook "%s" because object "%s" has method "%s" but it cannot be accessed' => 'Impossible d\'enregistrer le crochet « %s » car l\'objet « %s » possède bien la méthode « %s », mais cette dernière n\'est pas accessible',
		'Unable to register hook "%s" because "%s" does not refer to a class or an object' => 'Impossible d\'enregistrer le crochet « %s » car « %s » ne correspond ni à une classe ni à un objet',
		'Unable to register hook "%s" because "%s" does not refer to a callable' => 'Impossible d\'enregistrer le crochet « %s » car « %s » ne correspond pas à une fonction de rappel',

		// Traduction dans la classe « services/LanguageService »
		'The value "%s" is not valid for the language' => 'La valeur « %s » n\'est pas valide pour la langue',

		// Traduction dans la classe « services/Service »
		'The service "%s" has already been initialised' => 'Le service « %s » a déjà été initialisé',
		'The service "%s" has not been created' => 'Le service « %s » n\'a pas été crée',

		// Traduction dans la classe « utils/DatabaseFactory »
		'Unable to create the database service because "%s" is missing from the configuration' => 'Impossible de créer le service de base de données car « %s » est absent de la configuration',
		'Unable to create the database service because "%s" does not match any' => 'Impossible de créer le service de base de données car « %s » ne correspond à aucun d\'entre eux',

		// Traduction dans la classe « utils/Logger »
		'Date' => 'Date',
		'IP address' => 'Adresse IP',
		'User agent' => 'Agent utilisateur',
		'Referer' => 'Référent',
		'Request' => 'Requête',

		// Traduction dans la classe « utils/Template »
		'Unable to find a template file matching "%s"' => 'Impossible de trouver un fichier de template correspondant à « %s »',
		'The template binding array must be associative' => 'Le tableau de variables de template doit être associatif',
	);
?>