<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Image {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from images WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function get() {
            $req = $this->db->prepare('SELECT * from images WHERE deleted = 0');
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from images WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO images (libelle, path, type, genre_id, user_id, film_id, created_at, updated_at)VALUES(:libelle, :path, :type, :genre_id, :user_id, :film_id, NOW(), null)');
            $req->bindParam(':libelle', $inputs['libelle']);
            $req->bindParam(':path', $inputs['path']);
            $req->bindParam(':type', $inputs['type']);
            $req->bindParam(':genre_id', $inputs['genre_id']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $req->bindParam(':film_id', $inputs['film_id']);
            $data['success'] = false;
            $data['image'] = null;
            if ($req->execute()) {
                $getImage = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                $getImage->execute();
                if ($image = $getImage->fetch()) {
                    $data['image'] = $image;
                }
            }
            return $data;
        }

        public function delete($id) {
            $req = $this->db->prepare('DELETE from images WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function deleteBy($key, $value) {
            $req = $this->db->prepare("DELETE from images WHERE $key=$value");
            return $req;
        }

        public function deleteBy2($key1, $value1, $key2, $value2) {
            $req = $this->db->prepare("DELETE from images WHERE $key1=$value1 AND $key2='$value2'");
            return $req;
        }
    }