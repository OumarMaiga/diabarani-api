<?php

    namespace Controller;

    use Model\Saison;

    class SaisonController {
        
        private $saison;
        
        public function __construct() {
            $this->saison = new Saison();
        }
        
        public function get($serie_id) {
            $code = 0;
            $error_code = null;
            $saisons = null;

            $request = $this->saison->get($serie_id);

            if ($request->execute()) {
                $code = 1;
                $message = "Saisons fetched";
                $saisons = $request->fetchAll();
            } else {
                $message = "Saison not fetched";
                $error_code = 'saisons_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saisons' => $saisons
                )
            );
            return;
        } 
        
        public function getAll() {
            $code = 0;
            $error_code = null;
            $saisons = null;

            $request = $this->saison->getAll();

            if ($request->execute()) {
                $code = 1;
                $message = "Saisons fetched";
                $saisons = $request->fetchAll();
            } else {
                $message = "Saison not fetched";
                $error_code = 'saisons_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saisons' => $saisons
                )
            );
            return;
        } 
        
        public function getById($id) {
            $code = 0;
            $error_code = null;
            $saison = null;

            $request = $this->saison->getById($id);
            $request->execute();

            if ($data = $request->fetch()) {
                $code = 1;
                $message = "Saison fetched";
                $saison = $data;
            } else {
                $message = "Saison not fetched";
                $error_code = 'saison_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
            return;
        } 

        /**
         * Recuperation des genres du saison
         */
        public function saison_genres($saison_id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $genres = null;

            $request = $this->saison->get_saison_genres($saison_id);

            if ($request->execute()) {
                $code = 1;
                $message = "Serie genres fetched";
                $genres = $request->fetchAll();
            } else {
                $message = "Serie genres not fetched";
                $error_code = 'saison_genres_not_fetched';
            }

            echo json_encode (
                array (
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'genres' => $genres
                )
            );
            return;
        }

        public function store($serie_id) {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $saison = null;
            
            if (!isset($_POST['title']) || $_POST['title'] == null || $_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'saison' => $saison
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
            $data = $this->saison->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Saison saved";
                $saison = $data['saison'];
            } else {
                $message = "Saison unsaved";
                $error_code = 'saison_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
            return;
        }

        /**
         * Mise à jour du saison
         */
        public function update($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $saison = null;
            $code = 0;
            $error_code = null;
            
            if (!isset($_POST['title']) || $_POST['title'] == null || $_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'saison' => $saison
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->saison->update($id, $_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Saison saved";
                $saison = $data['saison'];
                if (isset($_POST['genre'])) {
                    // On supprime les saison_genre qui existe
                    $saison_genre_deleted = $this->saison->delete_saison_genre($id);
                    if ($saison_genre_deleted->execute()) {
                        foreach ($_POST['genre'] as $genre_id) {
                            $inputs = array(
                                'saison_id' => $saison['id'],
                                'genre_id' => $genre_id
                            );
                            $this->saison->save_genre_saison($inputs);
                        }
                    }
                }
            } else {
                $message = "Saison unsaved";
                $error_code = 'saison_unsaved';
            }

            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
        }
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $saison = null;

            $request = $this->saison->delete($id);
            if($request->execute()) {                
                $code = 1;
                $message = "Saison deleted";
            } else {
                $message = "Saison not deleted";
                $error_code = 'saison_not_deleted';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
            return;
        } 

    }