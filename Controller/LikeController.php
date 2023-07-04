<?php

    namespace Controller;

    use Model\Like;

    class LikeController {
        
        private $like;
        
        public function __construct() {
            $this->like = new Like();
        }
        
        public function get($entite, $id) {
            $code = 0;
            $error_code = null;
            $likes = null;

            $request = $this->like->get($entite, $id);

            if ($request->execute()) {
                $code = 1;
                $message = "Likes fetched";
                $likes = $request->fetchAll();
            } else {
                $message = "Like not fetched";
                $error_code = 'likes_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'likes' => $likes
                )
            );
            return;
        }

        public function store() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $like = null;
            
            if (!isset($_POST['user_id']) || $_POST['user_id'] == null || $_POST['user_id'] == "") {
                echo json_encode(
                    array(
                        'message' => "user is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'like' => $like
                    )
                );
                return;
            }
                
            $data = $this->like->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Like saved";
                $like = $data['like'];
            } else {
                $message = "Like unsaved";
                $error_code = 'like_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'like' => $like
                )
            );
            return;
        }

    }