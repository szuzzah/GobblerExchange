<?php

class Poll extends DbObject {
    const DB_TABLE = "poll";

    //database fields
    protected $id;
    protected $title;
    protected $userId;     //author
    protected $forumId;

    //constructor
    public function __construct($args = array()){
        $defaultArgs = array(
            'id' => null,
            'title' => null,
            'userId' => null,
            'forumId' => null
        );

        $args += $defaultArgs;

        $this->id = $args['id'];
        $this->title = $args['title'];
        $this->userId = $args['userId'];
        $this->forumId = $args['forumId'];
    }

    //save changes to database
    public function save(){
        $db = Db::instance();

        $db_properties = array(
            'title' => $this->title,
            'userId' => $this->userId,
            'forumId' => $this->forumId
        );

        $db->store($this, __CLASS__, self::DB_TABLE, $db_properties);
    }

    public function delete()
    {
        $db = Db::instance();
        $query = sprintf(" DELETE FROM %s  WHERE id = '%s' ",
            self::DB_TABLE,
            $this->id
        );
        $ex = mysql_query($query);
        if(!$ex) die ('Query failed:' . mysql_error());
    }

    public static function loadById($id){
        $db = Db::instance();
        $obj = $db->fetchById($id, __CLASS__, self::DB_TABLE);
        return $obj;
    }

    public static function getPollOptions(){
        return PollOptions::getPollOptions($this->id);
    }
}
?>