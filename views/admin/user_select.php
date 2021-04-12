<?php

include '../../config/config.php';

sessionInit();

// Nombre de retards
$delaysItemSelect = $connexion->prepare("
    SELECT COUNT(*)
    FROM instants
    WHERE id_pers =  :id_pers
    AND heure_arrivee BETWEEN '07:46:00' AND '07:59:59'
");

// Nombre d'absences
$absencesItemSelect = $connexion->prepare('
    SELECT SUM(nbre_heures)
    FROM absences
    WHERE id_pers = :id_pers
');

$lvlSelect = $connexion->prepare('SELECT DISTINCT niveau FROM etudiants');
$lvlSelect->execute();

$lvlStudentsSelect = $connexion->prepare('
  SELECT *
  FROM etudiants
  NATURAL JOIN personnes
  WHERE niveau = :niveau
  ORDER BY nom
');

$ensSelect = $connexion->prepare('
  SELECT *
  FROM enseignants
  NATURAL JOIN personnes
  ORDER BY nom
');
$ensSelect->execute();

$staffSelect = $connexion->prepare('
  SELECT *
  FROM staff
  NATURAL JOIN personnes
  ORDER BY nom
');
$staffSelect->execute();

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Selectionner utilisateur</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="../../style/raleway.css">
    <link rel="stylesheet" href="../../style/w3schools.css">
    <link rel="stylesheet" href="../../style/personalized_styles.css">
  </head>
  
  <body class="w3-content w3-light-grey">
    
    <!-- Sidebar/menu -->
    <nav class="w3-blue-grey w3-sidebar w3-collapse w3-hover-shadow w3-animate-left" id="mySidebar">
      <div class="w3-container">
        <a href="#" onclick="w3_close()" class="w3-hide-large w3-right w3-jumbo w3-padding w3-hover-text-red" title="close menu">
          <i class="fa fa-times"></i>
        </a>
        <img src="../../images/img.jpg" id="user-img" class="w3-circle w3-margin-top"><br><br>
        <h5><i class="fas fa-user w3-margin-right"></i><b>
            <?php
            // Recuperer uniquement le premier mot du nom et du prenom
            $userName = explode(' ', $_SESSION['nom'])[0];
            $userFirstname = explode(' ', $_SESSION['prenom'])[0];
                        
            if(strlen($userName) > 11 || strlen($userFirstname) > 11) echo $userName;
            else echo $userFirstname. ' ' .$userName;
            ?>    
        </b></h5>
      </div>
      
      <form method="post" action="../../models/actions.php">
        <!-- User infos -->
        <div class="w3-bar-block w3-margin-top">
          <button type="submit" name="display-choice" value="today" id="today" class="w3-bar-item w3-button w3-hover-red w3-padding"><i class="fas fa-calendar-day w3-margin-right"></i>Aujourd'hui</button>
          
          <?php
            $delaysItemSelect->bindParam(':id_pers', $_SESSION['id']);
            $absencesItemSelect->bindParam(':id_pers', $_SESSION['id']);
            
            $delaysItemSelect->execute();
            $absencesItemSelect->execute();
            
            $delaysItemUserResult = $delaysItemSelect->fetch();
            $absencesItemUserResult = $absencesItemSelect->fetch();
          ?>
          <button type="submit" name="display-choice" value="delays" id="delays" class="w3-bar-item w3-button w3-hover-red w3-padding"><i class="fas fa-clock w3-margin-right"></i>Mes retards (<?php echo $delaysItemUserResult[0] ?>)</button>
          <button type="submit" name="display-choice" value="absences" id="absences" class="w3-bar-item w3-button w3-hover-red w3-padding"><i class="fas fa-calendar-times w3-margin-right"></i>Mes absences (<?php echo ($absencesItemUserResult[0] == NULL) ? '0' : $absencesItemUserResult[0] ?>H)</button>
        </div>
        
        <!-- Admin options -->
        <div class="w3-bar-block w3-margin-top w3-border-top">
          <h6 class="w3-container"><i class="fas fa-users w3-margin-top w3-margin-right"></i><b>ESSFAR</b></h6>

          <a href="user_select.php" class="w3-bar-item w3-button w3-hover-red w3-padding" title="Consulter la carte d'un utilisateur">
            <?php
            if(isset($_SESSION['users-list']) && !empty($_SESSION['users-list']))
              echo $studentResult["prenom"]. ' ' .$studentResult["nom"];       
            else
              echo 'Infos utilisateur';
            ?>
          </a>
          
          <?php
          if(isset($_SESSION['users-list']) && !empty($_SESSION['users-list'])){
            $delaysItemSelect->bindParam(':id_pers', $_SESSION['users-list']);
            $absencesItemSelect->bindParam(':id_pers', $_SESSION['users-list']);
            
            $delaysItemSelect->execute();
            $absencesItemSelect->execute();
            
            $delaysItemusersResult = $delaysItemSelect->fetch();
            $absencesItemusersResult = $absencesItemSelect->fetch();
          ?>
          <p class="w3-margin-left"><small>Retards(<?php echo $delaysItemusersResult[0] ?>) - Absences(<?php echo ($absencesItemusersResult[0] == NULL) ? '0' : $absencesItemusersResult[0] ?>H)</small></p>
          <?php
            unset($_SESSION['users-list']);
          } 
          ?>
          <a href="in_out_manager.php" class="w3-bar-item w3-button w3-hover-red w3-padding">Gestionnaire d'horaires</a>
          
          <a href="absences_manager.php" class="w3-bar-item w3-button w3-hover-red w3-padding">Gestionnaire d'absences</a>
          
          <a href="user_registration.php" class="w3-bar-item w3-button w3-hover-red w3-padding">Ajouter un utilisateur</a>
          
          <a href="general_stats.php" class="w3-bar-item w3-button w3-hover-red w3-padding">Statistiques</a>
        </div>
        
        <button type="submit" name="logout" value="logout" id="logout" class="w3-display-bottommiddle"><b>Deconnexion</b></button>
      </form>  
    </nav>
    
    <!-- Overlay effect when opening sidebar on small screens -->
    <div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" title="close side menu" id="myOverlay"></div>
    
    <!-- !PAGE CONTENT! -->
    <div class="w3-main">
      
      <!-- Header -->
      <header class="w3-padding w3-margin-bottom w3-border-bottom w3-center">
        <img src="../../images/img.jpg" id="user-img-resp" class="w3-circle w3-right w3-margin w3-hide-large">
        <span class="w3-button w3-hide-large w3-xxlarge w3-text-blue-grey w3-hover-text-red w3-left" onclick="w3_open()" title="Side menu"><i class="fa fa-bars"></i></span>
        
        <div class="w3-container">
          <img src="../../images/essfar.png" id="logo-essfar">
        </div>
      </header>
      
      <!-- Content -->
        <h5 class="w3-margin w3-text-blue-grey"><b>Choississez un utilisateur</b></h5>
        <form method="post" action="../../models/actions.php" id="actions">
          <?php
          $userTypesItem = 2;
          while($lvlResult = $lvlSelect->fetch()){
            $userTypesItem++;
            $lvlStudentsSelect->bindParam(':niveau', $lvlResult['niveau']);
            $lvlStudentsSelect->execute();
          ?>
          <select name="users-list" class="w3-button w3-hover-red w3-left-align w3-border-bottom w3-round-large w3-padding-small w3-margin users-list">
            <option class="w3-red" selected="selected" disabled="disabled"><?php echo $lvlResult['niveau'] ?></option>
            <?php
            while($lvlStudentsResult = $lvlStudentsSelect->fetch()){
            ?>
            <option class="w3-red" value="<?php echo $lvlStudentsResult['id_pers'] ?>"><?php echo $lvlStudentsResult['nom']. ' ' .$lvlStudentsResult['prenom'] ?></option>
            <?php
            }
            ?>
          </select>
          <?php
          }
          ?>
          
          <select name="users-list" class="w3-button w3-hover-red w3-left-align w3-border-bottom w3-round-large w3-padding-small w3-margin users-list">
            <option class="w3-red" selected="selected" disabled="disabled">Enseignants</option>
            <?php
            while($ensResult = $ensSelect->fetch()){
            ?>
            <option class="w3-red" value="<?php echo $ensResult['id_pers'] ?>"><?php echo $ensResult['nom']. ' ' .$ensResult['prenom'] ?></option>
            <?php
            }
            ?>
          </select>
          
          <select name="users-list" class="w3-button w3-hover-red w3-left-align w3-border-bottom w3-round-large w3-padding-small w3-margin users-list">
            <option class="w3-red" selected="selected" disabled="disabled">Staff</option>
            <?php
            while($staffResult = $staffSelect->fetch()){
            ?>
            <option class="w3-red" value="<?php echo $staffResult['id_pers'] ?>"><?php echo $staffResult['nom']. ' ' .$staffResult['prenom'] ?></option>
            <?php
            }
            ?>
          </select>
        </form>
    </div>
    
    <script>
      // Script to open and close sidebar
      function w3_open() {
          document.getElementById("mySidebar").style.display = "block";
          document.getElementById("myOverlay").style.display = "block";
      }
      
      function w3_close() {
          document.getElementById("mySidebar").style.display = "none";
          document.getElementById("myOverlay").style.display = "none";
      }
      
      // Afficher les infos d'un utilisateur
      var actions = document.getElementById("actions");
      
      var usersList = document.querySelectorAll(".users-list");
      var userTypesItem = <?php echo $userTypesItem ?>;
      
      for(i = 0; i < userTypesItem; i++)
        usersList[i].addEventListener("change", function(){actions.submit()});
      
      // Deconnexion
      var logout = document.getElementById("logout");
      logout.addEventListener('click', function(event){
        if(!confirm('Souhaitez-vous vraiment vous deconnecter?')) event.preventDefault();
      });

    </script>
  </body>
</html>