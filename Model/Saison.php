<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Saison {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from saisons WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function getAll() {
            $req = $this->db->prepare('SELECT * from saisons WHERE deleted = 0');
            return $req;
        }

        public function get() {
            $req = $this->db->prepare('SELECT * from saisons WHERE deleted = 0 && etat = 1');
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from saisons WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO saisons (title, slug, overview, etat, deleted, serie_id, user_id, created_at, updated_at)VALUES(:title, :slug, :overview, :etat, :deleted, :serie_id, :user_id, NOW(), null)');
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $req->bindParam(':serie_id', $inputs['serie_id']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['saison'] = null;
            if ($req->execute()) {
                $getSaison = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                $getSaison->execute();
                if ($saison = $getSaison->fetch()) {
                    $data['saison'] = $saison;
                }
            }
            return $data;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE saisons SET title=:title, slug=:slug, overview=:overview, etat=:etat, deleted=:deleted, serie_id=:serie_id, user_id=:user_id, updated_at=NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $req->bindParam(':serie_id', $inputs['serie_id']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['saison'] = null;
            if ($req->execute()) {
                $getSaison = $this->getById($id);
                $data['success'] = true;
                $getSaison->execute();
                if ($saison = $getSaison->fetch()) {
                    $data['saison'] = $saison;
                }
            }
            return $data;
        }

        public function delete($id) {
            $req = $this->db->prepare('DELETE from saisons WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }
    }