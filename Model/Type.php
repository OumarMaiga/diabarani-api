<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Type {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from types WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function getAll() {
            $req = $this->db->prepare('SELECT * from types WHERE deleted = 0');
            return $req;
        }

        public function get() {
            $req = $this->db->prepare('SELECT * from types WHERE deleted = 0 && etat = 1');
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from types WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO types (libelle, slug, etat, deleted, user_id, created_at, updated_at)VALUES(:libelle, :slug, :etat, :deleted, :user_id, NOW(), null)');
            $req->bindParam(':libelle', $inputs['libelle']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['type'] = null;
            if ($req->execute()) {
                $getType = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                $getType->execute();
                if ($type = $getType->fetch()) {
                    $data['type'] = $type;
                }
            }
            return $data;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE types SET libelle=:libelle, etat=:etat, user_id=:user_id, updated_at=NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':libelle', $inputs['libelle']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $data['success'] = false;
            $data['type'] = null;
            if ($req->execute()) {
                $getType = $this->getById($id);
                $data['success'] = true;
                $getType->execute();
                if ($type = $getType->fetch()) {
                    $data['type'] = $type;
                }
            }
            return $data;
        }

        public function delete($id) {
            $req = $this->db->prepare('UPDATE types SET deleted=1, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $data['success'] = false;
            $data['type'] = null;
            if ($req->execute()) {
                $getType = $this->getById($id);
                $data['success'] = true;
                $getType->execute();
                if ($type = $getType->fetch()) {
                    $data['type'] = $type;
                }
            }
            return $data;
        }

        public function destroy($id) {
            $req = $this->db->prepare('DELETE from types WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }
    }