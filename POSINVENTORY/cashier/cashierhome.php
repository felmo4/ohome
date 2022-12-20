<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    {  

        //for functions.................................................................................................
        //For activity log
        $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");
       
        //Applied discounts
        $Display = "SELECT tblpromos.*,tblproducts.description as pdescription,tblproducts.price,tblproducts.quantity FROM tblpromos INNER JOIN tblproducts 
        WHERE tblpromos.product = tblproducts.productcode AND tblproducts.quantity !=0
            AND (tblpromos.periodstart<=NOW() AND (tblpromos.periodend>=NOW() OR tblpromos.periodend=0))
            AND (tblpromos.uselimit=0 OR (tblpromos.uselimit-tblpromos.used)>0)";
        $pdoApplied = $pdoConnect->prepare($Display); 
        $pdoApplied->execute();
        $discounts = $pdoApplied->fetchall(); 
         

        //Settle payment
        if(isset($_POST["Done"])) 
        {  
            try
            {   
                //record using tblsalesinvoice             
                $pdoSalesInvoice = $pdoConnect->prepare("INSERT INTO tblsalesinvoice (invoiceNO, time, discount, total, bill, billchange) VALUES (:InvoiceNO, NOW(), :Discount, :Total, :Bill, :BillChange)");  
                $pdoSalesInvoice->execute(
                    array(
                        'InvoiceNO'     =>     $_POST["InvoiceNO"],
                        'Discount'      =>     number_format((float)$_POST["Discount"], 2, '.', ''),
                        'Total'         =>     number_format((float)$_POST["Total"], 2, '.', ''),                            
                        'Bill'          =>     number_format((float)$_POST["Bill"], 2, '.', ''),
                        'BillChange'    =>     number_format((float)$_POST["Bill"]-$_POST["Total"], 2, '.', '')
                        
                    )
                );
                
                //record individual items using tblsales and adjusts product quantity
                $pdoSales = $pdoConnect->prepare("INSERT INTO tblsales (invoiceNO, productcode, description, quantity, itemtotal, discount, discountdetails) VALUES (:InvoiceNO, :ProductCode, :Description, :Quantity, :ItemTotal, :Discount, :discountdetails)"); 
                $pdoQuantity = $pdoConnect->prepare("UPDATE tblproducts SET quantity=quantity-:Quantity WHERE productcode=:ProductCode"); 
                $pdoUsed = $pdoConnect->prepare("UPDATE tblpromos SET used=used+:Quantity WHERE promoID=:promoID");     
                foreach($_SESSION["Transaction"] as $keys => $item)  
                {  
                    $pdoSales->execute(
                        array(
                            'InvoiceNO'         =>     $_POST["InvoiceNO"],
                            'ProductCode'       =>     $_SESSION["Transaction"][$keys]['ProductCode'],
                            'Description'       =>     $_SESSION["Transaction"][$keys]['Description'],
                            'Quantity'          =>     $_SESSION["Transaction"][$keys]['Quantity'],
                            'ItemTotal'         =>     $_SESSION["Transaction"][$keys]['ItemTotal'],
                            'Discount'          =>     number_format((float)$_SESSION["Transaction"][$keys]["Discount"], 2, '.', ''),
                            'discountdetails'   =>     $_SESSION["Transaction"][$keys]["Discount"]>0 ? $_SESSION["Transaction"][$keys]['D.Description']." ".$_SESSION["Transaction"][$keys]['D.Reward'] : ""
                        )
                    );
                    $pdoQuantity->execute(
                        array(
                            'ProductCode'   =>     $_SESSION["Transaction"][$keys]['ProductCode'],
                            'Quantity'      =>     $_SESSION["Transaction"][$keys]['Quantity']
                        )
                    );

                    //record used promo
                    if ($_SESSION["Transaction"][$keys]["Discount"]>0) 
                    {
                        $pdoUsed->execute(
                            array(
                                'promoID'       =>     $_SESSION["Transaction"][$keys]['D.ID'],
                                'Quantity'      =>     $_SESSION["Transaction"][$keys]['Quantity']
                            )
                        );
                    }
                    
                }
                    
                $cartmessage = 'Transaction successful'; 
                $details = "New invoice: invoice number: ".$_POST["InvoiceNO"];              
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_SESSION["Transaction"]);    
                
            }                
            catch(PDOException $error) {  
                $cartmessage = $error->getMessage();}
        
        }        

        //Enter barcode
        if(isset($_POST["ProductBarcode"]))
        {
            $found = false;
            $pdoScan = $pdoConnect->prepare("SELECT * FROM tblproducts WHERE barcode='".$_POST["ProductBarcode"]."'");
            $pdoScan->execute();
            foreach ($pdoScan as $product) 
            {
                $found = true;
                if($product["quantity"] == 0){
                    $cartmessage = 'Item is out of stock';}
                else{

                    //Checks if item is discounted
                    $item_array_id = array_column($discounts, "product");  
                    if(in_array($product["productcode"], $item_array_id))  
                    {  
                        foreach($discounts as $keys => $discounted)  
                        {  
                            if($discounted["product"] == $product["productcode"])  
                            {  
                                $discount = number_format((float) ($discounted["rewardtype"]=='Php' ? $discounted["reward"] : $discounted["price"]*($discounted["reward"]/100)), 2, '.', '');
                                $price = number_format((float) $discounted["price"]-$discount, 2, '.', '');
                                $item_array = array(  
                                    'ProductCode'       =>     $product["productcode"],  
                                    'Description'       =>     $product["description"],   
                                    'Price'             =>     $price,
                                    'Quantity'          =>     1,
                                    'ItemTotal'         =>     $price,
                                    'Stock'             =>     $discounted['uselimit']==0 ? $product["quantity"] : ($discounted['uselimit']-$discounted['used']<$product["quantity"] ? $discounted['uselimit']-$discounted['used'] : $product["quantity"]),
                                    'Discount'          =>     $discount,
                                    'ItemDiscount'      =>     $discount,
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
                            'ProductCode'       =>     $product["productcode"],  
                            'Description'       =>     $product["description"],   
                            'Price'             =>     $product["price"],
                            'Quantity'          =>     1,
                            'ItemTotal'         =>     $product["price"],
                            'Stock'             =>     $product["quantity"],
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
                        }  
                        else  
                        {  
                            foreach($_SESSION["Transaction"] as $keys => $item)  
                            {  
                                if($item["ProductCode"] == $item_array["ProductCode"] && $item["Quantity"] < $item["Stock"])  
                                {  
                                    $_SESSION["Transaction"][$keys]["Quantity"] += 1;
                                    $_SESSION["Transaction"][$keys]['ItemTotal'] += $item_array["Price"]; 
                                    $_SESSION["Transaction"][$keys]['ItemDiscount'] += $item_array["Discount"]; 
                                    break;
                                }  
                            }  
                        }  
                    }
                    else  
                    {  
                        $_SESSION["Count"] = 0;
                        $_SESSION["Transaction"][0] = $item_array; 
                        $_SESSION["Transaction"][0]["ID"] = 0;  
                    }
                }  
            }
            if(!$found){
                $cartmessage = 'No match found';}
        }

        //Item options
        if(isset($_POST["Increase"]) || isset($_POST["Decrease"]) || isset($_POST["Remove"]))  
        {
            foreach($_SESSION["Transaction"] as $keys => $item)  
            {  
                if($item["ID"] == $_POST["ID"])  
                {  
                    
                    //Increase item quantity
                    if(isset($_POST["Increase"]) && $item["Quantity"] < $item["Stock"]){
                        $_SESSION["Transaction"][$keys]["Quantity"] += 1;
                        $_SESSION["Transaction"][$keys]['ItemTotal'] += $item["Price"];
                        $_SESSION["Transaction"][$keys]['ItemDiscount'] += $item["Discount"];} 

                    //Decrease item quantity
                    if(isset($_POST["Decrease"]) && $item["Quantity"] > 1){
                        $_SESSION["Transaction"][$keys]["Quantity"] -= 1;
                        $_SESSION["Transaction"][$keys]['ItemTotal'] -= $item["Price"];
                        $_SESSION["Transaction"][$keys]['ItemDiscount'] -= $item["Discount"];}

                    //Remove item
                    if(isset($_POST["Remove"])){
                        unset($_SESSION["Transaction"][$keys]);
                        $cartmessage='Item removed';}
                }  
            }
        }
        
        //Clear items
        if(isset($_POST["Clear"]) || empty($_SESSION["Transaction"]))  
        {
            unset($_SESSION["Transaction"]);
            unset($_SESSION["Count"]);  
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
        <title>Cashier</title>
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
                <form method="post"> 
                    <input type="text" name="ProductBarcode" placeholder="Barcode"> 
                </form>
                </div>

                <!--- cashier cart -->
                <div>     
                <table class="display">
                <?php
                    if(isset($cartmessage))  
                    {
                        echo '<tr><td colspan=6>
                                '.$cartmessage.'
                                </td></tr>';
                    }
                    
                    if(!empty($_SESSION["Transaction"]))   
                    {
                        echo '<tr>
                                <th>Code</th><th>Description</th><th>Price</th><th>Quantity</th><th>Item Total</th><th>Options</th>
                            </tr>';
                        
                        //Display each item 
                        $total = 0;   
                        $discount = 0;
                        $discountdisplay = "";
                        $discountdescription = "";
                        foreach($_SESSION["Transaction"] as $keys => $item)  
                        {
                            $total = number_format((float)$total+$item['ItemTotal'], 2, '.', '');
                            $badge="";
                            if($item['ItemDiscount']>0)
                                {
                                    $discount += number_format((float)$item['ItemDiscount'], 2, '.', '');
                                    $discountdisplay .= "-".number_format((float)$item['ItemDiscount'], 2, '.', '')."<br>";
                                    $discountdescription .= "".$item['D.Description']." ".$item['D.Reward']."<br>";
                                    $badge="Promo ";
                                }
                            echo '<tr>
                                <td>'.$badge.''.$item['ProductCode'].'</td>
                                <td>'.$item['Description'].'</td>
                                <td>'.$item['Price'].'</td>
                                <td>'.$item['Quantity'].'</td>
                                <td>'.number_format((float)$item['ItemTotal'], 2, '.', '').'</td>                                                         
                                <td>
                                <form method="post"> 
                                    <input type="hidden" name="ID" value="'.$item["ID"].'">
                                    <button name="Decrease">-</button>
                                    <button name="Increase">+</button>
                                    <button name="Remove">x</button>
                                </form>                                                                 
                                </td>
                                </tr>';
                                
                        }
                    }
                    else 
                    {
                        echo '<tr><td>
                                Cart empty
                                </td></tr>';
                    }                      
                ?>
                </table>
                </div>

                <!-- Other details on the bottom part -->
                <table class="display">
                <?php
                    if(!empty($_SESSION["Transaction"]))   
                    {
                        echo $discount>0 ? '<tr><td colspan=3></td><td>'.$discountdescription.'</td><td>'.$discountdisplay.'</td></tr>' : ''; echo'
                        <tr><td colspan=3></td><td>TOTAL</td><td>
                        '.$total.'                                                                 
                        </td></tr>
                        <form method="post">
                            <tr><td colspan=6>                            
                            <input type="text" name="InvoiceNO" placeholder="Invoice No." required>
                            <input type="number" name="Bill" step="0.01" min='.$total.' placeholder="Bill" required>                                
                            <input type="hidden" name="Total" value='.$total.'>
                            <input type="hidden" name="Discount" value='.$discount.'>
                            </td></tr>
                            <tr><td colspan=6>
                            <button name="Clear" form="clear">Clear</button>
                            <button name="Done">Done</button> 
                        </form>        
                        <form method="post" id="clear"></form>                                                         
                        </td></tr>';
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
            
        </div>
        
        <!--- footer --->
        <div class="footer"> 
            <?php  
                
            ?>
        </div>   

    </body>  
</html> 

            