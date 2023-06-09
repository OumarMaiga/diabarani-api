<?php
    
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: *");

    require "./bootstrap.php";

    define("ROOT",__DIR__.'/');
    define("API_URL",'http://localhost/diabarani-api/');

    use Controller\UserController;
    use Controller\AuthController;
    use Controller\GenreController;
    use Controller\FilmController;
    use Controller\VideoController;
    use Controller\ImageController;
    use Controller\HistoriqueController;
    use Controller\SerieController;
    use Controller\SaisonController;
    use Controller\EpisodeController;
    use Controller\VueController;
    use Controller\LikeController;
    /*use \Router;*/

    $auth = new AuthController();
    $user = new UserController();
    $genre = new GenreController();
    $film = new FilmController();
    $video = new VideoController();
    $image = new ImageController();
    $historique = new HistoriqueController();
    $serie = new SerieController();
    $saison = new SaisonController();
    $episode = new EpisodeController();
    $vue = new VueController();
    $like = new LikeController();
    
    /*header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode( '/', $uri );

    
    // all of our endpoints start with /person
    // everything else results in a 404 Not Found
    if ($uri[1] !== 'person') {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    // the user id is, of course, optional and must be a number:
    $userId = null;
    if (isset($uri[2])) {
        $userId = (int) $uri[2];
    }

    // authenticate the request with Okta:
    if (! authenticate()) {
        header("HTTP/1.1 401 Unauthorized");
        exit('Unauthorized');
    }

    $requestMethod = $_SERVER["REQUEST_METHOD"];

    // pass the request method and user ID to the PersonController:
    $controller = new PersonController($dbConnection, $requestMethod, $userId);
    $controller->processRequest();

    function authenticate() {
        try {
            switch(true) {
                case array_key_exists('HTTP_AUTHORIZATION', $_SERVER) :
                    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
                    break;
                case array_key_exists('Authorization', $_SERVER) :
                    $authHeader = $_SERVER['Authorization'];
                    break;
                default :
                    $authHeader = null;
                    break;
            }
            preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
            if(!isset($matches[1])) {
                throw new \Exception('No Bearer Token');
            }
            $jwtVerifier = (new \Okta\JwtVerifier\JwtVerifierBuilder())
                ->setIssuer(getenv('OKTAISSUER'))
                ->setAudience('api://default')
                ->setClientId(getenv('OKTACLIENTID'))
                ->build();
            return $jwtVerifier->verify($matches[1]);
        } catch (\Exception $e) {
            return false;
        }
    }*/
    
    switch ($_GET['action']) {

        //////////////////// Auth ////////////////////
        case  'login':
            $auth->login();
            break;
        case  'logout':
            logout();
            break;
        case  'register':
            $auth->register();
            break;
        case  'generate-token':
            generate_token();
            break;
        case 'password-reset':
            password_reset();
            break;

        //////////////////// User ////////////////////
        case 'get-user':
            $user->getById($_GET['id']);
            break;
        case 'update-user':
            $user->updateUser($_GET['id']);
            break;

        //////////////////// Genre ////////////////////
        case 'genre':
            $genre->getById($_GET['id']);
            break;
        case 'genres':
            $genre->get();
            break;
        case 'genres-all':
            $genre->getAll();
            break;
        case 'store-genre':
            $genre->store();
            break;
        case 'edit-genre':
            $genre->getById($_GET['id']);
            break;
        case 'destroy-genre':
            $genre->destroy($_GET['id']);
            break;
        case 'delete-genre':
            $genre->delete($_GET['id']);
            break;
        case 'update-genre':
            $genre->update($_GET['id']);
            break;

        //////////////////// Serie ////////////////////
        case 'serie':
            $serie->getById($_GET['id']);
            break;
        case 'series':
            $serie->get();
            break;
        case 'series-all':
            $serie->getAll();
            break;
        case 'store-serie':
            $serie->store();
            break;
        case 'edit-serie':
            $serie->getById($_GET['id']);
            break;
        case 'destroy-serie':
            $serie->destroy($_GET['id']);
            break;
        case 'delete-serie':
            $serie->delete($_GET['id']);
            break;
        case 'update-serie':
            $serie->update($_GET['id']);
            break;
        case 'serie-genres':
            $serie->serie_genres($_GET['serie_id']);
            break;
        case 'new-series':
            $serie->new_series();
            break;
        case 'upcoming-series':
            $serie->upcoming();
            break;
        case 'genre-series':
            $serie->genre_series($_GET['genre_id']);
            break;
        case 'genres-series':
            $serie->genres_series();
            break;
        case 'some-genres-series':
            $serie->some_genres_series($_GET['genre_ids']);
            break;

        //////////////////// Saison ////////////////////
        case 'saison':
            $saison->getById($_GET['id']);
            break;
        case 'saisons':
            $saison->get($_GET['serie_id']);
            break;
        case 'saisons-all':
            $saison->getAll($_GET['serie_id']);
            break;
        case 'store-saison':
            $saison->store($_GET['serie_id']);
            break;
        case 'edit-saison':
            $saison->getById($_GET['id']);
            break;
        case 'destroy-saison':
            $saison->destroy($_GET['id']);
            break;
        case 'delete-saison':
            $saison->delete($_GET['id']);
            break;
        case 'update-saison':
            $saison->update($_GET['id']);
            break;
        case 'new-saisons':
            $saison->new_saisons();
            break;
        case 'upcoming-saisons':
            $saison->upcoming();
            break;

        //////////////////// Episode ////////////////////
        case 'episode':
            $episode->getById($_GET['id']);
            break;
        case 'episodes':
            $episode->get($_GET['serie_id'], $_GET['saison_id']);
            break;
        case 'episodes-all':
            $episode->getAll($_GET['serie_id'], $_GET['saison_id']);
            break;
        case 'store-episode':
            $episode->store($_GET['serie_id'], $_GET['saison_id']);
            break;
        case 'edit-episode':
            $episode->getById($_GET['id']);
            break;
        case 'destroy-episode':
            $episode->destroy($_GET['id']);
            break;
        case 'delete-episode':
            $episode->delete($_GET['id']);
            break;
        case 'update-episode':
            $episode->update($_GET['id']);
            break;
        case 'new-episodes':
            $episode->new_episodes();
            break;
        case 'upcoming-episodes':
            $episode->upcoming();
            break;

        //////////////////// Film ////////////////////
        case 'film':
            $film->getById($_GET['id']);
            break;
        case 'films':
            $film->get();
            break;
        case 'films-all':
            $film->getAll();
            break;
        case 'store-film':
            $film->store();
            break;
        case 'edit-film':
            $film->getById($_GET['id']);
            break;
        case 'destroy-film':
            $film->destroy($_GET['id']);
            break;
        case 'delete-film':
            $film->delete($_GET['id']);
            break;
        case 'update-film':
            $film->update($_GET['id']);
            break;
        case 'film-genres':
            $film->film_genres($_GET['film_id']);
            break;
        case 'new-films':
            $film->new_films();
            break;
        case 'upcoming-films':
            $film->upcoming();
            break;
        case 'genre-films':
            $film->genre_films($_GET['genre_id']);
            break;
        case 'genres-films':
            $film->genres_films();
            break;
        case 'some-genres-films':
            $film->some_genres_films($_GET['genre_ids']);
            break;

        //////////////////// Video ////////////////////
        case 'upload-video':
            $video->upload();
            break;

        //////////////////// Image ////////////////////
        case 'upload-image':
            $image->upload();
            break;
            
        //////////////////// HISTORIQUE ////////////////////
        case 'store-historique':
            $historique->store();
            break;
        case 'user-historiques':
            $historique->getByUser($_GET['user_id']);
            break;
        case 'destroy-historique':
            $historique->destroy($_GET['id']);
            break;

        //////////////////// Vue ////////////////////
        case 'vues':
            $vue->get($_GET['entite'], $_GET['id']);
            break;
        case 'store-vue':
            $vue->store();
            break;

        //////////////////// Like ////////////////////
        case 'likes':
            $like->get($_GET['entite'], $_GET['id']);
            break;
        case 'store-like':
            $like->store();
            break;

        default:
            echo"wrong url";
    }

    function generate_token($user_id) {
        $connexion = connect_db();
        //Verif si le token exist
        do {
            $bytes = random_bytes(16);
            $token = bin2hex($bytes);
            $token_exist = $connexion->prepare("SELECT * FROM users WHERE token=:token");
            $token_exist->execute(array(':token' => $token));
        } while ($token_exist->rowCount() > 0);
        return $token;
    }

/*
    $router = new Router($_GET['url']);
    $router->get('/', function($id) {
        echo "Bienvenue sur ma page";
    });
    $router->get('/posts/:id', function($id) {
        echo "l'article $id";
    });
    $router->run();
    */