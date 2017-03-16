<?php

include_once '../global.php';

// get the identifier for the page we want to load
$action = $_GET['action'];

// instantiate a GroupController and route it
$sc = new GroupController();
$sc->route($action);

class GroupController {

	// route us to the appropriate class method for this action
	public function route($action) {
		switch($action) {
			case 'newGroup':
				$this->newGroup();
				break;
			case 'newGroup_submit':
				$this->newGroup_submit();
				break;
		}
	}

    public function newGroup() {
		SiteController::loggedInCheck();

		include_once SYSTEM_PATH.'/view/newgroup.tpl';								//TODO: check tpl name
	}

	public function newGroup_submit() {
		SiteController::loggedInCheck();

		//user canceled new event
		if (isset($_POST['Cancel'])) {
			header('Location: '.BASE_URL.'/calendar');											//TODO: update
			exit();
		}

		// protected $number;      //crn, if a class, null otherwise
	    // protected $group_name;
		protected $calendarId;
		protected $forumId;
		protected $chatId;
		protected $whiteboardId;
		// protected $userId;

		$groupName = $_POST['group_name'];
																				//TODO: check if group name exists already
		$type = $_POST['type'];		//class or non-class

		 // if class, number = crn; null otherwise
		 $number = NULL;
		if($type == "crn"){
			$number = $_POST['number'];
		}

		//get author's id
		$user_row = User::loadByUsername($_SESSION['username']);
		$userid = $user_row->get('id');

		$group = new Group();
		$group->set('number', $number);
		$group->set('group_name', $groupName);
		$group->set('calendarId', 1);												//TODO: set these next 5 IDs
		$group->set('forumId', 1);
		$group->set('chatId', 1);
		$group->set('whiteboardId', 1);
		$group->set('userId', 1);
		$group->save();

		header('Location: '.BASE_URL);
		exit();
	}
}
