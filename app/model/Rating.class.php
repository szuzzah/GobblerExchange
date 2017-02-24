<?php

class Rating extends DbObject {
    const DB_TABLE = "rating";

    //database fields
    protected $id;
    protected $rating;

    //one or the other of these must be null
    protected $postId;
    protected $notesId;

    protected $userId; //rating was given by this user

    //constructor
    public function __construct($args = array()){
        $defaultArgs = array(
            'id' => null,
            'rating' => null,
            'postId' => null,
            'notesId' => null,
            'userId' => null
        );

        $args += $defaultArgs;

        $this->id = $args['id'];
        $this->rating = $args['rating'];
        $this->postId = $args['postId'];
        $this->notesId = $args['notesId'];
        $this->userId = $args['userId'];
    }

    //save changes to database
    public function save(){
        $db = Db::instance();

        $db_properties = array(
            'rating' => $this->rating,
            'postId' => $this->postId,
            'notesId' => $this->notesId,
            'userId' => $this->userId
        );

        $db->store($this, __CLASS__, self::DB_TABLE, $db_properties);
    }

    public static function loadById($id){
        $db = Db::instance();
        $obj = $db->fetchById($id, __CLASS__, self::DB_TABLE);
        return $obj;
    }

   public function delete()
   {
       $db = Db::instance();
       $query = sprintf(" DELETE FROM %s WHERE id = '%s'",
           self::DB_TABLE,
           $this->id
       );
       mysql_query($query);
   }

   //--------------------------------------------------------------------------

    //gets all the ratings for a post; used for calcAvg()
    private static function getAllRatingsByPost($postId){
        $query = sprintf(" SELECT * FROM %s WHERE postId=%s",
            self::DB_TABLE,
            $postId
        );

        $db = Db::instance();
        $result = $db->lookup($query);
        if(!mysql_num_rows($result))
            return null;
        else {
            $objects = array();
            while($row = mysql_fetch_assoc($result)) {
                $objects[] = self::loadById($row['id']);
            }
            return ($objects);
        }
    }

    //gets all the ratings for a specific set of notes; used for calcAvg()
    private static function getAllRatingsByNotes($notesId){
        $query = sprintf(" SELECT * FROM %s WHERE notesId=%s",
            self::DB_TABLE,
            $notesId
        );

        $db = Db::instance();
        $result = $db->lookup($query);
        if(!mysql_num_rows($result))
            return null;
        else {
            $objects = array();
            while($row = mysql_fetch_assoc($result)) {
                $objects[] = self::loadById($row['id']);
            }
            return ($objects);
        }
    }

   // Get the rating given by a user for a specific post
   // Used to show the user they have rated a post
   //**This function can be called from the ForumPost class.
   public static function loadByUserAndPostId($userId, $postId) {
       $query = sprintf(" SELECT * FROM %s WHERE postId = '%s' AND userId='%s' ",
           self::DB_TABLE,
           $postId,
           $userId
           );
       $db = Db::instance();
       $result = $db->lookup($query);
       if(!mysql_num_rows($result))
           return null;
       else {
           $row = mysql_fetch_assoc($result);
           $obj = self::loadById($row['id']);
           return ($obj);
       }
   }

   // Get the rating given by a user for specific notes
   // Used to show the user they have rated notes
   //**This function can be called from the Notes class.
   public static function loadByUserAndNotesId($userId, $notesId) {
       $query = sprintf(" SELECT * FROM %s WHERE notesId = '%s' AND userId='%s' ",
           self::DB_TABLE,
           $notesId,
           $userId
           );
       $db = Db::instance();
       $result = $db->lookup($query);
       if(!mysql_num_rows($result))
           return null;
       else {
           $row = mysql_fetch_assoc($result);
           $obj = self::loadById($row['id']);
           return ($obj);
       }
   }

   //**This function can be called from the ForumPost class.
   public function upvoteForumPost($postId, $userId){
       $old_rating = this::loadByUserAndPostId($userId, $postId);

       if($old_rating != null){
           //user has previously downvoted the post - remove (neutralize) their rating
           $old_rating->delete();
           header('Location: '.BASE_URL);
           exit();
       }
       else {
           //user has not rated the post; record their vote as +1
           $rating = new Rating();
           $rating->set('userId', $userId);
           $rating->set('postId', $postId);
           $rating->set('rating', 1);
           $rating->save();
       }
   }

   //**This function can be called from the ForumPost class.
   public function downvoteForumPost($postId, $userId){
       $old_rating = this::loadByUserAndPostId($userId, $postId);

       if($old_rating != null){
           //user has previously upvoted the post - remove (neutralize) their rating
           $old_rating->delete();
           header('Location: '.BASE_URL);
           exit();
       }
       else {
           //user has not rated the post; record their vote as -1
           $rating = new Rating();
           $rating->set('userId', $userId);
           $rating->set('postId', $postId);
           $rating->set('rating', -1);
           $rating->save();
       }
   }

   //**This function can be called from the Notes class.
   public function upvoteNotes($notesId, $userId){
       $old_rating = this::loadByUserAndNotesId($userId, $notesId);

       if($old_rating != null){
           //user has previously downvoted the post - remove (neutralize) their rating
           $old_rating->delete();
           header('Location: '.BASE_URL);
           exit();
       }
       else {
           //user has not rated the post; record their vote as +1
           $rating = new Rating();
           $rating->set('userId', $userId);
           $rating->set('notesId', $notesId);
           $rating->set('rating', 1);
           $rating->save();
       }
   }

   //**This function can be called from the Notes class.
   public function downvoteNotes($notesId, $userId){
       $old_rating = this::loadByUserAndNotesId($userId, $notesId);

       if($old_rating != null){
           //user has previously upvoted the post - remove (neutralize) their rating
           $old_rating->delete();
           header('Location: '.BASE_URL);
           exit();
       }
       else {
           //user has not rated the post; record their vote as -1
           $rating = new Rating();
           $rating->set('userId', $userId);
           $rating->set('notesId', $notesId);
           $rating->set('rating', -1);
           $rating->save();
       }
   }

   //get a post's overall rating
   //**This function can be called from the ForumPost class.
   public function getPostRating($postid){
       $ratings = this::getAllRatingsByPost($postid);
       if($ratings == null) return 0;

       $total = 0;
       foreach ($ratings as $rating) {
           $total += $rating->get('rating');
       }
       return $total;
   }

   //get a notes's overall rating
   //**This function can be called from the Notes class.
   public function getNotesRating($notesId){
       $ratings = this::getAllRatingsByNotes($notesId);
       if($ratings == null) return 0;

       $total = 0;
       foreach ($ratings as $rating) {
           $total += $rating->get('rating');
       }
       return $total;
   }
}
?>
