<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Film {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from films WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function getAll() {
            $req = $this->db->prepare('SELECT * from films WHERE deleted = 0');
            return $req;
        }

        public function get() {
            $req = $this->db->prepare('SELECT * from films WHERE deleted = 0 && etat = 1');
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from films WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO films (title, slug, overview, realisateur, release_date, user_id, deleted, etat, created_at, updated_at)VALUES(:title, :slug, :overview, :realisateur, :release_date, :user_id, :deleted, :etat, NOW(), null)');
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':realisateur', $inputs['realisateur']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $data['success'] = false;
            $data['film'] = null;
            if ($req->execute()) {
                $getFilm = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                if ($getFilm->execute()) {
                    $data['film'] = $getFilm->fetch();
                }
            }
            return $data;
        }

        public function save_film_genre($inputs) {
            // On enregistre les nouveau film_genre dans la base de donnees
            $req = $this->db->prepare('INSERT INTO film_genre (film_id, genre_id)VALUES(:film_id, :genre_id)');
            $req->bindParam(':film_id', $inputs['film_id']);
            $req->bindParam(':genre_id', $inputs['genre_id']);
            $data['success'] = false;
            $data['genre'] = null;
            $data = $req->execute();
            return $data;
        }

        public function delete_film_genre($film_id) {
            $req = $this->db->prepare('DELETE from film_genre WHERE film_id=:film_id');
            $req->bindParam(':film_id', $film_id);
            return $req;
        }

        public function get_film_genres($film_id) {
            $req = $this->db->prepare('SELECT film_genre.genre_id as id, 
                genres.libelle as libelle, genres.slug as slug, genres.deleted as deleted 
                from film_genre 
                LEFT JOIN genres ON film_genre.genre_id = genres.id 
                WHERE film_genre.film_id=:film_id AND genres.deleted = 0');
            $req->bindParam(':film_id', $film_id);
            return $req;
        }

        public function get_genre_films($genre_id) {
            $today = date('Y-m-d');
            $req = $this->db->prepare('SELECT film_genre.film_id as id, 
                films.title as title, films.slug as slug, films.deleted as deleted 
                from film_genre 
                LEFT JOIN films ON film_genre.film_id = films.id 
                WHERE release_date < :today && film_genre.genre_id=:genre_id AND films.deleted = 0');
            $req->bindParam(':genre_id', $genre_id);
            $req->bindParam(':today', $today);
            return $req;
        }

        public function get_some_genres_films($genre_ids) {
            $today = date('Y-m-d');
            $req = $this->db->prepare('SELECT film_genre.film_id as id, 
                films.title as title, films.slug as slug, films.deleted as deleted 
                from film_genre 
                LEFT JOIN films ON film_genre.film_id = films.id 
                WHERE release_date < :today && film_genre.genre_id IN (:genre_ids) AND films.deleted = 0');
            $req->bindParam(':genre_ids', $genre_ids);
            $req->bindParam(':today', $today);
            return $req;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE films SET title=:title, overview=:overview, realisateur=:realisateur, release_date=:release_date, etat=:etat, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':realisateur', $inputs['realisateur']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':etat', $inputs['etat']);
            $data['success'] = false;
            $data['film'] = null;
            if ($req->execute()) {
                $getFilm = $this->getById($id);
                $data['success'] = true;
                if ($getFilm->execute()) {
                    $data['film'] = $getFilm->fetch();
                }
            }
            return $data;
        }

        public function update_poster($id, $inputs) {
            $req = $this->db->prepare('UPDATE films SET poster_path=:poster_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':poster_path', $inputs['poster_path']);
            $data['success'] = false;
            $data['film'] = null;
            if ($req->execute()) {
                $getFilm = $this->getById($id);
                $data['success'] = true;
                if ($getFilm->execute()) {
                    $data['film'] = $getFilm->fetch();
                }
            }
            return $data;
        }

        public function update_cover($id, $inputs) {
            $req = $this->db->prepare('UPDATE films SET cover_path=:cover_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':cover_path', $inputs['cover_path']);
            $data['success'] = false;
            $data['film'] = null;
            if ($req->execute()) {
                $getFilm = $this->getById($id);
                $data['success'] = true;
                if ($getFilm->execute()) {
                    $data['film'] = $getFilm->fetch();
                }
            }
            return $data;
        }

        public function update_video($id, $inputs) {
            $req = $this->db->prepare('UPDATE films SET video_path=:video_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':video_path', $inputs['video_path']);
            $data['success'] = false;
            $data['film'] = null;
            if ($req->execute()) {
                $getFilm = $this->getById($id);
                $data['success'] = true;
                if ($getFilm->execute()) {
                    $data['film'] = $getFilm->fetch();
                }
            }
            return $data;
        }

        public function delete($id) {
            $req = $this->db->prepare('UPDATE films SET deleted=1, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $data['success'] = false;
            $data['film'] = null;
            if ($req->execute()) {
                $getFilm = $this->getById($id);
                $data['success'] = true;
                if ($getFilm->execute()) {
                    $data['film'] = $getFilm->fetch();
                }
            }
            return $data;
        }

        public function destroy($id) {
            $req = $this->db->prepare('DELETE from films WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function new_film() {
            $today = date('Y-m-d');
            $req = $this->db->prepare('SELECT * from films WHERE release_date < :today && deleted = 0 && etat = 1 LIMIT 0, 12');
            $req->bindParam(':today', $today);
            return $req;
        }
    }