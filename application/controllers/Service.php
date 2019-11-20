<?php

use Restserver \Libraries\REST_Controller;

class Service extends REST_Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('ServiceModel');
        $this->load->library('form_validation');
        $this->load->helper(['jwt', 'Authorization']);
    }

    public function index_get()
    {
        return $this->returnData($this->db->get('services')->result(), false);
    }

    public function index_post($id = null)
    {
        $service = new ServiceData();
        $service->name = $this->post('name');
        $service->price = $this->post('price');
        $service->types = $this->post('types');
        date_default_timezone_set("Asia/Kolkata");
        $service->created_at = Date('Y-m-d h:i:s');
        if ($id == null) {
            $response = $this->ServiceModel->store($service);
        } else {
            $response = $this->ServiceModel->update($service, $id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null)
    {
        if ($id == null) {
            return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->ServiceModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }
    
    public function returnData($msg, $error)
    {
        $response['error'] = $error;
        $response['message'] = $msg;
        return $this->response($response);
    }
}

class ServiceData
{
    public $name;
    public $price;
    public $types;
    public $created_at;
}
