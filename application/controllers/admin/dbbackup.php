<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Dbbackup extends Site_admin_controller
{	
	
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		if ( !$this->user->is_group('admin') ) {
			set_message_note($this->lang->line('error_invalid_access'), MESSAGE_NOTE_FAILURE);
			redirect('admin/home');			
		}
		$this->load->library('database_manager');
		
		$files = $this->database_manager->get_backup_file_list();
		
		toolbar_process_task($this);

		$data = array('rows'=>$files, 
						'version'=>$this->database_manager->get_current_version(),
						);
						
						
		$this->load->view('admin/dbbackup_list', $data);
	}

	public function toolbar_delete()
	{
		
		foreach ( form_checkids_ids() as $id=>$file ) {
			$this->database_manager->remove_backup_file($file);
		}
		redirect(uri_string());
	}
	
	public function toolbar_backup()
	{
		$result = '';
		ini_set('memory_limit', '-1');
        $filename = $this->database_manager->generate_backup_filename();
        $saved_filename = $this->database_manager->backup($filename);
        if ( file_exists($saved_filename)) {
			set_message_note($this->lang->line('success_backup', $filename), MESSAGE_NOTE_SUCCESS);
        }
        else {
			set_message_note($this->lang->line('error_backup', $this->database_manager->get_error()->message), MESSAGE_NOTE_FAILURE);
        }
		redirect(uri_string());
	}
	
	public function toolbar_restore()
	{
		$result = '';
		$checkid = form_checkids_id_value();
		if ( $checkid ) {			
			$filename = trim($checkid);
			if ( $filename ) {
				$result = $this->database_manager->restore_database($filename);
				if ( $result ) {
					set_message_note($this->lang->line('success_restore', $filename), MESSAGE_NOTE_SUCCESS);
				}
				else {
					set_message_note($this->lang->line('error_restore', $this->database_manager->get_error()->message), MESSAGE_NOTE_FAILURE);
				}
			}
		}
		else {
			set_message_note($this->lang->line('error_no_check_id'), MESSAGE_NOTE_WARNING);
		}
//		redirect(uri_string());
	}


	public function download($id)
	{
		if ( !$this->user->is_group('admin') ) {
			set_message_note($this->lang->line('error_invalid_access'), MESSAGE_NOTE_FAILURE);
			redirect('admin/home');			
		}
		$this->load->library('database_manager');
		
		$files = $this->database_manager->get_backup_file_list();
		foreach ( $files as $file ) {
			if ( $file->id == $id ) {
				$fp = fopen($file->get_filename(), 'r');
				if ( $fp ) {
					$fileSize = filesize($file->get_filename());
					header("Pragma: public");
					header("Expires: 0"); // set expiration time
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header('Content-Length: '.$fileSize);
					header('Content-Type: application/gz');
					header('Content-disposition: attachment; filename='.$file->name);
					header("Content-Transfer-Encoding: binary");
					
					while ( !feof($fp) ) {
						print fread($fp, 2048);
					}
					fclose($fp);
				}
			}
		}		
	}
}

?>
