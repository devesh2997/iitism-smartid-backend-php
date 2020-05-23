<?php
class User
{

    //database connection and table name
    private $conn;
    private $table_name = 'users';
    private $smartid_table_name = 'smartid_users';
    private $transaction_table_name = 'transactions';
    private $merchant_table_name = 'merchants';

    //object properties
    public $id;
    public $password;
    public $auth_id;
    public $created_date;
    public $status;
    public $remark;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $sex;
    public $category;
    public $allocated_category;
    public $dob;
    public $email;
    public $photopath;
    public $marital_status;
    public $physically_challenged;
    public $dept_id;
    public $updated;
    public $branch_id;
    public $course_id;
    public $smartid_no;
    public $balance;
    public $role_id;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //get id prefixes, e.g. 16JE, 17MT etc.
    function getIdPrefixes()
    {
        //select all query
        $query = "SELECT LEFT(a.id,4) AS prefix FROM users a WHERE a.auth_id='stu'
        GROUP BY LEFT(a.id,4)";


        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    //get user transactions
    function getTransactions(){
        $this->getById();
        if($this->smartid_no == null) return null;

        //query
        $query = "SELECT t.id, t.type,t.amount, m.business_name, m.first_name, m.middle_name, m.last_name, t.created_at FROM ".$this->transaction_table_name." t 
            LEFT JOIN ".$this->merchant_table_name." m ON t.merchant_id = m.id 
            WHERE user_id=:id";
        
        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        return $stmt;
    }

    //initialize smartid record
    //Function used for creating a new smart card 
    //user entry into the database.
    function initializeSmartId($id, $smartid_no)
    {
        $this->getById();
        if ($this->smartid_no != null) return false;
        // insert query
        $query = "INSERT INTO " . $this->smartid_table_name . "
            SET
                id = :id,
                smartid_no = :smartid_no,
                balance = 0,
                created_date = :created_date,
                updated_date = :updated_date";
        // prepare the query
        $stmt = $this->conn->prepare($query);

        $current_time = new DateTime();
        $current_time = $current_time->format('Y-m-d H:i:s');

        // bind the values
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':smartid_no', $smartid_no);
        $stmt->bindParam(':created_date', $current_time);
        $stmt->bindParam(':updated_date', $current_time);

        // execute the query, also check if query was successful
        if ($stmt->execute()) {
            return true;
        } else {
            echo json_encode($stmt->errorInfo());
        }

        return false;
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

    // read users
    // get all the users who have been issued smart id cards.
    function getAll()
    {

        // select all query
        $query = "SELECT
                u.id, u.auth_id, u.created_date, u.status, u.remark, ud.first_name, ud.middle_name, ud.last_name, ud.sex, ud.category, ud.allocated_category, ud.dob, ud.email, ud.photopath, ud.marital_status, ud.physically_challenged, ud.dept_id, ud.updated , st.course_id, st.branch_id, si.smartid_no, si.balance
            FROM
                " . $this->table_name . " u
                LEFT JOIN
                    user_details ud
                        ON u.id = ud.id
                LEFT JOIN stu_academic st
                        ON u.id = st.admn_no
                LEFT JOIN smartid_users si
                    ON u.id = si.id
            ORDER BY
                u.created_date DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    //Function for getting the information of the user with the given id
    function getById()
    {
        // select all query
        $query = "SELECT
                u.id, u.password, u.auth_id, u.created_date, u.status, u.remark, ud.first_name, ud.middle_name, ud.last_name, ud.sex, ud.category, ud.allocated_category, ud.dob, ud.email, ud.photopath, ud.marital_status, ud.physically_challenged, ud.dept_id, ud.updated , st.course_id, st.branch_id, si.smartid_no, si.balance
            FROM
                " . $this->table_name . " u
                LEFT JOIN
                    user_details ud
                        ON u.id = ud.id 
                LEFT JOIN stu_academic st
                        ON u.id = st.admn_no
                LEFT JOIN smartid_users si
                        ON u.id = si.id 
                WHERE u.id = ?                
            ORDER BY
                u.created_date DESC";


        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind id of product to be updated
        $stmt->bindParam(1, $this->id);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->auth_id = $row['auth_id'];
        $this->password = $row['password'];
        $this->created_date = $row['created_date'];
        $this->status = $row['status'];
        $this->remark = $row['remark'];
        $this->first_name = $row['first_name'];
        $this->middle_name = $row['middle_name'];
        $this->last_name = $row['last_name'];
        $this->sex = $row['sex'];
        $this->category = $row['category'];
        $this->allocated_category = $row['allocated_category'];
        $this->dob = $row['dob'];
        $this->email = $row['email'];
        $this->photopath = $row['photopath'];
        $this->marital_status = $row['marital_status'];
        $this->physically_challenged = $row['physically_challenged'];
        $this->dept_id = $row['dept_id'];
        $this->updated = $row['updated'];
        $this->branch_id = $row['branch_id'];
        $this->course_id = $row['course_id'];
        $this->smartid_no = $row['smartid_no'];
        $this->balance = $row['balance'];
    }

    // search users
    //Function for searching for existing smart 
    //card users based on their name and admission number.
    function search($keywords, $prefix)
    {

        $query = "SELECT
                u.id, u.auth_id, u.created_date, u.status, u.remark, ud.first_name, ud.middle_name, ud.last_name, ud.sex, ud.category, ud.allocated_category, ud.dob, ud.email, ud.photopath, ud.marital_status, ud.physically_challenged, ud.dept_id, ud.updated , st.course_id, st.branch_id, si.smartid_no, si.balance
            FROM
                " . $this->table_name . " u
                LEFT JOIN
                    user_details ud
                        ON u.id = ud.id
                    LEFT JOIN stu_academic st
                        ON u.id = st.admn_no 
                    LEFT JOIN smartid_users si
                        ON u.id = si.id
                    WHERE u.id LIKE ? OR ud.first_name LIKE ? OR ud.middle_name LIKE ? OR ud.last_name LIKE ? 
            ORDER BY
                u.created_date DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = $prefix . "%{$keywords}%";

        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    //function to check if user exists with given Id
    function userExists()
    {

        $this->getById();
        if ($this->auth_id != null) return true;
        else {
            return false;
        }
    }
}
