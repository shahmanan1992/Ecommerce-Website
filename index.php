<?php
/**
 * Created by PhpStorm.
 * User: Manan
 * Date: 3/11/2016
 * Time: 1:19 PM
 */
session_start();
include ("header.php");
include ("LIB_project1.php");
?>


<html>
<head>
    <title>index</title>
</head>
<body style="background-image:url('images/background.jpg')">
<?php
/* Checking the condition for current page index */
    if(isset($_GET['page']))
    {
        $paging=$_GET['page'];
        $rowsPage=5;
        $total=count($data->getTotalCatalog());
        $total_pages=ceil($total/$rowsPage);
        if($paging>$total_pages)
        {
            $paging=$total_pages;
        }
    }

/* Initial page condition where page is 1*/
    if(!isset($paging))
    {
        $paging=1;
        $rowsPage=5;
        $total=count($data->getTotalCatalog());
        $total_pages=ceil($total/$rowsPage);
    }

/* User logIn/logout condition check to display login/logout accordingly */
    if(!empty($_SESSION['logout']))
    {
        echo "<p id='Message'>You have logged out successfully</p>";
        unset($_SESSION['logout']);
    }
    else if(!empty($_SESSION['itemInCart']))
    {
        echo "<p id='Message'>{$_SESSION['itemInCart']}</p>";
        unset($_SESSION['itemInCart']);
    }
    else if(!empty($_SESSION['loggedIn']))
    {
        echo "<p id='Message'>Welcome {$_SESSION['loggedIn']}</p>";
    }
?>


<div class='content'>
    <div class='Sales-Catalog'>
        On Sale
        <ul>
        <?php

        /* To calculate and display the total sale items */

        foreach($result as $row)
        {
            echo"<div id='sale_items'>

                <li><span class='img_heading'><label for='{$row['Product_Name']}'>{$row['Product_Name']}</label><img src='images/{$row['Image_Name']}' name='{$row['Product_Name']}'/></span>
                <div class='item-bar'>
                        <div class='description'>{$row['Description']}. Only at <strike>\${$row['Price']}</strike>  \${$row['Saleprice']}</div>
                        <div class='item_left'>Only {$row['Quantity']} left!</div>
                        <div class='cartButton'>
                            <form method='post' action='cart.php'>
                                <input type='hidden' value='{$row['Product_Name']}' name='product' />
                                <input type='submit' value='Add to cart' name='submit' class='submit'/>
                            </form>
                        </div>

                    </div>
                </li>

    </div>";
        }


        ?>
        </ul>
        <hr width="100%"/>

        <div id="gap">
        Browse our Catalog Section
            <ul>
        <?php

        /* To calculate and display catalog items */
        $catalog=$data->getCatalogItems($paging,$rowsPage);
        foreach($catalog as $row)
        {
            echo"<div id='sale_items'>

                <li><span class='img_heading'><label for='{$row['Product_Name']}'>{$row['Product_Name']}</label><img src='images/{$row['Image_Name']}' name='{$row['Product_Name']}'/></span>
                <div class='item-bar'>
                        <div class='description'>{$row['Description']}. Available at \${$row['Price']}</div>
                        <div class='item_left'>Only {$row['Quantity']} left!</div>
                        <div class='cartButton'>
                            <form method='post' action='cart.php'>
                                <input type='hidden' value='{$row['Product_Name']}' name='product' />
                                <input type='submit' value='Add to cart' name='submit' class='submit'/>
                            </form>
                        </div>
                    </div>
                </li>

    </div>";
        }

        ?>
            </ul>
    </div>
<!--        class="Sales-Catalog-->
<!--    <div  Paging">-->
<!--        <ul class="pagination">-->
<!--            <li><a href="#">«</a></li>-->
<!--            <li><a href="#">1</a></li>-->
<!--            <li><a class="active" href="#">2</a></li>-->
<!--            <li><a href="#">3</a></li>-->
<!--            <li><a href="#">4</a></li>-->
<!--            <li><a href="#">5</a></li>-->
<!--            <li><a href="#">»</a></li>-->
<!--        </ul>-->
<!---->
<!--    </div>-->
        <hr width="100%">
        <div id="navigation">
            <ul>
                <?php

                /* Determining the paging accordingly based on user click */

                    if($total_pages>1)
                    {
                        $previous=$paging-1;
                        $next=$paging+1;

                        if($previous<=1)
                            $previous=1;

                        if($next>=$total_pages)
                            $next=$total_pages;
                        echo "<li><a href='index.php?page=$previous' name='current' ><<</a></li>";
                        for($i=1;$i<=$total_pages;$i++)
                        {
                            echo "<li><a href='index.php?page=$i' name='current' >$i</a></li>";
                        }
                        echo "<li><a href='index.php?page=$next' name='current'>>></a>";
                    }
                ?>
            </ul>
        </div>

</div>
</div>
</body>
</html>

