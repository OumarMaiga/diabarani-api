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

        public function get($serie_id) {
            $req = $this->db->prepare('SELECT * from saisons WHERE serie_id = :serie_id AND deleted = 0 AND etat = 1');
            $req->bindParam(':serie_id', $serie_id);
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from saisons WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function new_saisons() {
            $today = date('Y-m-d');
            $req = $this->db->prepare('SELECT * from saisons WHERE release_date < :today && deleted = 0 && etat = 1 LIMIT 0, 12');
            $req->bindParam(':today', $today);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO saisons (title, slug, overview, release_date, etat, deleted, serie_id, user_id, created_at, updated_at)VALUES(:title, :slug, :overview, :release_date, :etat, :deleted, :serie_id, :user_id, NOW(), null)');
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $req->bindParam(':serie_id', $inputs['serie_id']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['saison'] = null;
            if ($req->execute()) {
                $getSaison = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                if ($getSaison->execute()) {
                    $data['saison'] = $getSaison->fetch();
                }
            }
            return $data;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE saisons SET title=:title, overview=:overview, release_date=:release_date, etat=:etat, serie_id=:serie_id, user_id=:user_id, updated_at=NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':serie_id', $inputs['serie_id']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['saison'] = null;
            if ($req->execute()) {
                $getSaison = $this->getById($id);
                $data['success'] = true;
                if ($getSaison->execute()) {
                    $data['saison'] = $getSaison->fetch();
                }
            }
            return $data;
        }

        public function update_poster($id, $inputs) {
            $req = $this->db->prepare('UPDATE saisons SET poster_path=:poster_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':poster_path', $inputs['poster_path']);
            $data['success'] = false;
            $data['saison'] = null;
            if ($req->execute()) {
                $getSaison = $this->getById($id);
                $data['success'] = true;
                if ($getSaison->execute()) {
                    $data['saison'] = $getSaison->fetch();
                }
            }
            return $data;
        }

        public function update_cover($id, $inputs) {
            $req = $this->db->prepare('UPDATE saisons SET cover_path=:cover_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':cover_path', $inputs['cover_path']);
            $data['success'] = false;
            $data['saison'] = null;
            if ($req->execute()) {
                $getSaison = $this->getById($id);
                $data['success'] = true;
                if ($getSaison->execute()) {
                    $data['saison'] = $getSaison->fetch();
                }
            }
            return $data;
        }

        public function delete($id) {
            $req = $this->db->prepare('UPDATE saisons SET deleted=1, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $data['success'] = false;
            $data['saison'] = null;
            if ($req->execute()) {
                $getSaison = $this->getById($id);
                $data['success'] = true;
                if ($getSaison->execute()) {
                    $data['saison'] = $getSaison->fetch();
                }
            }
            return $data;
        }

        public function destroy($id) {
            $req = $this->db->prepare('DELETE from saisons WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }
    }