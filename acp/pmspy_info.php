<?php
/**
*
* @package PM SPy
* @copyright (c) 2016 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\pmspy\acp;

class pmspy_info
{
	function module()
	{
		return array(
			'filename'	=> 'forumhulp\pmspy\acp\pmspy_info',
			'title'		=> 'ACP_PMSPY',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'spy'	=> array(
					'title'	=> 'ACP_PM_SPY',
					'auth'	=> 'ext_forumhulp/pmspy && acl_a_viewlogs',
					'cat'	=> array('ACP_MESSAGES')
				),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
