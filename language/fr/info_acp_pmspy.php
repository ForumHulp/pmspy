<?php
/**
 *
 * PM Spy. An extension for the phpBB Forum Software package.
 * French translation by Galixte (https://www.galixte.com)
 *
 * @copyright (c) 2016 ForumHulp <https://www.forumhulp.com>
 * @license GNU General Public License, version 2 (GPL-2.0-only)
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_PM_SPY'			=> 'Accès aux MP',
	'AUTHOR_IP'				=> 'Adresse IP de l’auteur',
	'DATE'					=> 'Date',
	'LAST_ONLINE'			=> 'Dernière visite :',
	'DELETE_PMS'			=> 'Supprimer les MP',
	'NO_PM_SELECTED'		=> 'Aucun MP sélectionné',
	'NO_PM_DATA'			=> 'Aucun MP trouvé.',
	'PM_BOX'				=> 'Contenu dans la boite',
	'PM_SPY_READ'			=> 'Consulter les messages privés',
	'PM_SPY_READ_EXPLAIN'	=> 'Depuis cette page, il est possible de lister tous les messages privés du forum, les rechercher et les supprimer.',
	'READ_MESSAGE'			=> 'Lire ce message',
	'TO'					=> 'À',
	'TOTAL_USERS'			=> 'Total des membres',
	'PM_COUNT'				=> 'Nombre de MP',

	'PM_HOLDBOX'			=> 'Gestion des brouillons de MP',
	'PM_INBOX'				=> 'Boite de réception',
	'PM_NOBOX'				=> 'Aucune boite',
	'PM_OUTBOX'				=> 'Boite d’envoi',
	'PM_SAVED'				=> 'MP sauvegardés',
	'PM_SENTBOX'			=> 'Messages envoyés',

	'SORT_FROM'				=> 'De',
	'SORT_TO'				=> 'À',
	'SORT_BCC'				=> 'CCi',
	'SORT_PM_BOX'			=> 'Boite de MP',

	'LOG_PM_SPY'			=> '<strong>MP supprimés par un administrateur</strong><br />',

	'FH_HELPER_NOTICE'		=> 'L’extension « Forumhulp helper » n’est pas installée ! C’est un pré-requis pour utiliser les extension de son auteur.<br />Merci de la télécharger : <a href="https://github.com/ForumHulp/helper" target="_blank">forumhulp/helper</a> et de l’installer.',
	'PMSPY_NOTICE'			=> '<div class="phpinfo"><p class="entry">Cette extension est disponible dans : %1$s » %2$s » %3$s.</p></div>',
));

// Description of extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Fonctionnalités',
	'DESCRIPTION_NOTICE'	=> 'Compatibilité',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'		=> 'Tri selon différents critères',
			'DESCRIPTION_2'		=> 'Purge des MP',
			'DESCRIPTION_3'		=> 'Recherche par mots clés et noms des membres',
		),
		'note' => array(
			'NOTICE_1'			=> 'phpBB 3.2.x & 3.3.x'
		)
	)
));
