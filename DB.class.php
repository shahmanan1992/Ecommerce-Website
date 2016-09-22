<?php

/**
 * Created by PhpStorm.
 * User: Manan
 * Date: 3/11/2016
 * Time: 11:45 AM
 */
session_start();

class DB
{
    private $db;

    function __construct()
    {
        try{
            require ("./DbInfo.php");
            $this->db=new PDO("mysql:host=$host;dbname=$dbname",$username,$pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            echo "Bad database connection : ".$e->getMessage();
        }

    }

    /* Function to display total content*/
    function getContent()
    {
        $data=array();
        $result=$this->db->prepare("select * from Products");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        while($row=$result->fetch())
        {
            $data[]=$row;
        }


        if(count($data)>0)
            return $data;
    }

    /* Function to display total sale items */
    function getSaleItems()
    {
        $data=array();
        $result=$this->db->prepare("select * from Products where Saleprice>0");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        while($row=$result->fetch())
        {
            $data[]=$row;
        }


        if(count($data)>0)
            return $data;

    }

    /* Function to get total catalog items */
    function getTotalCatalog()
    {
        $data=array();
        $result=$this->db->prepare("select * from Products where Saleprice=0");
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        while($row=$result->fetch())
        {
            $data[]=$row;
        }


        if(count($data)>0)
            return $data;

    }


    /* Function to return catalog items based on page number */
    function getCatalogItems($paging,$rowsPage)
    {

        $paging=($paging-1)*$rowsPage;
        $data=array();
        $result=$this->db->prepare("select * from Products where Saleprice=0 limit :start,:end");
        $result->bindParam(":start",$paging,PDO::PARAM_INT);
        $result->bindParam(":end",$rowsPage,PDO::PARAM_INT);
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        while($row=$result->fetch())
        {
            $data[]=$row;
        }


        if(count($data)>0)
            return $data;

    }


    /* Function to check user login */
    function loginData($user,$pass)
    {

        $result=$this->db->prepare("select * from login where username=:user && password=:pass");
        $result->bindParam(":user",$user,PDO::PARAM_STR);
        $result->bindParam(":pass",$pass,PDO::PARAM_STR);
        $result->execute();
        $data=$result->fetch();
        if(count($data)==1)
            return 0;
        else
            return 1;
    }

    /* Function to Add to cart where first quantity is checked for that product and later it is reduced by 1 and added to cart,
     if not out of stock  */
    function insertInCart($product)
    {
        $output = $this->db->prepare("select * from Products where Product_Name=:product");
        $output->bindParam(":product", $product, PDO::PARAM_STR);
        $output->execute();
        $row = $output->fetch(PDO::FETCH_ASSOC);
        if ($row['Quantity'] > 0) {
            $row['Quantity'] = $row['Quantity'] - 1;
            $result = $this->db->prepare("update Products set Quantity=:quantity where Product_Name=:product");
            $result->bindParam(":quantity", $row['Quantity'], PDO::PARAM_STR);
            $result->bindParam(":product", $product, PDO::PARAM_STR);
            $result->execute();

            $stmt = $this->db->prepare("insert into Cart(username,item) values(:username,:item)");
            $stmt->bindParam(":username", $_SESSION['loggedIn'], PDO::PARAM_STR);
            $stmt->bindParam(":item", $product, PDO::PARAM_STR);
            $stmt->execute();
            return 1;
        }
        else
        {
            return 0;
        }
    }

    /* Function to get all information for particular product requested by user */
    function getContentInfo($product)
    {
        $stmt=$this->db->prepare("select * from Products where Product_Name=:product");
        $stmt->bindParam(":product",$product,PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $data=$stmt->fetch();
        return $data;
    }


    /* Function to display cart contents for particular user */
    function displayCart()
    {
        $data=array();
        $stmt=$this->db->prepare("select * from Cart where username=:username");
        $stmt->bindParam(":username",$_SESSION['loggedIn'],PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        while($row=$stmt->fetch())
        {
            $data[]=$row;
        }

        $contentInfo=array();
        foreach($data as $info)
        {
            $contentInfo[]=$this->getContentInfo($info['item']);
        }
        if(count($contentInfo)>0)
            return $contentInfo;
        else
            return 0;
    }


    /* Function to empty Cart contents. First contents from cart are selected and each of these item's quantity is updated
    and later all the cart items are deleted */
    function emptyCart()
    {
        $data=array();
        $result=$this->db->prepare("select * from Cart where username=:username");
        $result->bindParam(":username",$_SESSION['loggedIn'],PDO::PARAM_STR);
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        while($row=$result->fetch())
        {
           $this->deleteItem($row);
        }

        $stmt=$this->db->prepare("delete from Cart where username=:username");
        $stmt->bindParam(":username",$_SESSION['loggedIn'],PDO::PARAM_STR);
        $stmt->execute();
        return "Empty Cart";
    }


    /* Function to delete each item from Cart as supplied by user  */
    function deleteItem($res)
    {
        $output = $this->db->prepare("select * from Products where Product_Name=:product");
        $output->bindParam(":product", $res['item'], PDO::PARAM_STR);
        $output->execute();
        $row = $output->fetch(PDO::FETCH_ASSOC);
        $row['Quantity']=$row['Quantity']+1;


        $result=$this->db->prepare("update Products set Quantity=:quantity where Product_Name=:item");
        $result->bindParam(":quantity", $row['Quantity'], PDO::PARAM_STR);
        $result->bindParam(":item", $row['Product_Name'], PDO::PARAM_STR);
        $result->execute();
    }

    /* Function to remove item from cart when user clicks on 'Remove Item' in Cart */
    function removeItem($item)
    {
        $stmt=$this->db->prepare("delete from Cart where username=:username and item=:item limit 1");
        $stmt->bindParam(":username",$_SESSION['loggedIn'],PDO::PARAM_STR);
        $stmt->bindParam(":item",$item,PDO::PARAM_STR);
        $stmt->execute();

        $output = $this->db->prepare("select * from Products where Product_Name=:product");
        $output->bindParam(":product", $item, PDO::PARAM_STR);
        $output->execute();
        $row = $output->fetch(PDO::FETCH_ASSOC);
        $row['Quantity']=$row['Quantity']+1;

        $result=$this->db->prepare("update Products set Quantity=:quantity where Product_Name=:item");
        $result->bindParam(":quantity", $row['Quantity'], PDO::PARAM_STR);
        $result->bindParam(":item", $item, PDO::PARAM_STR);
        $result->execute();

        $data=$this->displayCart();
        return $data;
    }

   /* Function to validate the input supplied by user during login */
    function validateData($data)
    {
        return htmlspecialchars(htmlentities(strip_tags(stripslashes(trim($data)))));
    }

   /* Function to add items into product by authorized user */
    function insertAdminItem($product_name,$description,$price,$quantity,$saleprice)
    {
        $stmt=$this->db->prepare("insert into Products(Product_Name,Description,Price,Quantity,Saleprice) values(:product_name,:description,:price,:quantity,:saleprice)");
        $stmt->bindParam(":product_name",$product_name,PDO::PARAM_STR);
        $stmt->bindParam(":description",$description,PDO::PARAM_STR);
        $stmt->bindParam(":price",$price,PDO::PARAM_STR);
        $stmt->bindParam(":quantity",$quantity,PDO::PARAM_STR);
        $stmt->bindParam(":saleprice",$saleprice,PDO::PARAM_STR);
        $stmt->execute();
        if($stmt->rowCount()>0)
            return "Item successfully added";

    }

    /* Function to update fields as supplied by authorized admin */
    function updateFields($fields)
    {
        $query="update Products set ";
        foreach($fields as $k=>$v)
        {
            switch($k)
            {
                case 'product_name':$query.="Product_Name='$v',";
                                    break;
                case 'description':$query.="Description='$v',";
                                    break;
                case 'price':$query.="Price=$v,";
                             break;
                case 'quantity':$query.="Quantity=$v,";
                                break;
                case 'saleprice':$query.="Saleprice=$v,";
                                break;
                case 'old_name':$oldname=$v;
                                break;
            }
        }

        $queryString=trim($query,",");
        $queryString.=" where Product_Name=:item";
        if($stmt=$this->db->prepare($queryString))
        {
            $stmt->bindParam(":item",$oldname,PDO::PARAM_STR);
            $stmt->execute();
            $numRows=$stmt->rowCount();
        }
        if($numRows>0)
        return 1;
    }



    /* Function to check if loggedIn user is admin for giving permission to Add/Edit all Products  */
    function checkAdmin()
    {
        $stmt=$this->db->prepare("select * from login where username=:username and admin=1");
        $stmt->bindParam(":username",$_SESSION['loggedIn'],PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount();
    }
}