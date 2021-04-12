<?php

include '../../config/config.php';

sessionInit();

// Niveau de l'utilisateur
$userLvlSelect = $connexion->prepare('SELECT niveau FROM etudiants WHERE id_pers = ' .$_SESSION['id']);
$userLvlSelect->execute();
$userLvlResult = $userLvlSelect->fetch();

// Camarades de classe de l'utilisateur
$studentsLvlSelect = $connexion->prepare("
  SELECT *
  FROM etudiants
  NATURAL JOIN personnes
  WHERE niveau = '" .$userLvlResult['niveau']. "'
  AND id_pers != '" .$_SESSION['id']. "'
  ORDER BY nom
");

$studentsLvlSelect->execute();

$actualDate = date('Y-m-d');
// Nombre de retards
$delaysItemSelect = $connexion->prepare("
    SELECT COUNT(*)
    FROM instants
    WHERE id_pers =  :id_pers
    AND heure_arrivee BETWEEN '07:46:00' AND '07:59:59'
");

// Nombre d'absences
$absencesItemSelect = $connexion->prepare("
    SELECT SUM(nbre_heures)
    FROM absences
    WHERE id_pers = :id_pers
");

// Nombre de retards et d'absences par type d'utilisateur
$lvlDelaysItemSelect = $connexion->prepare("
  SELECT COUNT(*)
  FROM instants
  WHERE date_jour = :date_jour
  AND id_pers IN
  (SELECT id_pers FROM etudiants WHERE niveau = :niveau)
  AND heure_arrivee BETWEEN '07:46:00' AND '07:59:59'
");

$lvlAbsencesItemSelect = $connexion->prepare("
  SELECT SUM(nbre_heures)
  FROM absences
  WHERE date_jour = :date_jour
  AND id_pers IN
  (SELECT id_pers FROM etudiants WHERE niveau = :niveau)
");

// Heures d'entree et de sortie
$hoursSelect = $connexion->prepare("
  SELECT *
  FROM instants
  WHERE id_pers = :id_pers
  AND date_jour = :date_jour
");

// Heures d'absence
$absenceHours = $connexion->prepare("
  SELECT *
  FROM absences
  NATURAL JOIN motifs
  WHERE id_pers = :id_pers
  AND date_jour = :date_jour
");

// Differents niveaux
$lvlSelect = $connexion->prepare('SELECT niveau FROM etudiants WHERE id_pers = ' .$_SESSION["id"]);

// Etudiants
$lvlStudentsSelect = $connexion->prepare('
  SELECT *
  FROM etudiants
  NATURAL JOIN personnes
  WHERE niveau = :niveau
  ORDER BY nom
');

if(isset($_SESSION['lvl-stats-day']) && !empty($_SESSION['lvl-stats-day'])){
  $lvlDelaysItemSelect->bindParam(':date_jour', $_SESSION['lvl-stats-day']);
  $lvlAbsencesItemSelect->bindParam(':date_jour', $_SESSION['lvl-stats-day']);
  $hoursSelect->bindParam(':date_jour', $_SESSION['lvl-stats-day']);
  $absenceHours->bindParam(':date_jour', $_SESSION['lvl-stats-day']);
}
else{
  $lvlDelaysItemSelect->bindParam(':date_jour', $actualDate);
  $lvlAbsencesItemSelect->bindParam(':date_jour', $actualDate);
  $hoursSelect->bindParam(':date_jour', $actualDate);
  $absenceHours->bindParam(':date_jour', $actualDate);
}
$lvlSelect->execute();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Statistiques du niveau</title>
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
      
      <form method="post" action="../../models/actions.php" id="actions1">
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
        
        <!-- Class prefect options -->
        <div class="w3-bar-block w3-margin-top w3-border-top">
          <h6 class="w3-container"><i class="fas fa-users w3-margin-right"></i><b><?php echo $userLvlResult['niveau'] ?></b></h6>

          <select name="classmates-list" id="classmates-list" class="w3-bar-item w3-button w3-hover-red w3-padding" title="Consulter la carte d'un de vos camarades de classe">
            <?php
            if(isset($_SESSION['classmates-list']) && !empty($_SESSION['classmates-list']))
              echo '<option class="w3-light-grey" selected="selected" disabled="disabled">' .$studentResult["nom"]. ' ' .$studentResult["prenom"]. '</option>';       
            else
              echo '<option class="w3-light-grey" selected="selected" disabled="disabled">Infos etudiant</option>';

            while($studentsLvlResult = $studentsLvlSelect->fetch()){
            ?>
            <option class="w3-red" value="<?php echo $studentsLvlResult['id_pers'] ?>"><?php echo $studentsLvlResult['nom']. ' ' .$studentsLvlResult['prenom'] ?></option>
            <?php
            }
            ?>
          </select>
          
          <?php
          if(isset($_SESSION['classmates-list']) && !empty($_SESSION['classmates-list'])){
            $delaysItemSelect->bindParam(':id_pers', $_SESSION['classmates-list']);
            $absencesItemSelect->bindParam(':id_pers', $_SESSION['classmates-list']);
            
            $delaysItemSelect->execute();
            $absencesItemSelect->execute();
            
            $delaysItemClassmateResult = $delaysItemSelect->fetch();
            $absencesItemClassmateResult = $absencesItemSelect->fetch();
          ?>
          <p class="w3-margin-left"><small>Retards(<?php echo $delaysItemClassmateResult[0] ?>) - Absences(<?php echo ($absencesItemClassmateResult[0] == NULL) ? '0' : $absencesItemClassmateResult[0] ?>H)</small></p>
          <?php
            unset($_SESSION['classmates-list']);
          } 
          ?>
          
          <a href="level_stats.php" class="w3-bar-item w3-button w3-hover-red w3-padding">Statistiques</a>
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
        <form method="post" action="../../models/actions.php" id="actions2" class="w3-row-padding w3-padding-large w3-center">
          <span><b>Journee du</b></span>
          <input type="date" name="lvl-stats-day" id="lvl-stats-day" value="<?php echo (isset($_SESSION['lvl-stats-day']) && !empty($_SESSION['lvl-stats-day'])) ? $_SESSION['lvl-stats-day'] : $actualDate ?>" class="w3-button w3-hover-red w3-padding-small">  
        </form>
      
          <table class="w3-table-all w3-centered w3-hoverable w3-margin-top w3-margin-bottom">
            <tr>
              <th>Noms &amp; prenoms</th>
              <th>Heure d'entree</th>
              <th>Heure de sortie</th>
              <th>Absences</th>
              <th>Motif</th>
            </tr>
            
            <?php
              if(isset($_SESSION['lvl-stats-day'])) unset($_SESSION['lvl-stats-day']);
              
              $lvlResult = $lvlSelect->fetch();
              $lvlStudentsSelect->bindParam(':niveau', $lvlResult['niveau']);
              $lvlStudentsSelect->execute();
              
              $lvlDelaysItemSelect->bindParam(':niveau', $lvlResult['niveau']);
              $lvlDelaysItemSelect->execute();
              $lvlDelaysItemResult = $lvlDelaysItemSelect->fetch();
              
              $lvlAbsencesItemSelect->bindParam(':niveau', $lvlResult['niveau']);
              $lvlAbsencesItemSelect->execute();
              $lvlAbsencesItemResult = $lvlAbsencesItemSelect->fetch();
            ?>
            <tr>
              <th colspan="5" class="w3-center">
                <?php
                  echo $lvlResult['niveau']. ' - Retards (';
                  echo ($lvlDelaysItemResult[0] == NULL) ? '0' : $lvlDelaysItemResult[0];
                  echo  ') - Absences (';
                  echo ($lvlAbsencesItemResult[0] == NULL) ? '0' : $lvlAbsencesItemResult[0];
                  echo 'H)';
                ?>
              </th>
            </tr>
            <?php
              while($lvlStudentsResult = $lvlStudentsSelect->fetch()){
                $hoursSelect->bindParam(':id_pers', $lvlStudentsResult['id_pers']);
                $hoursSelect->execute();
                $hoursResult = $hoursSelect->fetch();
                
                $absenceHours->bindParam(':id_pers', $lvlStudentsResult['id_pers']);
                $absenceHours->execute();
                $absenceHoursResult = $absenceHours->fetch();
            ?>
            <tr>
              <td><?php echo $lvlStudentsResult['nom']. ' ' .$lvlStudentsResult['prenom'] ?></td>
              <td>
                <?php
                  if(empty($hoursResult[0])) echo 'RAS';
                  else{
                    echo $hoursResult['heure_arrivee'];
                    if(($hoursResult['heure_arrivee'] > '07:46:00') && ($hoursResult['heure_arrivee'] < '07:59:59'))
                    echo ' <i title="Retard" class="fas fa-registered"></i>'; 
                  }
                ?>
              </td>
              <td><?php echo empty($hoursResult[0]) ? 'RAS' : $hoursResult['heure_depart'] ?></td>
              <td><?php echo empty($absenceHoursResult[0]) ? 'RAS' : $absenceHoursResult['nbre_heures']. 'H' ?></td>
              <td><?php echo empty($absenceHoursResult[0]) ? 'RAS' : $absenceHoursResult['libelle'] ?></td>
              <?php
              }
              ?>
            </tr>
        </table>
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
      
      var actions1 = document.getElementById("actions1");
      var classmatesList = document.getElementById("classmates-list");
      classmatesList.addEventListener("change", function(){actions1.submit()});
      
      var actions2 = document.getElementById("actions2");
      var lvlStatsDay = document.getElementById("lvl-stats-day");
      lvlStatsDay.addEventListener("change", function(){actions2.submit()});
      
      
      // Deconnexion
      var logout = document.getElementById("logout");
      logout.addEventListener('click', function(event){
        if(!confirm('Souhaitez-vous vraiment vous deconnecter?')) event.preventDefault();
      });
    </script>
  </body>
</html>