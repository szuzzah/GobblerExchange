<?php

include_once '../global.php';

// get the identifier for the page we want to load
$action = $_GET['action'];

// instantiate a PollController and route it
$sc = new PollController();
$sc->route($action);

class PollController {

	// route us to the appropriate class method for this action
	public function route($action) {
		switch($action) {
			case 'editpoll':
				$this->editpoll();
				break;
			case 'editpoll_submit':
				$this->editpoll_submit();
				break;

			case 'newpoll':
				$this->newpoll();
				break;
			case 'newpoll_submit':
				$this->newpoll_submit();
				break;

			case 'deletepoll':
				$this->deletepoll();
				break;
		}
	}

	/* Opens edit poll form
	 * Prereq (POST variables): edit (poll id)
	 * Page variables: title, options
	 */
	public function editpost(){
        SiteController::loggedInCheck();

        //retrieve the poll
		$pollid = $_POST['edit'];
		$poll = Poll::loadById($pollid);

        //retrieve poll author's username
		$authorid = $poll->get('userId');
		$user = User::loadById($authorid);
		$username = $user->get('username');

		//check if author of the post is the logged in user
		if($_SESSION['username'] != $username){
			$_SESSION['info'] = "You can only edit polls of which you are the author of.";
			header('Location: '.BASE_URL);
			exit();
		} else {
			//allow access to edit poll
			$title = $post->get('title');
            $options = $poll->getPollOptions();
			include_once SYSTEM_PATH.'/view/editpoll.tpl';                           //TODO: check tpl is correct
		}
	}

	/* Publishes an edited poll
	 * Prereq (POST variables): Cancel, title, options
	 * Page variables: N/A
	 */
	public function editpost_submit(){
        SiteController::loggedInCheck();

		if (isset($_POST['Cancel'])) {
			header('Location: '.BASE_URL);
			exit();
		}

		$pollid = $_POST['pollid'];
		$poll = Poll::loadById($pollid);

		$title = $_POST['title'];
		$options = $_POST['options'];
		$timestamp = date("Y-m-d", time());

		$poll->set('title', $title);
		$poll->set('timestamp', $timestamp);
		$poll->save();

        //remove old options
        $old_options = Poll::getPollOptions();
        foreach($old_options as $opt){
            $opt->delete();
        }

        //update options
        foreach ($options as $option){
            $poll_option = new PollOption();
            $poll_option->set('pollId', $pollid);
            $poll_option->set('poll_option', $option);
            $poll_option->save();
        }

		header('Location: '.BASE_URL);
	}

	/* Upvotes a post
	 * Prereq (POST variables): upvote (post id)
	 * Page variables: N/A
	 */
	public function upvote(){
        SiteController::loggedInCheck();

		//get post
		$postId = $_POST['upvote'];
        $post = ForumPost::loadById($postId);

        //get the user who upvoted the post
        $user = User::loadByUsername($_SESSION['username']);
        $userId = $user->get('id');

        //upvote the post
        $post->upvote($userId);

		header('Location: '.BASE_URL);
		exit();
	}

	/* Downvote a post
	 * Prereq (POST variables): downvote (post id)
	 * Page variables: N/A
	 */
	public function downvote(){
        SiteController::loggedInCheck();

        //get post
		$postId = $_POST['downvote'];
        $post = ForumPost::loadById($postId);

        //get the user who downvoted the post
        $user = User::loadByUsername($_SESSION['username']);
        $userId = $user->get('id');

        //downvote the post
        $post->downvote($userId);

		header('Location: '.BASE_URL);
		exit();
	}

	/* Opens form for a new poll forum post
	 * Prereq (POST variables): N/A
	 * Page variables: N/A
	 */
	public function newpoll(){
        SiteController::loggedInCheck();

		include_once SYSTEM_PATH.'/view/newpoll.tpl';                             //TODO make sure the tpl is correct
	}

	/* Publishes new poll to the forum
	 * Prereq (POST variables): Cancel, groupId, title, options (array)
	 * Page variables: N/A
	 */
	public function newpoll_submit(){
        SiteController::loggedInCheck();

		if (isset($_POST['Cancel'])) {
			header('Location: '.BASE_URL);
			exit();
		}

		//get forumId from the group
		$groupId = $_POST['groupId'];
		$group_entry = Group::loadById($groupId);
		$forumId = $group_entry->get('forumId');

		$title = $_POST['title'];
		$author = $_SESSION['username'];
		$timestamp = date("Y-m-d", time());
		$options = $_POST['options']; //array

		//get author's id
		$user_row = User::loadByUsername($author);
		$userid = $user_row->get('id');

		//create poll
		$poll = new Poll();
		$poll->set('userId', $userid);
		$poll->set('title', $title);
		$poll->set('forumId', $forumId);
		$poll->set('timestamp', $timestamp);
		$poll->save();

		//add options
		foreach ($options as $option){
			$poll_option = new PollOption();
			$poll_option->set('pollId', $poll->get('id'));
			$poll_option->set('poll_option', $option);
			$poll_option->save();
		}
		header('Location: '.BASE_URL);												//TODO update
	}


	/* Deletes a poll
	 * Prereq (POST variables): pollid
	 * Page variables: SESSION[info]
	 */
	public function deletepoll(){
    	SiteController::loggedInCheck();

		$pollid = $_POST['pollid'];
		$poll = Poll::loadById($pollid);
		$pollAuthorId = $poll->get('userId');
		$pollAuthor = User::loadById($pollAuthorId);

		//user is the author of the post, allow delete
		if($pollAuthor->get('username') == $_SESSION['username']){
			$poll->delete();
		} else {
			$_SESSION['info'] = "You can only delete polls you have created.";
		}

		//refresh page
		header('Location: '.BASE_URL);											//TODO update
	}
}
