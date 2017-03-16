<?php

include_once '../global.php';

// get the identifier for the page we want to load
$action = $_GET['action'];

// instantiate a ForumController and route it
$sc = new ForumController();
$sc->route($action);

class ForumController {

	// route us to the appropriate class method for this action
	public function route($action) {
		switch($action) {
			case 'forum':
				$this->forum();
				break;

			case 'editpost':
				$this->editpost();
				break;
			case 'editpost_submit':
				$this->editpost_submit();
				break;

			case 'newpost':
				$this->newpost();
				break;
			case 'newpost_submit':
				$this->newpost_submit();
				break;

			case 'deletepost':
				$this->deletepost();
				break;

			case 'upvote':
				$this->upvote();
				break;
			case 'downvote':
				$this->downvote();
				break;
		}
	}

    public function forum() {
		SiteController::loggedInCheck();

		//Get forumid associated with the current group
		$groupId = 1;                                                              //TODO: Not implemented
		$group_entry = Group::loadById($groupId);
		$forumId = $group_entry->get('forumId');
        $forum = Forum::loadById($forumId);

		//retrieve all posts from the forum
		$posts = $forum->getPosts();
        $pinned_posts = $forum->getPinnedPosts();
		include_once SYSTEM_PATH.'/view/forum.tpl';                               //TODO: make sure this is the correct tpl
	}

	public function editpost(){
        SiteController::loggedInCheck();

        //retrieve the post
		$postid = $_POST['edit'];
		$post_row = ForumPost::loadById($postid);

        //retrieve post author's username
		$authorid = $post_row->get('userId');
		$user = User::loadById($authorid);
		$username = $user->get('username');

		//check if author of the post is the logged in user
		if($_SESSION['username'] != $username){
			$_SESSION['info'] = "You can only edit posts of which you are the author of.";
			header('Location: '.BASE_URL);
			exit();
		} else {
			//allow access to edit post
			$title = $post_row->get('title');
			$body = $post_row->get('description');
			$tag = $post_row->get('tag');
			include_once SYSTEM_PATH.'/view/edit.tpl';                           //TODO: check tpl is correct
		}
	}

	public function editpost_submit(){
        SiteController::loggedInCheck();

		if (isset($_POST['Cancel'])) {
			header('Location: '.BASE_URL);
			exit();
		}

		$postid = $_POST['postid'];
		$post = ForumPost::loadById($postid);

		$title = $_POST['title'];
		$body = $_POST['description'];
		$tag = $_POST['tag'];
		$timestamp = date("Y-m-d", time());

		$post = ForumPost::loadById($postid);
		$post->set('title', $title);
		$post->set('description', $body);
		$post->set('tag', $tag);
		$post->set('timestamp', $timestamp);
		$post->save();

		header('Location: '.BASE_URL);
	}

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
	}

	public function downvote(){
        SiteController::loggedInCheck();

        //get post
		$postId = $_POST['downvote'];
        $post = ForumPost::loadById($postId);

        //get the user who downvoted the post
        $user = User::loadByUsername($_SESSION['username']);
        $userId = $user->get('id');

        //upvote the post
        $post->upvote($userId);

		header('Location: '.BASE_URL);
		exit();
	}

	public function newpost(){
        SiteController::loggedInCheck();

		include_once SYSTEM_PATH.'/view/newpost.tpl';                             //TODO make sure the tpl is correct
	}
	public function newpost_submit(){
        SiteController::loggedInCheck();

		if (isset($_POST['Cancel'])) {
			header('Location: '.BASE_URL);
			exit();
		}

		$title = $_POST['title'];
		$description = $_POST['description'];
		$tag = $_POST['tag'];
		$timestamp = date("Y-m-d", time());
		$author = $_SESSION['username'];

		//get author's id
		$user_row = User::loadByUsername($author);
		$userid = $user_row->get('id');

		//add a rating
		$rating = new Rating();
		$rating->set('userId', $userid);
		$rating->set('rating', 0);
		$rating->save();

		$post = new ForumPost();
		$post->set('userId', $userid);
		$post->set('timestamp', $timestamp);
		$post->set('title', $title);
		$post->set('description', $description);
		$post->set('tag', $tag);
		$post->set('ratingId', $rating->get('id'));
		$post->set('forumId', 1);                                                 //TODO hardcoded, only 1 CRN page exists
		$post->save();

        //add postId to rating
		$rating->set('postId', $post->get('id'));
		$rating->save();
		header('Location: '.BASE_URL);
	}

	public function deletepost(){
    	SiteController::loggedInCheck();

		$postid = $_POST['delete'];
		$post_row = ForumPost::loadById($postid);
		$post_author_id = $post_row->get('userId');
		$post_author = User::loadById($post_author_id);

		//user is the author of the post, allow delete
		if($post_author->get('username') == $_SESSION['username']){
			$post_row->delete();
		} else {
			$_SESSION['info'] = "You can only delete posts you have created.";
		}

		//refresh page
		header('Location: '.BASE_URL);
	}
}
