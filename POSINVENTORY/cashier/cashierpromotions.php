<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    { 
        //Displays all
        $Display = "SELECT tblpromos.*,tblproducts.description as pdescription,tblproducts.price,tblproducts.quantity FROM tblpromos INNER JOIN tblproducts 
        WHERE tblpromos.product = tblproducts.productcode AND tblproducts.quantity !=0
            AND (tblpromos.periodstart<=NOW() AND (tblpromos.periodend>=NOW() OR tblpromos.periodend=0))
            AND (tblpromos.uselimit=0 OR (tblpromos.uselimit-tblpromos.used)>0)";

        //Search bar is used
        if(isset($_GET["Search"])) {
            $Display .= " AND (tblpromos.description LIKE '%".$_GET["Search"]."%' OR tblpromos.product LIKE '%".$_GET["Search"]."%' OR tblproducts.description LIKE '%".$_GET["Search"]."%')";
            $_SESSION["Search"] = $_GET["Search"];}
        elseif(isset($_SESSION["Search"])) {  
            $Display .= " AND (tblpromos.description LIKE '%".$_SESSION["Search"]."%' OR tblpromos.product LIKE '%".$_SESSION["Search"]."%' OR tblproducts.description LIKE '%".$_SESSION["Search"]."%')";}

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
                    <form method="get"> 
                        <input type="search" name="Search" placeholder="Search" value="<?php echo isset($_SESSION['Search']) ? $_SESSION['Search'] : ''; ?>"> 
                    </form>

                </div>     

                <!--- records display -->
                <table class="table">

                <?php  

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