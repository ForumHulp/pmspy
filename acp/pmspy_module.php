<?php
/**
*
* @package PM SPy
* @copyright (c) 2016 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\pmspy\acp;

class pmspy_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $request, $template, $phpbb_container, $phpbb_root_path, $phpEx;

		$this->config			= $config;
		$this->db				= $db;
		$this->user				= $user;
		$this->request			= $request;
		$this->template			= $template;
		$this->pagination		= $phpbb_container->get('pagination');
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->php_ext			= $phpEx;
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
			$start			= $this->request->variable('start', 0);
			$sort_key		= $this->request->variable('sk', 'd');
			$sd = $sort_dir	= $this->request->variable('sd', 'd');
			$delete			= $this->request->is_set_post('delete');

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

			$sort_dir = ($sort_dir == 'd') ? ' DESC' : ' ASC';

			switch ($sort_key)
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
					'AUTHOR_IP'			=> $row['author_ip'],
					'BCC'				=> ($row['bcc_address']) ? $this->get_pm_user_data($row['user_id'], $row['author_id']) : '',
					'DATE'				=> $this->user->format_date($row['message_time']),
					'FOLDER'			=> ($row['folder_id'] > PRIVMSGS_INBOX) ? $this->user->lang['PM_SAVED'] : $pm_box_ary[$row['folder_id']],
					'FROM'				=> $this->get_pm_user_data($row['author_id']),
					'IS_GROUP'			=> (strstr($row['to_address'], 'g')) ? $this->get_pm_group($row['to_address']) : '',

					'LAST_VISIT_FROM'	=> $this->get_last_visit($row['author_id']),
					'LAST_VISIT_TO'		=> ($row['to_address']) ? $this->get_last_visit($row['user_id'], $row['author_id']) : '',

					// We have to replace " in this variable because the template system will not parse it.
					'PM_ID'				=> str_replace('"', '#', serialize(array('msg_ids' => $row['msg_id'], 'user_id' => $row['user_id'], 'folder_id' => $row['folder_id']))),

					// Create a unique key for the js script
					'PM_KEY'			=> $row['msg_id'] . $row['user_id'],
					'PM_SUBJECT'		=> $row['message_subject'],
					'PM_TEXT'			=> generate_text_for_display($row['message_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags),
					'TO'				=> ($row['to_address']) ? $this->get_pm_user_data($row['user_id'], $row['author_id']) : '',
				));
			}
			$this->db->sql_freeresult($result);

			$sort_by_text = array('f' => $this->user->lang['SORT_FROM'],
									't' => $this->user->lang['SORT_TO'],
									'b' => $this->user->lang['SORT_BCC'],
									'p' => $this->user->lang['SORT_PM_BOX'],
									'i' => $this->user->lang['SORT_IP'],
									'd' => $this->user->lang['SORT_DATE']);
			$limit_days = array();
			$s_sort_key = $s_limit_days = $s_sort_dir = $u_sort_param = '';
			gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sd, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

			$base_url = $this->u_action . '&amp;sk=' . $sort_key . '&amp;sd=' . $sd;
			$start = $this->pagination->validate_start($start, 1, $total_pm);
			$this->pagination->generate_template_pagination($base_url, 'pagination', 'start', $total_pm, $config['topics_per_page'], $start);

			$this->template->assign_vars(array(
				'MESSAGE_COUNT'		=> $total_pm,
				'S_SORT_KEY'		=> $s_sort_key,
				'S_SORT_DIR'		=> $s_sort_dir,
				'U_ACTION'			=> $this->u_action,
			));
		}
	}

	function get_pm_group($group)
	{
		//$group = (int) $group;
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

	function get_last_visit($user_id, $author = 0)
	{
		if ($user_id == $author)
		{
			$last_visit = '';
		}
		else
		{
			$sql = 'SELECT session_user_id, MAX(session_time) AS session_time
				FROM ' . SESSIONS_TABLE . '
				WHERE session_time >= ' . (time() - $this->config['session_length']) . '
					AND ' . $this->db->sql_in_set('session_user_id', $user_id) . '
				GROUP BY session_user_id';
			$result = $this->db->sql_query($sql);

			$session_times = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$session_times[$row['session_user_id']] = $row['session_time'];
			}
			$this->db->sql_freeresult($result);

			$sql = 'SELECT user_lastvisit
				FROM ' . USERS_TABLE . '
				WHERE ' . $this->db->sql_in_set('user_id', $user_id);
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$session_time = (!empty($session_times[$user_id])) ? $session_times[$user_id] : 0;
				$last_visit = (!empty($session_time)) ? $session_time : $row['user_lastvisit'];
				$last_visit = ($last_visit) ? $this->user->format_date($last_visit) : '';
			}
			$this->db->sql_freeresult($result);
		}

		return $last_visit;
	}

	function get_pm_user_data($pm_user, $author = 0)
	{
		if ($pm_user == $author)
		{
			$user_info = '';
		}
		else
		{
			$sql = 'SELECT username, user_colour
				FROM ' . USERS_TABLE . '
				WHERE ' . $this->db->sql_in_set('user_id', $pm_user);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);

			$user_info = get_username_string('full',(int) $pm_user, $row['username'], $row['user_colour']);
		}
		return $user_info;
	}
}
