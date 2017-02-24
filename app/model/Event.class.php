<?php

class Event extends DbObject {
    const DB_TABLE = "event";

    //database fields
    protected $id;
    protected $timestamp; //date and time of event
    protected $userId;
    protected $location;
    protected $description;
    protected $calendarId;

    //constructor
    public function __construct($args = array()){
        $defaultArgs = array(
            'id' => null,
            'timestamp' => null,
            'userId' => null,
            'location' => null,
            'description' => null,
            'calendarId' => null
        );

        $args += $defaultArgs;

        $this->id = $args['id'];
        $this->timestamp = $args['timestamp'];
        $this->userId = $args['userId'];
        $this->location = $args['location'];
        $this->description = $args['description'];
        $this->calendarId = $args['calendarId'];
    }

    //save changes to database
    public function save(){
        $db = Db::instance();

        $db_properties = array(
            'timestamp' => $this->timestamp,
            'userId' => $this->userId,
            'location' => $this->location,
            'description' => $this->description,
            'calendarId' => $this->calendarId
        );

        $db->store($this, __CLASS__, self::DB_TABLE, $db_properties);
    }

    //SQL formatted date/time
    public function getSQLTimestamp(){
        date("Y-m-d H:i:s", $this->timestamp);
    }

    //getter for date in readable format (ex. March 10, 2017)
    public function getDate(){
        return date("F j, Y", $this->timestamp);
    }

    //Getters for numeric month, day, and year
    public function getMonthNumber(){
        return date("m", $this->timestamp);
    }
    public function getDay(){
        return date("d", $this->timestamp);
    }
    public function getYear(){
        return date("Y", $this->timestamp);
    }

    //Getter for the time in a readable format (ex. 2:30)
    public function getTime(){
        return date("H:i", $this->timestamp);
    }

    //Getters for the numeric hour, minute
    public function getHour(){
        return date("H", $this->timestamp);
    }
    public function getMinute(){
        return date("i", $this->timestamp);
    }

    public function delete()
    {
        $db = Db::instance();
        $query = sprintf(" DELETE FROM %s WHERE id = '%s' ",
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

    private static function getAllEventsByDate($timestamp){
        //get just the database
        $year = date("Y", $timestamp);
        $month = date("m", $timestamp);
        $day = date("d", $timestamp);

        $start = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, $day, $year));
        $end = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day, $year));

        $query = sprintf("SELECT * FROM TABLE WHERE timestamp BETWEEN %s and %s",
            self::DB_TABLE,
            $start,
            $end
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

    // -------------------------------------------------------------------------

    //**This function can be called from the Calendar class.
    private static function getAllEventsByCalendar($calendarId){

        $query = sprintf("SELECT * FROM TABLE WHERE calendarId=%s ",
            self::DB_TABLE,
            $calendarId
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
}
?>
