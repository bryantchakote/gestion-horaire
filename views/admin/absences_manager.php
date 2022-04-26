<?php

include '../../config/config.php';

sessionInit();

$actualDate = date('Y-m-d');
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

// Heures d'absence
$absenceHours = $connexion->prepare("
  SELECT *
  FROM absences
  WHERE id_pers = :id_pers
  AND date_jour = :date_jour
");

// Differents niveaux
$lvlSelect = $connexion->prepare('SELECT DISTINCT niveau FROM etudiants');
$lvlSelect->execute();

$lvlStudentsSelect = $connexion->prepare('
  SELECT *
  FROM etudiants
  NATURAL JOIN personnes
  WHERE niveau = :niveau
  AND id_pers IN
  (SELECT id_pers FROM absences WHERE date_jour = :date_jour AND id_motif = 1)
  ORDER BY nom
');

$ensSelect = $connexion->prepare('
  SELECT *
  FROM enseignants
  NATURAL JOIN personnes
  WHERE id_pers IN
  (SELECT id_pers FROM absences WHERE date_jour = :date_jour AND id_motif = 1)
  ORDER BY nom
');
  
$staffSelect = $connexion->prepare('
  SELECT *
  FROM staff
  NATURAL JOIN personnes
  WHERE id_pers IN
  (SELECT id_pers FROM absences WHERE date_jour = :date_jour AND id_motif = 1)
  ORDER BY nom
');

if(isset($_SESSION['absence-day']) && !empty($_SESSION['absence-day'])){
  $absenceHours->bindParam(':date_jour', $_SESSION['absence-day']);
  $lvlStudentsSelect->bindParam(':date_jour', $_SESSION['absence-day']);
  $ensSelect->bindParam(':date_jour', $_SESSION['absence-day']);
  $staffSelect->bindParam(':date_jour', $_SESSION['absence-day']);
}
else{
  $absenceHours->bindParam(':date_jour', $actualDate);
  $lvlStudentsSelect->bindParam(':date_jour', $actualDate);
  $ensSelect->bindParam(':date_jour', $actualDate);
  $staffSelect->bindParam(':date_jour', $actualDate);
  
}
$ensSelect->execute();
$staffSelect->execute();

$motifsSelect = $connexion->prepare('SELECT * FROM motifs');

$ensItem = $ensSelect->rowCount();
$staffItem = $staffSelect->rowCount();
$lvlItem = $lvlSelect->rowCount();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Gestionnaire d'absences</title>
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
        <a href="#" onclick="w3_close()" class="w3-hide-large w3-right w3-jumbo w3-padding w3-hover-grey" title="close menu">
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
        
        <button type="submit" name="logout" value="logout" id="logout" class="w3-display-bottommiddle"><b>Déconnexion</b></button>
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
        <form method="post" action="../../models/actions.php" id="actions" class="w3-row-padding w3-padding-large w3-center">
          <span><b>Journée du</b></span>
          <input type="date" name="absence-day" id="absence-day" value="<?php echo (isset($_SESSION['absence-day']) && !empty($_SESSION['absence-day'])) ? $_SESSION['absence-day'] : $actualDate ?>" class="w3-button w3-hover-red w3-padding-small">
          
          <table class="w3-table-all w3-centered w3-hoverable w3-margin-top w3-margin-bottom">
            <tr>
              <th>Noms &amp; prénoms</th>
              <th>Nombre d'heures</th>
              <th>Justification</th>
            </tr>
            
            <?php
            unset($_SESSION['absence-day']);
            $i = 0;
            while($lvlResult = $lvlSelect->fetch()){
              $lvlStudentsSelect->bindParam(':niveau', $lvlResult['niveau']);
              $lvlStudentsSelect->execute();
            ?>
            <tr><th colspan="3" class="w3-center" id="lvl-content<?php echo $i ?>"><?php echo $lvlResult['niveau'] ?></th></tr>
            <?php
              $lvlCount[$i] = 0;
              while($lvlStudentsResult = $lvlStudentsSelect->fetch()){
                $lvlCount[$i]++;
                $absenceHours->bindParam(':id_pers', $lvlStudentsResult['id_pers']);
                $absenceHours->execute();
                $absenceHoursResult = $absenceHours->fetch();
            ?>
            <tr class="std<?php echo $i ?>-list">
              <td>
                <input type="text" name="user-id<?php echo $lvlStudentsResult['id_pers'] ?>" value="<?php echo $lvlStudentsResult['id_pers'] ?>" class="display-none"><?php echo $lvlStudentsResult['nom']. ' ' .$lvlStudentsResult['prenom'] ?>
              </td>
              <td><input type="text" name="hours<?php echo $lvlStudentsResult['id_pers'] ?>" value="<?php echo $absenceHoursResult['nbre_heures'] ?>" class="display-none"><?php echo $absenceHoursResult['nbre_heures'] ?>H</td>
              <td class="padding-0">
                <select name="motif<?php echo $lvlStudentsResult['id_pers'] ?>" class="select-table w3-button w3-hover-red">
                <?php
                $motifsSelect->execute();
                while($motifResult = $motifsSelect->fetch()){
                ?>
                  <option value="<?php echo $motifResult['id_motif'] ?>"><?php echo $motifResult["libelle"] ?></option>
                <?php
                }
                ?>
                </select>
              </td>
              <?php
              }
              ?>
            </tr>
            <?php
            $i++;
            }
            ?>

            
            <tr><th colspan="3" class="w3-center" id="ens-content">Enseignants</th></tr>
            <?php
              while($ensResult = $ensSelect->fetch()){
                $absenceHours->bindParam(':id_pers', $ensResult['id_pers']);
                $absenceHours->execute();
                $absenceHoursResult = $absenceHours->fetch();
            ?>
            <tr class="ens-list">
              <td>
                <input type="text" name="user-id<?php echo $ensResult['id_pers'] ?>" value="<?php echo $ensResult['id_pers'] ?>" class="display-none"><?php echo $ensResult['nom']. ' ' .$ensResult['prenom'] ?>
              </td>
              <td><input type="text" name="hours<?php echo $ensResult['id_pers'] ?>" value="<?php echo $absenceHoursResult['nbre_heures'] ?>" class="display-none"><?php echo $absenceHoursResult['nbre_heures'] ?>H</td>
              <td class="padding-0">
                <select name="motif<?php echo $ensResult['id_pers'] ?>" class="select-table w3-button w3-hover-red">
                <?php
                $motifsSelect->execute();
                while($motifResult = $motifsSelect->fetch()){
                ?>
                  <option value="<?php echo $motifResult['id_motif'] ?>"><?php echo $motifResult["libelle"] ?></option>
                <?php
                }
                ?>
                </select>
              </td>
              <?php
              }
              ?>
            </tr>
            
            
            <tr><th colspan="3" class="w3-center" id="stf-content">Staff</th></tr>
            <?php
              while($staffResult = $staffSelect->fetch()){
                $absenceHours->bindParam(':id_pers', $staffResult['id_pers']);
                $absenceHours->execute();
                $absenceHoursResult = $absenceHours->fetch();
            ?>
            <tr class="stf-list">
              <td>
                <input type="text" name="user-id<?php echo $staffResult['id_pers'] ?>" value="<?php echo $staffResult['id_pers'] ?>" class="display-none"><?php echo $staffResult['prenom']. ' ' .$staffResult['nom'] ?>
              </td>
              <td><input type="text" name="hours<?php echo $staffResult['id_pers'] ?>" value="<?php echo $absenceHoursResult['nbre_heures'] ?>" class="display-none"><?php echo $absenceHoursResult['nbre_heures'] ?>H</td>
              <td class="padding-0">
                <select name="motif<?php echo $staffResult['id_pers'] ?>" class="select-table w3-button w3-hover-red">
                <?php
                $motifsSelect->execute();
                while($motifResult = $motifsSelect->fetch()){
                ?>
                  <option value="<?php echo $motifResult['id_motif'] ?>"><?php echo $motifResult["libelle"] ?></option>
                <?php
                }
                ?>
                </select>
              </td>
              <?php
              }
              ?>
            </tr>
          </table>
          
          <button type="submit" name="confirm-absences" value="confirm-absences" class="w3-button w3-hover-red w3-blue-grey w3-padding">Confirmer</button>
          <button type="reset" class="w3-button w3-hover-red w3-blue-grey w3-padding">Réinitialiser</button>
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
      
      
      // Affichage des acteurs
      var stfListState = 0;
      var stfContent = document.getElementById("stf-content");
      var stfList = document.querySelectorAll(".stf-list");
      var staffItem = <?php echo $staffItem ?>;
      
      stfContent.addEventListener("click", function(){
        if(stfListState == 0){
          for(i = 0; i < staffItem; i++)
            stfList[i].style.display = "table-row";
          stfListState = 1;
        }
        else{
          for(i = 0; i < staffItem; i++)
            stfList[i].style.display = "";
          stfListState = 0;
        }
        return stfListState;
      });
      
      var ensListState = 0;
      var ensContent = document.getElementById("ens-content");
      var ensList = document.querySelectorAll(".ens-list");
      var ensItem = <?php echo $ensItem ?>;
      
      ensContent.addEventListener("click", function(){
        if(ensListState == 0){
          for(i = 0; i < ensItem; i++)
            ensList[i].style.display = "table-row";
          ensListState = 1;
        }
        else{
          for(i = 0; i < ensItem; i++)
            ensList[i].style.display = "";
          ensListState = 0;
        }
        return ensListState;
      });
            
      <?php for($j = 0; $j < $lvlItem; $j++){ ?>
        var std<?php echo $j ?>ListState = 0;
        var lvlContent<?php echo $j ?> = document.getElementById("lvl-content<?php echo $j ?>");
        var std<?php echo $j ?>List = document.querySelectorAll(".std<?php echo $j ?>-list");
        var lvlCount<?php echo $j ?> = <?php echo $lvlCount[$j] ?>;
        
        lvlContent<?php echo $j ?>.addEventListener("click", function(){
          if(std<?php echo $j ?>ListState == 0){
            for(i = 0; i < lvlCount<?php echo $j ?>; i++)
              std<?php echo $j ?>List[i].style.display = "table-row";
            std<?php echo $j ?>ListState = 1;
          }
          else{
            for(i = 0; i < lvlCount<?php echo $j ?>; i++)
              std<?php echo $j ?>List[i].style.display = "";
            std<?php echo $j ?>ListState = 0;
          }
          return std<?php echo $j ?>ListState;
        });
      <?php } ?>
      
      var actions = document.getElementById("actions");
      var absenceDay = document.getElementById("absence-day");
      absenceDay.addEventListener("change", function(){actions.submit()});
      
      // Deconnexion
      var logout = document.getElementById("logout");
      logout.addEventListener('click', function(event){
        if(!confirm('Souhaitez-vous vraiment vous deconnecter?')) event.preventDefault();
      });

    </script>
  </body>
</html>