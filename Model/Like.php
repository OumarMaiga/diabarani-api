<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Like {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function get($entite, $id) {
            $req = $this->db->prepare("SELECT * from likes WHERE ".$entite."_id = $id");
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from likes WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO likes (user_id, serie_id, saison_id, episode_id, film_id, created_at, updated_at)VALUES(:user_id, :serie_id, :saison_id, :episode_id, :film_id, NOW(), null)');
            $req->bindParam(':user_id', $inputs['user_id']);
            $req->bindParam(':serie_id', $inputs['serie_id']);
            $req->bindParam(':saison_id', $inputs['saison_id']);
            $req->bindParam(':episode_id', $inputs['episode_id']);
            $req->bindParam(':film_id', $inputs['film_id']);
            $data['success'] = false;
            $data['like'] = null;
            if ($req->execute()) {
                $getLike = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                if ($getLike->execute()) {
                    $data['like'] = $getLike->fetch();
                }
            }
            return $data;
        }

    }