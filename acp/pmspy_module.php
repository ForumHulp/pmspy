<?php
/**
*
* @package PM SPy
* @copyright (c) 2016 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\pmspy\acp;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\user;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\pagination;

class pmspy_module
{
	public $u_action;

	private $config;
	private $db;
	private $user;
	private $request;
	private $template;

	private function setup(config $config, driver_interface $db, user $user, request_interface $request, template $template, pagination $pagination, $phpbb_root_path, $phpEx)
	{
		$this->config 			= $config;
		$this->db 				= $db;
		$this->user 			= $user;
		$this->request 			= $request;
		$this->template 		= $template;
		$this->pagination		= $pagination;
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->php_ext			= $phpEx;
	}

	function main($id, $mode)
	{
		global $config, $db, $user, $request, $template, $phpbb_container, $phpbb_root_path, $phpEx;

		$this->setup($config, $db, $user, $request, $template, $phpbb_container->get('pagination'), $phpbb_root_path, $phpEx);

		$this->tpl_name			= 'acp_pm_spy';
		$this->page_title		= 'ACP_PM_SPY';

		if ($this->request->variable('action', '') == 'details')
		{
			$this->user->add_lang_ext('forumhulp/pmspy', 'info_acp_pmspy');
			$phpbb_container->get('forumhulp.helper')->detail('forumhulp/pmspy');
			$this->tpl_name = 'acp_ext_details';
		} else
		{
			// Start initial var setup
			$start		= $this->request->variable('start', 0);
			$sk			= $this->request->variable('sk', 'd');
			$sd			= $this->request->variable('sd', 'd');
			$delete		= $this->request->is_set_post('delete');

			if ($delete)
			{
				$pm_spy_list = $this->request->variable('mark', array(''));

				// Restore the array to its correct format
				$pm_spy_list = str_replace('#', '"', $pm_spy_list);

				foreach ($pm_spy_list as $pm_msg_list)
				{
					$pm_list[] = unserialize($pm_msg_list);
				}

				if (!sizeof($pm_spy_list))
				{
					trigger_error('NO_PM_SELECTED');
				}

				if (!function_exists('delete_pm'))
				{
					include($this->phpbb_root_path . 'includes/functions_privmsgs.' . $this->php_ext);
				}

				foreach ($pm_list as $row)
				{
					delete_pm($row['user_id'], $row['msg_ids'], $row['folder_id']);
				}

				add_log('admin', 'LOG_PM_SPY');
			}

			$sort_dir = ($sd == 'd') ? ' DESC' : ' ASC';

			switch ($sk)
			{
				case 'b':
					$order_by = 'u.username_clean' . $sort_dir;
					$order_sql = ' AND t.user_id = u.user_id ';
				break;

				case 'd':
					$order_by = 'p.message_time' . $sort_dir;
					$order_sql = ' AND t.user_id = u.user_id ';
				break;

				case 'f':
					$order_by = 'u.username_clean' . $sort_dir;
					$order_sql = ' AND t.author_id = u.user_id ';
				break;

				case 'i':
					$order_by = 'p.author_ip' . $sort_dir. ', u.username_clean ASC';
					$order_sql = ' AND t.user_id = u.user_id ';
				break;

				case 'p':
					$order_by = 't.folder_id' . $sort_dir. ', u.username_clean ASC';
					$order_sql = ' AND t.user_id = u.user_id ';
				break;

				case 't':
					$order_by = 'to_username' . $sort_dir;
					$order_sql = ' AND t.user_id = u.user_id ';
				break;
			}

			// Get PM count for pagination
			$sql = 'SELECT COUNT(msg_id) AS total_pm FROM ' . PRIVMSGS_TO_TABLE;
			$result = $this->db->sql_query($sql);
			$total_pm = (int) $this->db->sql_fetchfield('total_pm');
			$this->db->sql_freeresult($result);

			if ($total_pm == 0)
			{
				trigger_error($this->user->lang['NO_PM_DATA']);
			}

			$pm_box_ary = array(
				PRIVMSGS_HOLD_BOX	=> $this->user->lang['PM_HOLDBOX'],
				PRIVMSGS_NO_BOX		=> $this->user->lang['PM_NOBOX'],
				PRIVMSGS_OUTBOX		=> $this->user->lang['PM_OUTBOX'],
				PRIVMSGS_SENTBOX	=> $this->user->lang['PM_SENTBOX'],
				PRIVMSGS_INBOX		=> $this->user->lang['PM_INBOX'],
			);

			$flags = (($this->config['auth_bbcode_pm']) ? OPTION_FLAG_BBCODE : 0) +
					(($this->config['auth_smilies_pm']) ? OPTION_FLAG_SMILIES : 0) +
					(($this->config['allow_post_links']) ? OPTION_FLAG_LINKS : 0);

			$sql = 'SELECT p.msg_id, p.message_subject, p.message_text, p.bbcode_uid, p.bbcode_bitfield, p.message_time, p.bcc_address, p.to_address, p.author_ip, t.user_id,
					t.author_id, t.folder_id, LOWER(u.username) AS to_username
					FROM ' . PRIVMSGS_TABLE . ' p, ' . PRIVMSGS_TO_TABLE . ' t, ' . USERS_TABLE . ' u
					WHERE p.msg_id = t.msg_id ' .
						$order_sql . '
					ORDER BY ' . $order_by;
			$result = $this->db->sql_query_limit($sql, $this->config['topics_per_page'], $start);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('pm_row', array(
					'AUTHOR_IP'		=> $row['author_ip'],
					'FROM'			=> $this->get_pm_user_data($row['author_id']),
					'TO'			=> ($row['to_address'] && ($row['folder_id'] < PRIVMSGS_OUTBOX || !$row['folder_id'])) ? $this->get_pm_user_data($row['user_id']) : '',
					'BCC'			=> ($row['bcc_address'] && ($row['folder_id'] < PRIVMSGS_OUTBOX || !$row['folder_id'])) ? $this->get_pm_user_data($row['user_id']) : '',
					'DATE'			=> $this->user->format_date($row['message_time']),
					'FOLDER'		=> ($row['folder_id'] > PRIVMSGS_INBOX) ? $this->user->lang['PM_SAVED'] : $pm_box_ary[$row['folder_id']],
					'IS_GROUP'		=> (strstr($row['to_address'], 'g')) ? $this->get_pm_group($row['to_address']) : '',
					'PM_ID'			=> str_replace('"', '#', serialize(array('msg_ids' => $row['msg_id'], 'user_id' => $row['user_id'], 'folder_id' => $row['folder_id']))),
					'PM_KEY'		=> $row['msg_id'] . $row['user_id'],
					'PM_SUBJECT'	=> $row['message_subject'],
					'PM_TEXT'		=> generate_text_for_display($row['message_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags),
				));
			}
			$this->db->sql_freeresult($result);

			$base_url = $this->u_action . '&amp;sk=' . $sk . '&amp;sd=' . $sd;
			$start = $this->pagination->validate_start($start, 1, $total_pm);
			$this->pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_pm, $config['topics_per_page'], $start);

			$this->template->assign_vars(array(
				'MESSAGE_COUNT'		=> $total_pm,
				'U_NAME'			=> $sk,
				'U_SORT'			=> $sd,
				'U_ACTION'			=> $this->u_action,
			));
		}
	}

	private function get_pm_group($group)
	{
		$group = str_replace('g_', '', $group);
		$group = explode(':', $group);
		$sql = 'SELECT group_name FROM ' . GROUPS_TABLE . ' WHERE group_id IN (' . implode(',', $group) . ')';
		$result = $this->db->sql_query($sql);
		$groupname = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$groupname[] = (isset($this->user->lang['G_' . utf8_strtoupper($row['group_name'])])) ? $this->user->lang['G_' . utf8_strtoupper($row['group_name'])] : $row['group_name'];
		}
		return implode(', ', $groupname);
	}

	private function get_pm_user_data($pm_user)
	{
		$sql = 'SELECT username, user_colour, user_lastvisit, MAX(session_time) AS session_time FROM ' . USERS_TABLE . ' u
				LEFT JOIN ' . SESSIONS_TABLE . ' s ON s.session_user_id = u.user_id
				WHERE user_id = ' . $pm_user;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		$user_info = get_username_string('full',(int) $pm_user, $row['username'], $row['user_colour']);
		$last_visit = $this->user->format_date(max($row['user_lastvisit'], $row['session_time']));
		$user_info = str_replace('<a href', '<a' . ((max($row['user_lastvisit'], $row['session_time'])) ? ' title="' . $this->user->lang['LAST_ONLINE'] . ' ' . $last_visit . '"' : '')  . ' href', $user_info);
		return $user_info;
	}
}
