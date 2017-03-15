<?php

include_once '../global.php';

// get the identifier for the page we want to load
$action = $_GET['action'];

// instantiate a calendarController and route it
$sc = new calendarController();
$sc->route($action);

class SiteController {

	// route us to the appropriate class method for this action
	public function route($action) {
		switch($action) {
			case 'calendar':
				$this->calendar();
				break;
		}
	}

    public function calendar() {
		siteController::loggedInCheck();

		//Get calendarid associated with the group
		$groupId = 1;                                                             //TODO: Not implemented
		$group_entry = Group::loadById($groupId);
		$calendarId = $group_entry->get('calendarId');

		//retrieve all events
        $calendar = Calendar::loadById($calendarId);
        $events = $calendar->getEvents();
		include_once SYSTEM_PATH.'/view/calendar.tpl';                            //TODO: make sure this is the right tpl
	}
}
