<?php

    namespace Controller;

    use Model\Film;
    use Model\Serie;
    use Model\Saison;
    use Model\Episode;
    
    class ImageController {
        
        private $film;
        private $serie;
        private $saison;
        private $episode;
        
        public function __construct() {
            $this->film = new Film;
            $this->serie = new Serie;
            $this->saison = new Saison;
            $this->episode = new Episode;
        }

        public function upload() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            
            if(!file_exists($_POST['target_dir']))
                mkdir($_POST['target_dir'], 777, true);

            if(file_put_contents(ROOT . $_POST['target_file'], file_get_contents($_POST['file_url'])) ) 
            {
                if($_POST['table'] == 'film')
                {
                    if($_POST['type'] == 'poster')
                        $this->film->update_poster($_POST['film_id'],['poster_path' => $_POST['target_file']]);
                    
                    if($_POST['type'] == 'cover')
                        $this->film->update_cover($_POST['film_id'],['cover_path' => $_POST['target_file']]);
                }

                if($_POST['table'] == 'serie')
                {
                    if($_POST['type'] == 'poster')
                        $this->serie->update_poster($_POST['serie_id'],['poster_path' => $_POST['target_file']]);
                    
                    if($_POST['type'] == 'cover')
                        $this->serie->update_cover($_POST['serie_id'],['cover_path' => $_POST['target_file']]);
                }

                if($_POST['table'] == 'saison')
                {
                    if($_POST['type'] == 'poster')
                        $this->saison->update_poster($_POST['saison_id'],['poster_path' => $_POST['target_file']]);
                    
                    if($_POST['type'] == 'cover')
                        $this->saison->update_cover($_POST['saison_id'],['cover_path' => $_POST['target_file']]);
                }

                if($_POST['table'] == 'episode')
                {
                    if($_POST['type'] == 'poster')
                        $this->episode->update_poster($_POST['episode_id'],['poster_path' => $_POST['target_file']]);
                    
                    if($_POST['type'] == 'cover')
                        $this->episode->update_cover($_POST['episode_id'],['cover_path' => $_POST['target_file']]);
                }
            
                $code = 1;
                $message = "Image uploaded";
            } else {
                $message = "Image unuploaded";
                $error_code = 'image_unuploaded';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                )
            );
            return;
        }
    }