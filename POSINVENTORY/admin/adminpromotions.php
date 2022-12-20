<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    { 
        //All products tab - default tab
        if(isset($_POST["DiscountsTab"]) || isset($_POST["AppliedTab"]) ||  isset($_POST["NewPromoProduct"]))
        {
            unset($_SESSION["AppliedTab"]);
            unset($_SESSION["NewPromoProduct"]);
            unset($_SESSION["Search"]);
            //New item discounts
            if(isset($_POST["NewPromoProduct"])) {
                $_SESSION["NewPromoProduct"]=TRUE;}
            //Applied discounts
            if(isset($_POST["AppliedTab"])) {
                $_SESSION["AppliedTab"]=TRUE;}
        }

        //Cancel
        if(isset($_POST["Cancel"])) {
            unset($_POST["NewPromoProduct"]);
            unset($_SESSION["NewPromoProduct"]);}

        if(isset($_SESSION["AppliedTab"]))
        {
            $Display = "SELECT tblpromos.*,tblproducts.description as pdescription,tblproducts.price,tblproducts.quantity FROM tblpromos INNER JOIN tblproducts 
                            WHERE tblpromos.product = tblproducts.productcode AND tblproducts.quantity !=0
                                AND (tblpromos.periodstart<=NOW() AND (tblpromos.periodend>=NOW() OR tblpromos.periodend=0))
                                AND (tblpromos.uselimit=0 OR (tblpromos.uselimit-tblpromos.used)>0)";
            //Search bar is used
            if(isset($_GET["Search"])) {
                $Display .= " AND (tblpromos.description LIKE '%".$_GET["Search"]."%' OR tblpromos.product LIKE '%".$_GET["Search"]."%' OR tblproducts.description LIKE '%".$_GET["Search"]."%')";
                $_SESSION["Search"] = $_GET["Search"];}
                
        }
        elseif(isset($_SESSION["NewPromoProduct"]))
        {
            $Display = "SELECT * FROM tblproducts";
            //Search bar is used
            if(isset($_GET["Search"])) {
                $Display .= " WHERE productcode LIKE '%".$_GET["Search"]."%' OR barcode LIKE '%".$_GET["Search"]."%' OR description LIKE '%".$_GET["Search"]."%' OR category LIKE '%".$_GET["Search"]."%'";
                $_SESSION["Search"] = $_GET["Search"];}
        }
        else
        {
            $Display = "SELECT * FROM tblpromos";
            //Search bar is used
            if(isset($_GET["Search"])) {
                $Display .= " WHERE (product LIKE '%".$_GET["Search"]."%' OR reward LIKE '%".$_GET["Search"]."%' OR description LIKE '%".$_GET["Search"]."%' )";
                $_SESSION["Search"] = $_GET["Search"];}
            $Display .= " ORDER BY periodstart DESC";
        }

        $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");
        
        //Save product promo
        if(isset($_POST["PromoProductDone"])) 
        { 
            try
            {
                $pdoPromo = $pdoConnect->prepare("INSERT INTO tblpromos (promotype, description, rewardtype, reward, product, uselimit, periodstart, periodend) VALUES (:promotype, :description, :rewardtype, :reward, :product, :uselimit, :periodstart, :periodend)");  
                //$pdoDiscount = $pdoConnect->prepare("UPDATE tblproducts SET discount=:discount WHERE productcode=:productcode"); 
                foreach($_SESSION["PromoProductCart"] as $keys => $item)  
                {  
                    $pdoPromo->execute(
                        array(
                            'promotype'         =>     $_POST["promotype"],
                            'description'       =>     $_POST["description"],
                            'rewardtype'        =>     $_POST["rewardtype"],
                            'reward'            =>     $_POST["reward"],                        
                            'uselimit'          =>     $_POST["uselimit"],
                            'periodstart'       =>     $_POST["periodstart"],
                            'periodend'         =>     $_POST["periodend"],
                            'product'           =>     $_SESSION["PromoProductCart"][$keys]["ProductCode"]
                        )
                    );
                }

                $details = "New item discount: ". $_POST["description"];
                $message = 'Item discount was saved';
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_SESSION["PromoProductCart"]);
                unset($_SESSION["Count"]);
                unset($_SESSION["PromoProductSave"]);
            }
            catch(PDOException $error) {  
                $message = $error->getMessage();}
        }

        //Edit product promo
        if(isset($_POST["EditPromoDone"])) 
        { 
            try
            {
                $pdoPromo = $pdoConnect->prepare("UPDATE tblpromos SET description=:description, rewardtype=:rewardtype, reward=:reward, uselimit=:uselimit, periodstart=:periodstart, periodend=:periodend WHERE promoID=:promoID"); 
                $pdoPromo->execute(
                    array(
                        'promoID'           =>     $_POST["promoID"],
                        'description'       =>     $_POST["description"],
                        'rewardtype'        =>     $_POST["rewardtype"],
                        'reward'            =>     $_POST["reward"],                        
                        'uselimit'          =>     $_POST["uselimit"],
                        'periodstart'       =>     $_POST["periodstart"],
                        'periodend'         =>     $_POST["periodend"]
                    )
                );
                $details = "Item discount edited: ". $_POST["description"];
                $message = 'Item discount was saved';
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_SESSION["EditPromo"]);
            }
            catch(PDOException $error) {  
                $message = $error->getMessage();}
        }

        //Delete product promo
        if(isset($_POST["DeletePromoDone"]))
        {
            try
            {
                $pdoPromo = $pdoConnect->prepare("DELETE FROM tblpromos WHERE promoID=:promoID");  
                $pdoPromo->execute(
                    array(
                        'promoID'       =>     $_POST["promoID"]
                    )
                );
                $details = "Item discount deleted: ". $_POST["description"];
                $message = 'Item discount was deleted';
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_POST["DeletePromoDone"]);

            }
            catch(PDOException $error) {  
                $message = $error->getMessage();}            
        }


        //Add item
        if(isset($_POST["PromoProductAdd"]))  
        {  
            $item_array = array(  
                'ProductCode'       =>     $_POST["ProductCode"],  
                'Description'       =>     $_POST["Description"],   
                'Price'             =>     $_POST["Price"],
                'Discount'          =>     number_format((float) isset($_SESSION['PromoProductSave']) ? ($_SESSION['PromoProductSave']['rewardtype']=='Php' ? $_SESSION['PromoProductSave']['reward'] : $_POST['Price']*($_SESSION['PromoProductSave']['reward']/100)) : 0 , 2, '.', ''),
                'DiscountedPrice'   =>     number_format((float) isset($_SESSION['PromoProductSave']) ? ($_SESSION['PromoProductSave']['rewardtype']=='Php' ? $_POST['Price']-$_SESSION['PromoProductSave']['reward'] : $_POST['Price']-($_POST['Price']*($_SESSION['PromoProductSave']['reward']/100))) : $_POST["Price"], 2, '.', '')
            ); 
            if(isset($_SESSION["PromoProductCart"]))  
            {  
                $item_array_id = array_column($_SESSION["PromoProductCart"], "ProductCode");  
                if(!in_array($_POST["ProductCode"], $item_array_id))  
                {
                    $_SESSION["Count"] += 1; 
                    $_SESSION["PromoProductCart"][$_SESSION["Count"]] = $item_array;  
                    $_SESSION["PromoProductCart"][$_SESSION["Count"]]["ID"] = $_SESSION["Count"]; 
                }
            }  
            else  
            {  
                $_SESSION["Count"] = 0;
                $_SESSION["PromoProductCart"][0] = $item_array; 
                $_SESSION["PromoProductCart"][0]["ID"] = 0; 
            }
            
        } 

        //Remove item
        if(isset($_POST["PromoProductRemove"]))  
        {
            foreach($_SESSION["PromoProductCart"] as $keys => $item)  
            {  
                if($item["ID"] == $_POST["ID"])  
                {  
                    unset($_SESSION["PromoProductCart"][$keys]);
                    $cartmessage='Item removed';  
                }
            }
        }
        
        //Clear items
        if(isset($_POST["PromoProductClear"]) || empty($_SESSION["PromoProductCart"]))  
        {
            unset($_SESSION["PromoProductCart"]);
            unset($_SESSION["Count"]);
            unset($_SESSION["PromoProductSave"]);
            $cartmessage='Items cleared';  
        }
        
        //Compute discount
        if(isset($_POST["ComputeDiscount"]))  
        {
            $item_array = array(
                'promotype'         =>     $_POST["promotype"],
                'description'       =>     $_POST["description"],
                'reward'            =>     $_POST["reward"],
                'rewardtype'        =>     $_POST["rewardtype"],
                'uselimit'          =>     $_POST["uselimit"],
                'periodstart'       =>     $_POST["periodstart"],
                'periodend'         =>     $_POST["periodend"]
            );
            $_SESSION["PromoProductSave"] = $item_array;
            foreach($_SESSION["PromoProductCart"] as $keys => $item)  
            {  
                $_SESSION["PromoProductCart"][$keys]["Discount"] = number_format((float) ($_POST["rewardtype"]=='Php' ? $_POST["reward"] : $_SESSION["PromoProductCart"][$keys]["Price"]*($_POST["reward"]/100)), 2, '.', '');
                $_SESSION["PromoProductCart"][$keys]["DiscountedPrice"] = number_format((float) ($_POST["rewardtype"]=='Php' ? $_SESSION["PromoProductCart"][$keys]["Price"]-$_POST["reward"] : $_SESSION["PromoProductCart"][$keys]["Price"]-($_SESSION["PromoProductCart"][$keys]["Price"]*($_POST["reward"]/100))), 2, '.', '');
                $cartmessage='Discounts applied';  
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
        <title>Promotions</title>
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
                        <button name="DiscountsTab">Item Discounts</button>  
                        <button name="AppliedTab">Applied Discounts</button>
                    </form>
                    <form method="get"> 
                        <input type="search" name="Search" placeholder="Search" value="<?php echo isset($_SESSION['Search']) ? $_SESSION['Search'] : ''; ?>"> 
                    </form>

                </div>     

                <!--- records display -->
                <table class="table">

                <?php                    

                    //NewPromoProduct
                    if(isset($_SESSION["NewPromoProduct"]))
                    {
                        echo   '<tr>
                                    <th>#</th><th>Product Code</th><th>Barcode</th><th>Description</th><th>Price</th><th>Category</th><th>Options</th>
                                </tr> ';
                        $pdoChoice = $pdoConnect->prepare($Display);  
                        $pdoChoice->execute(); 
                        $num=0;
                        foreach ($pdoChoice as $choice) 
                        {
                            //Item is already included
                            if(isset($_SESSION["PromoProductCart"])){
                                $item_array_id = array_column($_SESSION["PromoProductCart"], "ProductCode");  
                                if(in_array($choice['productcode'], $item_array_id)){
                                    continue;}}

                            $num++;
                            echo    '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$choice['productcode'].'</td>
                                    <td>'.$choice['barcode'].'</td>
                                    <td>'.$choice['description'].'</td>
                                    <td>'.$choice['price'].'</td>
                                    <td>'.$choice['category'].'</td>
                                    <td>
                                        <form method="post"> 
                                        <input type="hidden" name="ProductCode" value="'.$choice["productcode"].'">
                                        <input type="hidden" name="Description" value="'.$choice["description"].'">
                                        <input type="hidden" name="Price" value="'.$choice["price"].'">
                                        <button name="PromoProductAdd">Add</button>                                                                 
                                        </form>
                                    </td>
                                    </tr>';
                        }
                    }

                    elseif(isset($_SESSION["AppliedTab"]))
                    {
                        echo   '<tr>
                                    <th>#</th><th>Applied Discount</th><th>Product</th><th>Description</th><th>Price</th><th>Discount</th><th>Reward</th><th>Usable</th><th>Expiry</th>
                                </tr> ';
                        $pdoApplied = $pdoConnect->prepare($Display);  
                        $pdoApplied->execute();                         
                        $num=0;
                        foreach ($pdoApplied as $item) 
                        {
                            $discount = number_format((float) ($item["rewardtype"]=='Php' ? $item["reward"] : $item["price"]*($item["reward"]/100)), 2, '.', '');
                            $discounted = number_format((float) $item["price"]-$discount, 2, '.', '');
                            $num++;
                            echo    '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$item['description'].'</td>
                                    <td>'.$item['product'].'</td>
                                    <td>'.$item['pdescription'].'</td>
                                    <td>'.$item['price'].' -> '.$discounted.'</td>
                                    <td>'.$discount.'</td>
                                    <td>'.$item['reward'].''.$item['rewardtype'].' OFF</td>
                                    <td>'; echo $item['uselimit']==0 ? 'Infinite' : $item['uselimit']-$item['used']; echo'</td>
                                    <td>'; echo $item['periodend']==0 ? 'None' : $item['periodend']; echo'</td>
                                    </tr>';
                        }
                    }

                    //Item Discounts tab
                    else
                    {
                        echo '<tr>
                            <th>#</th><th>Product</th><th>Description</th><th>Reward</th><th>Start</th><th>End</th><th>Use Limit</th><th>Used</th><th>Option</th>
                        </tr>';  
                        
                        if(isset($message))  
                        {  
                            echo    '<tr class="crud">
                                    <td></td>
                                        <td colspan=9>
                                        '.$message.'
                                        </td>
                                    </tr>';
                        }
                        
                        echo    '<tr class="crud">
                                <td></td>
                                    <td colspan=9>
                                    <form method="post"> 
                                        <button name="NewPromoProduct">Add</button>                                                                 
                                    </form>
                                    </td>
                                </tr>';
                        $pdoDisplay = $pdoConnect->prepare($Display);
                        $pdoDisplay->execute();
                        $num=0;
                        foreach ($pdoDisplay as $promo) 
                        {
                            $num++;
                            //click on edit
                            if(isset($_POST["EditPromo"]) && $promo["promoID"]==$_POST["promoID"])
                            {                      

                                echo   '<tr class="crud"><td></td>
                                        <form method="post">
                                            <td>'.$promo['product'].'</td>
                                            <td><input type="text" name="description" placeholder="Description" value="'.$promo['description'].'" required></td>
                                            <td><input type="number" name="reward" min=1 placeholder="Discount Value" value="'.$promo['reward'].'" required>
                                            <select name="rewardtype">\r\n
                                                <option value="Php" '; echo $promo['rewardtype']=='Php' ? 'selected' : '' ; echo'>Php</option>\r\n
                                                <option value="%" '; echo $promo['rewardtype']=='%' ? 'selected' : '' ; echo'>%</option>\r\n
                                            </select></td>
                                            <td><input type="date" name="periodstart" min="'; echo $promo['periodstart']>date('Y-m-d') ? date('Y-m-d') : $promo['periodstart']; echo'" placeholder="Start" value="'.$promo['periodstart'].'" required></td>
                                            <td><input type="date" name="periodend" min="'.date('Y-m-d').'" placeholder="End" value="'; echo $promo['periodend']==0 ? '' : $promo['periodend']; echo'"></td>
                                            <td><input type="number" name="uselimit" min=1 placeholder="Use Limit" value="'; echo $promo['uselimit']==0 ? '' : $promo['uselimit']; echo'"></td>
                                            <td>'.$promo['used'].'</td>
                                            <input type="hidden" name="promoID" value="'.$promo["promoID"].'">
                                            <input type="hidden" name="product" value="'.$promo["product"].'">
                                            <td><button name="Cancel" form="cancel">Cancel</button> <button name="EditPromoDone">Done</button></td>
                                        </form> 
                                        <form method="post" id="cancel"></form>                                                                  
                                        </tr>'; 
                            }
                            elseif(isset($_POST["DeletePromo"]) && $promo["promoID"]==$_POST["promoID"])
                            {
                                echo    '<tr class="crud"><td></td>
                                        <td colspan=9>
                                        Are you sure you want to delete this discount?
                                        </td></tr>
                                        <tr class="crud"><td></td>
                                        <td>'.$promo['product'].'</td>
                                        <td>'.$promo['description'].'</td>
                                        <td>'.$promo['reward'].''.$promo['rewardtype'].' OFF</td>
                                        <td>'.$promo['periodstart'].'</td>
                                        <td>'; echo $promo['periodend']==0 ? 'None' : $promo['periodend']; echo'</td>
                                        <td>'; echo $promo['uselimit']==0 ? 'None' : $promo['uselimit']; echo'</td>
                                        <td>'.$promo['used'].'</td>
                                        <td>
                                        <form method="post" class="display">
                                            <input type="hidden" name="promoID" value="'.$promo["promoID"].'">
                                            <input type="hidden" name="description" value="'.$promo["description"].'">
                                            <button name="Cancel">Cancel</button> <button name="DeletePromoDone">Yes</button>
                                        </form>
                                        </td>
                                        </tr>';
                            }
                            else
                            {
                                echo   '<tr>
                                    <td>'.$num.'</td>
                                    <td>'.$promo['product'].'</td>
                                    <td>'.$promo['description'].'</td>
                                    <td>'.$promo['reward'].''.$promo['rewardtype'].' OFF</td>
                                    <td>'.$promo['periodstart'].'</td>
                                    <td>'; echo $promo['periodend']==0 ? 'None' : $promo['periodend']; echo'</td>
                                    <td>'; echo $promo['uselimit']==0 ? 'None' : $promo['uselimit']; echo'</td>
                                    <td>'.$promo['used'].'</td>
                                    <td>
                                    <form method="post">
                                        <input type="hidden" name="promoID" value="'.$promo["promoID"].'">
                                        <button name="EditPromo">Edit</button> <button name="DeletePromo">Delete</button>
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

            <!--- PromoProductCart --->
            <?php

                if(isset($_SESSION["NewPromoProduct"]))  
                {
                    echo '<div class="stockcart"> 
                            <table class="display">';
                    if(isset($message))  
                    {
                        echo '<tr><td colspan=5>
                                '.$message.'
                                </td></tr>';
                    }                    

                    if(isset($_SESSION["PromoProductCart"]))  
                    {                          
                        $num = 0;

                        //Display each item 
                        foreach($_SESSION["PromoProductCart"] as $keys => $item)  
                        {
                            $num++;
                            
                            echo '<tr>
                                <td>'.$num.'</td>
                                <td>'.$item['ProductCode'].'</td>
                                <td>'.$item['Description'].'</td>
                                <td>'.$item['Price'].' - '.$item['Discount'].' -> '.$item['DiscountedPrice'].'</td>                                                
                                <td>
                                <form method="post"> 
                                    <input type="hidden" name="ID" value="'.$item["ID"].'">
                                    <button name="PromoProductRemove">x</button>
                                </form>                                                                 
                                </td>
                                </tr>';

                        }

                        //Other details on the bottom part 
                        echo '<tr><td></td>
                                <form method="post">
                                    <td>Start:<input type="date" name="periodstart" min="'.date('Y-m-d').'" placeholder="Start" value="'; echo isset($_SESSION['PromoProductSave']) ? $_SESSION['PromoProductSave']['periodstart'] : date('Y-m-d'); echo'" required></td>
                                    <td><input type="text" name="description" placeholder="Description" value="'; echo isset($_SESSION['PromoProductSave']) ? $_SESSION['PromoProductSave']['description'] : ''; echo'" required></td>
                                    <td><input type="number" name="reward" min=1 placeholder="Discount Value" value="'; echo isset($_SESSION['PromoProductSave']) ? $_SESSION['PromoProductSave']['reward'] : ''; echo'" required>
                                    <select name="rewardtype">\r\n
                                        <option value="Php" '; echo isset($_SESSION['PromoProductSave']) ? ($_SESSION['PromoProductSave']['rewardtype']=='Php' ? 'selected' : '') : 'selected'; echo'>Php</option>\r\n
                                        <option value="%" '; echo isset($_SESSION['PromoProductSave']) ? ($_SESSION['PromoProductSave']['rewardtype']=='%' ? 'selected' : '') : ''; echo'>%</option>\r\n
                                    </select> OFF</td>
                                    <td></td></tr><tr><td></td>
                                    <td>End:<input type="date" name="periodend" placeholder="End" value="'; echo isset($_SESSION['PromoProductSave']) ? $_SESSION['PromoProductSave']['periodend'] : date('Y-m-d', strtotime('1 day')); echo'"></td>
                                    <td><input type="number" name="uselimit" min=1 placeholder="Use Limit" value="'; echo isset($_SESSION['PromoProductSave']) ? $_SESSION['PromoProductSave']['uselimit'] : ''; echo'"></td>
                                    <td><input type="submit" name="ComputeDiscount" value="Apply Discount"></td>
                                    <td></td></tr><tr><td colspan=6>
                                    <input type="hidden" name="promotype" value="Item Discount">
                                    <button name="PromoProductClear" form="clear">Clear</button>
                                    <button name="PromoProductDone">Done</button> 
                                </form>       
                                <form method="post" id="clear"></form>                                                         
                                </td></tr>';

                    }
                    else 
                    {
                        echo '<tr><td>
                                <label>Selected items will get discounts from this promo</label>
                                </td></tr><tr><td>
                                <form method="post">
                                    <button name="Cancel">Cancel</button>
                                </form></td></tr>';
                    }
                                       
                    echo '</table>
                    </div>';
                   
                }                       
            ?> 
        
        <!--- footer --->
        <div class="footer"> 
            <?php  
                
            ?>
        </div>   

    </body>  
</html> 