<?php

    namespace Controller;

    use Model\Film;
    use Model\Episode;
    
    class VideoController {
        
        private $film;
        private $episode;
        
        public function __construct() {
            $this->film = new Film;
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
                    if($_POST['type'] == 'video')
                        $this->film->update_video($_POST['film_id'],['video_path' => $_POST['target_file']]);
                }

                if($_POST['table'] == 'episode')
                {    
                    if($_POST['type'] == 'video')
                        $this->episode->update_video($_POST['episode_id'],['video_path' => $_POST['target_file']]);
                }
            
                $code = 1;
                $message = "Video uploaded";
            } else {
                $message = "Video unuploaded";
                $error_code = 'video_unuploaded';
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