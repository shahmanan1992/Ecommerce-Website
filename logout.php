<?php
/**
 * Created by PhpStorm.
 * User: Manan
 * Date: 3/12/2016
 * Time: 12:03 AM
 */

/* To check if user is already loggedIn, if so then 'Logout' button is displayed */
session_start();
if(isset($_SESSION['loggedIn']))
{
    unset($_SESSION['loggedIn']);
    $_SESSION['logout']="logout";
    header("Location:index.php");
    exit();
}

?>