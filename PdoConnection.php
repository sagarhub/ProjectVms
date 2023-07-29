<?php
function get_PdoConnection()
{

    //create connection
    return new PDO('mysql:host=localhost;dbname=project','root','');
    
}
?>