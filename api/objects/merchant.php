<?php
class Merchant
{

    //database connection and table name
    private $conn;
    private $table_name = 'merchants';

    //object properties
    public $id;
    public $password;
    public $business_name;
    public $created_at;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $email;
    public $mobile_no;
    public $balance;
    public $updated_at;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //Function for getting the information of the admin with the given id
    function getById()
    {
        // select all query
        $query = "SELECT
                m.id, m.password, m.created_at, m.first_name, m.middle_name, m.last_name, m.mobile_no, m.email, m.updated_at, m.balance
            FROM
                " . $this->table_name . " m
                m.created_date DESC";


        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind id of product to be updated
        $stmt->bindParam(1, $this->id);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->password = $row['password'];
        $this->created_at = $row['created_at'];
        $this->first_name = $row['first_name'];
        $this->middle_name = $row['middle_name'];
        $this->last_name = $row['last_name'];
        $this->email = $row['email'];
        $this->mobile_no = $row['mobile_no'];
        $this->balance = $row['balance'];
    }

    //match password
    //Function used for matching password while logging in an user.
    function verifyPassword($password)
    {
        $this->getById();
        $date = $this->created_date;
        $date = strtotime($date);
        $year = date('Y', $date);
        $salt = 'ISM';
        $tempHash = $password . (string) $date . (string) $salt;
        for ($i = 0; $i < $year; $i++) $tempHash = md5($tempHash);
        return $tempHash === $this->password;
    }

    //function to check if user exists with given Id
    function merchantExists()
    {

        $this->getById();
        if ($this->auth_id != null) return true;
        else {
            return false;
        }
    }
}
