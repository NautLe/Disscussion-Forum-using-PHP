<?php
session_start();
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://kit.fontawesome.com/2c74ee7434.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    
    

</head>

<body>
    
    <div class="background">
        <video autoplay muted loop class="video">
            <source src="2.mp4" type="video/mp4" class="v"></source>
        </video>
        <div class="profile-check" id="signIn">
            <h1 class="form-title">Sign In</h1>
            <?php
            if (isset($errors['login'])) {
                echo '<div class="error-main">
                            <p>' . $errors['login'] . '</p>
                          </div>';
                unset($errors['login']);
            }
            ?>
            <form method="POST" action="user-account.php">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <?php
                    if (isset($errors['email'])) {
                        echo ' <div class="error">
                                <p>' . $errors['email'] . '</p>
                              </div>';
                    }
                    ?>
                </div>
                <div class="input-group password">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i id="eye" class="fa fa-eye"></i>
                    <?php
                    if (isset($errors['password'])) {
                        echo ' <div class="error">
                                <p>' . $errors['password'] . '</p>
                              </div>';
                    }
                    ?>
                </div>
                <p class="recover">
                    <a href="forgot_password.php">Recover Password</a>
                </p>
                <input type="submit" class="btn" value="Sign In" name="signin">
            </form>
            <p class="or">
                ----------Or----------
            </p>
        
            <div class="admin-login">
            <a href="admin_login.php" class="button">Admin Login</a>

            <div class="contact-admin">
                <a href="mailto:dauding159@gmail.com" class="button">Contact Admin</a>
            </div>
            
            </div>
            <div class="links">
                <p>Don't have an account yet?</p>
                <a href="register.php">Sign Up</a>
            </div>
            <div id="footer">   

                <p class="copyright">Powered by <a href="https://www.facebook.com/Nautifood">Tuan Le</a></p>
      
</div>
</div>    
            
    </div>

        </div>
        <!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/674ae9032480f5b4f5a619c9/1idu9se04';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const passwordField = document.getElementById("password");
                const toggleIcon = document.getElementById("eye");

                toggleIcon.addEventListener("click", function () {
                    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
                    passwordField.setAttribute("type", type);

                    // Toggle icon classes for visibility state
                    this.classList.toggle("fa-eye");
                    this.classList.toggle("fa-eye-slash");
                });
            });
        </script>
        
        
    </div>
</body>

</html>
<?php
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
?>
