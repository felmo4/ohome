<?php 

    session_start();  
    require '../include/dbcon.php';

    if(isset($_SESSION["ID"]))  
    {  
        //for navigation.................................................................................................
        //Users tab
        if(isset($_GET["UsersTab"])) {
            unset($_SESSION["EmployeesTab"]);
            unset($_SESSION["ChangePassword"]);}

        //EmployeesTab tab 
        if(isset($_GET["EmployeesTab"])) {
            $_SESSION["EmployeesTab"]=TRUE;
            unset($_SESSION["ChangePassword"]);}

        //ChangePassword tab 
        if(isset($_GET["ChangePassword"])) {
            $_SESSION["ChangePassword"]=TRUE;
            unset($_SESSION["EmployeesTab"]);}

        //Cancel in category tab
        if(isset($_SESSION["EmployeesTab"]) && isset($_POST["Cancel"])) {
            unset($_GET["AddEmployee"]);
            unset($_GET["EditEmployee"]);
            unset($_GET["DeactivateEmployee"]);}


        //for functions.................................................................................................
        //For activity log
        $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");

        //Add user
        if(isset($_POST["AddUser"])) 
        {  
            $pdoExisting = $pdoConnect->prepare("SELECT username FROM tblusers WHERE username='".$_POST["username"]."'");
            $pdoExisting->execute();
            if($pdoExisting->rowCount() > 0)  
                {$_SESSION["NewMessage"] = 'Username is already taken';}
            elseif($_POST["password"]!=$_POST["confirmpassword"])
                {$_SESSION["NewMessage"] = 'Password do not match';}
            else
            {
                try
                {
                    $query = "INSERT INTO tblusers (employeeID, username, password, role) VALUES (:employeeID, :username, :password, :role)";
                    $pdoAddEdit = $pdoConnect->prepare($query);  
                    $pdoAddEdit->execute(
                        array(
                            'employeeID'    =>     $_POST["employeeID"],
                            'username'      =>     $_POST["username"],
                            'password'      =>     $_POST["password"],
                            'role'          =>     $_POST["role"]
                        )
                    );            
                    $details = "New user registered: ".$_POST["employeeID"];
                    $pdoActivity->execute(
                        array(
                            'Details'       =>     $details
                        )
                    );
                    $_SESSION["Message"] = 'User was registered successfully';
                    header("location:adminusers.php");   
                    
                }                
                catch(PDOException $error) {  
                    $_SESSION["Message"] = $error->getMessage();}
            }  
        }

        //User status change
        if(isset($_POST["DeactivateUser"]) || isset($_POST["ActivateUser"]))
        {
            try
            {
                $pdoStatus = $pdoConnect->prepare("UPDATE tblusers SET status='".$_POST["status"]."' WHERE username='".$_POST["username"]."'");
                $pdoStatus->execute();
                $details = $_POST["status"]=='inactive' ? "User deactivated: ".$_POST["username"] : "User activated: ".$_POST["username"];
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                $_SESSION["Message"] = "User is now ".$_POST["status"];
                header("location:adminusers.php"); 
                
            }
            catch(PDOException $error) {  
                $_SESSION["Message"] = $error->getMessage();}            
        }  


        
        //Add employee
        if(isset($_POST["AddEmployee"])) 
        {  
            $pdoExisting = $pdoConnect->prepare("SELECT employeeID FROM tblemployees WHERE employeeID='".$_POST["employeeID"]."'");
            $pdoExisting->execute();
            if($pdoExisting->rowCount() > 0)  
                {$_SESSION["NewMessage"] = 'ID is already taken';}
            else
            {
                try
                {   
                    $query = "INSERT INTO tblemployees (employeeID, name) VALUES (:employeeID, :name)";
                    $pdoAddEdit = $pdoConnect->prepare($query);  
                    $pdoAddEdit->execute(
                        array(
                            'employeeID'    =>     $_POST["employeeID"],
                            'name'          =>     $_POST["name"]
                        )
                    );            
                    $details = "New employee registered: ".$_POST["employeeID"];
                    $pdoActivity->execute(
                        array(
                            'Details'       =>     $details
                        )
                    );
                    $_SESSION["Message"] = 'Employee was registered successfully';
                    header("location:adminusers.php");

                }                
                catch(PDOException $error) {  
                    $_SESSION["Message"] = $error->getMessage();}
            }  
        }

        //Employee status change
        if(isset($_POST["EmployeeResigned"]))
        {
            try
            {
                $pdoStatus = $pdoConnect->prepare("UPDATE tblemployees SET status='resigned' WHERE employeeID='".$_POST["employeeID"]."'");
                $pdoStatus->execute();
                $details = "Employee resigned: ".$_POST["employeeID"];
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );
                $_SESSION["Message"] = "Employee has resigned";
                header("location:adminusers.php"); 
                
            }
            catch(PDOException $error) {  
                $_SESSION["Message"] = $error->getMessage();}            
        }  


        //password change
        if(isset($_POST["ChangePassword"])) 
        {  
            if($_SESSION["Password"]!=$_POST["currentpassword"])  
                {$_SESSION["NewMessage"] = 'Current password do not match';}
            elseif($_POST["newpassword"]!=$_POST["confirmpassword"])
                {$_SESSION["NewMessage"] = 'New password do not match';}
            else
            {
                try
                {
                    $pdoStatus = $pdoConnect->prepare("UPDATE tblusers SET password='".$_POST["newpassword"]."' WHERE username='".$_SESSION["Username"]."'");
                    $pdoStatus->execute();
                    $details = "User password changed";
                    $pdoActivity->execute(
                        array(
                            'Details'       =>     $details
                        )
                    );
                    $_SESSION["Message"] = "Password was changed successfully";
                    $_SESSION["Password"] = $_POST["newpassword"];
                    header("location:adminusers.php"); 
                }              
                catch(PDOException $error) {  
                    $_SESSION["Message"] = $error->getMessage();}
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
        <title>Users</title>
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
                        <button name="UsersTab">Users</button>
                        <button name="EmployeesTab">Employees</button>
                        <button name="ChangePassword">Change Password</button>
                    </form>
                </div>     
              
                <?php

                    //change password
                    if(isset($_SESSION["ChangePassword"]))
                    {
                        echo    '<div class="users">';
                        if(isset($_SESSION["NewMessage"])) {  
                            echo    '<label>'.$_SESSION["NewMessage"].'</label>'; unset($_SESSION["NewMessage"]);}

                        echo '<form method="post">
                                <br><br>
                                <input type="password" name="currentpassword" placeholder="Current Password" required><br><br>
                                <input type="password" name="newpassword" placeholder="New Password" required><br><br>
                                <input type="password" name="confirmpassword" placeholder="Confirm Password" required><br><br>
                                <button name="ChangePassword">Done</button>
                                <br><br>
                            </form>
                            </tr></div>';

                    }


                    //show employees table
                    elseif(isset($_SESSION["EmployeesTab"]))
                    {
                        echo    '<div class="users">';
                        if(isset($_SESSION["NewMessage"])) {  
                            echo    '<label>'.$_SESSION["NewMessage"].'</label>'; unset($_SESSION["NewMessage"]);}

                        echo    '<form method="post">
                                    <br><br>
                                    <input type="text" name="employeeID" placeholder="Employee ID" required><br><br>
                                    <input type="text" name="name" placeholder="Name" required><br><br>
                                    <button name="AddEmployee">Done</button>
                                    <br><br>
                                </form>
                                </tr>';

                        echo '</div>';

                        echo    '<table class="table">
                                <tr>
                                    <th>#</th><th>ID</th><th>Employee</th><th>Options</th>
                                </tr>';
                        if(isset($_SESSION["Message"]))  
                        {  
                            echo    '<tr class="crud">
                                    <td></td>
                                        <td colspan=4>
                                        <label>'.$_SESSION["Message"].'</label>
                                        </td>
                                    </tr>';
                            unset($_SESSION["Message"]);
                        }

                        $pdoDisplay = $pdoConnect->prepare("SELECT * FROM tblemployees WHERE status='active'");  
                        $pdoDisplay->execute();
                        $num=0;
                        foreach ($pdoDisplay as $employee) {
                        $num++;

                            //Resign
                            if(isset($_POST["Resign"]) && $employee["employeeID"]==$_POST["employeeID"])
                            {
                                echo    '<tr class="crud"><td></td>
                                        <td colspan=3>
                                        <label>This employee has resigned?</label>
                                        </td></tr>
                                        <tr class="crud"><td></td>
                                        <td>'.$employee['employeeID'].'</td>
                                        <td>'.$employee['name'].'</td>
                                        <td>
                                        <form method="post">
                                            <input type="hidden" name="employeeID" value="'.$employee["employeeID"].'">           
                                            <button name="Cancel" form="cancel">Cancel</button>
                                            <button name="EmployeeResigned">Yes</button>
                                        </form>
                                        <form method="post" id="cancel"></form>
                                        </td>
                                        </tr>';
                            }
                            else
                            {
                                echo   '<tr>
                                <td>'.$num.'</td>
                                <td>'.$employee['employeeID'].'</td>
                                <td>'.$employee['name'].'</td>
                                <td>
                                <form method="post"> 
                                    <input type="hidden" name="employeeID" value="'.$employee["employeeID"].'">
                                    <button name="Resign">Resign</button>                                                                      
                                </form>
                                </td>
                                </tr>'; 
                            }
                        }
                        echo '</table>';
                    }


                    //show users table
                    else
                    {
                        $pdoEmployee = $pdoConnect->prepare("SELECT tblemployees.employeeID FROM tblemployees LEFT JOIN tblusers ON tblemployees.employeeID=tblusers.employeeID WHERE tblusers.employeeID IS NULL AND tblemployees.status='active'");  
                        $pdoEmployee->execute();
                        $dropdown = '<select name="employeeID">\r\n<option value="" selected hidden>Employee</option>';
                        foreach ($pdoEmployee as $employee) {
                            $dropdown .= "\r\n<option value='{$employee['employeeID']}'>{$employee['employeeID']}</option>";
                            }
                        $dropdown .= '\r\n</select> ';

                        echo    '<div class="users">';
                        if(isset($_SESSION["NewMessage"])) {  
                            echo    '<label>'.$_SESSION["NewMessage"].'</label>'; unset($_SESSION["NewMessage"]);}

                        echo    '<form method="post">
                                    <br><br>
                                    '.$dropdown.'
                                    <select name="role" required>
                                        <option value="" selected hidden>User Type</option>
                                        <option value="admin">Admin</option>
                                        <option value="cashier">Cashier</option>
                                    </select><br><br>
                                    <input type="text" name="username" placeholder="Username" required><br><br>
                                    <input type="password" name="password" placeholder="Password" required><br><br>
                                    <input type="password" name="confirmpassword" placeholder="Confirm Password" required><br><br>
                                    <button name="AddUser">Done</button>
                                    <br><br>
                                </form>
                                </tr>';

                        echo '</div>';

                        echo    '<table class="table">
                                <tr>
                                    <th>#</th><th>ID</th><th>Employee</th><th>Type</th><th>Options</th>
                                </tr>';
                        if(isset($_SESSION["Message"]))  
                        {  
                            echo    '<tr class="crud">
                                    <td></td>
                                        <td colspan=5>
                                        <label>'.$_SESSION["Message"].'</label>
                                        </td>
                                    </tr>';
                            unset($_SESSION["Message"]);
                        }
                        
                        

                        $pdoDisplay = $pdoConnect->prepare("SELECT tblusers.*,tblemployees.name FROM tblusers INNER JOIN tblemployees WHERE tblusers.employeeID=tblemployees.employeeID ORDER BY status");  
                        $pdoDisplay->execute();
                        $num=0;
                        foreach ($pdoDisplay as $user) {
                        $num++;

                            //Deactivate
                            if((isset($_POST["Deactivate"]) || isset($_POST["Activate"])) && $user["username"]==$_POST["username"])
                            {
                                echo    '<tr class="crud"><td></td>
                                        <td colspan=5>
                                        <label>Are you sure you want to '; echo ($user['status'] == 'active') ? 'deactivate' : 'activate'; echo' this user?</label>
                                        </td></tr>
                                        <tr class="crud"><td></td>
                                        <td>'.$user['employeeID'].'</td>
                                        <td>'.$user['name'].'</td>
                                        <td>'.$user['role'].'</td>
                                        <td>
                                        <form method="post">
                                            <input type="hidden" name="username" value="'.$user["username"].'">           
                                            <button name="Cancel" form="cancel">Cancel</button>
                                            '; echo ($user['status'] == 'active') ? '<button name="DeactivateUser">Deactivate</button><input type="hidden" name="status" value="inactive">' : '<button name="ActivateUser">Deactivate</button><input type="hidden" name="status" value="active">'; echo '
                                        </form>
                                        <form method="post" id="cancel"></form>
                                        </td>
                                        </tr>';
                            }
                            else
                            {
                                echo   '<tr>
                                <td>'.$num.'</td>
                                <td>'.$user['employeeID'].'</td>
                                <td>'.$user['name'].'</td>
                                <td>'.$user['role'].'</td>
                                <td>
                                <form method="post"> 
                                    <input type="hidden" name="username" value="'.$user["username"].'">
                                    '; echo ($user['status'] == 'active') ? '<button name="Deactivate">Deactivate</button>' : '<button name="Activate">Activate</button>'; echo '                                                                       
                                </form>
                                </td>
                                </tr>'; 
                            }
                        }
                        echo '</table>';
                    }

                ?>
                
                

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

            