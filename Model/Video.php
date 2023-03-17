<?php

    namespace Model;

    use Database\DatabaseConnector;

    class Video {
        
        private $db;

        public function __construct() {
            $this->db = (new DatabaseConnector())->getConnection();
        }

        public function getBy($key, $operateur, $value) {
            $req = $this->db->prepare('SELECT * from videos WHERE '.$key.''.$operateur.':value');
            $req->bindParam(':value', $value);
            return $req;
        }

        public function get() {
            $req = $this->db->prepare('SELECT * from videos WHERE deleted = 0');
            return $req;
        }

        public function getById($id) {
            $req = $this->db->prepare('SELECT * from videos WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }

        public function save($inputs) {
            $req = $this->db->prepare('INSERT INTO videos (libelle, path, type, user_id, film_id, created_at, updated_at)VALUES(:libelle, :path, :type, :user_id, :film_id, NOW(), null)');
            $req->bindParam(':libelle', $inputs['libelle']);
            $req->bindParam(':path', $inputs['path']);
            $req->bindParam(':type', $inputs['type']);
            $req->bindParam(':user_id', $inputs['user_id']);
            $req->bindParam(':film_id', $inputs['film_id']);
            $data['success'] = false;
            $data['video'] = null;
            if ($req->execute()) {
                $getVideo = $this->getById($this->db->lastInsertId());
                $data['success'] = true;
                $getVideo->execute();
                if ($video = $getVideo->fetch()) {
                    $data['video'] = $video;
                }
            }
            return $data;
        }

        public function save_video_genre($inputs) {
            // On enregistre les nouveau video_genre dans la base de donnees
            $req = $this->db->prepare('INSERT INTO video_genre (video_id, genre_id)VALUES(:video_id, :genre_id)');
            $req->bindParam(':video_id', $inputs['video_id']);
            $req->bindParam(':genre_id', $inputs['genre_id']);
            $data['success'] = false;
            $data['genre'] = null;
            $data = $req->execute();
            return $data;
        }

        public function delete_video_genre($video_id) {
            $req = $this->db->prepare('DELETE from video_genre WHERE video_id=:video_id');
            $req->bindParam(':video_id', $video_id);
            return $req;
        }

        public function get_video_genre($video_id) {
            $req = $this->db->prepare('SELECT video_genre.genre_id, video_genre.video_id, 
                genres.libelle as libelle, genres.slug as slug, genres.deleted as deleted 
                from video_genre 
                LEFT JOIN genres ON video_genre.genre_id = genres.id 
                WHERE video_genre.video_id=:video_id AND genres.deleted = 0');
            $req->bindParam(':video_id', $video_id);
            return $req;
        }

        public function update($id, $inputs) {
            $req = $this->db->prepare('UPDATE videos SET title=:title, overview=:overview, realisateur=:realisateur, release_date=:release_date, type=:type, deleted=:deleted, etat=:etat, updated_at = NOW() WHERE id=:id');
            $req->bindParam(':id', $id);
            $req->bindParam(':title', $inputs['title']);
            $req->bindParam(':overview', $inputs['overview']);
            $req->bindParam(':realisateur', $inputs['realisateur']);
            $req->bindParam(':type', $inputs['type']);
            $req->bindParam(':release_date', $inputs['release_date']);
            $req->bindParam(':deleted', $inputs['deleted']);
            $req->bindParam(':etat', $inputs['etat']);
            $data['success'] = false;
            $data['video'] = null;
            if ($req->execute()) {
                $getVideo = $this->getById($id);
                $data['success'] = true;
                $getVideo->execute();
                if ($video = $getVideo->fetch()) {
                    $data['video'] = $video;
                }
            }
            return $data;
        }

        public function delete($id) {
            $req = $this->db->prepare('DELETE from videos WHERE id=:id LIMIT 1');
            $req->bindParam(':id', $id);
            return $req;
        }
    }