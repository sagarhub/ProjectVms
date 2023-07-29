
<?php 

function getParam($name, $defaultValue = null)
{
    $value = $_GET[$name] ?? null;
    if ($value === "") {
        $value = null;
    }
    return $value ?? $defaultValue;

}


    ?>