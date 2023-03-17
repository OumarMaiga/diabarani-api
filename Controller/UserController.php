<?php

    namespace Controller;

    use Model\User;

    class UserController {

        private $user;
        
        public function __construct() {
            $this->user = new User();
        }
    
        public function getById($id) {
            $user = null;
            $code = 0;
            $error_code = null;
            // Récuperation de l'utilsateur par son token 
            $req = $this->user->getById($id);
            $req->execute();
            if($data = $req->fetch()) {
                $user = $data;
                $code = 1;
                $message = 'User fetched';
            } else {
                $code = 0;
                $error_code = 'user_not_fetch';
                $message = 'User fetching fail';
            }
            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'user' => $user
                )
            );
            return;
        }

        /**
         * Mise à jour de l'utilisateur
         */
        public function updateUser($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $user = null;
            $token = null;
            $code = 0;
            $error_code = null;

            // Recuperation de l'utilsateur
            $req = $this->user->getById($id);
            $req->execute();
            $data = $req->fetch();
            if($data < 1) {
                $code = 0;
                $error_code = 'user_not_fetch';
                $message = 'User fetching fail';
            } 
            // Mise à jour de l'utilsateur
            $req = $this->user->updateUser($id, $_POST);
            if($req->execute()) {
                // Recuperation de l'utilsateur mise a jour
                $req = $this->user->getById($id);
                $req->execute();
                $data = $req->fetch();

                $token = $data['token'];
                $user = $data;
                $code = 1;
                $message = 'User Updated';
            } else {
                $code = 0;
                $error_code = 'user_not_updated';
                $message = 'User update failed';
            }
            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'token' => $token,
                    'user' => $user
                )
            );
        }
        
    }