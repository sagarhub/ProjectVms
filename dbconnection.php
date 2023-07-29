<?php
function get_dbconnection()
{
    $server = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'project';
    //create connection
    return new mysqli($server,$user,$pass,$db);
    
}
?>