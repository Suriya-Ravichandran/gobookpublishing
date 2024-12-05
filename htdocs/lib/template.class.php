<?php 
class template {

    // Template Loading Function 
    function load_template($folder_name,$file_name){
        include $_SERVER['DOCUMENT_ROOT']."/$folder_name/$file_name.php";
    }
}

?>