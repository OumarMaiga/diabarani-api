<?php

    namespace Controller;

    use Model\Type;

    class TypeController {
        
        private $type;
        
        public function __construct() {
            $this->type = new Type();
        }
        
        public function get() {
            $code = 0;
            $error_code = null;
            $types = null;

            $request = $this->type->get();

            if ($request->execute()) {
                $code = 1;
                $message = "Types fetched";
                $types = $request->fetchAll();
            } else {
                $message = "Type not fetched";
                $error_code = 'types_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'types' => $types
                )
            );
            return;
        } 
        
        public function getAll() {
            $code = 0;
            $error_code = null;
            $types = null;

            $request = $this->type->getAll();

            if ($request->execute()) {
                $code = 1;
                $message = "Types fetched";
                $types = $request->fetchAll();
            } else {
                $message = "Type not fetched";
                $error_code = 'types_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'types' => $types
                )
            );
            return;
        } 
        
        public function getById($id) {
            $code = 0;
            $error_code = null;
            $type = null;

            $request = $this->type->getById($id);
            $request->execute();

            if ($data = $request->fetch()) {
                $code = 1;
                $message = "Type fetched";
                $type = $data;
            } else {
                $message = "Type not fetched";
                $error_code = 'type_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'type' => $type
                )
            );
            return;
        } 

        public function store() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $type = null;
            
            if (!isset($_POST['libelle']) || $_POST['libelle'] == null || $_POST['libelle'] == "") {
                echo json_encode(
                    array(
                        'message' => "libelle is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'type' => $type
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->type->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Type saved";
                $type = $data['type'];
            } else {
                $message = "Type unsaved";
                $error_code = 'type_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'type' => $type
                )
            );
            return;
        }

        /**
         * Mise à jour du type
         */
        public function update($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $type = null;
            $code = 0;
            $error_code = null;
            
            if (!isset($_POST['libelle']) || $_POST['libelle'] == null || $_POST['libelle'] == "") {
                echo json_encode(
                    array(
                        'message' => "libelle is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'type' => $type
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->type->update($id, $_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Type saved";
                $type = $data['type'];
            } else {
                $message = "Type unsaved";
                $error_code = 'type_unsaved';
            }

            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'type' => $type
                )
            );
        }
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $type = null;

            $request = $this->type->delete($id);
            if($request->execute()) {                
                $code = 1;
                $message = "Type deleted";
            } else {
                $message = "Type not deleted";
                $error_code = 'type_not_deleted';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'type' => $type
                )
            );
            return;
        } 

    }