<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    { 

        $Display = "SELECT * FROM tblactivity ";
        //Filter
        if (isset($_GET["Load"])) {
            if($_GET["Filter"]!="All") {
                $Display .= empty($_GET["Search"]) ? " WHERE time >= '".$_GET["Filter"]."'" : " WHERE time >= '".$_GET["Filter"]."' AND (time LIKE '%".$_GET["Search"]."%' OR details LIKE '%".$_GET["Search"]."%' OR employeeID LIKE '%".$_GET["Search"]."%') ";;
                !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]=""; $_SESSION["Filter"]=$_GET["Filter"];}
            else {
                $Display .= empty($_GET["Search"]) ? "" : " WHERE (time LIKE '%".$_GET["Search"]."%' OR details LIKE '%".$_GET["Search"]."%' OR employeeID LIKE '%".$_GET["Search"]."%') ";;
                !empty($_GET["Search"]) ? $_SESSION["Search"]=$_GET["Search"] : $_SESSION["Search"]="";
                unset($_SESSION["Filter"]);}
        }
        $Display .= " ORDER BY time DESC";
        
    }
    else  
    {  
        header("location:../index.php");  
    }  

 ?> 


<!DOCTYPE html>  
<html>  
    <head>  
        <title>Activity Log</title>
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
                
                <?php //print( date('Y-m-d', strtotime('-1 day')) ) ?>
                <!--- top bar --->   
                <div class="tabs">    
                    
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
                
                    echo '<tr>
                        <th>#</th><th>Time</th><th>Details</th><th>Employee</th>
                    </tr>';                                         
                    $pdoDisplay = $pdoConnect->prepare($Display);
                    $pdoDisplay->execute();
                    $num=0;
                    foreach ($pdoDisplay as $item) 
                    {                                           
                        $num++;
                        echo   '<tr>
                                <td>'.$num.'</td>
                                <td>'.$item['time'].'</td>
                                <td>'.$item['details'].'</td>
                                <td>'.$item['employeeID'].'</td>
                                </tr>';                                         
                    }
                    unset($_SESSION["Search"]);
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