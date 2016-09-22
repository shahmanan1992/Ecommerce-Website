<?php
session_start();
include_once ('header.php');

/* Function to validate data and check login condition*/
if(isset($_POST['submit']))
{
    include ('DB.class.php');
    $db=new DB();

    /* Validating username */
    $a=$db->validateData($_POST['fname']);
    $data=$db->loginData($a,md5($_POST['pass']));

    if($data==1)
    {
        $_SESSION['loggedIn']=$_POST['fname'];
        header('Location:index.php');
        exit();
    }
    else
    {
        $error="Login insuccessful. Try again";
    }
}
?>


<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
</head>

<body style="background-image:url('images/loginback.png')">
<?php
    if(isset($_SESSION['loginMsg']))
    {
        echo "<p id='CartMessage'>{$_SESSION['loginMsg']}</p>";
        unset($_SESSION['loginMsg']);
    }
?>


<div id="login">
    <div class="fixed_width">
        <p>Login to your account</p>
        <?php
            if(isset($error))
            {
                echo "<p class='middle' style='color:red'>$error</p>";
            }
            else
            {
                echo "<p class='middle'>Enter your username and password below!</p>";
            }
        ?>
        <div class="information">
            <form action="login.php" method="POST">
                <p><input type="text" name="fname"  placeholder="Username"/></p>
                <p><input type="password" name="pass"  placeholder="Password" /></p>
                <div id="submit">
                    <input type="submit" value="Submit" name="submit" />
                </div>
            </form>
        </div>

    </div>
</div>

</body>
