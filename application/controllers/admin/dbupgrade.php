<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Dbupgrade extends Site_admin_controller
{	
	public function index()
	{
		
		if ( !$this->user->is_group('admin') ) {
			set_message_note($this->lang->line('error_invalid_access'), MESSAGE_NOTE_FAILURE);
			redirect('admin/home');			
		}
		
		ini_set('memory_limit', '64M');		
/*		
		$this->set_template('admin');

		$this->load->helper(array('toolbar', 
						'form_tools', 
						'user_helper',
						'format_datetime',
						'log')
				);
		if ( $this->config->item('autoload_language_folder') ) {
			$this->set_autoload_language_folder( $this->config->item('autoload_language_folder'));
		}
		
		$this->load->library('menu', array('config'=>'admin/menu'));
		$this->set_output_data('menubar', $this->menu->show());
		$this->set_output_data('user', $this->user);
*/

		$this->load->library('database_manager');
		
		$files = $this->database_manager->get_upgrade_file_list();
		
		toolbar_process_task($this);

								
		$data = array('rows'=>$files, 
						'version'=>$this->database_manager->get_current_version(),
					);
						
		$this->load->view('admin/dbupgrade_list', $data);
	}


	public function toolbar_upgrade()
	{
		$result = '';
		$checkid = form_checkids_id_value();
		if ( $checkid ) {
			$files =  $this->database_manager->get_upgrade_file_list();
			foreach ( $files as $file ) {
				if ( $file->name == $checkid) {
					$result = $this->database_manager->execute($file);
					if ( $result) {
						set_message_note($this->lang->line('success_upgarde'), MESSAGE_NOTE_SUCCESS);
					}
					else {
						set_message_note($this->database_manager->get_error()->message, MESSAGE_NOTE_WARNING);
					}
					break;
				}
			}
		}
		else {
			set_message_note($this->lang->line('error_no_check_id', 'upgrade'), MESSAGE_NOTE_WARNING);
		}
		// redirect(uri_string());
	}
}

?>
