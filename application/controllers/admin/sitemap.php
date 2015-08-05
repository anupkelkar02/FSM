<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . '/core/site_admin_controller' . EXT);

class Sitemap extends Site_admin_controller {

    private $_sort_order;
    private $_row;

    public function __construct() {
        parent::__construct();
        $this->load->model('site_model');
    }

    public function index($row_pos = 0) {
        $this->load->library('googlemaps');
        $config['center'] = '37.4419, -122.1419';
        $config['zoom'] = 'auto';
        $this->googlemaps->initialize($config);
        $this->_filter = filter_load('filter', array('name_match' => '',
            'is_published' => 'True')
        );
        $row_count = $this->site_model->get_row_count($this->_filter);
        $rows = $this->site_model->get_rows($this->_filter);
        $i = 1;
        foreach ($rows as $row) {

            $loc = $this->reverseAddress(urlencode($row->street_name . ' ' . $row->postcode . ',' . $row->country));

            if ($loc['meta']['status'] == 200) {
                $lat = $loc['response']['lat'];
                $lng = $loc['response']['lng'];


                $marker = array();
                $marker['position'] = $lat . ',' . $lng;
                $marker['infowindow_content'] = $row->name;
                $marker['animation'] = 'DROP';
                $marker['icon'] = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' . $i . '|9999FF|000000';
                $this->googlemaps->add_marker($marker);



                $i++;
            }
        }

        $data['map'] = $this->googlemaps->create_map();
        $this->load->view('admin/site_map', $data);
    }

    public function reverseAddress($address) {
        $use_curl = false;
        if ($use_curl) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&sensor=true");
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
                "Content-type: application/binary"
            ));
            curl_setopt($ch, CURLOPT_POST, 1);
            $response = curl_exec($ch);
            if (curl_errno($ch))
                return -1;

            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $str = substr($response, $header_size);

            curl_close($ch);

            $data = json_decode($str, true);
            if (isset($data["results"]) && is_array($data["results"])) {
                $latLong = $data["results"][0]["geometry"]["location"];
                $meta = array(
                    "status" => 200,
                    "message" => "Succeed."
                );
                return array("meta" => $meta, "response" => $latLong);
            } else {
                $meta = array(
                    "status" => 406,
                    "message" => "Address is not known."
                );
                return array("meta" => $meta);
            }
        } else {
            $str = @file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&sensor=true");
            $data = json_decode($str, true);
            if (isset($data["results"]) && is_array(@$data["results"][0])) {
                $latLong = $data["results"][0]["geometry"]["location"];
                $meta = array(
                    "status" => 200,
                    "message" => "Succeed."
                );
                return array("meta" => $meta, "response" => $latLong);
            } else {
                $meta = array(
                    "status" => 406,
                    "message" => "Address is not known."
                );
                return array("meta" => $meta);
            }
        }
    }

}

?>
