<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/core/site_admin_controller'.EXT);

class Sites extends Site_admin_controller
{	
	
	private $_sort_order;
	private $_row;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('staff_model',
            'staff_assignment_model',
            'site_model','twilio_model','site_shift_model','work_status_model')
        );
		$this->load->helper('site_helper');
	}

	public function index($row_pos = 0)
	{	
                //echo 'call staff';exit;
		$this->_sort_order = $this->site_model->get_sort_order();
		
		$this->_filter = filter_load('filter', array('name_match'=>'',
													'is_published'=>'True')
									);
		toolbar_process_task($this);
		
		
		$this->load->library('pagination');
		$row_pos = intval($row_pos);
		$row_count = $this->site_model->get_row_count($this->_filter);
		$config = array('total_rows'=>$row_count, 
						'base_url'=>site_url('admin/sites/index'),
						'cur_page'=>$row_pos,
						'per_page'=>100
					);
		$this->pagination->initialize($config);


		$rows = $this->site_model->get_rows($this->_filter, $this->_sort_order, $row_pos, $this->pagination->per_page);

		
		$data = array(
						'rows'=>$rows,
						'filter'=>$this->_filter,
						'sort_order'=>$this->_sort_order,
						'pagination_links'=>$this->pagination->create_links(),

					);
		$this->load->view('admin/site_list', $data);		
	}

	public function toolbar_reload()
	{
		
	}
	
	public function toolbar_add()
	{
		$data = array(
						'name'=>'New Site',
						'is_published'=>'True', 
						'update_time'=>date('Y-m-d H:i:s'),
					);
		$id = $this->site_model->add_row($data);
		redirect('admin/sites/edit/'.$id);
		
	}
	
	public function toolbar_delete()
	{
		$count = 0;
		$checkids = $this->get_checkids();
		foreach ( $checkids as $id ) {
			if ( $this->site_model->delete_id($id) ) {
				$count ++;
			}
		} 
		if ( $count > 0 ) {
			set_message_note($this->lang->line('info_delete_items', $count), MESSAGE_NOTE_INFORMATION);
		}
		else {
			set_message_note($this->lang->line('error_no_check_id', 'delete'), MESSAGE_NOTE_WARNING);
		}

		redirect(uri_string());
	}
	
	public function toolbar_toggle_published()
	{
		$checkid = form_checkids_id_value();
		if ( $checkid ) {
			$this->site_model->toggle_is_published($checkid);
			redirect(uri_string());		
		}
		else {
			set_message_note($this->lang->line('error_no_check_id', 'publish/unpublish'), MESSAGE_NOTE_WARNING);
		}
	}
	
	public function toolbar_sort_order_changed()
	{
		$this->_sort_order = form_sort_order_apply($this->_sort_order);		
	}
	
	public function toolbar_edit()
	{
		$checkid = $this->get_checkid();
		if ( $checkid ) {
			redirect('admin/sites/edit/'.$checkid);		
		}
		else {
			set_message_note($this->lang->line('error_no_check_id','edit'), MESSAGE_NOTE_WARNING);
		}
	}


	public function edit($id)
	{
		if ( ! $this->site_model->load_by_id(intval($id)) ) {
			set_message_note($this->lang->line('error_no_record'), MESSAGE_NOTE_FAILURE);
			redirect('admin/sites');
		}
		$this->load->model(array('staff_model', 'site_shift_model', 'postal_district_model', 'staff_assignment_model'));
		$this->load->helper('jquery_tab_helper');
		$this->load->library(array('form_validation'));
		
		$this->form_validation->set_rules('name', 'Name', 'required');
$this->form_validation->set_rules('code', 'Code', 'required');
		$this->form_validation->set_rules('street_number', 'Street Number', '');
		$this->form_validation->set_rules('street_name', 'Street Name', '');
		$this->form_validation->set_rules('unit_number', 'Unit Number', '');
		$this->form_validation->set_rules('city', 'City', '');
		$this->form_validation->set_rules('postcode', 'Post Code', '');
		$this->form_validation->set_rules('country', 'Country', '');
		$this->form_validation->set_rules('is_published', 'Published', '');
	                   $this->form_validation->set_rules('contract_sdate', 'Contract Start Date', '');
                                      $this->form_validation->set_rules('contract_edate', 'Contract End Date', '');
		
		
		$this->_row = $this->site_model->get_row();
		$this->_row = $this->form_validation->get_input_row($this->_row);
		
		toolbar_process_task($this);
		
		
		$data = array('row'=>$this->_row,
					'shift_rows'=>$this->site_shift_model->get_rows(array('site_id'=>$this->site_model->id)),
					'postal_disctrict_row'=>reset($this->postal_district_model->get_rows(array('postcode'=>$this->_row->postcode))),
					'assignment_rows'=>$this->staff_assignment_model->get_rows(array('site_id'=>$this->site_model->id)),
					'shift_type_list'=>$this->staff_assignment_model->get_shift_type_dropdown_list('Shift Type'),
					'assign_type_list'=>$this->staff_assignment_model->get_assign_type_dropdown_list('Assign Type'),
					'staff_list'=>$this->staff_model->get_dropdown_list('Select Staff'),
					'workingdays_rows'=>$this->site_model->get_workingdays_rows(array('site_id'=>$this->site_model->id))
					);
//var_dump($this->staff_model->get_dropdown_list('Select Staff'));exit;
		$this->load->view('admin/site_edit', $data);		
	}
	
	protected function _save()
	{
		if ( $this->form_validation->run() ) {
			$this->site_model->update_row($this->_row, $this->site_model->id);
			$this->_save_assignment_rows();
 //*************** Changed By Dhruvisha On 17th July 2015 start **************//
                        		$this->_save_workingdays_rows();
 //*************** Changed By Dhruvisha On 17th July 2015 end **************//
			$this->_save_site_shift();
                      
			set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
			return true;
		}
		else {
			set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
		}
		return false;
	}
	
	public function toolbar_apply()
	{
		$this->_save();
		redirect(uri_string());
	}
	
	public function toolbar_save()
	{
		if ( $this->_save() ) {
			redirect('admin/sites');
		}
	}
	
	public function toolbar_cancel()
	{
		redirect('admin/sites');
	}

	public function toolbar_add_staff()
	{
		$data = array('site_id'=>$this->site_model->id);
		$this->staff_assignment_model->add_row($data);
	}

 public function _save_site_shift(){
            
            
                $shiftids=$this->input->post('shiftid');  
                $starttime=$this->input->post('start_time');  
                $endtime=$this->input->post('end_time');  
               // var_dump($shiftids);exit;
                $i=0;
                foreach ($shiftids as $ids){
                    $data=array('start_time'=>$starttime[$i],'end_time'=>$endtime[$i]);
                     $this->site_shift_model->update_row($data,$shiftids[$i]);
                     $i++;
		}
                
            jquery_tab_set_tab_index(2);
        }
	
	protected function _save_assignment_rows()
	{
		$staff_ids = $this->input->post('assignment_staff_id');
		$shift_types = $this->input->post('assignment_shift_type');
		$assign_types = $this->input->post('assignment_assign_type');
		
		foreach ( $staff_ids as $assignemnt_id=>$staff_id ) {
			$data = array('staff_id'=>$staff_id,
						'shift_type'=>$shift_types[$assignemnt_id],
						'assign_type'=>$assign_types[$assignemnt_id],
					);
			$this->staff_assignment_model->update_row($data, $assignemnt_id);
		}
		jquery_tab_set_tab_index(2);
	}
	public function toolbar_remove_staff()
	{
		$ids = form_checkids_ids('assignment_check_id');
		if ( count($ids) == 0 ) {
			set_message_note($this->lang->line('error_no_staff_check_id', 'delete'), MESSAGE_NOTE_WARNING);
		}
		foreach ( $ids as $id ) {
			$this->staff_assignment_model->delete_id($id);
		}
		jquery_tab_set_tab_index(2);
	}

       public function toolbar_google_sitesync() 
           
       {
        include_once APPPATH . "libraries/Google/examples/templates/base.php";
        session_start();
        //require_once $_SERVER['DOCUMENT_ROOT'] . '/../googleapi.secrets/secrets.inc.php';
        require_once APPPATH . "libraries/Google/src/Google/autoload.php";
        $client_id = '562916899979-sqstcvrkb5ji1364at447l35sl5pqfa3.apps.googleusercontent.com';
        $client_secret = 'JT8lSp-62FHMjbXNqFp2Gy4V';
        //$redirect_uri = 'http://roster.plug-point.com/index.php/admin/sites/';
        $redirect_uri = 'http://roster.plug-point.com/index.php/admin/sites/getGoogledata';
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        //$client->addScope("https://www.googleapis.com/auth/contacts.readonly");
        $client->addScope("https://www.google.com/m8/feeds");
       // echo 'call google';exit;
        if(isset($_REQUEST['logout'])) {
            unset($_SESSION['access_token']);
        }

        if(isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }

        if(isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            $authUrl = $client->createAuthUrl();
        }
        //echo $authUrl; exit; 
        if($client->getAccessToken()) {
            $_SESSION['access_token'] = $client->getAccessToken();
            $access_token = json_decode($client->getAccessToken())->access_token;
            $url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&v=3.0&oauth_token=' . $access_token;
            $response = file_get_contents($url);
        }

        if($client_id == '<YOUR_CLIENT_ID>' || $client_secret == '<YOUR_CLIENT_SECRET>' || $redirect_uri == '<YOUR_REDIRECT_URI>') {
            echo missingClientSecretsWarning();
        }

        if (isset($authUrl)) {
            header('location:' . $authUrl);
            //echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
        } else {
            //echo "<h3>Results Of Contacts List:</h3>";
            $response = str_replace('$', '_', $response);
            $j = json_decode($response);
            //echo '<pre>';
            $contacts = $j->feed->entry;
           //var_dump($contacts);exit;
            for ($i = 0; $i < count($contacts); $i++) {
                // echo $gcontact=$contacts[$i]->gContact_userDefinedField[0];exit;
                $name = $contacts[$i]->gd_name->gd_fullName->_t;
               
                $site_name = $contacts[$i]->gd_organization[0]->gd_orgName->_t;
                $email = $contacts[$i]->gd_email[0]->address;
                $phone = $contacts[$i]->gd_phoneNumber[0]->_t;
                $street = $contacts[$i]->gd_structuredPostalAddress[0]->gd_formattedAddress->_t;
                //echo $street;exit;
                $city = $contacts[$i]->gd_structuredPostalAddress[0]->gd_city->_t;
                $postalcode = $contacts[$i]->gd_structuredPostalAddress[0]->gd_postcode->_t;
                $customs = $contacts[$i]->gContact_userDefinedField;
                $cluster = $singaporean = $shift1_start = $shift1_end = $shift2_start = $shift2_end = $shift3_start = $shift3_end = $type1 = $ic = $emp_type = $shift_type = $payment_type = $call_preference = $nationality = '';
                for ($j = 0; $j < count($customs); $j++) {

                    $key = $customs[$j]->key;
                    if ($key == 'Cluster') {
                        $cluster = $customs[$j]->value;
                    } else if (strtolower($key) == 'singaporean only') {
                        $singaporean = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift1 start') {
                        $shift1_start = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift1 end') {
                        $shift1_end = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift2 start') {
                        $shift2_start = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift2 end') {
                        $shift2_end = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift3 start') {
                        $shift3_start = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift3 end') {
                        $shift3_end = $customs[$j]->value;
                    } else if (strtolower($key) == 'type') {
                        $type1 = $customs[$j]->value;
                    } else if (strtolower($key) == 'ic') {
                        $ic = $customs[$j]->value;
                    } else if (strtolower($key) == 'employment type') {
                        $emp_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift type') {
                        $shift_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'payment type') {
                        $payment_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'call preference') {
                        $call_preference = $customs[$j]->value;
                    } else if (strtolower($key) == 'nationality') {
                        $nationality = $customs[$j]->value;
                    }
                }
                //echo 'Shift1-start='.$shift2_start;
                //echo ' ==Shift-type='.$shift_type;
                //echo 'ic=' . strtolower($ic);
                if (strtolower($type1) == 'sites') {
                    //insert into db
                    // echo 'code='.$name;exit;
                    $code_exists = $this->site_model->matchsitecode($name);
                    $date = date('Y-m-d h:i:s', time());
                    $data = array('code' => $name, 'name' => $site_name, 'phone' => $phone, 'street_name' => $street, 'city' => $city, 'postcode' => $postalcode,'is_published'=>'True' ,'update_time' => $date);
                    // var_dump($code_exists);
                    
                    if ($code_exists > 0) {
                        //update
                       // echo 'code exists='.$code_exists;
                        $this->site_model->updateSite($data, array('code' => $name));
                        $siteid=$this->site_model->getSiteId($name);
                       // echo 'site_id='.$siteid;
 //delete site shift
                            $this->site_model->deletesiteshift($siteid);          

                    } else {
                         
                        //insert

                        $siteid=$this->site_model->insertSite($data);
}
                       
                        if($shift1_start!=null){
                            $shift1_data= array('site_id'=>$siteid,'shift_type'=>'Day','start_time'=>$shift1_start,'end_time'=>$shift1_end,'update_time' => $date);
                            $this->site_model->insertsiteshift($shift1_data);
                        }
			if($shift2_start!=null){
                            //echo $shift2_start;
                            $shift2_data= array('site_id'=>$siteid,'shift_type'=>'Night','start_time'=>$shift2_start,'end_time'=>$shift2_end,'update_time' => $date);
                            $this->site_model->insertsiteshift($shift2_data);
                        }
                        
                    
                }

               
                /*if (strtolower($type1) == 'staff') {
                    $staff_data = array('code' => $name, 'name' => $site_name, 'phone' => $phone, 'street_name' => $street, 'city' => $city, 'postcode' => $postalcode, 'update_time' => $date);
                }*/
            }
            set_message_note('Site Sync Successfully', MESSAGE_NOTE_INFORMATION);
            redirect(uri_string());		
            exit;
        }
        
    }


 public function export(){
        $this->load->library('PHPExcel');
//$this->load->library('PHPExcel/iofactory');
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'Name')
                ->setCellValue('C1', 'Code')
                ->setCellValue('D1', 'Street Number')
                ->setCellValue('E1', 'Street Name')
                ->setCellValue('F1', 'Unit Number')
                ->setCellValue('G1', 'Phone')
                ->setCellValue('H1', 'City')
                ->setCellValue('I1', 'Postal Code')
                ->setCellValue('J1', 'Country')
                ->setCellValue('K1', 'Published')
                ->setCellValue('L1', 'Updated Time');

               $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("C1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("D1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("E1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("F1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("G1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("H1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("I1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("J1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("K1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("L1")->getFont()->setBold(true);
        
        $i = 2;
       $site_list = $this->site_model->getSiteList();
        foreach ($site_list as $slist) {
        foreach (range('A', 'L') as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $slist->id)
                    ->setCellValue('B' . $i, $slist->name)
                    ->setCellValue('C' . $i, $slist->code)
                    ->setCellValue('D' . $i, $slist->street_number)
                    ->setCellValue('E' . $i, $slist->street_name)
                    ->setCellValue('F' . $i, $slist->unit_number)
                    ->setCellValue('G' . $i, $slist->phone)
                    ->setCellValue('H' . $i, $slist->city)
                    ->setCellValue('I' . $i, $slist->postcode)
                    ->setCellValue('J' . $i, $slist->country)
                    ->setCellValue('K' . $i, $slist->is_published)
                    ->setCellValue('L' . $i, $slist->update_time);

            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Sites List');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Sites.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }


  function getGoogledata() {
	
	include_once APPPATH . "libraries/Google/examples/templates/base.php";
        session_start();
        //require_once $_SERVER['DOCUMENT_ROOT'] . '/../googleapi.secrets/secrets.inc.php';
        require_once APPPATH . "libraries/Google/src/Google/autoload.php";
        $client_id = '562916899979-sqstcvrkb5ji1364at447l35sl5pqfa3.apps.googleusercontent.com';
        $client_secret = 'JT8lSp-62FHMjbXNqFp2Gy4V';
        //$redirect_uri = 'http://roster.plug-point.com/index.php/admin/sites/';
        $redirect_uri = 'http://roster.plug-point.com/index.php/admin/sites/getGoogledata';
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        //$client->addScope("https://www.googleapis.com/auth/contacts.readonly");
        $client->addScope("https://www.google.com/m8/feeds");
       // echo 'call google';exit;
        if(isset($_REQUEST['logout'])) {
            unset($_SESSION['access_token']);
        }

        if(isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            //$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            //header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }

        if(isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            $authUrl = $client->createAuthUrl();
        }
        //echo $authUrl; exit; 
        if($client->getAccessToken()) {
            $_SESSION['access_token'] = $client->getAccessToken();
            $access_token = json_decode($client->getAccessToken())->access_token;
            $url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&v=3.0&oauth_token=' . $access_token;
            $response = file_get_contents($url);
        }

        if($client_id == '<YOUR_CLIENT_ID>' || $client_secret == '<YOUR_CLIENT_SECRET>' || $redirect_uri == '<YOUR_REDIRECT_URI>') {
            echo missingClientSecretsWarning();
        }

        if (isset($authUrl)) {
            header('location:' . $authUrl);
            //echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
        } else {
           // echo "<h3>Results Of Contacts List:</h3>";
            $response = str_replace('$', '_', $response);
            $j = json_decode($response);
          //  echo '<pre>';
            $contacts = $j->feed->entry;
           //var_dump($contacts);exit;
            for ($i = 0; $i < count($contacts); $i++) {
                // echo $gcontact=$contacts[$i]->gContact_userDefinedField[0];exit;
                $name = $contacts[$i]->gd_name->gd_fullName->_t;
//                $full_name=$contacts[$i]->gd_name[0]->gd_fullName->_t;
//                $staff_mobile=$contacts[$i]->gd_phoneNumber[0]->_t;
                $site_name = $contacts[$i]->gd_organization[0]->gd_orgName->_t;
                $email = $contacts[$i]->gd_email[0]->address;
                $phone = $contacts[$i]->gd_phoneNumber[0]->_t;
                $street = $contacts[$i]->gd_structuredPostalAddress[0]->gd_formattedAddress->_t;
                //echo $street;exit;
                $city = $contacts[$i]->gd_structuredPostalAddress[0]->gd_city->_t;
                $postalcode = $contacts[$i]->gd_structuredPostalAddress[0]->gd_postcode->_t;
                $customs = $contacts[$i]->gContact_userDefinedField;
                $cluster = $singaporean = $shift1_start = $shift1_end = $shift2_start = $shift2_end = $shift3_start = $shift3_end = $type1 = $ic = $emp_type = $shift_type = $payment_type = $call_preference = $nationality = '';
                for ($j = 0; $j < count($customs); $j++) {

                    $key = $customs[$j]->key;
                    if ($key == 'Cluster') {
                        $cluster = $customs[$j]->value;
                    } else if (strtolower($key) == 'singaporean only') {
                        $singaporean = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift1 start') {
                        $shift1_start = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift1 end') {
                        $shift1_end = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift2 start') {
                        $shift2_start = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift2 end') {
                        $shift2_end = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift3 start') {
                        $shift3_start = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift3 end') {
                        $shift3_end = $customs[$j]->value;
                    } else if (strtolower($key) == 'type') {
                        $type1 = $customs[$j]->value;
                    } else if (strtolower($key) == 'ic') {
                        $ic = $customs[$j]->value;
                    } else if (strtolower($key) == 'employment type') {
                        $emp_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'shift type') {
                        $shift_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'payment type') {
                        $payment_type = $customs[$j]->value;
                    } else if (strtolower($key) == 'call preference') {
                        $call_preference = $customs[$j]->value;
                    } else if (strtolower($key) == 'nationality') {
                        $nationality = $customs[$j]->value;
                    }
                }
                //echo ' type='.strtolower($type1);
                // echo 'ic=' . strtolower($ic);
                if (strtolower($type1) == 'sites') {
                    //insert into db
                    // echo 'code='.$name;exit;
                    $code_exists = $this->site_model->matchsitecode($name);
                    $date = date('Y-m-d h:i:s', time());
                    $data = array('code' => $name, 'name' => $site_name, 'phone' => $phone, 'street_name' => $street, 'city' => $city, 'postcode' => $postalcode, 'update_time' => $date);
                    // var_dump($code_exists);
                    if ($code_exists > 0) {
                        //update
                        $this->site_model->updateSite($data, array('code' => $name));
                    } else {
                        //insert
                        $this->site_model->insertSite($data);
			if($shift1_start!=null){
                            $shift1_data= array('site_id'=>$siteid,'shift_type'=>$shift_type,'start_time'=>$shift1_start,'end_time'=>$shift1_end);
                            $this->site_model->insertsiteshift($shift1_data);
                        }else if($shift2_start!=null){
                            $shift2_data= array('site_id'=>$siteid,'shift_type'=>$shift_type,'start_time'=>$shift2_start,'end_time'=>$shift2_end);
                            $this->site_model->insertsiteshift($shift2_data);
                        }

                    }
                }

                if (strtolower($type1) == 'staff') {
                    $staff_data = array('code' => $name, 'name' => $site_name, 'phone' => $phone, 'street_name' => $street, 'city' => $city, 'postcode' => $postalcode, 'update_time' => $date);
                }
            }
            set_message_note('Site Sync Successfully', MESSAGE_NOTE_INFORMATION);
            redirect(uri_string());		
            exit;

        }
}
 public function import_sites() {
        $this->load->view('admin/import_sites');
    }

    public function import_sitesexcel() {

        $this->load->library('upload');
        $this->load->helper('file');
        $rootpath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/sites/';
        
$config['upload_path'] = $rootpath;
       $config['allowed_types'] = '*';
        $config['max_size'] = '10000000';


//redirect(uri_string());		 
        $this->upload->initialize($config);

      //  echo $this->upload->file_type; exit;
        if (!$this->upload->do_upload('file')) {

            set_message_note($this->upload->display_errors(), MESSAGE_NOTE_FAILURE);
        } else {
            require_once APPPATH . '/libraries/PHPExcel.php';
            require_once APPPATH . '/libraries/PHPExcel/IOFactory.php';
            //load excel file
            $upload_data = $this->upload->data(); //Returns array of containing all of the data related to the file you uploaded.
         
   $file_name = $upload_data['file_name'];
$inputFileType = PHPExcel_IOFactory::identify($rootpath .$file_name);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            //set to read only
            $objReader->setReadDataOnly(true);

            $objPHPExcel = $objReader->load($rootpath . $file_name);
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            //load model
            $this->load->model("site_model");
            $highestRow = $objWorksheet->getHighestRow();

            //$highestRow = $worksheet->getHighestRow(); 
            //loop from first data until last data
 $checkid = $objWorksheet->getCellByColumnAndRow(0, 1)->getValue();

            $checkname = $objWorksheet->getCellByColumnAndRow(1, 1)->getValue();
            $checkcode = $objWorksheet->getCellByColumnAndRow(2, 1)->getValue();
            $checkstreet_number = $objWorksheet->getCellByColumnAndRow(3, 1)->getValue();
            //$address='nothing';
            $checkstreet_name = $objWorksheet->getCellByColumnAndRow(4, 1)->getValue();
            $checkunit_number = $objWorksheet->getCellByColumnAndRow(5, 1)->getValue();
            //$email='nothng@gmail.com';
            $checkphone = $objWorksheet->getCellByColumnAndRow(6, 1)->getValue();
            $checkcity = $objWorksheet->getCellByColumnAndRow(7, 1)->getValue();
            //$call_type="None";
            $checkpostcode = $objWorksheet->getCellByColumnAndRow(8, 1)->getValue();
            $checkcountry = $objWorksheet->getCellByColumnAndRow(9, 1)->getValue();
            $checkis_published = $objWorksheet->getCellByColumnAndRow(10, 1)->getValue();

$checkupdate_time = $objWorksheet->getCellByColumnAndRow(11, 1)->getValue();
if (strtolower($checkid) !== "id") {
                set_message_note('ID field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }

            if (strtolower($checkname) !== "name") {
                set_message_note('name field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            if (strtolower($checkcode) !== "code") {
                set_message_note('code field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }

            if (strtolower($checkstreet_number) !== "street number") {
                set_message_note('street_number field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            if (strtolower($checkstreet_name) !== "street name") {
                set_message_note('street_name field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            if (strtolower($checkunit_number) !== "unit number") {
                set_message_note('unit_number field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            if (strtolower($checkphone) !== "phone") {
                set_message_note('phone field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            if (strtolower($checkcity) !== "city") {
                set_message_note('city field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            if (strtolower($checkpostcode) !== "postal code") {
                set_message_note('postcode field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            if (strtolower($checkcountry) !== "country") {
                set_message_note('country field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            if (strtolower($checkis_published) !== "published") {
                set_message_note('is_published field is not present in excel sheet', MESSAGE_NOTE_FAILURE);
                redirect('admin/sites/import_sites');
                exit;
            }
            for ($i = 2; $i <= $highestRow; $i++) {
                $name = $objWorksheet->getCellByColumnAndRow(1, $i)->getValue();
                $code = $objWorksheet->getCellByColumnAndRow(2, $i)->getValue();
                $street_number = $objWorksheet->getCellByColumnAndRow(3, $i)->getValue();
                //$address='nothing';
                $street_name = $objWorksheet->getCellByColumnAndRow(4, $i)->getValue();
                $unit_number = $objWorksheet->getCellByColumnAndRow(5, $i)->getValue();
                //$email='nothng@gmail.com';
                $phone = $objWorksheet->getCellByColumnAndRow(6, $i)->getValue();
                $city = $objWorksheet->getCellByColumnAndRow(7, $i)->getValue();
                //$call_type="None";
                $postcode = $objWorksheet->getCellByColumnAndRow(8, $i)->getValue();
                $country = $objWorksheet->getCellByColumnAndRow(9, $i)->getValue();
                $is_published = $objWorksheet->getCellByColumnAndRow(10, $i)->getValue();
$date = $objWorksheet->getCellByColumnAndRow(11, $i)->getValue();
if(empty($date)){

                $date = date('Y-m-d H:i:s', time());
}else{
$date = date('Y-m-d H:i:s',strtotime($date));
}
                $data_user = array(
                    "name" => $name,
                    "code" => $code,
                    "street_number" => $street_number,
                    "street_name" => $street_name,
                    "unit_number" => $unit_number,
                    "phone" => $phone,
                    "city" => $city,
                    "postcode" => $postcode,
                    "country" => $country,
                    "is_published" => $is_published,
                    "update_time" => $date);
                //var_dump($data_user); exit;
                $existcode = $this->site_model->checkcode($code);

                if ($existcode) {
                    $result = $this->site_model->update_sitedata($data_user, array('code' => $code));
                } else {
                    $result = $this->site_model->add_sitedata($data_user);
                }
            }
            if ($result) {

                set_message_note('Sites imported successfully');
            } else {

                set_message_note('Somethiong wrong', MESSAGE_NOTE_WARNING);
            }
        }

        redirect('admin/sites/import_sites');
        exit;
    }
public function toolbar_request_attendance()
	{
	require_once(APPPATH.'/controllers/admin/staff_call'.EXT);	
                $ids = form_checkids_ids('assignment_check_id');
		if ( count($ids) == 0 ) {
			set_message_note($this->lang->line('error_no_staff_check_id', 'request attendance'), MESSAGE_NOTE_WARNING);
		}
                $this->load->library('staff_phone_manager');
                $staff_ins = new Staff_call();
//var_dump($ids); //exit;
		foreach ( $ids as $id ) {
                        $this->staff_assignment_model->load_by_id($id);
                        $next_shift = $this->_get_next_shift_date_time($this->staff_assignment_model->staff_id, $this->staff_assignment_model->site_id, $this->staff_assignment_model->shift_type);
                      //  var_dump($next_shift); exit;
                        if ($next_shift) {
                            if($next_shift->call_type!='Voice'){
                                $this->staff_phone_manager->request_attendance($next_shift->staff_id, $next_shift->site_id, $next_shift->schedule_id, $next_shift->shift_type, $next_shift->start_time,$next_shift->call_type);
                            }else{
                                $res= $staff_ins->Random_Call($next_shift->staff_id,$next_shift->schedule_id);
                            }
                        }
			
		}
redirect(uri_string());	
		jquery_tab_set_tab_index(2);
	}

          protected function _get_next_shift_date_time($staff_id, $site_id, $shift_type) {
        $result = new StdClass();
        $result->staff_id = $staff_id;
        $result->site_id = $site_id;
        $result->shift_type = $shift_type;
        $result->schedule_id = 0;
        $staffs = $this->staff_model->get_rows(array('id' => $staff_id));
//return $staffs; exit;
        $result->call_type = $staffs[0]->call_type;
        $schedule_row = reset($this->schedule_model->get_rows(array('staff_id' => $staff_id,
                    'site_id' => $site_id,
                    'start_date >=' => date('Y-m-d')
                        ), 'start_date'
                )
        );

        if ($schedule_row) {
            $result->schedule_id = $schedule_row->id;
            $result->start_time = $schedule_row->start_date;
            $this->work_status_model->load_by_id($schedule_row->work_status_id);
            if ($this->work_status_model->code == 'WD') {
                $result->shift_type = 'Day';
            } else if ($this->work_status_model->code == 'WN') {
                $result->shift_type = 'Night';
            }
            $shift_row = reset($this->site_shift_model->get_rows(array('site_id' => $site_id, 'shift_type' => $result->shift_type)));
            if ($shift_row) {
                $result->start_time .= ' ' . $shift_row->start_time;
            }
            return $result; 
        }
        return FALSE;
    }
function _save_workingdays_rows(){
        $site_days = $this->input->post('days');
		$site_days_type = $this->input->post('selday');
		$siteid= $this->site_model->id;
		
		foreach ( $site_days as $d ) {
                    $filter = filter_load('filter', array('site_id'=>$siteid,'working_day'=>$d));
                    $row_count = $this->site_model->get_row_workingday_count($filter);
                   
			$data = array('site_id'=>$siteid,'working_day'=>$d,
						'shift_type'=>$site_days_type[$d-1]);
                                           // var_dump($data);
                        if($row_count!=0){
                           
                            $this->site_model->updateSiteWorkingDays($data,array('site_id'=>$siteid,'working_day'=>$d));
                        }else{
                           
                           $this->site_model->insertSiteWorkingDays($data);
                        }
		}
                //exit;
                jquery_tab_set_tab_index(3);
    }
}
?>
