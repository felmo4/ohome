<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    {  
        //for navigation.................................................................................................
        
        $Display = "SELECT * FROM tblproducts  WHERE category != 'Bundle'";
        //Search bar is used
        if(isset($_GET["Search"])) {
            $Display .= " AND (productcode LIKE '%".$_GET["Search"]."%' OR barcode LIKE '%".$_GET["Search"]."%' OR description LIKE '%".$_GET["Search"]."%' OR category LIKE '%".$_GET["Search"]."%')";
            $_SESSION["Search"] = $_GET["Search"];}

        //Add stock 
        if(isset($_GET["AddStock"])) {
            unset($_SESSION["AdjustStock"]);
            unset($_SESSION["Search"]);}

        //Adjust stock 
        if(isset($_GET["AdjustStock"])) {
            $_SESSION["AdjustStock"]=TRUE;
            unset($_SESSION["Search"]);}

        //Cancel
        if(isset($_POST["Cancel"])) {
            unset($_POST["StockAdjust"]);}


        //for functions.................................................................................................
        //For activity log and supplier
        $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");
        $pdoSuppliers = $pdoConnect->prepare("SELECT name FROM tblsuppliers"); 

        //Stock done and will be processed
        if(isset($_POST["StockDone"])) 
        {  
            try
            {   
                //record using tblstockinvoice             
                $pdoStockInvoice = $pdoConnect->prepare("INSERT INTO tblstockinvoice (referenceNO, time, total, supplier, admin) VALUES (:ReferenceNO, NOW(), :Total, :Supplier, :Admin)");  
                $pdoStockInvoice->execute(
                    array(
                        'ReferenceNO'   =>     $_POST["ReferenceNO"],
                        'Total'         =>     number_format((float)$_POST["Total"], 2, '.', ''),   
                        'Supplier'      =>     $_POST["Supplier"],
                        'Admin'         =>     $_SESSION["ID"]

                    )
                );

                //record individual items using tblstock and adjusts product quantity
                $pdoStock = $pdoConnect->prepare("INSERT INTO tblstock (referenceNO, productcode, description, quantity, itemtotal) VALUES (:ReferenceNO, :ProductCode, :Description, :Quantity, :ItemTotal)");  
                $pdoQuantity = $pdoConnect->prepare("UPDATE tblproducts SET quantity=quantity+:Quantity WHERE productcode=:ProductCode");  
                foreach($_SESSION["StockCart"] as $item)  
                {  
                    $pdoStock->execute(
                        array(
                            'ReferenceNO'   =>     $_POST["ReferenceNO"],
                            'ProductCode'   =>     $item["ProductCode"],
                            'Description'   =>     $item["Description"],
                            'Quantity'      =>     $item["Quantity"],
                            'ItemTotal'     =>     $item["ItemTotal"]
                        )
                    );
                    $pdoQuantity->execute(
                        array(
                            'ProductCode'   =>     $item["ProductCode"],
                            'Quantity'      =>     $item["Quantity"],
                        )
                    );
                }
                    
                $cartmessage = 'Stock was added successfully'; 
                $details = "Stock added: reference number: ".$_POST["ReferenceNO"];              
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_SESSION["StockCart"]);  
                unset($_SESSION["Count"]);  
                
            }                
            catch(PDOException $error) {  
                $cartmessage = $error->getMessage();}
            
        }

        //Stock adjust done and will be processed
        if(isset($_POST["AdjustDone"])) 
        {
            try
            {   
                $pdoQuantity = $pdoConnect->prepare("UPDATE tblproducts SET quantity=:Quantity WHERE productcode=:ProductCode");  
                $pdoQuantity->execute(
                    array(
                        'ProductCode'   =>     $_POST["ProductCode"],
                        'Quantity'      =>     $_POST["Quantity"]
                    )
                );
                    
                $message = 'Stock was adjusted successfully'; 
                $details = "Stock adjustment: product: ".$_POST["ProductCode"].", reason: ".$_POST["Reason"] ;              
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                ); 
                unset($_POST["StockAdjust"]);
                
            }                
            catch(PDOException $error) {  
                $cartmessage = $error->getMessage();}
              
        }
        
        //Add item to cart
        if(isset($_POST["StockAdd"]))  
        {  
            if(empty($_POST["Quantity"]))  
            {  
                $cartmessage = 'Please set a quantity';  
            }
            else
            {
                $item_array = array(  
                    'ProductCode'       =>     $_POST["ProductCode"],  
                    'Description'       =>     $_POST["Description"],   
                    'Price'             =>     $_POST["Price"],
                    'Quantity'          =>     $_POST["Quantity"],
                    'ItemTotal'         =>     number_format((float)$_POST["Price"]*$_POST["Quantity"], 2, '.', '') 
                ); 
                if(isset($_SESSION["StockCart"]))  
                {  
                    $item_array_id = array_column($_SESSION["StockCart"], "ProductCode");  
                    if(!in_array($_POST["ProductCode"], $item_array_id))  
                    {  
                        $_SESSION["Count"] += 1; 
                        $_SESSION["StockCart"][$_SESSION["Count"]] = $item_array;  
                        $_SESSION["StockCart"][$_SESSION["Count"]]["ID"] = $_SESSION["Count"]; 
                    }  
                    else  
                    {  
                        foreach($_SESSION["StockCart"] as $keys => $item)  
                        {  
                            if($item["ProductCode"] == $_POST["ProductCode"])  
                            {  
                                $_SESSION["StockCart"][$keys]["Quantity"] += $_POST["Quantity"];
                                $_SESSION["StockCart"][$keys]['ItemTotal'] += number_format((float)($_POST["Price"]*$_POST["Quantity"]), 2, '.', ''); 
                            }  
                        }  
                    }  
                }  
                else  
                {  
                    $_SESSION["Count"] = 0;
                    $_SESSION["StockCart"][0] = $item_array; 
                    $_SESSION["StockCart"][0]["ID"] = 0; 
                }
            }  
        } 
        
        //Remove item
        if(isset($_POST["StockRemove"]))  
        {
            foreach($_SESSION["StockCart"] as $keys => $item)  
            {  
                if($item["ID"] == $_POST["ID"])  
                {  
                    unset($_SESSION["StockCart"][$keys]);
                    $cartmessage='Item removed';  
                }  
            }
        }
        
        //Clear items
        if(isset($_POST["StockClear"]) || empty($_SESSION["StockCart"]))  
        {
            unset($_SESSION["StockCart"]);
            unset($_SESSION["Count"]);
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
        <title>Stock</title>
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
                        <button name="AddStock">Add Stock</button>
                        <button name="AdjustStock">Adjust Stock</button>
                    </form>
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
                    $option;
                    $option = isset($_SESSION["AdjustStock"]) ? '<button name="StockAdjust">Adjust</button>' : '<input type="number" name="Quantity" min=1 value=1 placeholder="Quantity"><button name="StockAdd">Add</button>';                 

                    foreach ($pdoDisplay as $product) {                                           
                        $num++;

                        //click on adjust
                        if(isset($_POST["StockAdjust"]) && $product["productcode"]==$_POST["ProductCode"])
                        {

                            echo    '<tr class="crud">
                                    <td></td>
                                    <td>'.$product['productcode'].'</td>
                                    <td>'.$product['barcode'].'</td>
                                    <td>'.$product['description'].'</td>
                                    <td>'.$product['price'].'</td>
                                    <td>'.$product['category'].'</td>
                                    <form method="post">
                                        <input type="hidden" name="ProductCode" value="'.$product["productcode"].'">
                                        <td><input type="number" name="Quantity" placeholder="Quantity"  value="'.$product['quantity'].'" required></td>  
                                        <td><button name="Cancel" form="cancel">Cancel</button></td>
                                        </tr><tr class="crud"><td></td>
                                        <td colspan=6>
                                        <label>Please state the reason for stock adjustment: </label>
                                        <input type="text" name="Reason" placeholder="Reason" required>
                                        </td><td>
                                        <button name="AdjustDone">Done</button>
                                    </form>
                                    <form method="post" id="cancel"></form>
                                    </td></tr>';
                        }
                        else
                        {
                            echo   '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$product['productcode'].'</td>
                                    <td>'.$product['barcode'].'</td>
                                    <td>'.$product['description'].'</td>
                                    <td>'.$product['price'].'</td>
                                    <td>'.$product['category'].'</td>
                                    <td>'.$product['quantity'].'</td>
                                    <td>
                                        <form method="post"> 
                                        <input type="hidden" name="ProductCode" value="'.$product["productcode"].'">
                                        <input type="hidden" name="Description" value="'.$product["description"].'">
                                        <input type="hidden" name="Price" value="'.$product["price"].'">
                                        '. $option.'                                                                
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

            <!--- stock cart --->
            <?php
                if(!isset($_SESSION["AdjustStock"]))  
                {
                    echo '<div class="stockcart"> 
                            <table class="display">';
                    if(isset($cartmessage))  
                    {
                        echo '<tr><td colspan=5>
                                '.$cartmessage.'
                                </td></tr>';
                    }

                    if(!empty($_SESSION["StockCart"]))  
                    {                          
                        $total = 0;                        
                        $pdoSuppliers->execute();
                        $dropdown = '<select name="Supplier" required>\r\n<option value="" selected hidden>Supplier</option>';
                        foreach ($pdoSuppliers as $supplier) {
                            $dropdown .= "\r\n<option value='{$supplier['name']}'>{$supplier['name']}</option>";
                            }
                        $dropdown .= '\r\n</select> ';
                        
                        //Display each item 
                        foreach($_SESSION["StockCart"] as $keys => $item)  
                        {
                            $total = number_format((float)$total+$item['ItemTotal'], 2, '.', '');
                            
                            echo '<tr>
                                <td>'.$item['ProductCode'].'</td>
                                <td>'.$item['Description'].'</td>
                                <td>'.$item['Price'].' x '.$item['Quantity'].'</td>
                                <td>'.$item['ItemTotal'].'</td>                                                         
                                <td>
                                <form method="post"> 
                                    <input type="hidden" name="ID" value="'.$item["ID"].'">
                                    <button name="StockRemove">x</button>
                                </form>                                                                 
                                </td>
                                </tr>';

                        }

                        //Other details on the bottom part
                        echo '<tr><td colspan=2></td><td>TOTAL</td><td>
                                '.$total.'                                                                 
                                </td><td></td></tr>
                                <tr><td colspan=5>
                                <form method="post">
                                    <input type="hidden" name="Total" value="'.$total.'">
                                    <input type="text" name="ReferenceNO" placeholder="Reference No." required>
                                    '.$dropdown.'                                            
                                    </td></tr>
                                    <tr><td colspan=5>
                                    <button name="StockClear" form="clear">Clear</button>  
                                    <button name="StockDone">Done</button> 
                                </form> 
                                <form method="post" id="clear"></form>                                                          
                                </td></tr>';

                    }
                    else 
                    {
                        echo '<tr><td>
                                <label>Please add some items to restock</label>
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

            