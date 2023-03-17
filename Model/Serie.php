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
            $req = $this->db->prepare('INSERT INTO series (title, slug, overview, etat, deleted, user_id, created_at, updated_at)VALUES(:title, :slug, :overview, :etat, :deleted, :user_id, NOW(), null)');
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['serie'] = null;
            if ($req->execute()) {
                $getSerie = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                $getSerie->execute();
                if ($serie = $getSerie->fetch()) {
                    $data['serie'] = $serie;
                }
            }
            return $data;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE series SET title=:title, slug=:slug, overview=:overview, etat=:etat, deleted=:deleted, user_id=:user_id, updated_at=NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['serie'] = null;
            if ($req->execute()) {
                $getSerie = $this->getById($id);
                $data['success'] = true;
                $getSerie->execute();
                if ($serie = $getSerie->fetch()) {
                    $data['serie'] = $serie;
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
                $getSerie->execute();
                if ($serie = $getSerie->fetch()) {
                    $data['serie'] = $serie;
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
                $getSerie->execute();
                if ($serie = $getSerie->fetch()) {
                    $data['serie'] = $serie;
                }
            }
            return $data;
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

        public function delete($id) {
            $req = $this->db->prepare('DELETE from series WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }
    }