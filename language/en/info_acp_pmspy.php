<?php
/**
*
* @package PM SPy
* @copyright (c) 2016 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_PM_SPY'			=> 'PM Spy',
	'AUTHOR_IP'				=> 'Author IP',
	'DATE'					=> 'Date',
	'LAST_ONLINE'			=> 'Last date online:',
	'DELETE_PMS'			=> 'Delete PM’s',
	'NO_PM_SELECTED'		=> 'No PM’s selected',
	'NO_PM_DATA'			=> 'NO PM’s data',
	'PM_BOX'				=> 'PM box',
	'PM_SPY_READ'			=> 'Private Message Spy',
	'PM_SPY_READ_EXPLAIN'	=> 'Here is a list of all private messages in your board.',
	'READ_MESSAGE'			=> 'Click to read this message',
	'TO'					=> 'To',
	'TOTAL_USERS'			=> 'Total users',
	'PM_COUNT'				=> 'PM count',

	'PM_HOLDBOX'			=> 'Held',
	'PM_INBOX'				=> 'Inbox',
	'PM_NOBOX'				=> 'No box',
	'PM_OUTBOX'				=> 'Outbox',
	'PM_SAVED'				=> 'Saved',
	'PM_SENTBOX'			=> 'Sent',

	'SORT_FROM'				=> 'From',
	'SORT_TO'				=> 'To',
	'SORT_BCC'				=> 'BCC',
	'SORT_PM_BOX'			=> 'PM box',

	'LOG_PM_SPY'			=> '<strong>PM’s deleted by PM Spy</strong><br />',

	'FH_HELPER_NOTICE'		=> 'Forumhulp helper application does not exist!<br />Download <a href="https://github.com/ForumHulp/helper" target="_blank">forumhulp/helper</a> and copy the helper folder to your forumhulp extension folder.',
	'PMSPY_NOTICE'			=> '<div class="phpinfo"><p class="entry">This extension resides in %1$s » %2$s » %3$s.</p></div>',
));

// Description of extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Description',
	'DESCRIPTION_NOTICE'	=> 'Extension note',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'		=> 'Sortable',
			'DESCRIPTION_2'		=> 'Automatic prune of older records',
			'DESCRIPTION_3'		=> 'Grouped in find and not find in post',
		),
		'note' => array(
			'NOTICE_1'			=> 'phpBB 3.2 ready'
		)
	)
));
