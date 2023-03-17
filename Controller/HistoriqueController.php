<?php

    namespace Controller;

    use Model\Historique;

    class HistoriqueController {
        
        private $historique;
        
        public function __construct() {
            $this->historique = new Historique();
        }
        
        public function getByUser($user_id) {
            $code = 0;
            $error_code = null;
            $historique = null;

            $request = $this->historique->getBy('user_id', '=', $user_id);

            if ($request->execute()) {
                $code = 1;
                $message = "Historique fetched";
                $historique = $request->fetchAll();
            } else {
                $message = "Historique not fetched";
                $error_code = 'historique_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'historique' => $historique
                )
            );
            return;
        } 

        public function store() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $historique = null;
            
            // On verifie si le film est déjà dans l'historique de l'utilisateur 
            $request = $this->historique->getUserHistorique($_POST['user_id'], $_POST['film_id']);
            $request->execute();

            if ($data = $request->fetch()) {
                $_POST['last_seen'] = time();
                $data = $this->historique->update($data['id'], $_POST);
            }
            else
            {
                $_POST['last_seen'] = time();
                $data = $this->historique->save($_POST);
            }
            
            if ($data['success']) {
                $code = 1;
                $message = "Historique saved";
                $historique = $data['historique'];
            } else {
                $message = "Historique unsaved";
                $error_code = 'historique_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'historique' => $historique
                )
            );
            return;
        }
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $historique = null;

            $request = $this->historique->delete($id);
            if($request->execute()) {                
                $code = 1;
                $message = "Historique deleted";
            } else {
                $message = "Historique not deleted";
                $error_code = 'historique_not_deleted';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'historique' => $historique
                )
            );
            return;
        } 

    }