<?php

include_once '../global.php';

// get the identifier for the page we want to load
$action = $_GET['action'];

// instantiate a calendarController and route it
$sc = new calendarController();
$sc->route($action);

class CalendarController {

	// route us to the appropriate class method for this action
	public function route($action) {
		switch($action) {
			case 'calendar':
				$this->calendar();
				break;
			case 'newEvent':
				$this->newEvent();
				break;
			case 'newEvent_submit':
				$this->newEvent();
				break;
		}
	}

	// Opens the calendar view page for a particular group
    public function calendar() {
		SiteController::loggedInCheck();

		//get calendar id from group
		$groupId = $_POST['groupId'];
		$group = Group::loadById($groupId);
		$calendarId = $group->get('calendarId');

		//Get calendarid associated with the group
		$calendarId =  $_POST['calendarId'];

		//retrieve all events
        $calendar = Calendar::loadById($calendarId);
        $events = $calendar->getEvents();
		include_once SYSTEM_PATH.'/view/calendar.tpl';                            //TODO: make sure this is the right tpl
	}

	//Opens the form to fill out a new event
	public function newEvent() {
		SiteController::loggedInCheck();

		include_once SYSTEM_PATH.'/view/newevent.tpl';								//TODO: check tpl name
	}

	//Submits the new event forum
	public function newEvent_submit() {
		SiteController::loggedInCheck();

		//user canceled new event
		if (isset($_POST['Cancel'])) {
			header('Location: '.BASE_URL.'/calendar');											//TODO: update location?
			exit();
		}

		$location = $_POST['location'];
		$description = $_POST['description'];
		$timestamp = date("Y-m-d", time());
		$author = $_SESSION['username'];

		//get calendar id from group
		$groupId = $_POST['groupId'];
		$group = Group::loadById($groupId);
		$calendarId = $group->get('calendarId');

		//get author's id
		$user_row = User::loadByUsername($author);
		$userid = $user_row->get('id');

		$event = new Event();
		$event->set('timestamp', $timestamp);
		$event->set('userId', $userid);
		$event->set('location', $location);
		$event->set('description', $description);
		$event->set('calendarId', $calendarId);
		$event->save();

		header('Location: '.BASE_URL.'/calendar');
		exit();
	}
}
