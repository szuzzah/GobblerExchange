<?php

include_once '../global.php';

// get the identifier for the page we want to load
$action = $_GET['action'];

// instantiate a SiteController and route it
$sc = new SiteController();
$sc->route($action);

class SiteController {

	// route us to the appropriate class method for this action
	public function route($action) {
		switch($action) {
			case 'home':
				$this->home();
				break;
			case 'editpost':
				$this->editpost();
				break;
			// case 'signup':
			// 	$this->signup();
			// 	break;
			// case 'login':
			// 	$this->login();
			// 	break;
			// case 'postlogin':
			// 	$this->postlogin();
			// 	break;
		}
	}

    public function home() {
		//self::loggedInCheck();

		//Get forumid associated with the current crn
		$crnid = 1; //Not implemented
		$crn_row = Crn::loadById($crnid);
		$forumid = $crn_row->get('forumId');

		//retrieve all posts from the forum
		$posts = ForumPost::getAllPosts($forumid);
		include_once SYSTEM_PATH.'/view/home.tpl';
	}

	public function editpost(){
		include_once SYSTEM_PATH.'/view/edit.tpl';
	}

	// public function signup(){
	// 	$teams = Team::getAllTeams();
	// 	include_once SYSTEM_PATH.'/view/signup.tpl';
	// }
	//
	// public function login(){
	// 	include_once SYSTEM_PATH.'/view/login.tpl';
	// }
	//
	// public function postlogin(){
	//  	$un = $_POST['username'];
	// 	$pw = $_POST['password'];
	// 	$user = User::loadByUsername($un);
	// 	if($user == null) {
	// 		// username not found
	// 		$_SESSION['error'] = "<b>Uh oh!</b> Username <u>".$un."</u> not found!";
	// 		header('Location: '.BASE_URL.'/login');
	// 	} // incorrect password
	// 	elseif($user->get('password') != $pw)
	// 	{
	// 		$_SESSION['error'] = "<b>Uh oh!</b> Incorrect password for username <u>".$un."</u>";
	// 		header('Location: '.BASE_URL.'/login');
	// 	}
	// 	else
	// 	{
	// 		$_SESSION['username'] = $un;
	// 		$_SESSION['success'] = "Welcome back, <u>".$un."</u>!";
	// 		header('Location: '.BASE_URL);
	// 	}
	// }
	//
	// private function loggedInCheck(){
	// 	//checks if user  is logged in
	// 	// if not redirects to sign up page
	// 	if( !isset($_SESSION['username']) || $_SESSION['username'] == '')
	// 	{
	// 		header('Location: '.BASE_URL.'/login');
	// 	}
	// 	else
	// 	{
	// 		$user = User::loadByUsername($_SESSION['username']);
	// 		$userName = $user->get('username');
	// 	}
	// }
// 	public function signupRegister() {
// 		// get post data
// 		$username  = $_POST['username'];
// 		$teamname = $_POST['nteams'];
// 		$passwd = $_POST['passwd'];
// 		$email  = $_POST['email'];
//
// 		// do some simple form validation
//
// 		// are all the required fields filled?
// 		if ($username == '' || $teamname == '' || $passwd == '' || $email == '') {
// 			// missing form data; send us back
// 			$_SESSION['error'] = 'Please complete all registration fields.';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
// 		if(strlen($username) > 25){
// 			$_SESSION['error'] = 'Sorry, that username is too long.';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
// 		if(strlen($passwd) > 30){
// 			$_SESSION['error'] = 'Sorry, that password is too long.';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
// 		if(preg_match('/[^A-Za-z0-9]/', $username)){
// 			$_SESSION['error'] = 'Sorry, that username contains invalid characters';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
//
// 		// is username in use?
// 		$user = User::loadByUsername($username);
// 		if(!is_null($user)) {
// 			// username already in use; send us back
// 			$_SESSION['error'] = 'Sorry, that username is already in use. Please pick a unique one.';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
// 		$user = User::loadByEmail($email);
// 		if(!is_null($user)) {
// 			// email is in use
// 			$_SESSION['error'] = 'Sorry, that email is already in use. Please pick a different one.';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
// 		$allowed_domains = array("vt.edu");
// 		$email_domain = array_pop(explode("@", $email));
// 		if(!in_array($email_domain, $allowed_domains)) {
//     	// Not an authorised email
// 			$_SESSION['error'] = 'Sorry, that email is not a vt.edu email';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
//
// 		$user = User::loadByEmail($email);
// 		if(!is_null($user)) {
// 			// username already in use; send us back
// 			$_SESSION['registerError'] = 'Sorry, that email is already in use. Please pick a different one.';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
// 		$allowed_domains = array("vt.edu");
// 		$email_domain = array_pop(explode("@", $email));
// 		if(!in_array($email_domain, $allowed_domains)) {
//     	// Not an authorised email
// 			$_SESSION['registerError'] = 'Sorry, that email is not a vt.edu email';
// 			header('Location: '.BASE_URL.'/signup');
// 			exit();
// 		}
//
// 		// okay, let's register
// 		$user = new User();
// 		$user->set('username', $username);
// 		$user->set('teamname', $teamname);
// 		$user->set('password', $passwd);
// 		$user->set('email', $email);
// 		$user->save(); // save to db
// /*
// Was going to try and update a counter
// 		//$user_row = User::loadByUsername($username);
// 		$team = Team::loadByTeamname($teamname);
// 		$count = $team->get('membercount');
// 		$team->set('score', $team->get('score'));
// 		$team->set('membercount', $count++);
// 		$team->save();
// 		*/
//
// 		// log in this freshly created user and redirect to home page
// 		$_SESSION['username'] = $username;
// 		$_SESSION['success'] = "You successfully registered as ".$username.".";
// 		header('Location: '.BASE_URL);
// 		exit();
// 	}
}
