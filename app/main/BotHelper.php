<?php

class BotHelper {

    private const SAVE_MYSQL_DB = "database-mysql";
    private const SAVE_FILE_TXT = "file_txt";

    private $conf;
    private $connection; // will be used for database or file connection
    private $fileNameTxt = "suggestion.txt"; //root path to save txt file

    public function __construct() {
        $this->conf = Config::getInstance();
        $saveType = $this->conf->getSuggestiveSave();

        if ($saveType == self::SAVE_MYSQL_DB) {

            $this->connection = $this->initMysql($this->conf->getDbHost(),
                    $this->conf->getDbUser(), $this->conf->getDbPass(),
                    $this->conf->getDbName());
        } elseif ($saveType == self::SAVE_FILE_TXT) {
            $file = $this->prepareFile($this->fileNameTxt);
            if ($file != null) {
                $this->connection = $file;
            } else {
                echo "<div class='error'>File could not be opened.</div>";
            }
        } else if ($saveType == self::SAVE_FILE_JSON) {
            $jsonFile = $this->prepareFile($this->fileNameJson);
            if ($jsonFile != null) {
                $this->connection = $jsonFile;
            } else {
                echo "<div class='error'>File could not be opened.</div>";
            }
        }
    }

    /**
     * Prepares the file for opening or modification.
     * @param type $fileName
     * @return type
     */
    private function prepareFile($fileName) {
        $file = null;
        if (file_exists($fileName)) {
            $file = fopen($fileName, 'a');
        } else {
            $file = fopen($fileName, 'w');
        }
        return $file;
    }

    /**
     * This initializes the connection to the database specified by the user.
     * @param String $dbhost
     * @param String $dbuser
     * @param String $dbpass
     * @param String $dbname
     * @return \PDO
     */
    private function initMysql($dbhost, $dbuser, $dbpass, $dbname) {
        $dsn = "mysql:host=" . $dbhost . ";dbname=" . $dbname;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true
        ];
        $conn = null;
        try {
            $conn = new PDO($dsn, $dbuser, $dbpass, $options);
        } catch (PDOException $e) {
            echo "<div class='error'>TalkerPHP Throws an Exception: " . $e->getMessage() . " in file: " . $e->getFile() . " on line: " . $e->getLine()
            . "</div>";
        }
        return $conn;
    }

    /**
     * This takes the string from the user and saves it in a database table:
     * 'suggestion'.
     * It creates the table if it does not already exist.
     * @param String $message
     * @return boolean
     */
    public function saveSuggestionMySQL($message) {
        if ($this->conf->getInstance()->getSuggestiveSave() == self::SAVE_MYSQL_DB) {
            try {
                if ($this->connection != null) {

                    $this->connection->exec("CREATE TABLE IF NOT EXISTS chat_suggestion (id int primary key auto_increment not null, suggest varchar(255) not null);");
                    $stmt = $this->connection->prepare("INSERT INTO chat_suggestion(suggest) VALUES(?)");
                    if (trim($message) != "") {
                        $stmt->bindValue(1, $message);
                        if ($stmt->execute()) {
                            return true;
                        } return false;
                    } else {
                        echo "<div class='error'>Suggestion cannot be null.</div>";
                    }
                } else {
                    echo "<div class='error'>TalkerPHP Throws a NullPointerError: Connection to database is not set.</div>";
                }
            } catch (Exception $e) {
                echo "<div class='error'>" . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='error'>The current save type is not 'database-mysql'</div>";
        }
    }

    /**
     * This method takes a string from the user and saves it in a file:
     * 'suggestion.txt'.
     * It creates the file in the root of your app.
     * @param String $suggestion
     */
    public function saveSuggestionTxt($suggestion) {
        if ($this->conf->getInstance()->getSuggestiveSave() == self::SAVE_FILE_TXT) {
            $result = fwrite($this->connection, $suggestion . "\r\n");
            fclose($this->connection);
            if (!$result) {
                echo "<div class='error'>Could not save file</div>";
            }
        } else {
            echo "<div class='error'>The current save type is not 'file_txt'</div>";
        }
    }

    public function generateDataSource($tableName, $questionCol, $answerCol) {
        $this->connection = $this->initMysql($this->conf->getDbHost(),
                $this->conf->getDbUser(), $this->conf->getDbPass(),
                $this->conf->getDbName());
        if ($this->connection != null) {
            $query = "SELECT " . $questionCol . "," . $answerCol . " FROM " . $tableName . ";";
            $stmt = $this->connection->prepare($query);
            try {
                $stmt->execute();
            } catch (PDOException $ex) {
                echo "<div class='error'>" . $ex->getMessage() . "</div>";
            }
            $resultSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $arr = [];
            if ($resultSet != null) {
                foreach ($resultSet as $result) {
                    $arr[$result['question']] = $result['answer'];
                }
                return $arr;
            } else {
                echo "<div class='error'>No result found!</div>";
            }
        } else {
            echo "<div class='error'>Connection to database was not established</div>";
        }
    }

}
