<?php

    namespace Controller;

    use Model\Episode;

    class EpisodeController {
        
        private $episode;
        
        public function __construct() {
            $this->episode = new Episode();
        }
        
        public function get($serie_id, $saison_id) {
            $code = 0;
            $error_code = null;
            $episodes = null;

            $request = $this->episode->get($serie_id, $saison_id);

            if ($request->execute()) {
                $code = 1;
                $message = "Episodes fetched";
                $episodes = $request->fetchAll();
            } else {
                $message = "Episode not fetched";
                $error_code = 'episodes_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'episodes' => $episodes
                )
            );
            return;
        } 
        
        public function getAll($serie_id, $saison_id) {
            $code = 0;
            $error_code = null;
            $episodes = null;

            $request = $this->episode->getAll($serie_id, $saison_id);

            if ($request->execute()) {
                $code = 1;
                $message = "Episodes fetched";
                $episodes = $request->fetchAll();
            } else {
                $message = "Episode not fetched";
                $error_code = 'episodes_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'episodes' => $episodes
                )
            );
            return;
        } 
        
        public function getById($id) {
            $code = 0;
            $error_code = null;
            $episode = null;

            $request = $this->episode->getById($id);
            $request->execute();

            if ($data = $request->fetch()) {
                $code = 1;
                $message = "Episode fetched";
                $episode = $data;
            } else {
                $message = "Episode not fetched";
                $error_code = 'episode_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'episode' => $episode
                )
            );
            return;
        } 

        public function store($serie_id, $saison_id) {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $episode = null;
            
            if (!isset($_POST['title']) || $_POST['title'] == null || $_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'episode' => $episode
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $_POST['serie_id'] = $serie_id;
            $_POST['saison_id'] = $saison_id;
            $data = $this->episode->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Episode saved";
                $episode = $data['episode'];
            } else {
                $message = "Episode unsaved";
                $error_code = 'episode_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'episode' => $episode
                )
            );
            return;
        }

        /**
         * Mise à jour du episode
         */
        public function update($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $episode = null;
            $code = 0;
            $error_code = null;
            
            if (!isset($_POST['title']) || $_POST['title'] == null || $_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'episode' => $episode
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->episode->update($id, $_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Episode saved";
                $episode = $data['episode'];
            } else {
                $message = "Episode unsaved";
                $error_code = 'episode_unsaved';
            }

            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'episode' => $episode
                )
            );
        }
        
        public function delete($id) {
            $code = 0;
            $error_code = null;
            $episode = null;

            $data = $this->episode->delete($id);
            if($data['success']) {                
                $code = 1;
                $message = "Episode deleted";
            } else {
                $message = "Episode not deleted";
                $error_code = 'episode_not_deleted';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'episode' => $episode
                )
            );
            return;
        } 
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $episode = null;

            $request = $this->episode->destroy($id);
            if($request->execute()) {                
                $code = 1;
                $message = "Episode destroy";
            } else {
                $message = "Episode not destroy";
                $error_code = 'episode_not_destroy';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'episode' => $episode
                )
            );
            return;
        } 

    }