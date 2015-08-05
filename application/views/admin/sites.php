<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . '/core/site_admin_controller' . EXT);

class Sites extends Site_admin_controller {

    private $_sort_order;
    private $_row;

    public function __construct() {
        parent::__construct();
        $this->load->model(array('site_model'));
        $this->load->helper('site_helper');
    }

    public function index($row_pos = 0) {
        $this->_sort_order = $this->site_model->get_sort_order();

        $this->_filter = filter_load('filter', array('name_match' => '',
            'is_published' => 'True')
        );
        toolbar_process_task($this);


        $this->load->library('pagination');
        $row_pos = intval($row_pos);
        $row_count = $this->site_model->get_row_count($this->_filter);
        $config = array('total_rows' => $row_count,
            'base_url' => site_url('admin/sites/index'),
            'cur_page' => $row_pos,
            'per_page' => 100
        );
        $this->pagination->initialize($config);


        $rows = $this->site_model->get_rows($this->_filter, $this->_sort_order, $row_pos, $this->pagination->per_page);


        $data = array(
            'rows' => $rows,
            'filter' => $this->_filter,
            'sort_order' => $this->_sort_order,
            'pagination_links' => $this->pagination->create_links(),
        );
        $this->load->view('admin/site_list', $data);
    }

    public function toolbar_reload() {
        
    }

    public function toolbar_add() {
        $data = array(
            'name' => 'New Site',
            'is_published' => 'True',
            'update_time' => date('Y-m-d H:i:s'),
        );
        $id = $this->site_model->add_row($data);
        redirect('admin/sites/edit/' . $id);
    }

    public function toolbar_delete() {
        $count = 0;
        $checkids = $this->get_checkids();
        foreach ($checkids as $id) {
            if ($this->site_model->delete_id($id)) {
                $count ++;
            }
        }
        if ($count > 0) {
            set_message_note($this->lang->line('info_delete_items', $count), MESSAGE_NOTE_INFORMATION);
        } else {
            set_message_note($this->lang->line('error_no_check_id', 'delete'), MESSAGE_NOTE_WARNING);
        }

        redirect(uri_string());
    }

    public function toolbar_toggle_published() {
        $checkid = form_checkids_id_value();
        if ($checkid) {
            $this->site_model->toggle_is_published($checkid);
            redirect(uri_string());
        } else {
            set_message_note($this->lang->line('error_no_check_id', 'publish/unpublish'), MESSAGE_NOTE_WARNING);
        }
    }

    public function toolbar_sort_order_changed() {
        $this->_sort_order = form_sort_order_apply($this->_sort_order);
    }

    public function toolbar_edit() {
        $checkid = $this->get_checkid();
        if ($checkid) {
            redirect('admin/sites/edit/' . $checkid);
        } else {
            set_message_note($this->lang->line('error_no_check_id', 'edit'), MESSAGE_NOTE_WARNING);
        }
    }

    public function edit($id) {
        if (!$this->site_model->load_by_id(intval($id))) {
            set_message_note($this->lang->line('error_no_record'), MESSAGE_NOTE_FAILURE);
            redirect('admin/sites');
        }
        $this->load->model(array('staff_model', 'site_shift_model', 'postal_district_model', 'staff_assignment_model'));
        $this->load->helper('jquery_tab_helper');
        $this->load->library(array('form_validation'));

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('street_number', 'Street Number', '');
        $this->form_validation->set_rules('street_name', 'Street Name', '');
        $this->form_validation->set_rules('unit_number', 'Unit Number', '');
        $this->form_validation->set_rules('city', 'City', '');
        $this->form_validation->set_rules('postcode', 'Post Code', '');
        $this->form_validation->set_rules('country', 'Country', '');
        $this->form_validation->set_rules('is_published', 'Published', '');



        $this->_row = $this->site_model->get_row();
        $this->_row = $this->form_validation->get_input_row($this->_row);

        toolbar_process_task($this);


        $data = array('row' => $this->_row,
            'shift_rows' => $this->site_shift_model->get_rows(array('site_id' => $this->site_model->id)),
            'postal_disctrict_row' => reset($this->postal_district_model->get_rows(array('postcode' => $this->_row->postcode))),
            'assignment_rows' => $this->staff_assignment_model->get_rows(array('site_id' => $this->site_model->id)),
            'shift_type_list' => $this->staff_assignment_model->get_shift_type_dropdown_list('Shift Type'),
            'assign_type_list' => $this->staff_assignment_model->get_assign_type_dropdown_list('Assign Type'),
            'staff_list' => $this->staff_model->get_dropdown_list('Select Staff')
        );
        $this->load->view('admin/site_edit', $data);
    }

    protected function _save() {
        if ($this->form_validation->run()) {
            $this->site_model->update_row($this->_row, $this->site_model->id);
            $this->_save_assignment_rows();
            set_message_note($this->lang->line('success_save'), MESSAGE_NOTE_SUCCESS);
            return true;
        } else {
            set_message_note($this->form_validation->error_string(), MESSAGE_NOTE_WARNING);
        }
        return false;
    }

    public function toolbar_apply() {
        $this->_save();
        redirect(uri_string());
    }

    public function toolbar_save() {
        if ($this->_save()) {
            redirect('admin/sites');
        }
    }

    public function toolbar_cancel() {
        redirect('admin/sites');
    }

    public function toolbar_add_staff() {
        $data = array('site_id' => $this->site_model->id);
        $this->staff_assignment_model->add_row($data);
    }

    protected function _save_assignment_rows() {
        $staff_ids = $this->input->post('assignment_staff_id');
        $shift_types = $this->input->post('assignment_shift_type');
        $assign_types = $this->input->post('assignment_assign_type');

        foreach ($staff_ids as $assignemnt_id => $staff_id) {
            $data = array('staff_id' => $staff_id,
                'shift_type' => $shift_types[$assignemnt_id],
                'assign_type' => $assign_types[$assignemnt_id],
            );
            $this->staff_assignment_model->update_row($data, $assignemnt_id);
        }
        jquery_tab_set_tab_index(2);
    }

    public function toolbar_remove_staff() {
        $ids = form_checkids_ids('assignment_check_id');
        if (count($ids) == 0) {
            set_message_note($this->lang->line('error_no_staff_check_id', 'delete'), MESSAGE_NOTE_WARNING);
        }
        foreach ($ids as $id) {
            $this->staff_assignment_model->delete_id($id);
        }
        jquery_tab_set_tab_index(2);
    }

    //**********************Changed By Dhruvisha On 23rd May 2015 start ****************************//
    public function toolbar_site_upload() {
        $this->load->view('admin/upload_site');
    }

    public function upload_excel() {
        require_once APPPATH . "/libraries/excel_reader2.php";
        // $this->load->library('excel_reader2');
        $temp = explode(".", $_FILES["site_sheet"]["name"]);
        $extension = end($temp);

        $allowedExts = array('xls', 'xlsx');
        if (in_array($extension, $allowedExts)) {
            $destination = $_FILES["site_sheet"]["name"];
            if (move_uploaded_file($_FILES["site_sheet"]["tmp_name"], $destination)) {
                $data = new Spreadsheet_Excel_Reader($destination);
                for ($i = 0; $i < count($data->sheets); $i++) { // Loop to get all sheets in a file.
                    if (count($data->sheets[$i][cells]) > 0) { // checking sheet not empty
                        for ($j = 1; $j <= count($data->sheets[$i][cells]); $j++) { // loop used to get each row of the sheet
                            echo $data->$data->sheets[$i][cells][$j] . '<br/>';
                        }
                    }
                }
                var_dump($_FILES['site_sheet']['type']);
                exit;
            } else {
                $this->data['error'] = 'Error occured ! Only excel sheet is allowed.';
            }
        } else {
            $this->data['error'] = 'Error occured ! Can n excel sheet is allowed.';
        }
    }

    public function toolbar_google_sitesync() 
    {
        var_dump('call');exit;
        include_once APPPATH . "libraries/Google/examples/templates/base.php";
        session_start();
        //require_once $_SERVER['DOCUMENT_ROOT'] . '/../googleapi.secrets/secrets.inc.php';
        require_once APPPATH . "libraries/Google/src/Google/autoload.php";
        $client_id = '354679217715-0f0fu7ejpvmh7m408ll1thjs7vlemmk5.apps.googleusercontent.com';
        $client_secret = 'hT9fCTljAoBSUcA5nucxb7FJ';
        //$scriptUri = 'http://fingerskies.com/aza_twilio/admin/sites';
        $redirect_uri = 'http://fingerskies.com/az-twilio/index.php/admin/sites/getGoogledata';
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        //$client->addScope("https://www.googleapis.com/auth/contacts.readonly");
        $client->addScope("https://www.google.com/m8/feeds");
    
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
            echo "<h3>Results Of Contacts List:</h3>";
            $response = str_replace('$', '_', $response);
            $j = json_decode($response);
            echo '<pre>';
            $contacts = $j->feed->entry;
            var_dump($contacts);exit;
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
                    }
                }

                if (strtolower($type1) == 'staff') {
                    $staff_data = array('code' => $name, 'name' => $site_name, 'phone' => $phone, 'street_name' => $street, 'city' => $city, 'postcode' => $postalcode, 'update_time' => $date);
                }
            }
            file_put_contents('tst.json', $response);
        }
    }

    function getGoogledata() {
        include_once APPPATH . "libraries/Google/examples/templates/base.php";
        session_start();

//require_once $_SERVER['DOCUMENT_ROOT'] . '/../googleapi.secrets/secrets.inc.php';

        require_once APPPATH . "libraries/Google/src/Google/autoload.php";

        /*         * **********************************************
          ATTENTION: Fill in these values! Make sure
          the redirect URI is to this page, e.g:
          http://localhost:8080/user-example.php
         * ********************************************** */
        $client_id = '354679217715-0f0fu7ejpvmh7m408ll1thjs7vlemmk5.apps.googleusercontent.com';
        $client_secret = 'hT9fCTljAoBSUcA5nucxb7FJ';
//$scriptUri = 'http://fingerskies.com/aza_twilio/admin/sites';
        $redirect_uri = 'http://fingerskies.com/az-twilio/index.php/admin/sites/getGoogledata';

        /*         * **********************************************
          Make an API request on behalf of a user. In
          this case we need to have a valid OAuth 2.0
          token for the user, so we need to send them
          through a login flow. To do this we need some
          information from our API console project.
         * ********************************************** */
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
//$client->addScope("https://www.googleapis.com/auth/contacts.readonly");
        $client->addScope("https://www.google.com/m8/feeds");

        /*         * **********************************************
          A general service created
         * ********************************************** */
//$contacts = new Google_Service($client);



        /*         * **********************************************
          Boilerplate auth management - see
          user-example.php for details.
         * ********************************************** */
        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['access_token']);
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            //var_dump($client);exit;
            $_SESSION['access_token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            $authUrl = $client->createAuthUrl();
        }
//echo $authUrl; exit; 
        /*         * **********************************************
          If we're signed in, retrieve contacts
         * ********************************************** */

        if ($client->getAccessToken()) {
            $_SESSION['access_token'] = $client->getAccessToken();

            $access_token = json_decode($client->getAccessToken())->access_token;

            //$url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=3&alt=json&v=3.0&oauth_token='.$access_token;
            //$Gurl = 'https://www.google.com/m8/feeds/groups/default/full?alt=json&v=3.0&oauth_token='.$access_token;
            //$Gresponse =  file_get_contents($Gurl);
            //echo '<pre>';
            //var_dump(json_decode($Gresponse); 
            $url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&v=3.0&oauth_token=' . $access_token;

            $response = file_get_contents($url);
        }

//echo pageHeader("User Query - Multiple APIs");
        if (
                $client_id == '<YOUR_CLIENT_ID>' || $client_secret == '<YOUR_CLIENT_SECRET>' || $redirect_uri == '<YOUR_REDIRECT_URI>') {
            echo missingClientSecretsWarning();
        }

        if (isset($authUrl)) {
            echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
        } else {
            echo "<h3>Results Of Contacts List:</h3>";
            $response = str_replace('$', '_', $response);
            //var_dump($response);
            //$t = str_replace("\n", '', $response);
            $j = json_decode($response);
            $j = str_replace('$', '_', $j);
            echo '<pre>';
            // var_dump($j->feed->entry);
            /* foreach($j->feed->entry as $contacts){
              $name = $contacts->title;
              $email = $contacts->gd$email[0]->address;
              $phone = $contacts->gd$phoneNumber[0]->$t;
              $address = $contacts->structuredPostalAddress[0]->gd$formattedAddress;
              $customs = $contacts->gContact$userDefinedField;
              foreach ($customs as $f){
              $key = $f->key;
              $val = $f->value;
              if($key=='Type' && $val=='Sites'){
              //Insert into DB
              echo $name.';'.$email.';'.$phone.';'.$address.';'.$key.';'.$val.'<br/>';
              }
              }
              } */
            file_put_contents('tst.json', $response);
        }
    }

}

?>