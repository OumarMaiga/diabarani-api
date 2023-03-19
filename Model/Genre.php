<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Genre {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from genres WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function getAll() {
            $req = $this->db->prepare('SELECT * from genres WHERE deleted = 0');
            return $req;
        }

        public function get() {
            $req = $this->db->prepare('SELECT * from genres WHERE deleted = 0 && etat = 1');
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from genres WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO genres (libelle, slug, etat, deleted, created_at, updated_at)VALUES(:libelle, :slug, :etat, :deleted NOW(), null)');
            $req->bindParam(':libelle', $inputs['libelle']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $data['success'] = false;
            $data['genre'] = null;
            if ($req->execute()) {
                $getGenre = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                $getGenre->execute();
                if ($genre = $getGenre->fetch()) {
                    $data['genre'] = $genre;
                }
            }
            return $data;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE genres SET libelle=:libelle, etat=:etat, updated_at=NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':libelle', $inputs['libelle']);
            $req->bindParam(':etat', $inputs['etat']);
            $data['success'] = false;
            $data['genre'] = null;
            if ($req->execute()) {
                $getGenre = $this->getById($id);
                $data['success'] = true;
                $getGenre->execute();
                if ($genre = $getGenre->fetch()) {
                    $data['genre'] = $genre;
                }
            }
            return $data;
        }

        public function delete($id) {
            $req = $this->db->prepare('UPDATE genres SET deleted=1, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $data['success'] = false;
            $data['genre'] = null;
            if ($req->execute()) {
                $getGenre = $this->getById($id);
                $data['success'] = true;
                $getGenre->execute();
                if ($genre = $getGenre->fetch()) {
                    $data['genre'] = $genre;
                }
            }
            return $data;
        }

        public function destroy($id) {
            $req = $this->db->prepare('DELETE from genres WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }
    }