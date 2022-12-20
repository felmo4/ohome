<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    {  
        //for navigation.................................................................................................
        //Search bar is used
        if(isset($_GET["Search"])) {
            $Display = "SELECT * FROM tblproducts WHERE (productcode LIKE '%".$_GET["Search"]."%' OR barcode LIKE '%".$_GET["Search"]."%' OR description LIKE '%".$_GET["Search"]."%' OR category LIKE '%".$_GET["Search"]."%') AND quantity != 0";
            $_SESSION["Search"] = $_GET["Search"];}
        elseif(isset($_SESSION["Search"])) {  
            $Display = "SELECT * FROM tblproducts WHERE (productcode LIKE '%".$_SESSION["Search"]."%' OR barcode LIKE '%".$_SESSION["Search"]."%' OR description LIKE '%".$_SESSION["Search"]."%' OR category LIKE '%".$_SESSION["Search"]."%') AND quantity != 0";}  
        //Displays all products
        else {
            $Display = "SELECT * FROM tblproducts WHERE quantity != 0";}

        //for functions.................................................................................................
        //For activity log
        $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");
        //Applied discounts
        $Applied = "SELECT tblpromos.*,tblproducts.description as pdescription,tblproducts.price,tblproducts.quantity FROM tblpromos INNER JOIN tblproducts 
        WHERE tblpromos.product = tblproducts.productcode AND tblproducts.quantity !=0
            AND (tblpromos.periodstart<=NOW() AND (tblpromos.periodend>=NOW() OR tblpromos.periodend=0))
            AND (tblpromos.uselimit=0 OR (tblpromos.uselimit-tblpromos.used)>0)";
        $pdoApplied = $pdoConnect->prepare($Applied); 
        $pdoApplied->execute();
        $discounts = $pdoApplied->fetchall(); 

        //Add item to cart
        if(isset($_POST["Add"]))  
        {            
            //Checks if item is discounted
            $item_array_id = array_column($discounts, "product");  
            if(in_array($_POST["productcode"], $item_array_id))  
            {  
                foreach($discounts as $keys => $discounted)  
                {  
                    if($discounted["product"] == $_POST["productcode"])  
                    {  
                        $discount = number_format((float) ($discounted["rewardtype"]=='Php' ? $discounted["reward"] : $discounted["price"]*($discounted["reward"]/100)), 2, '.', '');
                        $price = number_format((float) $discounted["price"]-$discount, 2, '.', '');
                        $item_array = array(  
                            'ProductCode'       =>     $_POST["productcode"],  
                            'Description'       =>     $_POST["description"],   
                            'Price'             =>     $price,
                            'Quantity'          =>     $_POST["quantity"],
                            'ItemTotal'         =>     $price*$_POST["quantity"],
                            'Stock'             =>     $discounted['uselimit']==0 ? $_POST["stock"] : ($discounted['uselimit']-$discounted['used']<$_POST["stock"] ? $discounted['uselimit']-$discounted['used'] : $_POST["stock"]),
                            'Discount'          =>     $discount,
                            'ItemDiscount'      =>     $discount*$_POST["quantity"],
                            'D.Description'     =>     $discounted['description'],
                            'D.Reward'          =>     $discounted['reward']."".$discounted['rewardtype']." OFF",
                            'D.ID'              =>     $discounted['promoID']
                        ); 
                    }  
                }   
            } 
            else
            {
                $item_array = array(  
                    'ProductCode'       =>     $_POST["productcode"],  
                    'Description'       =>     $_POST["description"],   
                    'Price'             =>     $_POST["price"],
                    'Quantity'          =>     $_POST["quantity"],
                    'ItemTotal'         =>     number_format((float)$_POST["price"]*$_POST["quantity"], 2, '.', ''),
                    'Stock'             =>     $_POST["stock"],
                    'Discount'          =>     0,
                    'ItemDiscount'      =>     0
                ); 
            }                 
        
            if(isset($_SESSION["Transaction"]))  
            {  
                $item_array_id = array_column($_SESSION["Transaction"], "ProductCode");  
                if(!in_array($item_array["ProductCode"], $item_array_id))  
                {   
                    $_SESSION["Count"] += 1;
                    $_SESSION["Transaction"][$_SESSION["Count"]] = $item_array;
                    $_SESSION["Transaction"][$_SESSION["Count"]]["ID"] = $_SESSION["Count"]; 
                    $message = "Item added"; 
                }  
                else  
                {  
                    foreach($_SESSION["Transaction"] as $keys => $item)  
                    {  
                        if($item["ProductCode"] == $item_array["ProductCode"] && $item["Quantity"]+$item_array["Quantity"] <= $item["Stock"])  
                        {  
                            $_SESSION["Transaction"][$keys]["Quantity"] += $item_array["Quantity"];
                            $_SESSION["Transaction"][$keys]['ItemTotal'] += $item_array["Price"]*$item_array["Quantity"];
                            $_SESSION["Transaction"][$keys]['ItemDiscount'] += $item_array["Discount"]*$item_array["Quantity"];  
                            $message = "Item added";
                            break;
                        }
                        else { $message = "Quantity exceeded";}  
                    }  
                }  
            }
            else  
            {  
                $_SESSION["Count"] = 0;
                $_SESSION["Transaction"][0] = $item_array; 
                $_SESSION["Transaction"][0]["ID"] = 0;  
                $message = "Item added"; 
            }          
        }      
    }
    else  
    {  
        header("location:../index.php");  
    }  

 ?> 


<!DOCTYPE html>  
<html>  
    <head>  
        <title>Item Search</title>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
        <link rel="stylesheet" href="../styles.css"> 
        <link rel="icon" href="../include/logo.png">
    </head>  
    <body>            
        
	<header>
		<div class="right_area">
			<h3>Ohome</h3>
		</div>
	</header>
            
        <div class="main"> 
            
            <!--- main activity --->
            <div class="activity"> 
                
                <!--- top bar --->   
                <div class="tabs">    
                    <form method="get"> 
                        <input type="search" name="Search" placeholder="Search" value="<?php echo isset($_SESSION['Search']) ? $_SESSION['Search'] : ''; ?>"> 
                    </form>
                </div>     

                <!--- product display -->
                <table class="table">
                <tr>
                    <th>#</th><th>Product Code</th><th>Barcode</th><th>Description</th><th>Price</th><th>Category</th><th>Quantity</th><th>Options</th>
                </tr>            
                       
                <?php

                    if(isset($message))  
                    {  
                        echo    '<tr class="crud">
                                <td></td>
                                    <td colspan=7>
                                    <label>'.$message.'</label>
                                    </td>
                                </tr>';
                    }
              
                    $pdoDisplay = $pdoConnect->prepare($Display);  
                    $pdoDisplay->execute();
                    $num=0;

                    foreach ($pdoDisplay as $product) 
                    { 
                        $badge=""; 
                        $max=0;
                        $item_array_id = array_column($discounts, "product");  
                        if(in_array($product['productcode'], $item_array_id))  {
                            $badge="Promo ";  
                            foreach($discounts as $keys => $discounted)  {  
                                if($discounted["product"] == $product['productcode'])  {  
                                    $max = $discounted['uselimit']==0 ? $product["quantity"] : ($discounted['uselimit']-$discounted['used']<$product["quantity"] ? $discounted['uselimit']-$discounted['used'] : $product["quantity"]); }}}  
                        else {$max = $product['quantity'];}                             
                        $num++;
                        echo   '<tr>
                                <td>'.$num.'</td>
                                <td>'.$badge.''.$product['productcode'].'</td>
                                <td>'.$product['barcode'].'</td>
                                <td>'.$product['description'].'</td>
                                <td>'.$product['price'].'</td>
                                <td>'.$product['category'].'</td>
                                <td>'.$product['quantity'].'</td>
                                <td>
                                    <form method="post"> 
                                    <input type="hidden" name="productcode" value="'.$product["productcode"].'">
                                    <input type="hidden" name="description" value="'.$product["description"].'">
                                    <input type="hidden" name="price" value="'.$product["price"].'">
                                    <input type="hidden" name="stock" value="'.$product["quantity"].'">
                                    <input type="number" name="quantity" min=1 max='.$max.' value=1 placeholder="Quantity">
                                    <button name="Add">Add</button>                                                                
                                    </form>
                                </td>
                                </tr>';                    
                    }
                ?>
                
                </table>

            </div>


            <!--- right navigation bar --->
            <div class="navigation"> 
                <?php  
                    include '../include/cashiernav.php';
                ?>
            </div>
        
        <!--- footer --->
        <div class="footer"> 
            <?php  
                
            ?>
        </div>   

    </body>  
</html> 

            