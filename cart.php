<?php
/**
 * Created by PhpStorm.
 * User: Manan
 * Date: 3/12/2016
 * Time: 9:05 AM
 */
session_start();
include_once ('header.php');
/* Cart conditions applied when admin is loggedIn. Cart is only visible to loggedIn users */
if(isset($_SESSION['loggedIn'])) {
    include_once('LIB_project1.php');
    if (!empty($_POST['emptyCart']))
    {
        $output = $data->emptyCart();
    }
    else if (!empty($_POST['removeCart']))
    {
        $output = $data->removeItem($_POST['itemName']);
    }
    else if (empty($_POST['product']))
    {
        $output = $data->displayCart();
    }
    else
    {
        $output = $data->insertInCart($_POST['product']);
        if($output==0)
        {
            $_SESSION['itemInCart']="Product out of stock";
        }
        else if($output==1)
            $_SESSION['itemInCart']="Item added to cart";
        else
            unset($_SESSION['itemInCart']);
        header('Location:index.php');
        exit();
    }

}
else
{
    /* Throwing error message if user tries to display cart without logging in */
    $_SESSION['loginMsg']="Need to login before adding contents to cart";
    header('Location:login.php');
    exit();
}
?>

<html>
<head>
    <title>Cart</title>
    <link rel="stylesheet" type="text/css" href="css/cart.css">
</head>
<body>
<?php
    if($output==0)
    {
        echo "<p class='cart-head'>Cart is Empty</p>";
    }
    else
    {
        echo "<p class='cart-head'>Review Your order :</p>";

?>
        <div id='content-bar'>
        <?php
        /* Displaying cart contents and also calculated the total cost  */
        $count=0;
        foreach ($output as $eachItem)
        {
            if ($eachItem['Saleprice'] > 0)
            {
                echo "<span class='cart-item'>{$eachItem['Product_Name']}</span><br />
                              <span class='contentInfo'>{$eachItem['Description']}</span><br />
                              <span class='contentInfo'>Price : \${$eachItem['Saleprice']}</span><br />
                              <span class='contentInfo'>Quantity : 1</span><br />
                              <span class='contentInfo'>
                              <form method='post' action='cart.php'>
                                <input type='hidden' name='itemName' value='{$eachItem['Product_Name']}' />
                                <input type='submit' name='removeCart' value='Remove' class='removeItem' />
                               </form>
                               </span>
                                <hr class='hr-line'/><br />";
                $count+=$eachItem['Saleprice'];
            }
            else
            {
                echo "<span class='cart-item'>{$eachItem['Product_Name']}</span><br />
                              <span class='contentInfo'>{$eachItem['Description']}</span><br />
                              <span class='contentInfo'>Price : \${$eachItem['Price']}</span><br />
                              <span class='contentInfo'>Quantity : 1</span><br />
                              <span class='contentInfo'>
                              <form method='post' action='cart.php'>
                                <input type='hidden' name='itemName' value='{$eachItem['Product_Name']}' />
                                <input type='submit' name='removeCart' value='Remove' class='removeItem' />
                               </form>
                               </span>
                                <hr class='hr-line'/><br />";
                $count+=$eachItem['Price'];
            }

        }

        ?>
        </div>
        <?php
        echo "<div id='total'>Total cost = \${$count}<br />
        <p><form method='post' action='cart.php'><input type='submit' name='emptyCart' value='Empty Cart' class='emptyCart'></form></p></div>";
    }
?>
</body>
</html>