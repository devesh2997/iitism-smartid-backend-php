<?php
class Admin
{

    //database connection and table name
    private $conn;
    private $table_name = 'users';
    private $smartid_table_name = 'smartid_users';

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
    public $role_id;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //check smartid auth
    // Here, we have also ensured that even if a person logs in to our system, 
    // using their existing username/password combination that they use 
    // to login into their MIS accounts, only those users will have access 
    // to our API who have the required ‘auth’ necessary.
    //(We have created a new auth, ‘smartid’ , 
    //which will give an admins access to our APIs.)
    function checkSmartIdAuth($id)
    {
        $query = "SELECT * from user_auth_types WHERE id = ? AND auth_id = 'smartid'";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind id of product to be updated
        $stmt->bindParam(1, $id);

        // execute query
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    //Function for getting the information of the admin with the given id
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
    function adminExists()
    {

        $this->getById();
        if ($this->auth_id != null) return true;
        else {
            return false;
        }
    }
}
