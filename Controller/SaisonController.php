<?php

    namespace Controller;

    use Model\Saison;
    use Model\Serie;

    class SaisonController {
        
        private $saison;
        private $serie;
        
        public function __construct() {
            $this->saison = new Saison();
            $this->serie = new Serie();
        }
        
        public function get($serie_id) {
            $code = 0;
            $error_code = null;
            $saisons = null;

            $request = $this->saison->get($serie_id);

            if ($request->execute()) {
                $saisons = $request->fetchAll();
                // On recupere la serie de chaque saison
                $i = -1;
                foreach ($saisons as $saison)
                {
                    $i++;
                    // On recupere les series de chaque saison
                    $saisons[$i]['serie'] = null;
                    $request = $this->serie->getById($saison['serie_id']);
                    if ($request->execute()) 
                    {
                        $serie = $request->fetch();
                        $saisons[$i]['serie'] = $serie;
                    }
                }
                $code = 1;
                $message = "Saisons fetched";
                $saisons = $saisons;
            } else {
                $message = "Saison not fetched";
                $error_code = 'saisons_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saisons' => $saisons
                )
            );
            return;
        } 
        
        public function getAll() {
            $code = 0;
            $error_code = null;
            $saisons = null;

            $request = $this->saison->getAll();

            if ($request->execute()) {
                $saisons = $request->fetchAll();
                // On recupere la serie de chaque saison
                $i = -1;
                foreach ($saisons as $saison)
                {
                    $i++;
                    // On recupere les series de chaque saison
                    $saisons[$i]['serie'] = null;
                    $request = $this->serie->getById($saison['serie_id']);
                    if ($request->execute()) 
                    {
                        $serie = $request->fetch();
                        $saisons[$i]['serie'] = $serie;
                    }
                }
                $code = 1;
                $message = "Saisons fetched";
                $saisons = $saisons;
            } else {
                $message = "Saison not fetched";
                $error_code = 'saisons_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saisons' => $saisons
                )
            );
            return;
        } 
        
        public function getById($id) {
            $code = 0;
            $error_code = null;
            $saison = null;

            $request = $this->saison->getById($id);

            if ($request->execute()) {
                $saison = $request->fetch();
                // On recupere les series de chaque saison
                $saison['serie'] = null;
                $request = $this->serie->getById($saison['serie_id']);
                if ($request->execute()) 
                {
                    $serie = $request->fetch();
                    $saison['serie'] = $serie;
                }
                $code = 1;
                $message = "Saison fetched";
                $saison = $saison;
            } else {
                $message = "Saison not fetched";
                $error_code = 'saison_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
            return;
        }

        public function upcoming() {
            $code = 0;
            $error_code = null;
            $message = null;
            $saisons = null;

            $request = $this->saison->getBy('release_date', '>', date('Y-m-d'));

            if ($request->execute()) {
                $saisons = $request->fetchAll();
                // On recupere la serie de chaque saison
                $i = -1;
                foreach ($saisons as $saison)
                {
                    $i++;
                    // On recupere les series de chaque saison
                    $saisons[$i]['serie'] = null;
                    $request = $this->serie->getById($saison['serie_id']);
                    if ($request->execute()) 
                    {
                        $serie = $request->fetch();
                        $saisons[$i]['serie'] = $serie;
                    }
                }
                $code = 1;
                $message = "saisons fetched";
                $saisons = $saisons;
            } else {
                $message = "saison not fetched";
                $error_code = 'saisons_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saisons' => $saisons
                )
            );
            return;
        }

        public function store($serie_id) {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $saison = null;
            
            if (!isset($_POST['title']) || $_POST['title'] == null || $_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'saison' => $saison
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data_slug = $this->saison->getBy('slug', '=', $_POST['slug']);
            $data_slug->execute();
            if($data_slug->rowCount() > 0)
                $_POST['slug'] = $_POST['slug'].'-'.$data_slug->rowCount();
                
            $_POST['serie_id'] = $serie_id;
            $data = $this->saison->save($_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Saison saved";
                $saison = $data['saison'];
            } else {
                $message = "Saison unsaved";
                $error_code = 'saison_unsaved';
            }

                echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
            return;
        }
        
        public function new_saisons() {
            $code = 0;
            $error_code = null;
            $message = null;
            $saisons = null;

            $request = $this->saison->new_saisons();

            if ($request->execute()) {
                $saisons = $request->fetchAll();
                // On recupere la serie de chaque saison
                $i = -1;
                foreach ($saisons as $saison)
                {
                    $i++;
                    // On recupere la serie de chaque saison
                    $saisons[$i]['serie'] = null;
                    $request = $this->serie->getById($saison['serie_id']);
                    if ($request->execute()) 
                    {
                        $serie = $request->fetch();
                        $saisons[$i]['serie'] = $serie;
                    }
                }

                $code = 1;
                $message = "saisons fetched";
                $saisons = $saisons;
            } else {
                $message = "saison not fetched";
                $error_code = 'saisons_not_fetched';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saisons' => $saisons
                )
            );
            return;
        } 

        /**
         * Mise à jour du saison
         */
        public function update($id) {
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $saison = null;
            $code = 0;
            $error_code = null;
            
            if (!isset($_POST['title']) || $_POST['title'] == null || $_POST['title'] == "") {
                echo json_encode(
                    array(
                        'message' => "title is required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'saison' => $saison
                    )
                );
                return;
            }

            if (isset($_POST['etat']) && ($_POST['etat'] == "1" || $_POST['etat'] == "true")) {
                $_POST['etat'] = true;
            } else {
                $_POST['etat'] = false;
            }
            
            $data = $this->saison->update($id, $_POST);
            
            if ($data['success']) {
                $code = 1;
                $message = "Saison saved";
                $saison = $data['saison'];
            } else {
                $message = "Saison unsaved";
                $error_code = 'saison_unsaved';
            }

            echo json_encode(
                array(
                    'message' => $message,
                    'code' => $code,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
        }
        
        public function delete($id) {
            $code = 0;
            $error_code = null;
            $saison = null;

            $data = $this->saison->delete($id);
            if($data['success']) {                
                $code = 1;
                $message = "Saison deleted";
            } else {
                $message = "Saison not deleted";
                $error_code = 'saison_not_deleted';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
            return;
        } 
        
        public function destroy($id) {
            $code = 0;
            $error_code = null;
            $saison = null;

            $request = $this->saison->destroy($id);
            if($request->execute()) {                
                $code = 1;
                $message = "Saison destroy";
            } else {
                $message = "Saison not destroy";
                $error_code = 'saison_not_destroy';
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'saison' => $saison
                )
            );
            return;
        } 

    }