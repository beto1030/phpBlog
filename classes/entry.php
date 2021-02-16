<?php 

class Entry {
    private $id;
    private $date;
    private $author;
    private $title;
    private $excerpt;
    private $content;
    private $dbh;
    private $error;

    /* this constructor establishes the connection to the database */
    public function __construct() {
        //echo "</br>entry.php - new Entry() instance made!";
        $dsn = "mysql:host=localhost;dbname=blog_tut";
        $username = "root";
        $password = "";
    
        $this->dbh = new PDO($dsn, $username, $password);
        //echo "end";
    }

    public function createNew( $author, $title, $excerpt, $content ) {
        $this->setByParams( -1, date("d.m.Y h:m"), $author, $title, $excerpt, $content);
    }

    public function createNewFromPOST( $post ) {
        echo "</br>inside createNewFromPOST()";
        //print_r($post);
        $this->createNew(
            $post['entry_author'],
            $post['entry_title'],
            $post['entry_excerpt'],
            $post['entry_content']
        );
        echo "end";
    }

    public function setByParams( $id, $date, $author, $title, $excerpt, $content ) {
        if (strlen($author) == 0) {
            $this->id = -1;
        } else {
            $this->id = $id;
            $this->author = $author;
            $this->date = $date;
            $this->title = $title;
            $this->excerpt = $excerpt;
            $this->content = $content;
        }
    }

    public function setByRow( $row ) {
        //print_r($row);
        $this->setByParams (
            $row['entry_id'],
            $row['entry_date'],
            $row['entry_author'],
            $row['entry_title'],
            $row['entry_excerpt'],
            $row['entry_content']
        );
    }


    /* function dealing with sql code */
    public function SqlInsertEntry() {
        echo "</br>made it into SqlInsertEntry()";
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<br><br>70<br><br>";

        // echo "<br>";
        // echo $this->date;
        // echo "<br>";
        // echo $this->author;
        // echo "<br>";
        // echo $this->title;
        // echo "<br>";
        // echo $this->excerpt;
        // echo "<br>";
        // echo $this->content;
        
        $date = $this->date;
        echo "<br>" . $date;
        $author = $this->author;
        echo "<br>" . $author;
        $title = $this->title;
        echo "<br>" . $title;
        $excerpt = $this->excerpt;
        echo "<br>" . $excerpt;
        $content =$this->content;
        echo "<br>" . $content;

        //$query = 'INSERT INTO entries (entry_date, entry_author, entry_title, entry_excerpt, entry_content) VALUES (?, ?, ?, ?, ?);';
        //$query = 'INSERT INTO entries (entry_author, entry_title, entry_excerpt, entry_content) VALUES (?, ?, ?, ?);';

        //$stmt = $this->dbh->prepare('INSERT INTO entries (entry_author, entry_title, entry_excerpt, entry_content) VALUES (:entry_author, :entry_title, :entry_excerpt, :entry_content);');
        try{
            date_default_timezone_set('America/Los_Angeles');
            //$date = date_default_timezone_get();
            //echo "The current date is " . $date('Y');
            $query = 'INSERT INTO entries (entry_date, entry_author, entry_title, entry_excerpt, entry_content) VALUES (:entry_date, :entry_author, :entry_title, :entry_excerpt, :entry_content)';
            $stmt = $this->dbh->prepare($query);

            //Bind parameters to statment
            $stmt->bindParam(':entry_date', date("Y") , PDO::PARAM_STR);
            $stmt->bindParam(':entry_author', $_REQUEST['entry_author'], PDO::PARAM_STR);
            $stmt->bindParam(':entry_title', $_REQUEST['entry_title'], PDO::PARAM_STR);
            $stmt->bindParam(':entry_excerpt', $_REQUEST['entry_excerpt'], PDO::PARAM_STR);
            $stmt->bindParam(':entry_content', $_REQUEST['entry_content'], PDO::PARAM_STR);

            echo "<br><br>before execute()<br><br>";
            $stmt->execute();
            echo "<br><br>after execute()<br><br>";
            //$stmt->execute(["$this->date", "$this->author", "$this->title", "$this->excerpt", "$this->content"]);

            echo "<br><br>80<br><br>";//code breakes after this line the problem seems to be with $result varible
        }catch(PDOException $e) {
            die("ERROR: Could not prepare/execute query: $query. " . $e->getMessage()); 
        }

       //$result = $stmt->execute([$date, $author, $title, $excerpt, $content]);
       //$result = $stmt->execute(array("Feb 3", "alberto alvarado", "title_here", "summary_here", "content_here"));
       //
       //**** THE PROBLEM IS HERE. Find out how to properly "bind" the placeholder and the value corresponding from the user input from the form.
       //$result = $stmt->execute(array(':entry_date' =>  echo $this->date , ':entry_author' => echo $this->author, ':entry_title' => echo $this->title , ':entry_excerpt' => echo $this->excerpt, ':entry_content' => echo $this->content));
        
        echo "<br><br>88<br><br>";

        //echo $result;
        $this->error = $this->dbh->errorInfo();
        //print_r($this->error);

        $query = '  SELECT entry_id 
                    FROM entries 
                    WHERE entry_author= :entry_author 
                    ORDER BY entry_id 
                    DESC LIMIT 1;';

        $stmt = $this->dbh->prepare($query);

        echo "<br><br>128<br><br>";

        $stmt->execute(array(
            ':entry_author' => $this->author
        ));

        $this->error = $this->dbh->errorInfo();
        //print_r($this->error);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //print_r($row);

        $this->id = $row['entry_id'];

        echo "<br><br>141<br><br>";
        return $result;
    }

    public function SqlSelectEntryById( $entry_id ) {
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = 'SELECT * FROM entries WHERE entry_id= :entry_id;';

        $stmt = $this->dbh->prepare($query);
        $result = $stmt->execute(array(
            ':entry_id' => $entry_id
        ));

        $this->error = $this->dbh->errorInfo();
        //print_r($this->error);

        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->setByRow($entry);
        //print_r($entry);

        return $result;
    }

    public function SqlUpdateEntryById( $entry_id ) {
        $query = '  UPDATE entries SET 
                    entry_author = :entry_author, 
                    entry_title = :entry_title, 
                    entry_content = :entry_content, 
                    entry_excerpt = :entry_excerpt 
                    WHERE entry_id = :entry_id;';

        $stmt = $this->dbh->prepare($query);
        $result = $stmt->execute(array(
            ':entry_author' => $this->author,
            ':entry_date' => $this->date,
            ':entry_excerpt' => $this->excerpt,
            ':entry_title' => $this->title,
            ':entry_content' => $this->content
        ));
        print_r($result);

        return $result;
    }

    private function validateString() {
        
    }

    /*------- GETTERS and SETTERS of database entries ---------*/
    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of author
     */ 
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set the value of author
     *
     * @return  self
     */ 
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of excerpt
     */ 
    public function getExcerpt()
    {
        return $this->excerpt;
    }

    /**
     * Set the value of excerpt
     *
     * @return  self
     */ 
    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    /**
     * Get the value of content
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */ 
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}

?>
