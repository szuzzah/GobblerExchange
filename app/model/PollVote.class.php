<?php

class PollVote extends DbObject {
    const DB_TABLE = "pollvote";

    //database fields
    protected $id;
    protected $pollId;
    protected $userId;
    
    //constructor
    public function __construct($args = array()){
        $defaultArgs = array(
            'id' => null,
            'pollId' => null,
            'userId' => null
        );

        $args += $defaultArgs;

        $this->id = $args['id'];
        $this->pollId = $args['pollId'];
        $this->userId = $args['userId'];
    }

    //save changes to database
    public function save(){
        $db = Db::instance();

        $db_properties = array(
            'pollId' => $this->pollId,
            'userId' => $this->userId
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
