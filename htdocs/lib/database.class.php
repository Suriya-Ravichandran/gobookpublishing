<?php
class database {
    function getconnection() {
        // Database connection
        $servername = "mysql.selfmade.ninja";
        $username = "gocourse";
        $password = "gocourselabs";
        $dbname = "gocourse_book";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn; // Return the connection object
    }
}
?>
