<?php

    use Restserver \Libraries\REST_Controller;

    class User extends REST_Controller {
        public function __construct() {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
            header("Access-Control-Allow-Headers: Authorization, Content-Type, Content-Length,Accept-Encoding");
            parent::__construct();
            $this->load->model('UserModel');
            $this->load->library('form_validation');
            $this->load->helper(['jwt', 'Authorization']);
        }

        
        public function index_get() {
            $data = $this->verify_request();
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' => $data];
            $this->response($response, $status);
            return $this->returnData($this->db->get('users')->result(), false);
        }


        public function index_post($id = null) {
            $validation = $this->form_validation;
            $rule = $this->UserModel->rules();

            if ($id == null) {
                array_push($rule, [
                    'field' => 'password',
                    'label' => 'password',
                    'rules' => 'required'
                ],
                [
                    'field' => 'email',
                    'label' => 'email',
                    'rules' => 'required|valid_email|is_unique[users.email]'    
                ]);
            } else {
                array_push($rule, [
                    'field' => 'email',
                    'label' => 'email',
                    'rules' => 'required|valid_email'
                ]);
            }

            $validation->set_rules($rule);

            if (!$validation->run()) 
                return $this->returnData($this->form_validation->error_array(), true);
            
            $user = new UserData();
            $user->name = $this->post('name');
            $user->password = $this->post('password');
            $user->email = $this->post('email');

            if ($id == null) 
                $response = $this->UserModel->store($user);
            else 
                $response = $this->UserModel->update($user, $id);

            return $this->returnData($response['msg'], $response['error']);
        }

        public function index_delete($id = null) {
            if ($id == null)
                return $this->returnData('Parameter ID Tidak Ditemukan', true);

            $response = $this->UserModel->destroy($id);
            return $this->returnData($response['msg'], $response['error']);
        }

        public function returnData($msg, $error) {
            $response['error'] = $error;
            $response['message'] = $msg;

            return $this->response($response);
        }

        // public function login_post() {
        //     $email = $this->post('email');
        //     $password = $this->post('password');
        //     $response = $this->UserModel->login($email, $password);
            
        //     if ($response) {
        //         return $this->response([
        //             "error" => $response['error'],
        //             "message" => $response['msg'],
        //             "token_encode" => $response['token_encode'],
        //         ]);
        //     } else {
        //         return $this->response([
        //             "error" => TRUE,
        //             "message" => "Gagal",
        //         ]);
        //     }
        // }

        // public function logout_post() {
            
        // }

        // public function isTokenExist() {
        //     $jwt = $this->input->get_request_header('Authorization');
        //     $temp = explode(' ', $jwt);
        //     if (count($temp) < 2)
        //         return 0;
        //     $type = $temp[0];
        //     $token = $temp[1];
            
        //     if ($type != 'Bearer' || $token == null)
        //         return 0;
        //     try {
        //         $user = $this->UserModel->checkAuthorization($token);
        //         if ($user) return 1;
        //     } catch (Exception $e) {
        //         return 0;
        //     }
        //     return 0;
        // }

        private function verify_request()
        {
            // Get all the headers
            $headers = $this->input->request_headers();
            // Extract the token
            $token = $headers['Authorization'];
            // Use try-catch
            // JWT library throws exception if the token is not valid
            try {
                // Validate the token
                // Successfull validation will return the decoded user data else returns false
                $data = AUTHORIZATION::validateToken($token);
                if ($data === false) {
                    $status = parent::HTTP_UNAUTHORIZED;
                    $response = ['status' => $status, 'msg' => 'Unauthorized Access!'];
                    $this->response($response, $status);
                    exit();
                } else {
                    return $data;
                }
            } catch (Exception $e) {
                // Token is invalid
                // Send the unathorized access message
                $status = parent::HTTP_UNAUTHORIZED;
                $response = ['status' => $status, 'msg' => 'Unauthorized Access! '];
                $this->response($response, $status);
            }
        }
    }


    class UserData {
        public $name;
        public $password;
        public $email;
    }

?>