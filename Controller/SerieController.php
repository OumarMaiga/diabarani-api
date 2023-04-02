<?php

    namespace Controller;

    use Model\Serie;
    use Model\Genre;

    class SerieController {
        
        private $serie;
        private $genre;
        
        public function __construct() {
            $this->serie = new Serie();
            $this->genre = new Genre();
        }
        
        public function get() {
            $code = 0;
            $error_code = null;
            $series = null;

            $request = $this->serie->get();

            if ($request->execute()) {
                $series = $request->fetchAll();
                $i = -1;
                foreach ($series as $serie)
                {
                    $i++;
                    // On recupere les genres de chaque serie
                    $series[$i]['genres'] = array();
                    $request = $this->serie->get_serie_genres($serie['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($series[$i]['genres'], $genre);
                        }
                    }
                }
                $code = 1;
                $message = "Series fetched";
                $series = $series;
            } else {
                $message = "Serie not fetched";
                $error_code = 'series_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'series' => $series
                )
            );
            return;
        } 
        
        public function getAll() {
            $code = 0;
            $error_code = null;
            $series = null;

            $request = $this->serie->getAll();

            if ($request->execute()) {
                $series = $request->fetchAll();
                $i = -1;
                foreach ($series as $serie)
                {
                    $i++;
                    // On recupere les genres de chaque serie
                    $series[$i]['genres'] = array();
                    $request = $this->serie->get_serie_genres($serie['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($series[$i]['genres'], $genre);
                        }
                    }
                }
                $code = 1;
                $message = "Series fetched";
                $series = $series;
            } else {
                $message = "Serie not fetched";
                $error_code = 'series_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'series' => $series
                )
            );
            return;
        } 
        
        public function getById($id) {
            $code = 0;
            $error_code = null;
            $serie = null;

            $request = $this->serie->getById($id);

            if ($request->execute()) {
                $serie = $request->fetch();
                // On recupere les genres de chaque serie
                $serie['genres'] = array();
                $request = $this->serie->get_serie_genres($serie['id']);
                if ($request->execute()) 
                {
                    $genres = $request->fetchAll();
                    foreach ($genres as $genre)
                    {
                        array_push($serie['genres'], $genre);
                    }
                }
                $code = 1;
                $message = "Serie fetched";
                $serie = $serie;
            } else {
                $message = "Serie not fetched";
                $error_code = 'serie_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'serie' => $serie
                )
            );
            return;
        } 

        public function store() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $serie = null;
            
            if (!isset($_POST['title']) || $_POST['title'] == null || $_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'serie' => $serie
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data_slug = $this->serie->getBy('slug', '=', $_POST['slug']);
            $data_slug->execute();
            if($data_slug->rowCount() > 0)
                $_POST['slug'] = $_POST['slug'].'-'.$data_slug->rowCount();

            $data = $this->serie->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Serie saved";
                $serie = $data['serie'];
                if (isset($_POST['genre'])) {
                    // On supprime les serie_genre qui existe
                    $serie_genre_deleted = $this->serie->delete_serie_genre($serie['id']);
                    if ($serie_genre_deleted->execute()) {
                        foreach ($_POST['genre'] as $genre_id) {
                            $inputs = array(
                                'serie_id' => $serie['id'],
                                'genre_id' => $genre_id
                            );
                            $this->serie->save_genre_serie($inputs);
                        }
                    }
                }
            } else {
                $message = "Serie unsaved";
                $error_code = 'serie_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'serie' => $serie
                )
            );
            return;
        }

        public function upcoming() {
            $code = 0;
            $error_code = null;
            $message = null;
            $series = null;

            $request = $this->serie->getBy('release_date', '>', date('Y-m-d'));

            if ($request->execute()) {
                $series = $request->fetchAll();
                // On recupere le poster(image) de chaque serie
                $i = -1;
                foreach ($series as $serie)
                {
                    $i++;
                    // On recupere les genres de chaque serie
                    $series[$i]['genres'] = array();
                    $request = $this->serie->get_serie_genres($serie['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($series[$i]['genres'], $genre);
                        }
                    }
                }

                $code = 1;
                $message = "series fetched";
                $series = $series;
            } else {
                $message = "serie not fetched";
                $error_code = 'series_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'series' => $series
                )
            );
            return;
        }
        
        public function new_series() {
            $code = 0;
            $error_code = null;
            $message = null;
            $series = null;

            $request = $this->serie->new_series();

            if ($request->execute()) {
                $series = $request->fetchAll();
                $i = -1;
                foreach ($series as $serie)
                {
                    // On recupere les genres de chaque serie
                    $i++;
                    $series[$i]['genres'] = array();
                    $request = $this->serie->get_serie_genres($serie['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($series[$i]['genres'], $genre);
                        }
                    }
                }

                $code = 1;
                $message = "series fetched";
                $series = $series;
            } else {
                $message = "serie not fetched";
                $error_code = 'series_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'series' => $series
                )
            );
            return;
        } 

        /**
         * Recuperation des series du genre
         */
        public function genre_series($genre_id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $series = null;

            $request = $this->serie->get_genre_series($genre_id);
            
            if ($request->execute()) {
                $series = $request->fetchAll();
                $i = -1;
                foreach ($series as $serie)
                {
                    $i++;
                    // On recupere les genres de chaque serie
                    $series[$i]['genres'] = array();
                    $request = $this->serie->get_serie_genres($serie['id']);
                    if ($request->execute()) 
                    {
                        $genres = $request->fetchAll();
                        foreach ($genres as $genre)
                        {
                            array_push($series[$i]['genres'], $genre);
                        }
                    }
                }

                $code = 1;
                $message = "Genre series fetched";
                $series = $series;
            } else {
                $message = "Genre series not fetched";
                $error_code = 'genre_series_not_fetched';
            }

            echo json_encode (
                array (
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'series' => $series
                )
            );
            return;
        }

        /**
         * Recuperation des genres du serie
         */
        public function serie_genres($serie_id) {
            $code = 0;
            $error_code = null;
            $message = null;
            $genres = null;

            $request = $this->serie->get_serie_genres($serie_id);

            if ($request->execute()) {
                $code = 1;
                $message = "Serie genres fetched";
                $genres = $request->fetchAll();
            } else {
                $message = "Serie genres not fetched";
                $error_code = 'serie_genres_not_fetched';
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
         * Recuperation des series des genres
         */
        public function genres_series() {
            $code = 0;
            $error_code = null;
            $message = null;
            $genres_series = null;

            $request = $this->genre->get();
            
            if ($request->execute()) 
            {
                $genres = $request->fetchAll();
                // On recupere les series de chaque genre
                $i = -1;
                foreach ($genres as $genre)
                {  
                    $i++;
                    $genres[$i]['series'] = array();
                    $request = $this->serie->get_genre_series($genre['id']);
                    
                    if ($request->execute()) 
                    {
                        $series = $request->fetchAll();
                        foreach ($series as $serie)
                        {
                            array_push($genres[$i]['series'], $serie);
                        }
                    }
                }

                $code = 1;
                $message = "Genre series fetched";
                $genres_series = $genres;
            } else {
                $message = "Genre series not fetched";
                $error_code = 'genre_series_not_fetched';
            }

            echo json_encode (
                array (
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'genres_series' => $genres_series
                )
            );
            return;
        }

        /**
         * Recuperation des series des genres
         */
        public function some_genres_series($genre_ids) {
            $code = 0;
            $error_code = null;
            $message = null;
            $series = null;

            $request = $this->serie->get_some_genres_series($genre_ids);
            
            if ($request->execute()) 
            {
                $code = 1;
                $message = "Genre series fetched";
                $series = $request->fetchAll();
            } else {
                $message = "Genre series not fetched";
                $error_code = 'genre_series_not_fetched';
            }

            echo json_encode (
                array (
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'series' => $series
                )
            );
            return;
        }

        /**
         * Mise à jour du serie
         */
        public function update($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $serie = null;
            $code = 0;
            $error_code = null;
            
            if (!isset($_POST['title']) || $_POST['title'] == null || $_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'serie' => $serie
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->serie->update($id, $_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Serie saved";
                $serie = $data['serie'];
                if (isset($_POST['genre'])) {
                    // On supprime les serie_genre qui existe
                    $serie_genre_deleted = $this->serie->delete_serie_genre($id);
                    if ($serie_genre_deleted->execute()) {
                        foreach ($_POST['genre'] as $genre_id) {
                            $inputs = array(
                                'serie_id' => $serie['id'],
                                'genre_id' => $genre_id
                            );
                            $this->serie->save_genre_serie($inputs);
                        }
                    }
                }
            } else {
                $message = "Serie unsaved";
                $error_code = 'serie_unsaved';
            }

            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'serie' => $serie
                )
            );
        }
        
        public function delete($id) {
            $code = 0;
            $error_code = null;
            $serie = null;

            $data = $this->serie->delete($id);
            if($data['success']) {                
                $code = 1;
                $message = "Serie deleted";
            } else {
                $message = "Serie not deleted";
                $error_code = 'serie_not_deleted';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'serie' => $serie
                )
            );
            return;
        } 
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $serie = null;

            $request = $this->serie->destroy($id);
            if($request->execute()) {                
                $code = 1;
                $message = "Serie destroy";
            } else {
                $message = "Serie not destroy";
                $error_code = 'serie_not_destroy';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'serie' => $serie
                )
            );
            return;
        } 

    }