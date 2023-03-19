<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Historique {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from historiques WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }
        
        public function getUserHistorique($user_id, $film_id) {
            $req = $this->db->prepare('SELECT * from historiques WHERE user_id=:user_id && film_id=:film_id');
            $req->bindParam(':user_id', $user_id);
            $req->bindParam(':film_id', $film_id);
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from historiques WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO historiques (user_id, film_id, last_seen)VALUES(:user_id, :film_id, :last_seen)');
            $req->bindParam(':user_id', $inputs['user_id']);
            $req->bindParam(':film_id', $inputs['film_id']);
            $req->bindParam(':last_seen', $inputs['last_seen']);
            $data['success'] = false;
            $data['historique'] = null;
            if ($req->execute()) {
                $getHistorique = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                $getHistorique->execute();
                if ($historique = $getHistorique->fetch()) {
                    $data['historique'] = $historique;
                }
            }
            return $data;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE historiques SET user_id=:user_id, film_id=:film_id, last_seen=:last_seen WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':user_id', $inputs['user_id']);
            $req->bindParam(':film_id', $inputs['film_id']);
            $req->bindParam(':last_seen', $inputs['last_seen']);
            $data['success'] = false;
            $data['historique'] = null;
            if ($req->execute()) {
                $getHistorique = $this->getById($id);
                $data['success'] = true;
                $getHistorique->execute();
                if ($historique = $getHistorique->fetch()) {
                    $data['historique'] = $historique;
                }
            }
            return $data;
        }

        public function destroy($id) {
            $req = $this->db->prepare('DELETE from historiques WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }
    }