<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    {  
        //for navigation.................................................................................................

        //All products tab - default tab
        if(isset($_GET["ProductsTab"]) || isset($_GET["BundlesTab"]) || isset($_GET["NewBundleTab"]))
        {
            unset($_SESSION["BundlesTab"]);
            unset($_SESSION["NewBundleTab"]);
            if (isset($_SESSION["Search"])) {unset($_SESSION["Search"]);}
            //Bundles tab
            if(isset($_GET["BundlesTab"])) {
                $_SESSION["BundlesTab"]=TRUE;}
            //New bundle tab
            elseif(isset($_GET["NewBundleTab"])) {
                $_SESSION["NewBundleTab"]=TRUE;}
        }
        
        //Search bar is used, else display all
        if(isset($_SESSION["BundlesTab"]) || isset($_SESSION["NewBundleTab"]))
        {
            $Display = "SELECT * FROM tblproducts WHERE category"; 
            $Display .= isset($_SESSION["BundlesTab"])? " = 'Bundle'" : " != 'Bundle'"; 
            if(isset($_GET["Search"]) || isset($_SESSION["Search"])) {
                $Display .= " AND (productcode LIKE '%".$_GET["Search"]."%' OR barcode LIKE '%".$_GET["Search"]."%' OR description LIKE '%".$_GET["Search"]."%' OR category LIKE '%".$_GET["Search"]."%')";
                $_SESSION["Search"] = $_GET["Search"];}
            $option = isset($_SESSION["BundlesTab"])? '<br><button name="BundleDetails">v</button> <button name="Increase">+</button> <button name="Decrease">-</button>' : '<button name="BundleAdd">Add</button>' ;
        }
        else
        {
            $Display = "SELECT tblproducts.*,tblproductbundles.bundlecode FROM tblproducts LEFT JOIN tblproductbundles ON tblproducts.productcode=tblproductbundles.productcode ";
            if(isset($_GET["Search"]) || isset($_SESSION["Search"])) {
                $Display .= " WHERE tblproducts.productcode LIKE '%".$_GET["Search"]."%' OR tblproducts.barcode LIKE '%".$_GET["Search"]."%' OR tblproducts.description LIKE '%".$_GET["Search"]."%' OR tblproducts.category LIKE '%".$_GET["Search"]."%'";
                $_SESSION["Search"] = $_GET["Search"];}
            $Display .= " ORDER BY tblproducts.productcode";
        }

        //Cancel
        if(isset($_POST["Cancel"])) {
            unset($_GET["NewProduct"]);
            unset($_POST["EditProduct"]);
            unset($_POST["DeleteProduct"]);
        }
        
        //for functions.................................................................................................
        //For activity log and category dropdown
        $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");
        $pdoCategory = $pdoConnect->prepare("SELECT description FROM tblcategory"); 

        //Add/Edit product/bundles 
        if(isset($_POST["AddProduct"]) || isset($_POST["ProductEdit"]) || isset($_POST["BundleSave"])) 
        {  
            $pdoExisting = $pdoConnect->prepare("SELECT productcode, barcode FROM tblproducts WHERE productcode=:ProductCode OR barcode=:Barcode");
            $pdoExisting->execute( array('ProductCode' => isset($_SESSION["NewBundleTab"]) ? $_POST["BundleCode"] : $_POST["ProductCode"], 'Barcode' => $_POST["Barcode"] ));
            
            //Existing
            if(((isset($_POST["AddProduct"]) OR isset($_POST["BundleSave"])) AND $pdoExisting->rowCount() > 0) OR (isset($_POST["ProductEdit"]) AND $pdoExisting->rowCount() > 1))  
            {
                !isset($_SESSION["NewBundleTab"]) ? $message = 'Product code or barcode is already in use' : $cartmessage = 'Product code or barcode is already in use';
            }
            else
            {
                try
                {   
                    //Add product/bundles
                    if(isset($_POST["AddProduct"]) || isset($_POST["BundleSave"])) {
                        // $bundlediscount = isset($_SESSION["NewBundleTab"]) && isset($_POST["Discount"]) ? ($_POST["Price"]-$_POST["Discount"]) : "";
                        $query = "INSERT INTO tblproducts (productcode, barcode, description, details, price, category) VALUES (:ProductCode, :Barcode, :Description, :Details, :Price, :Category)";
                        $details = isset($_SESSION["NewBundleTab"]) ? "Product bundle added: ".$_POST["BundleCode"] : "Product added: ".$_POST["ProductCode"];
                        !isset($_SESSION["NewBundleTab"]) ? $message = 'Product was added successfully' : $cartmessage =  'Product bundle was added successfully' ;}
                        
                    //Edit product
                    elseif (isset($_POST["ProductEdit"])) {
                        $query = "UPDATE tblproducts SET barcode=:Barcode, description=:Description, details=:Details, price=:Price, category=:Category WHERE productcode=:ProductCode";
                        $details = "Product edited: ". $_POST["ProductCode"];
                        $message = 'Product was edited successfully';
                        unset($_POST["EditProduct"]);}             
                    
                    $pdoCRUD = $pdoConnect->prepare($query);  
                    $pdoCRUD->execute(
                        array(
                            'ProductCode'   =>     isset($_SESSION["NewBundleTab"]) ? $_POST["BundleCode"] : $_POST["ProductCode"],
                            'Barcode'       =>     $_POST["Barcode"],
                            'Description'   =>     $_POST["Description"],
                            'Details'       =>     $_POST["Details"],
                            'Price'         =>     $_POST["Price"],
                            'Category'      =>     $_POST["Category"]
                        )
                    );                 

                    // record individual items using tblproductbundles
                    if(isset($_POST["BundleSave"]))
                    {
                        $pdoBundle = $pdoConnect->prepare("INSERT INTO tblproductbundles (bundlecode, productcode, quantity) VALUES (:BundleCode, :ProductCode, 1)");  
                        $pdoExisting = $pdoConnect->prepare("SELECT bundlecode FROM tblproductbundles WHERE bundlecode=:BundleCode AND productcode=:ProductCode"); 
                        $pdoQuantity = $pdoConnect->prepare("UPDATE tblproductbundles SET quantity=quantity+1 WHERE bundlecode=:BundleCode AND productcode=:ProductCode"); 
                        foreach($_SESSION["Bundle"] as $item)
                        {
                            $pdoExisting->execute(
                                array(
                                    'BundleCode'    =>     $_POST["BundleCode"],
                                    'ProductCode'   =>     $item["ProductCode"]
                                )
                            );
                            if($pdoExisting->rowCount() > 0)
                            {
                                $pdoQuantity->execute(
                                    array(
                                        'BundleCode'    =>     $_POST["BundleCode"],
                                        'ProductCode'   =>     $item["ProductCode"]
                                    )
                                );
                            }
                            else
                            {
                                $pdoBundle->execute(
                                    array(
                                        'BundleCode'    =>     $_POST["BundleCode"],
                                        'ProductCode'   =>     $item["ProductCode"]
                                    )
                                );
                            }
                        }
                        unset($_SESSION["Bundle"]);
                        unset($_SESSION["Count"]);
                        unset($_SESSION["BundleInfo"]);
                        unset($_SESSION["Total"]);
                    }

                    $pdoActivity->execute(
                        array(
                            'Details'       =>     $details
                        )
                    );
                }                
                catch(PDOException $error) {  
                    $message = $error->getMessage();}
            }         
        }
        
        //Delete product/bundles
        if(isset($_POST["ProductDelete"]))
        {
            try
            {
                //for bundled items
                if($_POST["Category"]=='Bundle')
                {
                    //Return reserved stock
                    if($_POST["Quantity"]>0){
                        $pdoBundle = $pdoConnect->prepare("SELECT * FROM tblproductbundles WHERE bundlecode='".$_POST['ProductCode']."'");
                        $pdoQuantity = $pdoConnect->prepare("UPDATE tblproducts SET quantity=quantity+:Quantity WHERE productcode=:ProductCode");
                        $pdoBundle->execute();
                        foreach ($pdoBundle as $bundleitem) 
                        {
                            $pdoQuantity->execute(
                                array(
                                    'ProductCode'   =>     $bundleitem["productcode"],
                                    'Quantity'      =>     $bundleitem["quantity"]
                                )
                            );
                        }
                    }

                    //Delete bundle
                    $pdoBundle = $pdoConnect->prepare("DELETE FROM tblproductbundles WHERE bundlecode='". $_POST["ProductCode"]."'"); 
                    $pdoBundle->execute();
                }
                $pdoCRUD = $pdoConnect->prepare("DELETE FROM tblproducts where productcode='".$_POST['ProductCode']."'");  
                $pdoCRUD->execute();
                $message = $_POST["Category"]=='Bundle' ? 'Product bundle was deleted successfully' : 'Product was deleted successfully';
                $details = $_POST["Category"]=='Bundle' ? "Product bundle deleted: ".$_POST["ProductCode"] : "Product deleted: ".$_POST["ProductCode"];
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_POST["DeleteProduct"]);

            }
            catch(PDOException $error) {  
                $message = $error->getMessage();}            
        }

        //Bundle stock reserve
        if(isset($_POST["IncreaseDone"]) || isset($_POST["DecreaseDone"]))
        {
            $minus = "UPDATE tblproducts SET quantity=quantity-:Quantity WHERE productcode=:ProductCode";
            $plus = "UPDATE tblproducts SET quantity=quantity+:Quantity WHERE productcode=:ProductCode";
            try
            { 
                $pdoQuantity = isset($_POST["IncreaseDone"]) ? $pdoConnect->prepare($minus) : $pdoConnect->prepare($plus);
                $pdoReserve = isset($_POST["IncreaseDone"]) ? $pdoConnect->prepare($plus) : $pdoConnect->prepare($minus);
                $pdoBundle = $pdoConnect->prepare("SELECT * FROM tblproductbundles WHERE bundlecode='".$_POST['ProductCode']."'");
                $pdoBundle->execute();
                foreach ($pdoBundle as $bundleitem) 
                {
                    $pdoQuantity->execute(
                        array(
                            'ProductCode'   =>     $bundleitem["productcode"],
                            'Quantity'      =>     $bundleitem["quantity"]*$_POST["Quantity"]
                        )
                    );
                }
                $pdoReserve->execute(
                    array(
                        'ProductCode'   =>     $_POST["ProductCode"],
                        'Quantity'      =>     $_POST["Quantity"]
                    )
                ); 
                $message = isset($_POST["IncreaseDone"]) ? 'Items were bundled successfully' : 'Items were unbundled successfully';
                $details = isset($_POST["IncreaseDone"]) ? "Items bundled: ".$_POST["Quantity"]." ".$_POST["ProductCode"] : "Items unbundled: ".$_POST["Quantity"]." ".$_POST["ProductCode"];
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_POST["DeleteProduct"]);
            }                
            catch(PDOException $error) {  
                $message = $error->getMessage();}
        }

        //Add to bundle
        if(isset($_POST["BundleAdd"]))  
        {  
            $item_array = array(  
                'ProductCode'     =>     $_POST["ProductCode"],  
                'Description'     =>     $_POST["Description"],   
                'Price'           =>     $_POST["Price"] 
            ); 
            if(isset($_SESSION["Bundle"]) && count($_SESSION["Bundle"])<10)  
            {      
                $_SESSION["Count"] += 1;
                $_SESSION["Bundle"][$_SESSION["Count"]] = $item_array;
                $_SESSION["Bundle"][$_SESSION["Count"]]["ID"] = $_SESSION["Count"];  
            }  
            elseif(!isset($_SESSION["Bundle"]))
            {  
                $_SESSION["Count"] = 0;
                $_SESSION["Bundle"][0] = $item_array;
                $_SESSION["Bundle"][0]["ID"] = 0;
            }
            else
            {
                $cartmessage='You have reached the item limit';
            }
            
        } 
        
        //Remove item
        if(isset($_POST["BundleRemove"]))  
        {
            foreach($_SESSION["Bundle"] as $keys => $item)  
            {  
                if($item["ID"] == $_POST["ID"])  
                {  
                    unset($_SESSION["Bundle"][$keys]);
                    $cartmessage='Item removed';  
                }
            }
        }
        
        //Clear items
        if(isset($_POST["BundleClear"]) || empty($_SESSION["Bundle"]))  
        {
            unset($_SESSION["Bundle"]);
            unset($_SESSION["Count"]);
            unset($_SESSION["BundleInfo"]);
            unset($_SESSION["Total"]);
            $cartmessage='Items cleared';  
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
        <title>Product</title>
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
                        <button name="ProductsTab">All Products</button>  
                        <button name="BundlesTab">Product Bundles</button>
                        <button name="NewBundleTab">New Bundle</button> 
                    </form>
                    <form method="get"> 
                        <input type="search" name="Search" placeholder="Search" value="<?php echo isset($_SESSION['Search']) ? $_SESSION['Search'] : ''; ?>"> 
                    </form>
                </div>     

                <!--- crud and table display-->
                <table class="table">
                <tr>
                    <th>#</th><th>Product Code</th><th>Barcode</th><th>Description</th><th>Price</th><th>Category</th><th>Options</th>
                </tr>            
                       
                <?php

                    if(isset($message))  
                    {  
                        echo    '<tr class="crud">
                                <td></td>
                                    <td colspan=6>
                                    '.$message.'
                                    </td>
                                </tr>';
                    }
                    if(!isset($_SESSION["BundlesTab"]) && !isset($_SESSION["NewBundleTab"]) && !isset($_POST["NewProduct"]))  
                    {  
                        echo    '<tr class="crud">
                                <td></td>
                                    <td colspan=6>
                                    <form method="post"> 
                                        <button name="NewProduct">Add</button>                                                                   
                                    </form>
                                    </td>
                                </tr>';
                    }

                    //click on new product
                    if(isset($_POST["NewProduct"])) 
                    {                         
                        $pdoCategory->execute();
                        $dropdown = '<select name="Category" required>\r\n<option value="" selected hidden>Category</option>';
                        foreach ($pdoCategory as $category) {
                            $dropdown .= "\r\n<option value='{$category['description']}'>{$category['description']}</option>";
                            }
                        $dropdown .= '\r\n</select> ';
                        echo    '<tr class="crud">
                                <td></td>
                                <form method="post">
                                    <td><input type="text" name="ProductCode" placeholder="Product Code ~ P0000" required></td>
                                    <td><input type="text" name="Barcode" placeholder="Barcode" required></td>
                                    <td><input type="text" name="Description" placeholder="Description"></td>
                                    <td><input type="number" name="Price" step="0.01" placeholder="Price" required></td>
                                    <td>'.$dropdown.'</td>
                                    <td><button name="Cancel" form="cancel">Cancel</button></td>
                                    <tr class="crud"><td></td>
                                    <td colspan=5><textarea name="Details" rows=5 placeholder="Details"></textarea></td>
                                    <td><button name="AddProduct">Add</button></td>
                                </form>
                                <form method="post" id="cancel"></form>
                                </tr>';
                    }                    
                
                    $pdoDisplay = $pdoConnect->prepare($Display);  
                    $pdoDisplay->execute();                    
                    $num = 0;
                    
                    foreach ($pdoDisplay as $product) {                                           
                        
                        $outofstock=false;
                        $max=0;
                        if(isset($_SESSION["BundlesTab"]))
                        {
                            //checks stock of each item
                            $pdoBundleDetails = $pdoConnect->prepare("SELECT tblproductbundles.*, tblproducts.description, tblproducts.quantity as stock FROM tblproductbundles INNER JOIN tblproducts WHERE tblproductbundles.productcode = tblproducts.productcode AND tblproductbundles.bundlecode='".$product['productcode']."'");
                            $pdoBundleDetails->execute();
                            foreach ($pdoBundleDetails as $bundleitem) 
                            {
                                if($bundleitem["stock"]/$bundleitem['quantity']<1){
                                    $outofstock = true; break;}
                                elseif($max==0){
                                    $max = $bundleitem["stock"]/$bundleitem['quantity'];}
                                else{
                                    $max = $bundleitem["stock"]/$bundleitem['quantity']<$max ? $bundleitem["stock"]/$bundleitem['quantity'] : $max;}
                            }
                        }
                        $num++;

                        //click on edit
                        if(isset($_POST["EditProduct"]) && $product["productcode"]==$_POST["ProductCode"])
                        {                      
                            $pdoCategory->execute();
                            $dropdown = '<select name="Category" required>\r\n<option value="'.$product['category'].'" selected>'.$product['category'].'</option>'; 
                            foreach ($pdoCategory as $category) {
                                $dropdown .= "\r\n<option value='{$category['description']}'>{$category['description']}</option>";
                                }
                            $dropdown .= '\r\n</select> ';                       
                            
                            echo    '<tr class="crud">
                                    <td></td>
                                    <form method="post">
                                        <td><input type="text" name="ProductCode" placeholder="Product Code" value="'.$product['productcode'].'" readonly></td>
                                        <td><input type="text" name="Barcode" placeholder="Barcode" value="'.$product['barcode'].'" required></td>
                                        <td><input type="text" name="Description" placeholder="Description" value="'.$product['description'].'"></td>
                                        <td><input type="number" name="Price" step="0.01" placeholder="Price" value="'.$product['price'].'" required></td>
                                        <td>'.$dropdown.'</td>
                                        <td><button name="Cancel" form="cancel">Cancel</button></td>
                                        <tr class="crud"><td></td>
                                        <td colspan=5><textarea name="Details" rows=5 placeholder="Details">'.$product['details'].'</textarea></td>
                                        <td><button name="ProductEdit">Save</button></td>
                                    </form>
                                    <form method="post" id="cancel"></form>
                                    </tr>';
                        }
                        

                        //click on delete
                        elseif(isset($_POST["DeleteProduct"]) && $product["productcode"]==$_POST["ProductCode"])
                        {
                            echo    '<tr class="crud"><td></td>
                                    <td colspan=6>
                                    Are you sure you want to delete this '; echo $_POST["Category"]=='Bundle'? 'bundle? *Reserved stocks will be returned to each item' : 'product?' ; echo'
                                    </td></tr>
                                    <tr class="crud"><td></td>
                                    <td>'.$product['productcode'].'</td>
                                    <td>'.$product['barcode'].'</td>
                                    <td>'.$product['description'].'</td>
                                    <td>'.$product['price'].'</td>
                                    <td>'.$product['category'].'</td>
                                    <td>
                                    <form method="post" class="display">
                                        <input type="hidden" name="ProductCode" value="'.$_POST['ProductCode'].'">
                                        <input type="hidden" name="Category" value="'.$product["category"].'">
                                        <input type="hidden" name="Quantity" value="'.$product["quantity"].'">
                                        <button name="Cancel" form="cancel">Cancel</button>
                                        <button name="ProductDelete">Delete</button>  
                                    </form>
                                    <form method="post" id="cancel"></form>
                                    </td>
                                    </tr>';
                        }

                        //bundle options
                        elseif((isset($_POST["BundleDetails"]) || (isset($_POST["Decrease"]) && $product['quantity']!=0) || isset($_POST["Increase"]) && $_POST["OOS"]!=1) && $product["productcode"]==$_POST["ProductCode"])
                        {
                            echo    '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$product['productcode'].'</td>
                                    <td>'.$product['barcode'].'</td>
                                    <td>'.$product['description'].'</td>
                                    <td>'.$product['price'].'</td>
                                    <td>'.$product['category'].'</td>
                                    <form method="post">
                                        <td><button name="Cancel">^</button> <button name="Increase" disabled>+</button> <button name="Decrease"disabled>-</button></td>
                                    </form>
                                    </tr>';

                            
                            if(isset($_POST["BundleDetails"]))
                            {
                                $pdoBundleDetails = $pdoConnect->prepare("SELECT tblproductbundles.*, tblproducts.description, tblproducts.quantity as stock FROM tblproductbundles INNER JOIN tblproducts WHERE tblproductbundles.productcode = tblproducts.productcode AND tblproductbundles.bundlecode='".$_POST["ProductCode"]."'");
                                $pdoBundleDetails->execute();
                                foreach ($pdoBundleDetails as $bundleitem) 
                                {
                                    echo  '<tr class="crud">
                                        <td></td>
                                        <td>'.$bundleitem['productcode'].'</td>
                                        <td colspan=3>'.$bundleitem['description'].'</td>
                                        <td>'.$bundleitem['quantity'].'</td>
                                        <td>'; echo $bundleitem['stock']==0? 'Out of stock' : ''; echo'</td>
                                        </tr>';
                                }
                            }
                            elseif(isset($_POST["Increase"]))
                            {
                                echo  '<tr class="crud">
                                    <td></td>
                                    <form method="post">
                                        <input type="hidden" name="ProductCode" value="'.$product["productcode"].'">
                                        <td colspan=5>
                                        <label>Increase reserved stock for this bundle by: </label>
                                        <input type="number" name="Quantity" min=1 max='.$max.' value=1 placeholder="Quantity" required>
                                        </td><td><button name="IncreaseDone">Done</button></td>
                                    </form>
                                    </tr>';
                            }
                            elseif(isset($_POST["Decrease"]))
                            {
                                    echo  '<tr class="crud">
                                        <td></td>
                                        <form method="post">
                                            <input type="hidden" name="ProductCode" value="'.$product["productcode"].'">
                                            <td colspan=5>
                                            <label>Decrease reserved stock for this bundle by: </label>
                                            <input type="number" name="Quantity" min=1 max='.$product['quantity'].' value=1 placeholder="Quantity" required>
                                            </td><td><button name="DecreaseDone">Done</button></td>
                                        </form>
                                        </tr>';
                            }

                        }

                        //rest of records
                        else
                        {
                            echo   '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$product['productcode'].'</td>
                                    <td>'.$product['barcode'].'</td>
                                    <td>'.$product['description'].'</td>
                                    <td>'.$product['price'].'</td>
                                    <td>'.$product['category'].'</td>
                                    <td>
                                        <form method="post"> 
                                        <input type="hidden" name="ProductCode" value="'.$product["productcode"].'">
                                        <input type="hidden" name="Description" value="'.$product["description"].'">
                                        <input type="hidden" name="Price" value="'.$product["price"].'">
                                        <input type="hidden" name="Category" value="'.$product["category"].'">
                                        '; echo isset($_SESSION['BundlesTab']) ? 'Reserved stock: '.$product["quantity"].'<input type="hidden" name="OOS" value="'.$outofstock.'">' : '';
                                         echo !isset($_SESSION['BundlesTab']) && !isset($_SESSION['NewBundleTab']) ? ($product["bundlecode"]===NULL? '<button name="EditProduct">Edit</button> <button name="DeleteProduct">Delete</button>' : '<button name="EditProduct">Edit</button> <button disabled>Bundled</button>') : $option; echo '                                                               
                                        </form>
                                    </td>
                                    </tr>';
                        }                        
                    }
                ?>
                
                </table>

            </div>

            
            <!--- right navigation bar --->
            <div class="navigation"> 
                <?php  
                    include '../include/adminnav.php';
                ?>
            </div>


            <!--- product bundle --->
            <?php
                if(isset($_SESSION["NewBundleTab"]))  
                {
                    echo '<div class="stockcart"> 
                            <table class="display">';
                    if(isset($cartmessage))  
                    {
                        echo '<tr><td colspan=5>
                                '.$cartmessage.'
                                </td></tr>';
                    }

                    if(isset($_SESSION["Bundle"]))  
                    {                          
                        $total = 0;
                        $num = 0;
                        
                        //Display each item 
                        foreach($_SESSION["Bundle"] as $keys => $item)  
                        {
                            $total = number_format((float)$total+$item['Price'], 2, '.', '');
                            $num++;
                            
                            echo '<tr>
                                <td>'.$num.'</td>
                                <td>'.$item['ProductCode'].'</td>
                                <td>'.$item['Description'].'</td>
                                <td>'.$item['Price'].'</td>                                                       
                                <td>
                                <form method="post"> 
                                    <input type="hidden" name="ID" value="'.$item["ID"].'">
                                    <button name="BundleRemove">x</button>
                                </form>                                                                 
                                </td>
                                </tr>';

                        }
                        $_SESSION["Total"] = $total;
                        //Other details on the bottom part 

                        echo '<tr><td></td>
                                <form method="post">
                                    <td><input type="text" name="BundleCode" placeholder="Bundle Code ~ PB0000" required></td>
                                    <td><input type="text" name="Description" placeholder="Description" ></td>
                                    <td><input type="number" name="Price" step="0.01" placeholder="Price" value="'.$total.'" readonly></td>
                                    <td></td></tr><tr><td></td>
                                    <td><input type="text" name="Barcode" placeholder="Barcode" required></td>
                                    <td><select name="Category" required>\r\n<option value="Bundle" selected>Bundle</option>\r\n</select></td>
                                    <td><input type="number" name="Discount" max='.$total.' step="0.01" placeholder="Set a discounted price"></td>
                                    <td></td></tr><tr><td></td>
                                    <td colspan=3><textarea name="Details" rows=5 placeholder="Details"></textarea></td>                                          
                                    </tr><tr><td colspan=5>
                                    <button name="BundleClear" form="clear">Clear</button> 
                                    <button name="BundleSave">Save</button> 
                                </form>    
                                <form method="post" id="clear"></form>                                                            
                                </td></tr>';

                    }
                    else 
                    {
                        echo '<tr><td>
                                <label>Please select some items to bundle (max: 10 items)</label>
                                </td></tr>';
                    }
                                       
                    echo '</table>
                    </div>';
                   
                }                       
            ?> 
           
        </div>
        
        <!--- footer --->
        <div class="footer"> 
            <?php  
                
            ?>
        </div>   

    </body>  
</html> 