<?php

    namespace Controller;

    use Model\Film;
    use Model\Serie;
    
    class ImageController {
        
        private $film;
        private $serie;
        
        public function __construct() {
            $this->film = new Film;
            $this->serie = new Serie;
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
                        $this->film->update_poster($_POST['film_id'],['poster_path' => API_URL . $_POST['target_file']]);
                    
                    if($_POST['type'] == 'cover')
                        $this->film->update_cover($_POST['film_id'],['cover_path' => API_URL . $_POST['target_file']]);
                }

                if($_POST['table'] == 'serie')
                {
                    if($_POST['type'] == 'poster')
                        $this->serie->update_poster($_POST['serie_id'],['poster_path' => API_URL . $_POST['target_file']]);
                    
                    if($_POST['type'] == 'cover')
                        $this->serie->update_cover($_POST['serie_id'],['cover_path' => API_URL . $_POST['target_file']]);
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