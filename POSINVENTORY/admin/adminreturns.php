<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    { 
        //Invoice tab
        if(isset($_GET["InvoiceTab"])) {
            unset($_SESSION["ItemTab"]);
            unset($_SESSION["Search"]);
            unset($_SESSION["Filter"]);
            unset($_GET["Load"]);}

        //Item tab 
        if(isset($_GET["ItemTab"])) {
            $_SESSION["ItemTab"]=TRUE;
            unset($_SESSION["Search"]);
            unset($_SESSION["Filter"]);
            unset($_GET["Load"]);}

        //Cancel
        if(isset($_POST["Cancel"])) {
            unset($_POST["Return"]);}

        if(isset($_SESSION["ItemTab"]))
        {
            if (!isset($_GET["Load"])) {
                $Display = "SELECT tblsales.*, tblsalesinvoice.time FROM tblsales INNER JOIN tblsalesinvoice WHERE tblsales.invoiceNO=tblsalesinvoice.invoiceNO";}
            //Filter
            else {
                $Display = "SELECT tblsales.*, tblsalesinvoice.time FROM tblsales INNER JOIN tblsalesinvoice WHERE tblsales.invoiceNO=tblsalesinvoice.invoiceNO";
                if($_GET["Filter"]!="All") {
                    $Display .= empty($_GET["Search"]) ? " AND tblsalesinvoice.time >= '".$_GET["Filter"]."'" : " AND tblsalesinvoice.time >= '".$_GET["Filter"]."' AND (tblsales.invoiceNO LIKE '%".$_GET["Search"]."%' OR tblsales.productcode LIKE '%".$_GET["Search"]."%' OR tblsales.description LIKE '%".$_GET["Search"]."%')";;
                    !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]=""; $_SESSION["Filter"]=$_GET["Filter"];}
                else {
                    $Display .= empty($_GET["Search"]) ? "" : " AND (tblsales.invoiceNO LIKE '%".$_GET["Search"]."%' OR tblsales.productcode LIKE '%".$_GET["Search"]."%' OR tblsales.description LIKE '%".$_GET["Search"]."%')";;
                    !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]="";
                    unset($_SESSION["Filter"]);}
            }
            $Display .= " ORDER BY tblsalesinvoice.time DESC";
        }
        else
        {
            if (!isset($_GET["Load"])) {
                $Display = "SELECT * FROM tblsalesinvoice ORDER BY time DESC";}
            //Filter
            else {
                if($_GET["Filter"]!="All") {
                    $Display = empty($_GET["Search"]) ? "SELECT * FROM tblsalesinvoice WHERE time >= '".$_GET["Filter"]."' ORDER BY time DESC" : "SELECT * FROM tblsalesinvoice WHERE time >= '".$_GET["Filter"]."' AND (invoiceNO LIKE '%".$_GET["Search"]."%' OR total LIKE '%".$_GET["Search"]."%' OR bill LIKE '%".$_GET["Search"]."%') ORDER BY time DESC";;
                    !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]=""; $_SESSION["Filter"]=$_GET["Filter"];}
                else {
                    $Display = empty($_GET["Search"]) ? "SELECT * FROM tblsalesinvoice ORDER BY time DESC" : "SELECT * FROM tblsalesinvoice WHERE (invoiceNO LIKE '%".$_GET["Search"]."%' OR total LIKE '%".$_GET["Search"]."%' OR bill LIKE '%".$_GET["Search"]."%') ORDER BY time DESC";;
                    !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]="";
                    unset($_SESSION["Filter"]);}
            }
        }

        //for functions.................................................................................................
        //For activity log 
        $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");

        //invoice return
        if(isset($_POST["InvoiceReturn"]))  
        {   
            try
            {   
                if(isset($_POST["Returned"]))
                {
                    $pdoItems = $pdoConnect->prepare("SELECT * FROM tblsales WHERE invoiceNO='". $_POST["InvoiceNO"]."'"); 
                    $pdoItems->execute();
                    //Adjust quantity
                    $pdoQuantity = $pdoConnect->prepare("UPDATE tblproducts SET quantity=quantity+:Quantity WHERE productcode=:ProductCode");
                    foreach($pdoItems as $item)  
                    {  
                        $pdoQuantity->execute(
                            array(
                                'ProductCode'   =>     $item['productcode'],
                                'Quantity'      =>     $item['quantity']
                            )
                        );
                    }
                }

                $pdoItems = $pdoConnect->prepare("SELECT * FROM tblsales WHERE invoiceNO='". $_POST["InvoiceNO"]."'"); 
                $pdoItems->execute();
                //Move to tblreturns
                $pdoReturn = $pdoConnect->prepare("INSERT INTO tblreturns (purchasetime, invoiceNO, productcode, description, quantity, itemtotal, discount) VALUES (:purchasetime, :InvoiceNO, :ProductCode, :Description, :Quantity, :ItemTotal, :Discount)");
                foreach($pdoItems as $item)  
                    {  
                        $pdoReturn->execute(
                            array(
                                'purchasetime'  =>     $_POST["time"],
                                'InvoiceNO'     =>     $item["invoiceNO"],
                                'ProductCode'   =>     $item['productcode'],
                                'Description'   =>     $item['description'],
                                'Quantity'      =>     $item['quantity'],
                                'ItemTotal'     =>     $item['itemtotal'],
                                'Discount'      =>     number_format((float)$item["discount"], 2, '.', '')
                            )
                        );
                    }

                //Delete invoice         
                $pdoSalesInvoice = $pdoConnect->prepare("DELETE FROM tblsalesinvoice WHERE invoiceNO='". $_POST["InvoiceNO"]."'");  
                $pdoSalesInvoice->execute();

                //Delete sales
                $pdoSales = $pdoConnect->prepare("DELETE FROM tblsales WHERE invoiceNO='". $_POST["InvoiceNO"]."'"); 
                $pdoSales->execute();

                $message = "Return successful"; 
                $details = "Invoice return: invoice number: ".$_POST["InvoiceNO"].", reason: ".$_POST["Reason"];              
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_POST["InvoiceReturn"]);   
                
            }                
            catch(PDOException $error) {  
                $cartmessage = $error->getMessage();}
        }

        //Item return
        if(isset($_POST["ItemReturn"]))  
        {   
            try
            {   
                if(isset($_POST["Returned"]))
                {
                    //Adjust quantity
                    $pdoQuantity = $pdoConnect->prepare("UPDATE tblproducts SET quantity=quantity+:Quantity WHERE productcode=:ProductCode");
                    $pdoQuantity->execute(
                        array(
                            'ProductCode'   =>     $_POST['ProductCode'],
                            'Quantity'      =>     $_POST['Quantity']
                        )
                    );
                }

                //Move to tblreturns
                $pdoReturn = $pdoConnect->prepare("INSERT INTO tblreturns (purchasetime, invoiceNO, productcode, description, quantity, itemtotal, discount) VALUES (:purchasetime, :InvoiceNO, :ProductCode, :Description, :Quantity, :ItemTotal, :Discount)");
                $pdoReturn->execute(
                    array(
                        'purchasetime'  =>     $_POST["time"],
                        'InvoiceNO'     =>     $_POST["InvoiceNO"],
                        'ProductCode'   =>     $_POST['ProductCode'],
                        'Description'   =>     $_POST['Description'],
                        'Quantity'      =>     $_POST['Quantity'],
                        'ItemTotal'     =>     $_POST['ItemTotal'],
                        'Discount'      =>     number_format((float)$_POST["Discount"], 2, '.', '')
                    )
                );

                //Delete sales
                $pdoSales = $pdoConnect->prepare("DELETE FROM tblsales WHERE saleNO='". $_POST["SaleNO"]."'"); 
                $pdoSales->execute();

                //Check if invoice is empty
                $pdoItems = $pdoConnect->prepare("SELECT * FROM tblsales WHERE invoiceNO='". $_POST["InvoiceNO"]."'"); 
                $pdoItems->execute();
                if($pdoItems->rowCount() > 0)  
                {                 
                    //Adjust invoice         
                    $pdoSalesInvoice = $pdoConnect->prepare("UPDATE tblsalesinvoice SET discount=discount-:Discount, total=total-:Total, billchange=billchange+:Total WHERE invoiceNO='". $_POST["InvoiceNO"]."'");  
                    $pdoSalesInvoice->execute(
                        array(
                            'Discount'      =>     number_format((float)$_POST["Discount"], 2, '.', ''),
                            'Total'         =>     number_format((float)$_POST["ItemTotal"]+$_POST["Discount"], 2, '.', ''),                            
                        )
                    );
                }
                else
                {
                    //Delete invoice         
                    $pdoSalesInvoice = $pdoConnect->prepare("DELETE FROM tblsalesinvoice WHERE invoiceNO='". $_POST["InvoiceNO"]."'");  
                    $pdoSalesInvoice->execute();
                }
                
                $message = "Return successful"; 
                $details = "Item return: invoice number: ".$_POST["InvoiceNO"].", reason: ".$_POST["Reason"];              
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                ); 
                unset($_POST["ItemReturn"]);  
            }                
            catch(PDOException $error) {  
                $cartmessage = $error->getMessage();}
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
        <title>Returns</title>
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
                        <button name="InvoiceTab">Invoice</button>  
                        <button name="ItemTab">Item</button> 
                    </form>
                    <form method="get"> 
                        <input type="search" name="Search" placeholder="Search" value="<?php echo isset($_SESSION['Search']) ? $_SESSION['Search'] : ''; ?>"> 
                        <select name="Filter">
                            <option value = "All" <?php echo isset($_SESSION['Filter']) ? '' : 'selected'; ?>>All Time</option>
                            <option value = "<?php echo date('Y-m-d 00:00:00')?>" <?php echo (isset($_SESSION['Filter']) && $_SESSION['Filter']==date(date('Y-m-d 00:00:00'))) ? 'selected' : ''; ?>>Today</option>
                            <option value = "<?php echo date('Y-m-d', strtotime('monday this week'))?>" <?php echo (isset($_SESSION['Filter']) && $_SESSION['Filter']==date('Y-m-d', strtotime('monday this week'))) ? 'selected' : ''; ?>>This Week</option>
                            <option value = "<?php echo date('Y-m-01 00:00:00')?>" <?php echo (isset($_SESSION['Filter']) && $_SESSION['Filter']==date('Y-m-01 00:00:00')) ? 'selected' : ''; ?>>This Month</option>
                            <option value = "<?php echo date('Y-01-01 00:00:00')?>" <?php echo (isset($_SESSION['Filter']) && $_SESSION['Filter']==date('Y-01-01 00:00:00')) ? 'selected' : ''; ?>>This Year</option>
                        </select>
                        <button name="Load">Load Data</button> 
                    </form>
                </div>     

                <!--- sales display -->
                <table class="table">

                <?php

                    //Invoice tab
                    if(!isset($_SESSION["ItemTab"]))
                    {
                        echo '<tr>
                            <th>#</th><th>Invoice NO</th><th>Time</th><th>Bill</th><th>Billchange</th><th>Total</th><th>Discount</th><th>Details</th>
                        </tr>';    
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

                        foreach ($pdoDisplay as $invoice) 
                        {                                           
                            $num++;

                            if(isset($_POST["Return"]) && $invoice['invoiceNO']==$_POST["invoiceNO"])  
                            { 
                                echo   '<tr class="crud">
                                    <td>'.$num.'</td>
                                    <td>'.$invoice['invoiceNO'].'</td>
                                    <td>'.$invoice['time'].'</td>
                                    <td>'.$invoice['bill'].'</td>
                                    <td>'.$invoice['billchange'].'</td>
                                    <td>'.$invoice['total'].'</td>
                                    <td>'.$invoice['discount'].'</td>
                                    <td>
                                    <form method="post">
                                        <button name="Cancel">Cancel</button>
                                        </td></tr>  
                                        <input type="hidden" name="InvoiceNO" value="'.$invoice["invoiceNO"].'">
                                        <input type="hidden" name="time" value="'.$invoice["time"].'">
                                        <input type="hidden" name="Discount" value="'.$invoice["discount"].'">
                                        <tr class="crud"><td></td>
                                        <td colspan=6>
                                        <input type="checkbox" name="Returned" checked><label for="Returned">Return all items to inventory</label>
                                        <input type="text" name="Reason" placeholder="Reason">
                                        </td><td>
                                        <button name="InvoiceReturn">Return</button>                                                                
                                    </form>
                                    </td>
                                    </tr>';
                            }
                            else
                            {
                                echo   '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$invoice['invoiceNO'].'</td>
                                    <td>'.$invoice['time'].'</td>
                                    <td>'.$invoice['bill'].'</td>
                                    <td>'.$invoice['billchange'].'</td>
                                    <td>'.$invoice['total'].'</td>
                                    <td>'.$invoice['discount'].'</td>
                                    <td>
                                    <form method="post"> 
                                        <input type="hidden" name="invoiceID" value="'.$invoice["invoiceID"].'">
                                        <input type="hidden" name="invoiceNO" value="'.$invoice['invoiceNO'].'">
                                        <input type="submit" name="InvoiceDetails" value="v">
                                        <button name="Return">Return</button>                                                                
                                    </form>
                                    </td>
                                    </tr>'; 

                                if(isset($_POST["InvoiceDetails"]) && $invoice['invoiceID']==$_POST["invoiceID"])
                                {
                                    $pdoDetails = $pdoConnect->prepare("SELECT * FROM tblsales WHERE invoiceNO='".$_POST["invoiceNO"]."'");
                                    $pdoDetails->execute();
                                    foreach ($pdoDetails as $item) 
                                    {
                                        echo  '<tr class="crud">
                                            <td></td>
                                            <td>'.$item['productcode'].'</td>
                                            <td colspan=2>'.$item['description'].'</td>
                                            <td>'.$item['quantity'].'</td>
                                            <td>'.$item['itemtotal'].'</td>
                                            <td>'.$item['discount'].'</td>
                                            <td></td>
                                            </tr>';
                                    }
                                }


                            }             
                        }
                    }

                    //Item tab
                    else
                    {
                        echo '<tr>
                            <th>#</th><th>Invoice NO</th><th>Time</th><th>Product Code</th><th>Description</th><th>Quantity</th><th>Item Total</th><th>Discount</th><th>Options</th>
                        </tr>'; 
                        if(isset($message))  
                        {  
                            echo    '<tr class="crud">
                                    <td></td>
                                        <td colspan=8>
                                        <label>'.$message.'</label>
                                        </td>
                                    </tr>';
                        }
                
                        $pdoDisplay = $pdoConnect->prepare($Display);
                        $pdoDisplay->execute();
                        $num=0;                

                        foreach ($pdoDisplay as $item) 
                        {                                           
                            $num++;

                            if(isset($_POST["Return"]) && $item['saleNO']==$_POST["SaleNO"])  
                            { 
                                echo   '<tr class="crud">
                                    <td>'.$num.'</td>
                                    <td>'.$item['invoiceNO'].'</td>
                                    <td>'.$item['time'].'</td>
                                    <td>'.$item['productcode'].'</td>
                                    <td>'.$item['description'].'</td>
                                    <td>'.$item['quantity'].'</td>
                                    <td>'.$item['itemtotal'].'</td>
                                    <td>'.$item['discount'].'</td>
                                    <td>
                                    <form method="post">
                                        <button name="Cancel">Cancel</button>
                                        </td></tr>  
                                        <input type="hidden" name="SaleNO" value="'.$item["saleNO"].'">
                                        <input type="hidden" name="time" value="'.$item["time"].'">
                                        <input type="hidden" name="InvoiceNO" value="'.$item["invoiceNO"].'">
                                        <input type="hidden" name="ProductCode" value="'.$item["productcode"].'">
                                        <input type="hidden" name="Description" value="'.$item["description"].'">
                                        <input type="hidden" name="Quantity" value="'.$item["quantity"].'">
                                        <input type="hidden" name="ItemTotal" value="'.$item["itemtotal"].'">
                                        <input type="hidden" name="Discount" value="'.$item["discount"].'">
                                        <tr class="crud"><td></td>
                                        <td colspan=7>
                                        <input type="checkbox" name="Returned" checked><label for="Returned">Return all items to inventory</label>
                                        <input type="text" name="Reason" placeholder="Reason">
                                        </td><td>
                                        <button name="ItemReturn">Return</button>                                                                
                                    </form>
                                    </td>
                                    </tr>';
                            }
                            else
                            {
                                echo   '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$item['invoiceNO'].'</td>
                                    <td>'.$item['time'].'</td>
                                    <td>'.$item['productcode'].'</td>
                                    <td>'.$item['description'].'</td>
                                    <td>'.$item['quantity'].'</td>
                                    <td>'.$item['itemtotal'].'</td>
                                    <td>'.$item['discount'].'</td>
                                    <td>
                                    <form method="post"> 
                                        <input type="hidden" name="SaleNO" value="'.$item["saleNO"].'">
                                        <button name="Return">Return</button>                                                                
                                    </form>
                                    </td>
                                    </tr>'; 
                            }             
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
        
        <!--- footer --->
        <div class="footer"> 
            <?php  
                
            ?>
        </div>   

    </body>  
</html> 