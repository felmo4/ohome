
 <?php  

    session_start();  
    require_once './include/dbcon.php';
    try  
    {  
        $pdoConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
        if(isset($_POST["login"]))  
        {  
            //scans db
            $pdoLogin = $pdoConnect->prepare("SELECT tblusers.*,tblemployees.name FROM tblusers INNER JOIN tblemployees WHERE tblusers.employeeID=tblemployees.employeeID AND (username = :UserName AND password = :PassWord AND tblusers.status = 'active')");  
            $pdoLogin->execute(  
                array(  
                    'UserName'     =>     $_POST["username"],  
                    'PassWord'     =>     $_POST["password"]
                )  
            );  

            //login info matched 
            if($pdoLogin->rowCount() > 0)  
            {  
                //assigns user infos to session
                foreach ($pdoLogin as $row) {
                    $_SESSION["ID"] = $row['employeeID'];
                    $_SESSION["Name"] = $row['name'];
                    $_SESSION["Username"] = $row['username'];
                    $_SESSION["Password"] = $row['password'];
                    $_SESSION["Role"] = $row['role'];}      
                
                $pdoActivity = $pdoConnect->prepare("INSERT INTO tblactivity (time, details, employeeID) VALUE (NOW(), :Details, '".$_SESSION["ID"]."')");
                $details = $_SESSION["Role"]=='admin' ? "Admin login successful" : "Cashier login successful";
                $pdoActivity->execute(
                    array(
                        'Details'       =>     $details
                    )
                );

                //redirect 
                header($_SESSION["Role"]=='admin' ? "location:admin/adminproduct.php" : "location:cashier/cashierhome.php");
            } 

            //login info mismatched
            else  
            {  
                $message = 'Incorrect username or password';  
            }  
            
        }  
    }  

    catch(PDOException $error)  
    {  
        $message = $error->getMessage();  
    }  

 ?>  

<!DOCTYPE html>  
<html>  
    <head>
        <title>OHOME LOG IN</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
        <link rel="stylesheet" href="index.css">
        <link rel="icon" href="include/logo.png">
</head>
<body>
    <div class="logo">
        <img src="include/logo.png" class="profile_image" alt="">
    </div>
    <div class="container">  

        <div class="login_box">

            <form method ="post">
                <?php  
                if(isset($message))  
                {  
                    echo '<label>'.$message.'</label>';  
                }  
                ?> 
                <p>
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" placeholder="username" required>
                </p>
                <p>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="password" required>
                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                </p>
                <button name="login" id="submit" class="submit">Log In</button>
            </form> 

        </div>
    </div>

      <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function (e) {
           
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
           
            this.classList.toggle("bi-eye");
        });
        // const form = document.querySelector("form");
        // form.addEventListener('submit', function (e) {
        //     e.preventDefault();
        // });
    </script>

</body>
    
</html> 