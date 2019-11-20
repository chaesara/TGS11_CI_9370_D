<?php

    defined('BASEPATH') OR exit('No direct script access allowed');


    require APPPATH . 'third_party/JWT.php';
    require APPPATH . 'third_party/BeforeValidException.php';
    require APPPATH . 'third_party/ExpiredException.php';
    require APPPATH . 'third_party/SignatureInvalidException.php';
    use \Firebase\JWT\JWT;


    class UserModel extends CI_Model {
        private $key = "secret";
        private $table = 'users';
        public $id;
        public $name;
        public $email;
        public $password;
        public $rule = [
            [
                'field' => 'name',
                'label' => 'name',
                'rules' => 'required'
            ],
        ];


        public function Rules() {
            return $this->rule;
        }


        public function getAll() {
            $this->db->get('data_mahasiswa')->result();
        }


        public function store($request) {
            $this->name = $request->name;
            $this->email = $request->email;
            $this->password = password_hash($request->password, PASSWORD_BCRYPT);

            if ($this->db->insert($this->table, $this)) {
                return [
                    'msg' => 'Berhasil',
                    'error' => FALSE,
                ];
            }

            return [
                'msg' => 'Gagal',
                'error' => TRUE,
            ];
        }


        public function update($request, $id) {
            $updateData = [
                'email' => $request->email,
                'name' => $request->name
            ];

            if ($this->db->where('id', $id)->update($this->table, $updateData)) {
                return [
                    'msg' => 'Berhasil',
                    'error' => FALSE,
                ];
            }

            return [
                'msg' => 'Gagal',
                'error' => TRUE,
            ];
        }


        public function destroy($id) {
            if (empty($this->db->select('*')->where(array('id' => $id))->get($this->table)->row())) {
                return [
                    'msg' => 'ID Tidak Ditemukan!',
                    'error' => TRUE,
                ];
            }
            
            if ($this->db->delete($this->table, array('id' => $id))) {
                return [
                    'msg' => 'Berhasil',
                    'error' => FALSE,
                ];
            }

            return [
                'msg' => 'Gagal',
                'error' => TRUE,
            ];
        }

        // public function login($email = null, $password = null) {
        //     if ($email != null && $password != null) {
        //         $data = $this->db->get_where($this->table, ['email' => $email])->result_array();
        //         if ($data) {
        //             if(password_verify($password, $data[0]['password'])) {
        //                 $token = [
        //                     "id" => $data[0]['id'],
        //                     "name" => $data[0]['name'],
        //                     "email" => $data[0]['email'],
        //                     "password" => $data[0]['password'],
        //                 ];
        //                 $jwt = JWT::encode($token, $this->key);
        //                 return [
        //                     'msg' => $data,
        //                     'error' => FALSE,
        //                     'token_encode' => $jwt
        //                 ];
        //             } else {
        //                 return 0;
        //             }
        //         } else {
        //             return 0;
        //         }
        //     } else {
        //         return 0;
        //     }
        // }

        // public function checkAuthorization($token) {
        //     $user =  JWT::decode($token, $this->key, ['HS256']);
        //     $data = $this->db->get_where($this->table, ['id' => $user->id])->result_array();
        //     if ($data) 
        //         return 1;
        //     else
        //         return 0;
        // }

        public function verification($request)
        {
            $user = $this->db->select('*')->where(array('email' => $request->email))->get($this->table)->row_array();
            if (!empty($user) && password_verify($request->password, $user['password'])) {
                return $user;
            } else {
                return ['msg' => 'Gagal', 'error' => true];
            }
        }
    }
