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

        public function get_saison_genres($saison_id) {
            $req = $this->db->prepare('SELECT genre_saison.genre_id as id, 
                genres.libelle as libelle, genres.slug as slug, genres.deleted as deleted 
                from genre_saison 
                LEFT JOIN genres ON genre_saison.genre_id = genres.id 
                WHERE genre_saison.saison_id=:saison_id AND genres.deleted = 0');
            $req->bindParam(':saison_id', $saison_id);
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

        public function save_genre_saison($inputs) {
            // On enregistre les nouveau genre_saison dans la base de donnees
            $req = $this->db->prepare('INSERT INTO genre_saison (saison_id, genre_id)VALUES(:saison_id, :genre_id)');
            $req->bindParam(':saison_id', $inputs['saison_id']);
            $req->bindParam(':genre_id', $inputs['genre_id']);
            $data['success'] = false;
            $data['genre'] = null;
            $data = $req->execute();
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

        public function update_poster($id, $inputs) {
            $req = $this->db->prepare('UPDATE saisons SET poster_path=:poster_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':poster_path', $inputs['poster_path']);
            $data['success'] = false;
            $data['saison'] = null;
            if ($req->execute()) {
                $getSerie = $this->getById($id);
                $data['success'] = true;
                $getSerie->execute();
                if ($saison = $getSerie->fetch()) {
                    $data['saison'] = $saison;
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
                $getSerie = $this->getById($id);
                $data['success'] = true;
                $getSerie->execute();
                if ($saison = $getSerie->fetch()) {
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

        public function delete_saison_genre($saison_id) {
            $req = $this->db->prepare('DELETE from genre_saison WHERE saison_id=:saison_id');
            $req->bindParam(':saison_id', $saison_id);
            return $req;
        }
    }