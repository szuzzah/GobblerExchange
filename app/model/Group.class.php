<?php

class Group extends DbObject {
    const DB_TABLE = "group";

    //database fields
    protected $id;
    protected $number;      //crn, if a class, null otherwise
    protected $course_title;
    protected $calendarId;
    protected $forumId;
    protected $chatId;
    protected $whiteboardId;
    protected $userId;      //group leader

    //constructor
    public function __construct($args = array()){
        $defaultArgs = array(
            'id' => null,
            'number' => null,
            'course_title' => null,
            'calendarId' => null,
            'forumId' => null,
            'chatId' => null,
            'whiteboardId' => null,
            'userId' => null
        );

        $args += $defaultArgs;

        $this->id = $args['id'];
        $this->number = $args['number'];
        $this->course_title = $args['course_title'];
        $this->calendarId = $args['calendarId'];
        $this->forumId = $args['forumId'];
        $this->chatId = $args['chatId'];
        $this->whiteboardId = $args['whiteboardId'];
        $this->userId = $args['userId'];
    }

    //save changes to database
    public function save(){
        $db = Db::instance();

        $db_properties = array(
            'number' => $this->number,
            'course_title' => $this->course_title,
            'calendarId' => $this->calendarId,
            'forumId' => $this->forumId,
            'chatId' => $this->chatId,
            'whiteboardId' => $this->whiteboardId,
            'userId' => $this->userId
        );

        $db->store($this, __CLASS__, self::DB_TABLE, $db_properties);
    }

    public static function loadById($id){
        $db = Db::instance();
        $obj = $db->fetchById($id, __CLASS__, self::DB_TABLE);
        return $obj;
    }

    //load by CRN
    public static function loadByCRN($crn) {
        if($crn === null)
            return null;
        $query = sprintf(" SELECT id FROM %s WHERE number = '%s' ",
            self::DB_TABLE,
            $crn
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

    public static function getUsers(){
        return UserGroup::getAllUsersInGroup($this->id);
    }
    public static function getNotes(){
        return Notes::getAllNotes($this->id);
    }
}
?>
