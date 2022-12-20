<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    {  
        //for navigation.................................................................................................
        //Supplier tab
        if(isset($_GET["Supplier"])) {
            unset($_SESSION["Category"]);}

        //Category tab 
        if(isset($_GET["Category"])) {
            $_SESSION["Category"]=TRUE;}

        //Cancel in supplier tab
        if(!isset($_SESSION["Category"]) && isset($_POST["Cancel"])) {
            unset($_GET["AddSupplier"]);
            unset($_GET["EditSupplier"]);
            unset($_GET["DeleteSupplier"]);}

        //Cancel in category tab
        if(isset($_SESSION["Category"]) && isset($_POST["Cancel"])) {
            unset($_GET["AddCategory"]);
            unset($_GET["EditCategory"]);
            unset($_GET["DeleteCategory"]);}


        //for functions.................................................................................................
        //For activity log
        $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");

        //Add/edit supplier
        if(isset($_POST["AddSupplier"]) || isset($_POST["EditSupplier"])) 
        {  
            try
            {   
                //Add supplier
                if(isset($_POST["AddSupplier"])) {
                    $query = "INSERT INTO tblsuppliers (name, person, phone, email) VALUES (:Name, :Person, :Phone, :Email)";
                    $details = "Supplier added: ".$_POST["Name"];
                    $message = 'Supplier was added successfully';
                    unset($_GET["AddSupplier"]);}
                //Edit supplier
                elseif (isset($_POST["EditSupplier"])) {
                    $query = "UPDATE tblsuppliers SET name=:Name, person=:Person, phone=:Phone, email=:Email WHERE supplierID='".$_POST["SupplierID"]."'";
                    $details = "Supplier edited: ".$_POST["Name"];
                    $message = 'Supplier was edited successfully';
                    unset($_GET["EditSupplier"]);}

                $pdoAddEdit = $pdoConnect->prepare($query);  
                $pdoAddEdit->execute(
                    array(
                        'Name'       =>     $_POST["Name"],
                        'Person'     =>     $_POST["Person"],
                        'Phone'      =>     $_POST["Phone"],
                        'Email'      =>     $_POST["Email"]

                    )
                );                                
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                ); 
                
            }                
            catch(PDOException $error) {  
                $message = $error->getMessage();}
              
        }

        //Delete supplier
        if(isset($_POST["DeleteSupplier"]))
        {
            try
            {
                $pdoDelete = $pdoConnect->prepare("DELETE FROM tblsuppliers WHERE supplierID='".$_POST['SupplierID']."'");  
                $pdoDelete->execute();
                $message = 'Supplier was deleted successfully';
                $details = "Supplier deleted: ".$_POST["Name"];
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_GET["DeleteSupplier"]);
                
            }
            catch(PDOException $error) {  
                $message = $error->getMessage();}            
        }  


        
        //Add/edit category
        if(isset($_POST["AddCategory"]) || isset($_POST["EditCategory"])) 
        {  
            try
            {   
                //Add category
                if(isset($_POST["AddCategory"])) {
                    $query = "INSERT INTO tblcategory (description) VALUES (:Description)";
                    $details = "Category added: ".$_POST["Description"];
                    $message = 'Category was added successfully';
                    unset($_GET["AddCategory"]);}
                //Edit category
                elseif (isset($_POST["EditCategory"])) {
                    $query = "UPDATE tblcategory SET description=:Description WHERE categoryID='".$_POST['CategoryID']."'";
                    $details = "Category edited: ".$_POST["Description"];
                    $message = 'Category was edited successfully';
                    unset($_GET["EditCategory"]);}

                $pdoAddEdit = $pdoConnect->prepare($query);  
                $pdoAddEdit->execute(
                    array(
                        'Description'       =>     $_POST["Description"]
                    )
                );                                
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                    
                
            }                
            catch(PDOException $error) {  
                $message = $error->getMessage();}
              
        }

        //Delete category
        if(isset($_POST["DeleteCategory"]))
        {
            try
            {
                $pdoDelete = $pdoConnect->prepare("DELETE FROM tblcategory WHERE categoryID='".$_POST['CategoryID']."'");  
                $pdoDelete->execute();
                $message = 'Category was deleted successfully';
                $details = "Category deleted: ".$_POST["Description"];
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                unset($_GET["DeleteCategory"]);
                
            }
            catch(PDOException $error) {  
                $message = $error->getMessage();}            
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
        <title>Supplier & Category</title>
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
                        <button name="Supplier">Supplier</button>
                        <button name="Category">Category</button>
                    </form>
                </div>     

                <table class="table">
                <?php
                    //show supplier table
                    if(!isset($_SESSION["Category"]))
                    {
                        echo    '<tr>
                                    <th>#</th><th>Name</th><th>Person</th><th>Phone</th><th>Email</th><th>Options</th>
                                </tr>';
                        if(isset($message))  
                        {  
                            echo    '<tr class="crud">
                                    <td></td>
                                        <td colspan=5>
                                        <label>'.$message.'</label>
                                        </td>
                                    </tr>';
                        }
                        if(!isset($_GET["AddSupplier"]))  
                        {  
                            echo    '<tr class="crud">
                                    <td></td>
                                        <td colspan=5>
                                        <form method="get"> 
                                            <button name="AddSupplier">Add</button>                                                                   
                                        </form>
                                        </td>
                                    </tr>';
                        }

                        //click on add
                        if(isset($_GET["AddSupplier"]))
                        {
                            echo    '<tr class="crud">
                                    <td></td>
                                    <form method="post">
                                        <td><input type="text" name="Name" placeholder="Name" required></td>
                                        <td><input type="text" name="Person" placeholder="Person"></td>
                                        <td><input type="text" name="Phone" placeholder="Phone"></td>
                                        <td><input type="text" name="Email" placeholder="Email"></td>
                                        <td>
                                        <button name="Cancel" form="cancel">Cancel</button>
                                        <button name="AddSupplier">Add</button>
                                        </td>
                                    </form>
                                    <form method="post" id="cancel"></form>
                                    </tr>';
                        }


                        $pdoDisplay = $pdoConnect->prepare("SELECT * FROM tblsuppliers ORDER BY supplierID");  
                        $pdoDisplay->execute();
                        $num=0;
                        foreach ($pdoDisplay as $supplier) { 
                            $num++;                                          
                            
                            //click on edit
                            if(isset($_GET["EditSupplier"]) && $supplier["supplierID"]==$_GET["SupplierID"])
                            {
                                echo    '<tr class="crud">
                                        <td></td>
                                        <form method="post">
                                            <td><input type="text" name="Name" placeholder="Name" value="'.$supplier['name'].'" required></td>
                                            <td><input type="text" name="Person" placeholder="Person" value="'.$supplier['person'].'"></td>
                                            <td><input type="text" name="Phone" placeholder="Phone" value="'.$supplier['phone'].'"></td>
                                            <td><input type="text" name="Email" placeholder="Email" value="'.$supplier['email'].'"></td>
                                            <td>
                                            <input type="hidden" name="SupplierID" value="'.$supplier['supplierID'].'">
                                            <button name="Cancel" form="cancel">Cancel</button>
                                            <button name="EditSupplier">Save</button>
                                            </td>
                                        </form>
                                        <form method="post" id="cancel"></form>
                                        </tr>';
                            }
                            //click on delete
                            elseif(isset($_GET["DeleteSupplier"]) && $supplier["supplierID"]==$_GET["SupplierID"])
                            {
                                echo    '<tr class="crud"><td></td>
                                        <td colspan=6>
                                        <label>Are you sure you want to delete this supplier?</label>
                                        </td></tr>
                                        <tr class="crud"><td></td>
                                        <td>'.$supplier['name'].'</td>
                                        <td>'.$supplier['person'].'</td>
                                        <td>'.$supplier['phone'].'</td>
                                        <td>'.$supplier['email'].'</td>
                                        <td>
                                        <form method="post" class="display">
                                            <input type="hidden" name="SupplierID" value="'.$supplier['supplierID'].'">
                                            <input type="hidden" name="Name" value="'.$supplier['name'].'">
                                            <button name="Cancel" form="cancel">Cancel</button>
                                            <button name="DeleteSupplier">Yes</button>
                                        </form>
                                        <form method="post" id="cancel"></form>
                                        </td></tr>';
                            }
                            else
                            {
                                echo   '<tr>
                                        <td>'.$num.'</td>
                                        <td>'.$supplier['name'].'</td>
                                        <td>'.$supplier['person'].'</td>
                                        <td>'.$supplier['phone'].'</td>
                                        <td>'.$supplier['email'].'</td>
                                        <td>
                                        <form method="get"> 
                                            <input type="hidden" name="SupplierID" value="'.$supplier["supplierID"].'">
                                            <button name="EditSupplier">Edit</button> 
                                            <button name="DeleteSupplier">Delete</button>                                                                   
                                        </form>
                                        </td>
                                        </tr>'; 
                            }
                        }

                    }
                    //show category tab
                    else
                    {
                        echo    '<tr>
                                    <th>#</th><th>Description</th><th>Options</th>
                                </tr>';
                        if(isset($message))  
                        {  
                            echo    '<tr class="crud">
                                    <td></td>
                                        <td colspan=5>
                                        <label>'.$message.'</label>
                                        </td>
                                    </tr>';
                        }
                        if(!isset($_GET["AddCategory"]))  
                        {  
                            echo    '<tr class="crud">
                                    <td></td>
                                        <td colspan=5>
                                        <form method="get"> 
                                            <button name="AddCategory">Add</button>                                                                   
                                        </form>
                                        </td>
                                    </tr>';
                        }

                        //click on add
                        if(isset($_GET["AddCategory"]))
                        {
                            echo    '<tr class="crud">
                                    <td></td>
                                    <form method="post">
                                        <td><input type="text" name="Description" placeholder="Description"></td>
                                        <td>
                                        <button name="Cancel" form="cancel">Cancel</button>
                                        <button name="AddCategory">Add</button>
                                        </td>
                                    </form>
                                    <form method="post" id="cancel"></form>
                                    </form>
                                    </tr>';
                        }


                        $pdoDisplay = $pdoConnect->prepare("SELECT * FROM tblcategory ORDER BY categoryID");  
                        $pdoDisplay->execute();
                        $num=0;
                        foreach ($pdoDisplay as $category) {
                            $num++;                                           
                            
                            //click on edit
                            if(isset($_GET["EditCategory"]) && $category["categoryID"]==$_GET["CategoryID"])
                            {
                                echo    '<tr class="crud">
                                        <td></td>
                                        <form method="post">
                                            <td><input type="text" name="Description" placeholder="Description" value="'.$category['description'].'"></td>
                                            <td>
                                            <input type="hidden" name="CategoryID" value="'.$category['categoryID'].'">
                                            <button name="Cancel" form="cancel">Cancel</button>
                                            <button name="EditCategory">Save</button>
                                            </td>
                                        </form>
                                        <form method="post" id="cancel"></form>
                                        </tr>';
                            }
                            //click on delete
                            elseif(isset($_GET["DeleteCategory"]) && $category["categoryID"]==$_GET["CategoryID"])
                            {
                                echo    '<tr class="crud"><td></td>
                                        <td colspan=6>
                                        <label>Are you sure you want to delete this category?</label>
                                        </td></tr>
                                        <tr class="crud"><td></td>
                                        <td>'.$category['description'].'</td>
                                        <td>
                                        <form method="post" class="display">
                                            <input type="hidden" name="CategoryID" value="'.$category['categoryID'].'">
                                            <input type="hidden" name="Description" value="'.$category['description'].'">
                                            <button name="Cancel" form="cancel">Cancel</button>
                                            <button name="DeleteCategory">Yes</button>
                                        </form>
                                        <form method="post" id="cancel"></form>
                                        </td>
                                        </tr>';
                            }
                            else
                            {
                                echo   '<tr>
                                        <td>'.$num.'</td>
                                        <td>'.$category['description'].'</td>
                                        <td>
                                        <form method="get"> 
                                            <input type="hidden" name="CategoryID" value="'.$category["categoryID"].'">
                                            <button name="EditCategory">Edit</button> 
                                            <button name="DeleteCategory">Delete</button>                                                                   
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
        </div>
        
        <!--- footer --->
        <div class="footer"> 
            <?php  
                
            ?>
        </div>   

    </body>  
</html> 

            