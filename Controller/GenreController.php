<?php

    namespace Controller;

    use Model\Genre;

    class GenreController {
        
        private $genre;
        
        public function __construct() {
            $this->genre = new Genre();
        }
        
        public function get() {
            $code = 0;
            $error_code = null;
            $genres = null;

            $request = $this->genre->get();

            if ($request->execute()) {
                $code = 1;
                $message = "Genres fetched";
                $genres = $request->fetchAll();
            } else {
                $message = "Genre not fetched";
                $error_code = 'genres_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'genres' => $genres
                )
            );
            return;
        } 
        
        public function getAll() {
            $code = 0;
            $error_code = null;
            $genres = null;

            $request = $this->genre->getAll();

            if ($request->execute()) {
                $code = 1;
                $message = "Genres fetched";
                $genres = $request->fetchAll();
            } else {
                $message = "Genre not fetched";
                $error_code = 'genres_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'genres' => $genres
                )
            );
            return;
        } 
        
        public function getById($id) {
            $code = 0;
            $error_code = null;
            $genre = null;

            $request = $this->genre->getById($id);
            $request->execute();

            if ($data = $request->fetch()) {
                $code = 1;
                $message = "Genre fetched";
                $genre = $data;
            } else {
                $message = "Genre not fetched";
                $error_code = 'genre_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'genre' => $genre
                )
            );
            return;
        } 

        public function store() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $genre = null;
            
            if (!isset($_POST['libelle']) || $_POST['libelle'] == null || $_POST['libelle'] == "") {
                echo json_encode(
                    array(
                        'message' => "libelle is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'genre' => $genre
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->genre->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Genre saved";
                $genre = $data['genre'];
            } else {
                $message = "Genre unsaved";
                $error_code = 'genre_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'genre' => $genre
                )
            );
            return;
        }

        /**
         * Mise à jour du genre
         */
        public function update($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $genre = null;
            $code = 0;
            $error_code = null;
            
            if (!isset($_POST['libelle']) || $_POST['libelle'] == null || $_POST['libelle'] == "") {
                echo json_encode(
                    array(
                        'message' => "libelle is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'genre' => $genre
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->genre->update($id, $_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Genre saved";
                $genre = $data['genre'];
            } else {
                $message = "Genre unsaved";
                $error_code = 'genre_unsaved';
            }

            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'genre' => $genre
                )
            );
        }
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $genre = null;

            $request = $this->genre->delete($id);
            if($request->execute()) {                
                $code = 1;
                $message = "Genre deleted";
            } else {
                $message = "Genre not deleted";
                $error_code = 'genre_not_deleted';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'genre' => $genre
                )
            );
            return;
        } 

    }