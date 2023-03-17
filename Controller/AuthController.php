<?php 

    namespace Controller;

    use Model\Auth;
    use Model\User;

    class AuthController {
        
        private $auth;
        private $user;
        
        public function __construct() {
            $this->auth = new Auth();
            $this->user = new User();
        }

        public function login() {
            
            $json = file_get_contents('php://input');
            
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $token = null;
            $user = null;
            
            if ($_POST['login'] == "" || $_POST['password'] == "") {
                echo json_encode(
                    array(
                        'message' => "login and password are required",
                        'code' => $code,
                        'error_code' => 'fields_empty',
                        'token' => $token,
                        'user' => $user
                    )
                );
                return;
            }

            if(!is_numeric($_POST['login']) && !filter_var($_POST['login'], FILTER_VALIDATE_EMAIL)) {
                
                echo json_encode(
                    array(
                        'message' => "Login must be email or phone number",
                        'code' => $code,
                        'error_code' => 'login_wrong_type',
                        'token' => $token,
                        'user' => $user
                    )
                );
                return;
            }
            //$password = md5($_POST['password']);
            if (isset($_POST['login']) && isset($_POST['password'])) {
                $request = $this->auth->authenticate($_POST);
                $request->execute();
                if ($data = $request->fetch()) {
                    $code = 1;
                    $message = "User loged in";
                    $token = $data['token'];
                    $user = $data;
                } else {
                    $message = "User not found";
                    $error_code = 'user_not_found';
                }
            }
            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'token' => $token,
                    'user' => $user
                )
            );
            return;
        }

        //Inscription
        public function register() {
            
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);
            $code = 0;
            $error_code = null;
            $token = null;
            $user = null;
                
            if ($_POST['phone'] == "" || $_POST['password'] == "") {
                echo json_encode(
                    array(
                        'message' => "Phone, password and password_confirm fields are required",
                        'code' => $code,
                        'error_code' => 'fields_required',
                        'token' => $token,
                        'user' => $user
                    )
                );
                return;
            }

            if (isset($_POST['phone']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
                
                if(isset($_POST['email']) && $_POST['email'] != ""){
                    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                    if ($email == false) {
                        echo json_encode(
                            array(
                                'message' => "Email invalid",
                                'code' => $code,
                                'error_code' => 'invalid_email',
                                'token' => $token,
                                'user' => $user
                            )
                        );
                        return;
                    }
                }

                /*if(!is_numeric($_POST['phone'])) {
                    echo json_encode(
                        array(
                            'message' => "Phone number must be number",
                            'code' => $code,
                            'error_code' => 'invalid_phone_number',
                            'token' => $token,
                            'user' => $user
                        )
                    );
                    return;
                }*/

                if(strlen($_POST['password']) < 8){
                    echo json_encode(
                        array(
                            'message' => "Password must be >= 8 characters",
                            'code' => $code,
                            'error_code' => 'short_password',
                            'token' => $token,
                            'user' => $user
                        )
                    );
                    return;
                }
                if ($_POST['password'] != $_POST['password_confirm']) {
                    echo json_encode(
                        array(
                            'message' => 'Password doesn\'t match',
                            'code' => $code,
                            'error_code' => 'password_not_match',
                            'token' => $token,
                            'user' => $user
                        )
                    );
                    return;
                }

                /* Verification si l'email est unique */
                if(isset($_POST['email']) && $_POST['email'] != ""){
                    $user = $this->user->getBy('email', '=', $_POST['email']);
                    $user->execute();
                    if ($user->fetch()) {
                        echo json_encode(
                            array(
                                'message' => 'This email is used',
                                'code' => $code,
                                'error_code' => 'email_used',
                                'token' => $token,
                                'user' => $user
                            )
                        );
                        return;
                    }
                }

                /* Verification si l'email est unique */
                if(isset($_POST['phone']) && $_POST['phone'] != ""){
                    $user = $this->user->getBy('phone', '=', $_POST['phone']);
                    $user->execute();
                    if ($user->fetch()) {
                        echo json_encode(
                            array(
                                'message' => 'This phone number is used',
                                'code' => $code,
                                'error_code' => 'phone_number_used',
                                'token' => $token,
                                'user' => $user
                            )
                        );
                        return;
                    }
                }

                /* Generation de token */
                do {
                    $bytes = random_bytes(16);
                    $token = bin2hex($bytes);
                    $token_exist = $this->user->checkToken($token);
                    $token_exist->execute();
                } while ($token_exist->fetch());

                $_POST['password'] = md5($_POST['password']);
                $_POST['token'] = $token;
                $data = $this->user->setUser($_POST);
                if ($data['success']) {
                    $message = "User registered successfully";
                    $code = 1;
                    $user = $data['user'];
                } else {
                    $token = null;
                    $message = "User registration failed";
                    $error_code = 'unknown';
                }
            }

            echo json_encode(
                array(
                    'code' => $code,
                    'message' => $message,
                    'error_code' => $error_code,
                    'token' => $token,
                    'user' => $user
                )
            );
            return;
        }

        function password_reset() {
            $token = random_int(1000, 9999);
            $json = file_get_contents('php://input');
            $_POST = json_decode($json, true);

            $request = $connexion->prepare('INSERT INTO password_resets (token, phone, created_at) VALUES (:token, :phone, NOW())');
            $request->bindParam(':token', $token);
            $request->bindParam(':phone', $_POST['phone']);
            if ($request->execute()) {
                $message = 'Number saved';
            } else {
                $message = 'Number unsaved';
            }
            echo json_encode(
                array(
                    'message' => $message
                )
            );
            return;
        }
        
    }