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

		//user canceled new group
		if (isset($_POST['Cancel'])) {
			header('Location: '.BASE_URL);											//TODO: update location?
			exit();
		}

		//Check if group name is available (doesn't already exist)
		$groupName = $_POST['group_name'];
		if(!Group::checkGroupNameAvailability($groupName)){
			//unavailable group name
			$_SESSION['error'] = 'Sorry, that group name is already taken.';	  //TODO make sure SESSION[error] is available in tpl
			header('Location: '.BASE_URL);											//TODO update location?
			exit();
		}

		$type = $_POST['type'];		//class or non-class

		 // if class, number = crn; null otherwise
		 $number = NULL;
		if($type == "crn"){
			$number = $_POST['number'];
		}

		//get author's id
		$user_row = User::loadByUsername($_SESSION['username']);
		$userid = $user_row->get('id');

		//create modules for the group
		$calendar = new Calendar();
		$forum = new Forum();
		$chat = new Chat();
		$whiteboard = new Whiteboard();

		$group = new Group();
		$group->set('number', $number);
		$group->set('group_name', $groupName);
		$group->set('userId', $userid);
		$group->set('calendarId', $calendar->get('id'));
		$group->set('forumId', $forum->get('id'));
		$group->set('chatId', $chat->get('id'));
		$group->set('whiteboardId', $whiteboard->get('id'));
		$group->save();

		header('Location: '.BASE_URL);
		exit();
	}
}
