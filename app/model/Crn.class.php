<?php

class Crn extends DbObject {
    const DB_TABLE = "crn";

    //database fields
    protected $id;
    protected $number;
    protected $course_title;
    protected $calendarId;
    protected $forumId;
    protected $chatId;

    //constructor
    public function __construct($args = array()){
        $defaultArgs = array(
            'id' => null,
            'number' => null,
            'course_title' => '',
            'calendarId' => null,
            'forumId' => null,
            'chatId' => null
        );

        $args += $defaultArgs;

        $this->id = $args['id'];
        $this->number = $args['number'];
        $this->course_title = $args['course_title'];
        $this->calendarId = $args['calendarId'];
        $this->forumId = $args['forumId'];
        $this->chatId = $args['chatId'];
    }

    //save changes to database
    public function save(){
        $db = Db::instance();

        $db_properties = array(
            'number' => $this->number,
            'course_title' => $this->course_title,
            'calendarId' => $this->calendarId,
            'forumId' => $this->forumId,
            'chatId' => $this->chatId
        );

        $db->store($this, __CLASS__, self::DB_TABLE, $db_properties);
    }

    public static function loadById($id){
        $db = Db::instance();
        $obj = $db->fetchById($id, __CLASS__, self::DB_TABLE);
        return $obj;
    }
}
?>
