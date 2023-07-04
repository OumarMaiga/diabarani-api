<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Vue {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function get($entite, $id) {
            $req = $this->db->prepare("SELECT * from vues WHERE ".$entite."_id = $id");
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from vues WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO vues (user_id, episode_id, film_id, created_at)VALUES(:user_id, :episode_id, :film_id, NOW())');
            $req->bindParam(':user_id', $inputs['user_id']);
            $req->bindParam(':episode_id', $inputs['episode_id']);
            $req->bindParam(':film_id', $inputs['film_id']);
            $data['success'] = false;
            $data['vue'] = null;
            if ($req->execute()) {
                $getVue = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                if ($getVue->execute()) {
                    $data['vue'] = $getVue->fetch();
                }
            }
            return $data;
        }

    }