<?php

    namespace Controller;

    use Model\Vue;

    class VueController {
        
        private $vue;
        
        public function __construct() {
            $this->vue = new Vue();
        }
        
        public function get($entite, $id) {
            $code = 0;
            $error_code = null;
            $vues = null;

            $request = $this->vue->get($entite, $id);

            if ($request->execute()) {
                $code = 1;
                $message = "Vues fetched";
                $vues = $request->fetchAll();
            } else {
                $message = "Vue not fetched";
                $error_code = 'vues_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'vues' => $vues
                )
            );
            return;
        }

        public function store() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $vue = null;
            
            if (!isset($_POST['user_id']) || $_POST['user_id'] == null || $_POST['user_id'] == "") {
                echo json_encode(
                    array(
                        'message' => "user is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'vue' => $vue
                    )
                );
                return;
            }
                
            $data = $this->vue->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Vue saved";
                $vue = $data['vue'];
            } else {
                $message = "Vue unsaved";
                $error_code = 'vue_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'vue' => $vue
                )
            );
            return;
        }

    }