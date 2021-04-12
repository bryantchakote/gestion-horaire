<?php
    include 'config/config.php';
    
    sessionInit();
    session_unset();
    session_destroy();
    
    if(isset($_POST['connex-submit']) && !empty($_POST['connex-submit'])){
        $usersBrowse = $connexion->prepare('SELECT * FROM personnes');
        $usersBrowse->execute();
        
        $staffBrowse = $connexion->prepare('SELECT * FROM staff WHERE id_pers = :id_pers');
        $studentsBrowse = $connexion->prepare('SELECT * FROM etudiants WHERE id_pers = :id_pers');
        
        while($userResult = $usersBrowse->fetch()){
            if(($userResult['email'] == $_POST['connex-email']) && password_verify($_POST['connex-pwd'], $userResult['mdp'])){
                sessionInit();
                
                $_SESSION['id'] = $userResult['id_pers'];
                $_SESSION['nom'] = $userResult['nom'];
                $_SESSION['prenom'] = $userResult['prenom'];
                $_SESSION['sexe'] = $userResult['sexe'];
                $_SESSION['status'] = 'user';
                
                $staffBrowse->bindParam(':id_pers', $_SESSION['id']);
                $staffBrowse->execute();
                $staffResult = $staffBrowse->fetch();
                
                $studentsBrowse->bindParam(':id_pers', $_SESSION['id']);
                $studentsBrowse->execute();
                $studentsResults = $studentsBrowse->fetch();
                
                if($staffResult['est_admin'] == 1){
                    $_SESSION['status'] = 'admin';
                    header('location: views/admin/admin.php');
                }
                elseif($studentsResults['est_delegue'] == 1){
                    $_SESSION['status'] = 'class_prefect';
                    header('location: views/class_prefect/class_prefect.php');
                }
                else
                    header('location: views/user/user.php');
            }
        }
        
        if(empty($_SESSION['id'])){
            echo "<script>alert('Identifiants incorrects')</script>";
            echo "<script>location.replace('index.php')</script>";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login Time-clock</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
        <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
        <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
        <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
        <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
        <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
        <link rel="stylesheet" type="text/css" href="style/util.css">
        <link rel="stylesheet" type="text/css" href="style/main.css">
    </head>
    <body>
        <div class="limiter">
            <div class="container-login100">
                <div class="wrap-login100">
                    <div class="login100-form-title" >
                        <img src="images/essfar.png" id="logo_essfar">
                        <div class="titre">Pointage horaire</div>
                    </div>

                    <form class="login100-form validate-form" action="" method="post">
                        <div class="wrap-input100 validate-input m-b-26" data-validate="Username is required">
                            <span class="label-input100"></span>
                            <input class="input100" type="mail" name="connex-email" placeholder="Email" required="required">
                            <span class="focus-input100"></span>
                        </div>

                        <div class="wrap-input100 validate-input m-b-18" data-validate="Password is required">
                            <span class="label-input100"></span>
                            <input class="input100" type="password" name="connex-pwd" placeholder="Password" required="required">
                            <span class="focus-input100"></span>
                        </div>

                        <div class="log">
                            <button class="login100-form-btn"  type="submit" name="connex-submit" value="connexion">Connexion</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
        <script src="vendor/animsition/js/animsition.min.js"></script>
        <script src="vendor/bootstrap/js/popper.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="vendor/select2/select2.min.js"></script>
        <script src="vendor/daterangepicker/moment.min.js"></script>
        <script src="vendor/daterangepicker/daterangepicker.js"></script>
        <script src="vendor/countdowntime/countdowntime.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>