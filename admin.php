<?php
/**
 * Created by PhpStorm.
 * User: Manan
 * Date: 3/11/2016
 * Time: 1:02 PM
 */
session_start();
include_once ('header.php');
include_once ('LIB_project1.php');

/* Conditions to check for authorized admin permission.  */
if(empty($_SESSION['loggedIn']))
{
    $_SESSION['loginMsg']="Need to login as an admin";
    header('Location:login.php');
    exit();
}
else
{
    if($data->checkAdmin()==0)
    {
        $_SESSION['loginMsg']="You are not an authorized admin";
        header('Location:login.php');
        exit();
    }
}


?>

<?php
$result=$data->getContent();
if(isset($_POST['submit']))
{
    /* Validating all input data provided by ADMIN  while adding new item to Products*/
    $product_name=$data->validateData($_POST['product_name']);
    $description=$data->validateData($_POST['description']);
    $price=$data->validateData($_POST['price']);
    $quantity=$data->validateData($_POST['quantity']);
    $saleprice=$data->validateData($_POST['saleprice']);
    if(!is_numeric($product_name) && !is_numeric($description) && is_numeric($price) && $price>0 && $saleprice>=0 && is_numeric( $quantity ) && floor( $quantity ) == $quantity && is_numeric($saleprice))
    {
        if($saleprice>0)
        {
            $output=count($data->getSaleItems());
            if($output>=5)
            {
                $output='';
            }
            else
            {
                $output=$data->insertAdminItem($product_name,$description,$price,$quantity,$saleprice);
            }
        }
        else
        {
            $output=$data->insertAdminItem($product_name,$description,$price,$quantity,$saleprice);
        }
    }
    else
    {
        $output='';
    }
}
else if(isset($_POST['editItem']))
{
    $_SESSION['edit']="On";
    $_SESSION['oldname']=$_POST['selectBar'];
    $res=$data->getContentInfo($_POST['selectBar']);
}
else if(isset($_POST['update']))
{
    /* Validating all input data provided by ADMIN  while editing existing item to Products*/
    $product_name=$data->validateData($_POST['product_name']);
    $description=$data->validateData($_POST['description']);
    $price=$data->validateData($_POST['price']);
    $quantity=$data->validateData($_POST['quantity']);
    $saleprice=$data->validateData($_POST['saleprice']);

    if(!is_numeric($product_name) && !is_numeric($description) && is_numeric($price) && $price>0 && $saleprice>=0 && is_numeric( $quantity ) && (floor($quantity)== $quantity) && is_numeric($saleprice)) {
        $update = $data->updateFields(Array("old_name" => $_SESSION['oldname'], "product_name" => $product_name, "description" => $description, "price" => $price, "quantity" => $quantity, "saleprice" => $saleprice));
    }
    else
    {
        $cannotUpdate="Cannot update Item";
    }
}
//else
//{
//
//}


?>

<html>
<head>
    <title>Admin</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css">
</head>
<body>
<div class="top">
    <div class="itemList">
        <div class="content">
            Select below any item and click 'Edit'<br/>
            <form acton="admin.php" method="post">
                <input type="submit" value="Edit" name="editItem" style=" width:60px;
    height:30px;
    padding:5px;
    background-color: dodgerblue;
    color:white;
    border-radius:5px;
    border:0;" />
                <select name="selectBar" class="selectBar">
                    <?php
                    foreach($result as $eachrow)
                    {
                        echo "<option>{$eachrow['Product_Name']}</option>";
                    }

                    ?>
                </select>
            </form>

            <?php if(isset($_SESSION['edit']))
            {
                ?>
                <div class="editList">
                    <span class="addItem">Edit Item</span>
                    <?php
                    if(!empty($update))
                    {
                        echo "<span class='success'>Item Updated Successfully</span>";
                        unset($_SESSION['edit']);
                        unset($_SESSION['oldname']);

                    }
                    if(!empty($cannotUpdate))
                    {
                        echo "<span class='success' style='color:red'>$cannotUpdate</span>";
                        unset($_SESSION['edit']);
                        unset($_SESSION['oldname']);

                    }


                    ?>
                    <form action="admin.php" method="post" class="addForm" enctype="multipart/form-data">
                        <input type="text" name="product_name" placeholder="Name" value="<?= $res['Product_Name'] ?>" class="placeHolder" /><br />
                        <textarea style="margin-left:-2px;" name="description" placeholder="Description" class="placeHolder" id="edit"><?= $res['Description'] ?></textarea><br />
                        <input type="text" name="price" placeholder="Price" value="<?= $res['Price'] ?>" class="placeHolder" /><br />
                        <input type="text" name="quantity" placeholder="Quantity" value="<?= $res['Quantity'] ?>" class="placeHolder" /><br />
                        <input type="text" name="saleprice" placeholder="SalePrice" value="<?= $res['Saleprice'] ?>" class="placeHolder" /><br />
                        <input type="file" name="image" class="placeHolder" placeholder="Upload Image" id="upload" /><br/>
                        <input type="reset" name="reset" value="Reset Form" class="placeHolder" id="submit" />
                        <input type="submit" name="update" value="Submit Form" class="placeHolder" id="submit"/>
                    </form>
                </div>
            <?php } ?>
        </div>

    </div>
    <div class="addList">

        <!--    <span class='success'>Output</span>-->
        <span class="addItem">Add Item</span>
        <?php
        if(isset($_POST['submit'])) {
            if (!empty($output)) {
                echo "<span class='success'>$output</span>";
            } else {
                echo "<span class='success' style='color:red;'>Cannot add item</span>";
            }
        }
        ?>
        <form action="admin.php" method="post" class="addForm" enctype="multipart/form-data">
            <input type="text" name="product_name" placeholder="Name" class="placeHolder" /><br />
            <textarea name="description" placeholder="Description" class="placeHolder"></textarea><br />
            <input type="text" name="price" placeholder="Price" class="placeHolder" /><br />
            <input type="text" name="quantity" placeholder="Quantity" class="placeHolder" /><br />
            <input type="text" name="saleprice" placeholder="SalePrice" class="placeHolder" /><br />
            <input type="file" name="image" class="placeHolder" placeholder="Upload Image" id="upload"/><br/>
            <input type="reset" name="reset" value="Reset Form" class="placeHolder" id="submit" />
            <input type="submit" name="submit" value="Submit Form" class="placeHolder" id="submit"/>
        </form>
    </div>
</div>
</body>
</html>

