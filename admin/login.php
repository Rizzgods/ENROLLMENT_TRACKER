<?php
require_once("../include/initialize.php");
?>
<?php
if (isset($_SESSION['ACCOUNT_ID'])){
    redirect(web_root."admin/index.php");
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bestlink Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom premium gradient background */
        .premium-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
            background-size: 200% 200%;
            animation: gradientAnimation 15s ease infinite;
        }
        
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Subtle shadow enhancement for card */
        .premium-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="min-h-screen premium-gradient flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-xl premium-shadow p-8">
            <div class="flex justify-center mb-6">
                <img src="../img/bcp_logo.png" alt="Bestlink Logo" class="h-16">
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Administrator Login</h2>
            
            <?php echo check_message(); ?>
            
            <form method="post" action="" role="login" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input 
                        type="text" 
                        name="user_email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="Enter your username"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input 
                        type="password" 
                        name="user_pass"
                        id="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="••••••••"
                        required
                    />
                </div>

                <button type="submit" name="btnLogin" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-medium py-2.5 rounded-lg transition-all duration-300 transform hover:scale-[1.01]">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                <div class="flex items-center justify-center">
                    <span class="bg-gray-300 h-px flex-grow t-2 relative top-2"></span>
                    <span class="flex-shrink mx-4 text-gray-400">Secure Admin Access</span>
                    <span class="bg-gray-300 h-px flex-grow t-2 relative top-2"></span>
                </div>
                <p class="mt-4">
                    Return to <a href="/onlineenrolmentsystem/" class="text-blue-600 hover:text-blue-700 font-medium">Main Site</a>
                </p>
            </div>
        </div>
    </div>
</body>

<?php 
if(isset($_POST['btnLogin'])){
    $email = trim($_POST['user_email']);
    $upass = trim($_POST['user_pass']);
  
    if ($email == '' OR $upass == '') {
        message("Invalid Username and Password!", "error");
        redirect("login.php");
    } else {  
        //it creates a new objects of member
        $user = new User();
        //make use of the static function, and we passed to parameters
        $res = $user::userAuthentication($email, $upass);
        if ($res==true) { 
            message("You logon as ".$_SESSION['ACCOUNT_TYPE'].".","success");
       
            $sql="INSERT INTO `tbllogs` (`USERID`, `LOGDATETIME`, `LOGROLE`, `LOGMODE`) 
                VALUES (".$_SESSION['ACCOUNT_ID'].",'".date('Y-m-d H:i:s')."','".$_SESSION['ACCOUNT_TYPE']."','Logged in')";
            $mydb->setQuery($sql);
            $mydb->executeQuery();

            if ($_SESSION['ACCOUNT_TYPE']=='Administrator'){ 
                redirect(web_root."admin/index.php");
            } elseif($_SESSION['ACCOUNT_TYPE']=='Registrar'){
                redirect(web_root."admin/index.php");
            } else{
                redirect(web_root."admin/login.php");
            }
        } else {
            message("Account does not exist! Please contact Administrator.", "error");
            redirect(web_root."admin/login.php"); 
        }
    }
} 
?>
</html>