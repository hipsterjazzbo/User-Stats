<?php
/*
Plugin Name: User Stats
Description: Adds user statistics to Super Admin > Users page
Version: 1.0
Author: Caleb Fidecaro
*/

function us_add_stats_columns($columns)
{
	$columns['stats'] = 'Stats';
	
	return $columns;
}

function us_fill_stats_columns($blank, $column_name, $user_id)
{
	$user = new WP_User($user_id);
	
	$stats = '<ul>';
	
	if(get_user_meta($user->ID, 'login_count', true) !== '')
	{
		$login_count = get_user_meta($user->ID, 'login_count', true);
		
		$stats .= "<li><strong>Login count:</strong> $login_count</li>";
	}
	
	else
	{
		$stats .= "<li><strong>Login count:</strong> Never logged in</li>";	
	}
	
	if(get_user_meta($user->ID, 'last_login', true) !== '')
	{
		$last_login = get_user_meta($user->ID, 'last_login', true);
		
		$stats .= "<li><strong>Last login:</strong> " . date('d/m/Y', $last_login) . "</li>";
	}
	
	else
	{
		$stats .= "<li><strong>Last login:</strong> Never logged in</li>";	
	}
	
	if(get_user_meta($user->ID, 'files_downloaded', true) !== '')
	{
		$files_downloaded = get_user_meta($user->ID, 'files_downloaded', true);
		
		$stats .= '<li><strong>Files downloaded:</strong> 
						<a class="expand_button" href="#">Click to view</a>
						<ul class="expand_list" style="margin-left: 15px; margin-top: 7px; display: none;">';
		
		foreach($files_downloaded as $file)
		{
			$stats .= '<li><a href="' . $file['file_url'] . '">' . $file['file_name'] . '</a></li>';
		}
		
		$stats .= "</ul></li>";
	}
	
	else
	{
		$stats .= "<li><strong>Files downloaded:</strong> None</li>";	
	}
	
	$stats .= '</ul>';
		
	return $stats;
}

function us_count_login($user_login)
{
	$user = get_userdatabylogin($user_login);
	
	if(get_user_meta($user->ID, 'login_count', true) !== '')
	{
		$login_count = get_user_meta($user->ID, 'login_count', true);
		update_user_meta($user->ID, 'login_count', ((int) $login_count + 1));
	}
	
	else
	{
		update_user_meta($user->ID, 'login_count', 1);
	}
	
	update_user_meta($user->ID, 'last_login', time());
}

function us_count_download($user, $file_name, $file_url)
{
	if(get_user_meta($user->ID, 'files_downloaded', true) !== '')
	{
		$files_downloaded = get_user_meta($user->ID, 'files_downloaded', true);
		$files_downloaded[] = array('file_name' => $file_name, 'file_url' => $file_url);
		
		update_user_meta($user->ID, 'files_downloaded', $files_downloaded);
	}
	
	else
	{
		update_user_meta($user->ID, 'files_downloaded', array(0 => array('file_name' => $file_name, 'file_url' => $file_url)));
	}
}

function us_add_script($slug)
{
	if($slug === "users.php")
	{
		wp_enqueue_script('slidey', '/wp-content/plugins/user-stats/js/slidey.js');
	}
}

add_filter('manage_users_columns', 'us_add_stats_columns');
add_filter('manage_users_custom_column', 'us_fill_stats_columns', 10, 3);
add_action('wp_login', 'us_count_login');
add_action('download_after', 'us_count_download', 10, 3);
add_action('admin_enqueue_scripts', 'us_add_script');