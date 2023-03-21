<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Episode {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from episodes WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function getAll($serie_id, $saison_id) {
            $req = $this->db->prepare('SELECT * from episodes WHERE serie_id=:serie_id AND saison_id=:saison_id AND deleted = 0');
            $req->bindParam(':serie_id', $serie_id);
            $req->bindParam(':saison_id', $saison_id);
            return $req;
        }

        public function get($serie_id, $saison_id) {
            $req = $this->db->prepare('SELECT * from episodes WHERE serie_id=:serie_id AND saison_id=:saison_id AND deleted = 0 && etat = 1');
            $req->bindParam(':serie_id', $serie_id);
            $req->bindParam(':saison_id', $saison_id);
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from episodes WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO episodes (title, slug, overview, realisateur, release_date, user_id, serie_id, saison_id, deleted, etat, created_at, updated_at)VALUES(:title, :slug, :overview, :realisateur, :release_date, :user_id, :serie_id, :saison_id, :deleted, :etat, NOW(), null)');
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':slug', $inputs['slug']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':realisateur', $inputs['realisateur']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $req->bindParam(':serie_id', $inputs['serie_id']);
            $req->bindParam(':saison_id', $inputs['saison_id']);
            $req->bindParam(':etat', $inputs['etat']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $data['success'] = false;
            $data['episode'] = null;
            if ($req->execute()) {
                $getEpisode = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                if ($getEpisode->execute()) {
                    $data['episode'] = $getEpisode->fetch();
                }
            }
            return $data;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE episodes SET title=:title, overview=:overview, realisateur=:realisateur, release_date=:release_date, etat=:etat, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':realisateur', $inputs['realisateur']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':etat', $inputs['etat']);
            $data['success'] = false;
            $data['episode'] = null;
            if ($req->execute()) {
                $getEpisode = $this->getById($id);
                $data['success'] = true;
                if ($getEpisode->execute()) {
                    $data['episode'] = $getEpisode->fetch();
                }
            }
            return $data;
        }

        public function update_poster($id, $inputs) {
            $req = $this->db->prepare('UPDATE episodes SET poster_path=:poster_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':poster_path', $inputs['poster_path']);
            $data['success'] = false;
            $data['episode'] = null;
            if ($req->execute()) {
                $getEpisode = $this->getById($id);
                $data['success'] = true;
                if ($getEpisode->execute()) {
                    $data['episode'] = $getEpisode->fetch();
                }
            }
            return $data;
        }

        public function update_cover($id, $inputs) {
            $req = $this->db->prepare('UPDATE episodes SET cover_path=:cover_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':cover_path', $inputs['cover_path']);
            $data['success'] = false;
            $data['episode'] = null;
            if ($req->execute()) {
                $getEpisode = $this->getById($id);
                $data['success'] = true;
                if ($getEpisode->execute()) {
                    $data['episode'] = $getEpisode->fetch();
                }
            }
            return $data;
        }

        public function update_video($id, $inputs) {
            $req = $this->db->prepare('UPDATE episodes SET video_path=:video_path, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':video_path', $inputs['video_path']);
            $data['success'] = false;
            $data['episode'] = null;
            if ($req->execute()) {
                $getEpisode = $this->getById($id);
                $data['success'] = true;
                if ($getEpisode->execute()) {
                    $data['episode'] = $getEpisode->fetch();
                }
            }
            return $data;
        }

        public function delete($id) {
            $req = $this->db->prepare('UPDATE episodes SET deleted=1, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $data['success'] = false;
            $data['episode'] = null;
            if ($req->execute()) {
                $getEpisode = $this->getById($id);
                $data['success'] = true;
                if ($getEpisode->execute()) {
                    $data['episode'] = $getEpisode->fetch();
                }
            }
            return $data;
        }

        public function destroy($id) {
            $req = $this->db->prepare('DELETE from episodes WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function new_episode() {
            $today = date('Y-m-d');
            $req = $this->db->prepare('SELECT * from episodes WHERE release_date < :today && deleted = 0 && etat = 1 LIMIT 0, 12');
            $req->bindParam(':today', $today);
            return $req;
        }
    }