<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    { 
        //tabs
       if(isset($_GET["TopTab"]) || isset($_GET["SalesTab"]) || isset($_GET["InvoiceTab"]) || isset($_GET["StockTab"]) || isset($_GET["ReturnsTab"]) || isset($_GET["DiscountsTab"]) || isset($_GET["PromotionsTab"])) 
       {
            //Top selling tab - default tab
            unset($_SESSION["Search"]);
            unset($_SESSION["Filter"]);
            unset($_GET["Load"]);
            unset($_SESSION["SalesTab"]);
            unset($_SESSION["InvoiceTab"]);
            unset($_SESSION["StockTab"]);
            unset($_SESSION["ReturnsTab"]);
            unset($_SESSION["DiscountsTab"]);
            unset($_SESSION["PromotionsTab"]);
            //Sales tab 
            if(isset($_GET["SalesTab"])) {
                $_SESSION["SalesTab"]=TRUE;}
            //Invoice tab 
            elseif(isset($_GET["InvoiceTab"])) {
                $_SESSION["InvoiceTab"]=TRUE;}
            //Stock tab 
            elseif(isset($_GET["StockTab"])) {
                $_SESSION["StockTab"]=TRUE;}
            //Returns tab 
            elseif(isset($_GET["ReturnsTab"])) {
                $_SESSION["ReturnsTab"]=TRUE;}
            //Discounts tab 
            elseif(isset($_GET["DiscountsTab"])) {
                $_SESSION["DiscountsTab"]=TRUE;}
            //Promotions tab 
            elseif(isset($_GET["PromotionsTab"])) {
                $_SESSION["PromotionsTab"]=TRUE;}
                
        } 
        

        if(isset($_SESSION["InvoiceTab"]))
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
        elseif(isset($_SESSION["ReturnsTab"]))
        {
            if (!isset($_GET["Load"])) {
                $Display = "SELECT * FROM tblreturns ORDER BY returntime DESC";}
            //Filter
            else {
                if($_GET["Filter"]!="All") {
                    $Display = empty($_GET["Search"]) ? "SELECT * FROM tblreturns WHERE returntime >= '".$_GET["Filter"]."' ORDER BY returntime DESC" : "SELECT * FROM tblreturns WHERE returntime >= '".$_GET["Filter"]."' AND (invoiceNO LIKE '%".$_GET["Search"]."%' OR productcode LIKE '%".$_GET["Search"]."%' OR description LIKE '%".$_GET["Search"]."%') ORDER BY returntime DESC";;
                    !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]=""; $_SESSION["Filter"]=$_GET["Filter"];}
                else {
                    $Display = empty($_GET["Search"]) ? "SELECT * FROM tblreturns ORDER BY returntime DESC" : "SELECT * FROM tblreturns WHERE (invoiceNO LIKE '%".$_GET["Search"]."%' OR productcode LIKE '%".$_GET["Search"]."%' OR description LIKE '%".$_GET["Search"]."%') ORDER BY returntime DESC";;
                    !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]="";
                    unset($_SESSION["Filter"]);}
            }
        }
        elseif(isset($_SESSION["StockTab"]))
        {
            if (!isset($_GET["Load"])) {
                $Display = "SELECT * FROM tblstockinvoice ORDER BY time DESC";}
            //Filter
            else {
                if($_GET["Filter"]!="All") {
                    $Display = empty($_GET["Search"]) ? "SELECT * FROM tblstockinvoice WHERE time >= '".$_GET["Filter"]."' ORDER BY time DESC" : "SELECT * FROM tblstockinvoice WHERE time >= '".$_GET["Filter"]."' AND (referenceNO LIKE '%".$_GET["Search"]."%' OR total LIKE '%".$_GET["Search"]."%' OR supplier LIKE '%".$_GET["Search"]."%') ORDER BY time DESC";;
                    !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]=""; $_SESSION["Filter"]=$_GET["Filter"];}
                else {
                    $Display = empty($_GET["Search"]) ? "SELECT * FROM tblstockinvoice ORDER BY time DESC" : "SELECT * FROM tblstockinvoice WHERE (referenceNO LIKE '%".$_GET["Search"]."%' OR total LIKE '%".$_GET["Search"]."%' OR supplier LIKE '%".$_GET["Search"]."%') ORDER BY time DESC";;
                    !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]="";
                    unset($_SESSION["Filter"]);}
            }
        }
        else
        {
            if (!isset($_GET["Load"])) {
                $Display = "SELECT tblsales.*, tblsalesinvoice.time FROM tblsales INNER JOIN tblsalesinvoice WHERE tblsales.invoiceNO=tblsalesinvoice.invoiceNO";
                isset($_SESSION["DiscountsTab"])? $Display .= " AND tblsales.discount > 0 " : "";}
            //Filter
            else {
                $Display = "SELECT tblsales.*, tblsalesinvoice.time FROM tblsales INNER JOIN tblsalesinvoice WHERE tblsales.invoiceNO=tblsalesinvoice.invoiceNO";
                isset($_SESSION["DiscountsTab"])? $Display .= " AND tblsales.discount > 0 " : "";
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
        
    }
    else  
    {  
        header("location:../index.php");  
    }  

 ?> 


<!DOCTYPE html>  
<html>  
    <head>  
        <title>Records</title>
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
                        <button name="TopTab">Top Selling</button>  
                        <button name="SalesTab">Sales</button> 
                        <button name="InvoiceTab">Invoice</button> 
                        <button name="ReturnsTab">Returns</button>
                        <button name="StockTab">Stock</button>
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

                <!--- records display -->
                <table class="table">

                <?php
                
                    //Sales tab & Discount tab
                    if(isset($_SESSION["SalesTab"]) || isset($_SESSION["DiscountsTab"]))
                    {
                        echo '<tr>
                            <th>#</th><th>Invoice NO</th><th>Time</th><th>Product Code</th><th>Description</th><th>Quantity</th><th>Item Total</th><th>Discount</th>
                        </tr>';                                         
                        $pdoDisplay = $pdoConnect->prepare($Display);
                        $pdoDisplay->execute();
                        $num=0;
                        foreach ($pdoDisplay as $item) 
                        {                                           
                            $num++;
                            echo   '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$item['invoiceNO'].'</td>
                                    <td>'.$item['time'].'</td>
                                    <td>'.$item['productcode'].'</td>
                                    <td>'.$item['description'].'</td>
                                    <td>'.$item['quantity'].'</td>
                                    <td>'.$item['itemtotal'].'</td>
                                    <td>'.$item['discount'].'</td>
                                    </tr>';                                         
                        }
                    }
                    
                    //Invoice tab
                    elseif(isset($_SESSION["InvoiceTab"]))
                    {
                        echo '<tr>
                            <th>#</th><th>Invoice NO</th><th>Time</th><th>Bill</th><th>Billchange</th><th>Total</th><th>Discount</th><th>Details</th>
                        </tr>';                 
                        $pdoDisplay = $pdoConnect->prepare($Display);
                        $pdoDisplay->execute();
                        $num=0;                
                        foreach ($pdoDisplay as $invoice) 
                        {                                           
                            $num++;
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
                                    <input type="hidden" name="invoiceID" value="'.$invoice['invoiceID'].'">
                                    <input type="hidden" name="invoiceNO" value="'.$invoice['invoiceNO'].'">
                                    <input type="submit" name="InvoiceDetails" value="v">                                                              
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

                    //Returns tab
                    elseif(isset($_SESSION["ReturnsTab"]))
                    {
                        echo '<tr>
                            <th>#</th><th>Purchase Time</th><th>Return Time</th><th>Invoice NO</th><th>Product</th><th>Description</th><th>Quantity</th><th>Item Total</th><th>Discount</th>
                        </tr>';                                         
                        $pdoDisplay = $pdoConnect->prepare($Display);
                        $pdoDisplay->execute();
                        $num=0;
                        foreach ($pdoDisplay as $item) 
                        {                                           
                            $num++;
                            echo   '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$item['purchasetime'].'</td>
                                    <td>'.$item['returntime'].'</td>
                                    <td>'.$item['invoiceNO'].'</td>
                                    <td>'.$item['productcode'].'</td>
                                    <td>'.$item['description'].'</td>
                                    <td>'.$item['quantity'].'</td>
                                    <td>'.$item['itemtotal'].'</td>
                                    <td>'.$item['discount'].'</td>
                                    </tr>';                                         
                        }
                    }

                    //Stock tab
                    elseif(isset($_SESSION["StockTab"]))
                    {
                        echo '<tr>
                            <th>#</th><th>Reference NO</th><th>Time</th><th>Supplier</th><th>Admin</th><th>Total</th><th>Details</th>
                        </tr>';                 
                        $pdoDisplay = $pdoConnect->prepare($Display);
                        $pdoDisplay->execute();
                        $num=0;                
                        foreach ($pdoDisplay as $stockinvoice) 
                        {                                           
                            $num++;
                            echo   '<tr>
                                <td>'.$num.'</td>
                                <td>'.$stockinvoice['referenceNO'].'</td>
                                <td>'.$stockinvoice['time'].'</td>
                                <td>'.$stockinvoice['supplier'].'</td>
                                <td>'.$stockinvoice['admin'].'</td>
                                <td>'.$stockinvoice['total'].'</td>
                                <td>
                                <form method="post"> 
                                    <input type="hidden" name="ReferenceNO" value="'.$stockinvoice['referenceNO'].'">
                                    <input type="submit" name="StockDetails" value="v">                                                              
                                </form>
                                </td></tr>'; 

                            if(isset($_POST["StockDetails"]) && $stockinvoice['referenceNO']==$_POST["ReferenceNO"])
                            {
                                $pdoDetails = $pdoConnect->prepare("SELECT * FROM tblstock WHERE referenceNO='".$_POST["ReferenceNO"]."'");
                                $pdoDetails->execute();
                                foreach ($pdoDetails as $stock) 
                                {
                                    echo  '<tr class="crud">
                                        <td></td>
                                        <td>'.$stock['productcode'].'</td>
                                        <td colspan=2>'.$stock['description'].'</td>
                                        <td>'.$stock['quantity'].'</td>
                                        <td>'.$stock['itemtotal'].'</td>
                                        <td></td>
                                        </tr>';
                                }
                            }
                        }
                    }

                    //Top selling tab
                    else
                    {                                                                
                        $pdoCount = $pdoConnect->prepare($Display);
                        $pdoCount->execute();
                        foreach ($pdoCount as $item) 
                        {                                           
                            $item_array = array(  
                                'ProductCode'       =>     $item["productcode"],  
                                'Description'       =>     $item["description"],   
                                'Quantity'          =>     $item["quantity"],
                                'ItemTotal'         =>     number_format((float)$item["itemtotal"], 2, '.', ''),
                                'Discount'          =>     number_format((float)$item["discount"], 2, '.', '')
                            );   
                            if(isset($_SESSION["TopSelling"]))  
                            {   
                                $item_array_id = array_column($_SESSION["TopSelling"], "ProductCode");  
                                if(in_array($item["productcode"], $item_array_id))  
                                {  
                                    foreach($_SESSION["TopSelling"] as $keys => $sale)  
                                    {  
                                        if($sale["ProductCode"] == $item["productcode"])  
                                        {  
                                            $_SESSION["TopSelling"][$keys]["Quantity"] += $item["quantity"];
                                            $_SESSION["TopSelling"][$keys]["ItemTotal"] += number_format((float)$item["itemtotal"], 2, '.', '');
                                            $_SESSION["TopSelling"][$keys]["Discount"] += number_format((float)$item["discount"], 2, '.', '');
                                        }  
                                    }  
                                }  
                                else  
                                {  
                                    $_SESSION["TopSelling"][count($_SESSION["TopSelling"])] =  $item_array;
                                }  
                            }
                            else  
                            {  
                                $_SESSION["TopSelling"][0] = $item_array;  
                            }                                    
                        }
                        //sort by quantity
                        $quantity = array_column($_SESSION["TopSelling"], "Quantity");
                        array_multisort($quantity, SORT_DESC, $_SESSION["TopSelling"]);

                        //display top selling
                        echo '<tr>
                            <th>Rank</th><th>Product Code</th><th>Description</th><th>Quantity Sold</th><th>Total Amount</th><th>Total Discount</th>
                        </tr>';
                        $num=0; 
                        foreach($_SESSION["TopSelling"] as $product)
                        {
                            $num++;
                            echo   '<tr>
                                <td>'.$num.'</td>
                                <td>'.$product['ProductCode'].'</td>
                                <td>'.$product['Description'].'</td>
                                <td>'.$product['Quantity'].'</td>
                                <td>'.number_format((float)$product["ItemTotal"], 2, '.', '').'</td>
                                <td>'.number_format((float)$product["Discount"], 2, '.', '').'</td>
                                </tr>'; 
                        }
                        unset($_SESSION["TopSelling"]);
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