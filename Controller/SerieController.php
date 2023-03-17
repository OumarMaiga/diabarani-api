<?php

    namespace Controller;

    use Model\Serie;

    class SerieController {
        
        private $serie;
        
        public function __construct() {
            $this->serie = new Serie();
        }
        
        public function get() {
            $code = 0;
            $error_code = null;
            $series = null;

            $request = $this->serie->get();

            if ($request->execute()) {
                $code = 1;
                $message = "Series fetched";
                $series = $request->fetchAll();
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
                $code = 1;
                $message = "Series fetched";
                $series = $request->fetchAll();
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
            $request->execute();

            if ($data = $request->fetch()) {
                $code = 1;
                $message = "Serie fetched";
                $serie = $data;
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
            
            if (!isset($_POST['libelle']) || $_POST['libelle'] == null || $_POST['libelle'] == "") {
                echo json_encode(
                    array(
                        'message' => "libelle is required",
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
            
            $data = $this->serie->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Serie saved";
                $serie = $data['serie'];
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

        /**
         * Mise à jour du serie
         */
        public function update($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $serie = null;
            $code = 0;
            $error_code = null;
            
            if (!isset($_POST['libelle']) || $_POST['libelle'] == null || $_POST['libelle'] == "") {
                echo json_encode(
                    array(
                        'message' => "libelle is required",
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
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $serie = null;

            $request = $this->serie->delete($id);
            if($request->execute()) {                
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

    }