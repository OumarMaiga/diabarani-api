<?php

    namespace Model;

    use Database\DatabaseConnector;

    class User {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from users WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from users WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        /* Verification du token */
        public function checkToken($token) {
            $req = $this->db->prepare("SELECT * FROM users WHERE token=:token LIMIT 1");
            $req->bindParam(':token', $token);
            return $req;
        }

        public function setUser($inputs) {
            $req = $this->db->prepare('INSERT INTO users (first_name, last_name, email, phone, password, token, deleted, created_at, updated_at)VALUES(:first_name, :last_name, :email, :phone, :password, :token, :deleted, NOW(), NOW())');
            $req->bindParam(':first_name', $inputs['prenom']);
            $req->bindParam(':last_name', $inputs['nom']);
            $req->bindParam(':phone', $inputs['phone']);
            $req->bindParam(':email', $inputs['email']);
            $req->bindParam(':password', $inputs['password']);
            $req->bindParam(':token', $inputs['token']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $data['success'] = false;
            $data['user'] = null;
            if ($req->execute()) {
                $getUser = $this->getUser($this->db->lastInsertId());
                $data['success'] = true;
                $getUser->execute();
                if ($user = $getUser->fetch()) {
                    $data['user'] = $user;
                }
            }
            return $data;
        }

        public function updateUser($id, $inputs) {
            $req = $this->db->prepare('UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email, phone=:phone, updated_at=NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':first_name', $inputs['first_name']);
            $req->bindParam(':last_name', $inputs['last_name']);
            $req->bindParam(':phone', $inputs['phone']);
            $req->bindParam(':email', $inputs['email']);
            return $req;
        }
    }