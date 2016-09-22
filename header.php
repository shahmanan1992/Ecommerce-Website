<?php
/**
 * Created by PhpStorm.
 * User: Manan
 * Date: 3/10/2016
 * Time: 4:49 PM
 */
session_start();
?>

<html>
<head>
	<title>SupplyAll.com</title>
	<link rel="stylesheet" type="text/css" href="css/design.css">
    <link href='https://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	</head>
<body>
<div class="container">
	<div class="heading">
        <div id="logo">
            <img src="images/Drawing.png" />
            <a href="index.php"><span class="supply">Supply</span><span id="all">All</span><span class="supply">.com</span></a>
        </div>
        <div id="menu">
<!--   Header information included in all files  -->
            <ul style="list-style-type:none">
                <li><a class="text" href="index.php">Home</a></li>
                <li><a class="text" href="cart.php">Cart</a></li>
                <li><a class="text" href="admin.php">Admin</a></li>
                <?php
                /* Checking if user is loggedIn or logged Out */
                    if(empty($_SESSION['loggedIn']))
                    {
                        echo "<li><a class='text' href='login.php'>Login</a></li>";
                    }
                    else
                    {
                        echo "<li><a class='text' href='logout.php'>Logout</a></li>";
                    }

                ?>

            </ul>
        </div>
    </div>
</div>
</body>
</html>
