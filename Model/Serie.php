<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Serie {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from series WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function getAll() {
            $req = $this->db->prepare('SELECT * from series WHERE deleted = 0');
            return $req;
        }

        public function get() {
            $req = $this->db->prepare('SELECT * from series WHERE deleted = 0 && etat = 1');
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from series WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO series (title, slug, overview, release_date, etat, deleted, user_id, created_at, updated_at)VALUES(:title, :slug, :overview, :release_date, :etat, :deleted, :user_id, NOW(), null)');
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['serie'] = null;
            if ($req->execute()) {
                $getSerie = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                if ($getSerie->execute()) {
                    $data['serie'] = $getSerie->fetch();
                }
            }
            return $data;
        }

        public function save_genre_serie($inputs) {
            // On enregistre les nouveau genre_serie dans la base de donnees
            $req = $this->db->prepare('INSERT INTO genre_serie (serie_id, genre_id)VALUES(:serie_id, :genre_id)');
            $req->bindParam(':serie_id', $inputs['serie_id']);
            $req->bindParam(':genre_id', $inputs['genre_id']);
            $data['success'] = false;
            $data['genre'] = null;
            $data = $req->execute();
            return $data;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE series SET title=:title, overview=:overview, release_date=:release_date, etat=:etat, user_id=:user_id, updated_at=NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['serie'] = null;
            if ($req->execute()) {
                $getSerie = $this->getById($id);
                $data['success'] = true;
                if ($getSerie->execute()) {
                    $data['serie'] = $getSerie->fetch();
                }
            }
            return $data;
        }

        public function update_poster($id, $inputs) {
            $req = $this->db->prepare('UPDATE series SET poster_path=:poster_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':poster_path', $inputs['poster_path']);
            $data['success'] = false;
            $data['serie'] = null;
            if ($req->execute()) {
                $getSerie = $this->getById($id);
                $data['success'] = true;
                if ($getSerie->execute()) {
                    $data['serie'] = $getSerie->fetch();
                }
            }
            return $data;
        }

        public function update_cover($id, $inputs) {
            $req = $this->db->prepare('UPDATE series SET cover_path=:cover_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':cover_path', $inputs['cover_path']);
            $data['success'] = false;
            $data['serie'] = null;
            if ($req->execute()) {
                $getSerie = $this->getById($id);
                $data['success'] = true;
                if ($getSerie->execute()) {
                    $data['serie'] = $getSerie->fetch();
                }
            }
            return $data;
        }

        public function new_series() {
            $today = date('Y-m-d');
            $req = $this->db->prepare('SELECT * from series WHERE release_date < :today && deleted = 0 && etat = 1 LIMIT 0, 12');
            $req->bindParam(':today', $today);
            return $req;
        }

        public function delete($id) {
            $req = $this->db->prepare('UPDATE series SET deleted=1, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $data['success'] = false;
            $data['serie'] = null;
            if ($req->execute()) {
                $getSerie = $this->getById($id);
                $data['success'] = true;
                if ($getSerie->execute()) {
                    $data['serie'] = $getSerie->fetch();
                }
            }
            return $data;
        }

        public function get_genre_series($genre_id) {
            $today = date('Y-m-d');
            $req = $this->db->prepare('SELECT genre_serie.serie_id as id, 
                series.title as title, series.slug as slug, series.poster_path as poster_path, 
                series.cover_path as cover_path, series.deleted as deleted 
                from genre_serie 
                LEFT JOIN series ON genre_serie.serie_id = series.id 
                WHERE release_date < :today && genre_serie.genre_id=:genre_id AND series.deleted = 0');
            $req->bindParam(':genre_id', $genre_id);
            $req->bindParam(':today', $today);
            return $req;
        }

        public function get_serie_genres($serie_id) {
            $req = $this->db->prepare('SELECT genre_serie.genre_id as id, 
                genres.libelle as libelle, genres.slug as slug, genres.deleted as deleted 
                from genre_serie 
                LEFT JOIN genres ON genre_serie.genre_id = genres.id 
                WHERE genre_serie.serie_id=:serie_id AND genres.deleted = 0');
            $req->bindParam(':serie_id', $serie_id);
            return $req;
        }

        public function get_some_genres_series($genre_ids) {
            $today = date('Y-m-d');
            $req = $this->db->prepare("SELECT genre_serie.serie_id as id, 
                series.title as title, series.slug as slug, series.poster_path as poster_path, 
                series.cover_path as cover_path, series.deleted as deleted 
                from genre_serie 
                LEFT JOIN series ON genre_serie.serie_id = series.id 
                WHERE release_date < :today && genre_serie.genre_id IN ($genre_ids) AND series.deleted = 0");
            $req->bindParam(':today', $today);
            return $req;
        }

        public function destroy($id) {
            $req = $this->db->prepare('DELETE from series WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function delete_serie_genre($serie_id) {
            $req = $this->db->prepare('DELETE from genre_serie WHERE serie_id=:serie_id');
            $req->bindParam(':serie_id', $serie_id);
            return $req;
        }
    }