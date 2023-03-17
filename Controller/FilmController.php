<?php

    namespace Controller;

    use Model\Film;
    use Model\Genre;

    class FilmController {
        
        private $film;
        private $genre;
        
        public function __construct() {
            $this->film = new Film();
            $this->genre = new Genre();
        }
        
        public function getAll() {
            $code = 0;
            $error_code = null;
            $message = null;
            $films = null;

            $request = $this->film->getAll();

            if ($request->execute()) {
                $films = $request->fetchAll();
                $i = -1;
                foreach ($films as $film)
                {
                    $i++;
                    // On recupere les genres de chaque film
                    $films[$i]['genres'] = array();
                    $request = $this->film->get_film_genres($film['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($films[$i]['genres'], $genre);
                        }
                    }
                }

                $code = 1;
                $message = "Films fetched";
                $films = $films;
            } else {
                $message = "Film not fetched";
                $error_code = 'films_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'films' => $films
                )
            );
            return;
        } 
        
        public function get() {
            $code = 0;
            $error_code = null;
            $message = null;
            $films = null;

            $request = $this->film->get();

            if ($request->execute()) {
                $films = $request->fetchAll();
                $i = -1;
                foreach ($films as $film)
                {
                    $i++;
                    // On recupere les genres de chaque film
                    $films[$i]['genres'] = array();
                    $request = $this->film->get_film_genres($film['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($films[$i]['genres'], $genre);
                        }
                    }
                }

                $code = 1;
                $message = "Films fetched";
                $films = $films;
            } else {
                $message = "Film not fetched";
                $error_code = 'films_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'films' => $films
                )
            );
            return;
        } 

        public function upcoming() {
            $code = 0;
            $error_code = null;
            $message = null;
            $films = null;

            $request = $this->film->getBy('release_date', '>', date('Y-m-d'));

            if ($request->execute()) {
                $films = $request->fetchAll();
                // On recupere le poster(image) de chaque film
                $i = -1;
                foreach ($films as $film)
                {
                    $i++;
                    // On recupere les genres de chaque film
                    $films[$i]['genres'] = array();
                    $request = $this->film->get_film_genres($film['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($films[$i]['genres'], $genre);
                        }
                    }
                }

                $code = 1;
                $message = "Films fetched";
                $films = $films;
            } else {
                $message = "Film not fetched";
                $error_code = 'films_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'films' => $films
                )
            );
            return;
        }
        
        public function getById($id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $film = null;

            $request = $this->film->getById($id);
            $request->execute();

            if ($film = $request->fetch()) {
                // On recupere les genres de chaque film
                $film['genres'] = array();
                $request = $this->film->get_film_genres($film['id']);
                if ($request->execute()) 
                {
                    $genres = $request->fetchAll();
                    foreach ($genres as $genre)
                    {
                        array_push($film['genres'], $genre);
                    }
                }

                $code = 1;
                $message = "Film fetched";
                $film = $film;
            } else {
                $message = "Film not fetched";
                $error_code = 'film_not_fetched';
            }
            
            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'film' => $film
                )
            );
            return;
        } 

        public function store() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $message = null;
            $film = null;
            
            if ($_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'film' => $film
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->film->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Film saved";
                $film = $data['film'];

                if (isset($_POST['genre']) && !empty($_POST['genre'])) {
                    foreach ($_POST['genre'] as $genre_id) {
                        $inputs = array(
                            'film_id' => $film['id'],
                            'genre_id' => $genre_id
                        );
                        $this->film->save_film_genre($inputs);
                    }
                }
            } else {
                $message = "Film unsaved";
                $error_code = 'film_unsaved';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'film' => $film
                )
            );
            return;
        }

        /**
         * Mise à jour du film
         */
        public function update($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $film = null;
            $code = 0;
            $error_code = null;
            $message = null;
            
            if ($_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'film' => $film
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->film->update($id, $_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Film updated";
                $film = $data['film'];
                
                if (isset($_POST['genre'])) {
                    // On supprime les film_genre qui existe
                    $film_genre_deleted = $this->film->delete_film_genre($id);
                    if ($film_genre_deleted->execute()) {
                        foreach ($_POST['genre'] as $genre_id) {
                            $inputs = array(
                                'film_id' => $film['id'],
                                'genre_id' => $genre_id
                            );
                            $this->film->save_film_genre($inputs);
                        }
                    }
                }
            } else {
                $message = "Film unsaved";
                $error_code = 'film_unupdated';
            }

            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'film' => $film
                )
            );
        }
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $film = null;

            $request = $this->film->delete($id);
            if($request->execute()) {                
                $code = 1;
                $message = "Film deleted";
            } else {
                $message = "Film not deleted";
                $error_code = 'film_not_deleted';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'film' => $film
                )
            );
            return;
        } 

        /**
         * Recuperation des genres du film
         */
        public function film_genres($film_id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $genres = null;

            $request = $this->film->get_film_genres($film_id);
            ;

            if ($request->execute()) {
                $code = 1;
                $message = "Film genres fetched";
                $genres = $request->fetchAll();
            } else {
                $message = "Film genres not fetched";
                $error_code = 'film_genres_not_fetched';
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

        /**
         * Recuperation des films du genre
         */
        public function genre_films($genre_id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $films = null;

            $request = $this->film->get_genre_films($genre_id);
            
            if ($request->execute()) {
                $films = $request->fetchAll();
                $i = -1;
                foreach ($films as $film)
                {
                    $i++;
                    // On recupere les genres de chaque film
                    $films[$i]['genres'] = array();
                    $request = $this->film->get_film_genres($film['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($films[$i]['genres'], $genre);
                        }
                    }
                }

                $code = 1;
                $message = "Genre films fetched";
                $films = $films;
            } else {
                $message = "Genre films not fetched";
                $error_code = 'genre_films_not_fetched';
            }

            echo json_encode (
                array (
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'films' => $films
                )
            );
            return;
        }

        /**
         * Recuperation des films des genres
         */
        public function genres_films() {
            $code = 0;
            $error_code = null;
            $message = null;
            $genres_films = null;

            $request = $this->genre->get();
            
            if ($request->execute()) 
            {
                $genres = $request->fetchAll();
                // On recupere les films de chaque genre
                $i = -1;
                foreach ($genres as $genre)
                {  
                    $i++;
                    $genres[$i]['films'] = array();
                    $request = $this->film->get_genre_films($genre['id']);
                    
                    if ($request->execute()) 
                    {
                        $films = $request->fetchAll();
                        foreach ($films as $film)
                        {
                            array_push($genres[$i]['films'], $film);
                        }
                    }
                }

                $code = 1;
                $message = "Genre films fetched";
                $genres_films = $genres;
            } else {
                $message = "Genre films not fetched";
                $error_code = 'genre_films_not_fetched';
            }

            echo json_encode (
                array (
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'genres_films' => $genres_films
                )
            );
            return;
        }

        /**
         * Recuperation des films des genres
         */
        public function some_genres_films($genre_ids) {
            $code = 0;
            $error_code = null;
            $message = null;
            $films = null;

            $request = $this->film->get_some_genres_films($genre_ids);
            ;    
            if ($request->execute()) 
            {
                $code = 1;
                $message = "Genre films fetched";
                $films = $request->fetchAll();
            } else {
                $message = "Genre films not fetched";
                $error_code = 'genre_films_not_fetched';
            }

            echo json_encode (
                array (
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'films' => $films
                )
            );
            return;
        }

        /**
         * Recuperation de la photo du film
         */
        public function film_poster($film_id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $poster = null;

            $request = $this->film->get_film_poster($film_id);
            $request->execute();

            if ($data = $request->fetch()) {
                $code = 1;
                $message = "Film poster fetched";
                $poster = $data;
            } else {
                $message = "Film poster not fetched";
                $error_code = 'film_poster_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'poster' => $poster
                )
            );
            return;
        }

        /**
         * Recuperation de la photo de couverture du film
         */
        public function film_couverture($film_id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $couverture = null;

            $request = $this->film->get_film_couverture($film_id);
            $request->execute();

            if ($data = $request->fetch()) {
                $code = 1;
                $message = "Film couverture fetched";
                $couverture = $data;
            } else {
                $message = "Film couverture not fetched";
                $error_code = 'film_couverture_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'couverture' => $couverture
                )
            );
            return;
        }

        /**
         * Recuperation de la video du film
         */
        public function film_video($film_id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $video = null;

            $request = $this->film->get_film_video($film_id);
            $request->execute();

            if ($data = $request->fetch()) {
                $code = 1;
                $message = "Film video fetched";
                $video = $data;
            } else {
                $message = "Film video not fetched";
                $error_code = 'film_video_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'video' => $video
                )
            );
            return;
        }
        
        public function new_film() {
            $code = 0;
            $error_code = null;
            $message = null;
            $films = null;

            $request = $this->film->new_film();

            if ($request->execute()) {
                $films = $request->fetchAll();
                $i = -1;
                foreach ($films as $film)
                {
                    // On recupere les genres de chaque film
                    $i++;
                    $films[$i]['genres'] = array();
                    $request = $this->film->get_film_genres($film['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($films[$i]['genres'], $genre);
                        }
                    }
                }

                $code = 1;
                $message = "Films fetched";
                $films = $films;
            } else {
                $message = "Film not fetched";
                $error_code = 'films_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'films' => $films
                )
            );
            return;
        } 

    }