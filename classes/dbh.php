<?php 
class Dbh {
    private $dbh;
    private $error;
    
    /* this constructor establishes the connection to the database */
    public function __construct() {
        $dsn = "mysql:host=localhost;dbname=blog_tut";
        $username = "root";
        $password = "";
    
        $this->dbh = new PDO($dsn, $username, $password);
    }

    /* these are just member functions that deal with sql queries */
    public function executeQuery($query) {
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $this->dbh->prepare($query);
        $result = $stmt->execute();
        $this->error = $this->dbh->errorInfo();
    }

    public function executeSelect($query) {
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $this->dbh->prepare($query);
        $result = $stmt->execute();

        $this->error = $this->dbh->errorInfo();
 //       print_r($this->error); // Why is this an error??

        $entry = $stmt->fetchAll(PDO::FETCH_ASSOC);

//        print_r($entry);

        return $entry;
    }
}

/* This code successfully connects to database and displays a echo message verifying sucess */
//$dsn = "mysql:host=localhost;dbname=blog_tut";
//$username = "root";
//$password = "";
//
//try{
//    $db = new PDO($dsn, $username, $password);
//    echo "You have connected!";
//}catch(PDOException $e){
//    $error_message = $e->getMessage();
//    echo $error_message;
//    exit();
//}
?>
