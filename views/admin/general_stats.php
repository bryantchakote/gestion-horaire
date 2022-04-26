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
$absencesItemSelect = $connexion->prepare("
    SELECT SUM(nbre_heures)
    FROM absences
    WHERE id_pers = :id_pers
");

// Nombre de retards et d'absences par type d'utilisateur
$usersDelaysItemSelect = $connexion->prepare("
  SELECT COUNT(*)
  FROM instants
  WHERE date_jour = :date_jour
  AND heure_arrivee BETWEEN '07:46:00' AND '07:59:59'
");

$usersAbsencesItemSelect = $connexion->prepare("
  SELECT SUM(nbre_heures)
  FROM absences
  WHERE date_jour = :date_jour
");

$ensDelaysItemSelect = $connexion->prepare("
  SELECT COUNT(*)
  FROM instants
  WHERE date_jour = :date_jour
  AND id_pers IN
  (SELECT id_pers FROM enseignants)
  AND heure_arrivee BETWEEN '07:46:00' AND '07:59:59'
");

$ensAbsencesItemSelect = $connexion->prepare("
  SELECT SUM(nbre_heures)
  FROM absences
  WHERE date_jour = :date_jour
  AND id_pers IN
  (SELECT id_pers FROM enseignants)
");

$staffDelaysItemSelect = $connexion->prepare("
  SELECT COUNT(*)
  FROM instants
  WHERE date_jour = :date_jour
  AND id_pers IN
  (SELECT id_pers FROM staff)
  AND heure_arrivee BETWEEN '07:46:00' AND '07:59:59'
");

$staffAbsencesItemSelect = $connexion->prepare("
  SELECT SUM(nbre_heures)
  FROM absences
  WHERE date_jour = :date_jour
  AND id_pers IN
  (SELECT id_pers FROM staff)
");

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
$lvlSelect = $connexion->prepare('SELECT DISTINCT niveau FROM etudiants');

// Etudiants
$lvlStudentsSelect = $connexion->prepare('
  SELECT *
  FROM etudiants
  NATURAL JOIN personnes
  WHERE niveau = :niveau
  ORDER BY nom
');

// Enseignants
$ensSelect = $connexion->prepare('
  SELECT *
  FROM enseignants
  NATURAL JOIN personnes
  ORDER BY nom
');
  
// Staff
$staffSelect = $connexion->prepare('
  SELECT *
  FROM staff
  NATURAL JOIN personnes
  ORDER BY nom
');

if(isset($_SESSION['general-stats-day']) && !empty($_SESSION['general-stats-day'])){
  $usersDelaysItemSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $usersAbsencesItemSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $ensDelaysItemSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $ensAbsencesItemSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $staffDelaysItemSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $staffAbsencesItemSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $lvlDelaysItemSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $lvlAbsencesItemSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $hoursSelect->bindParam(':date_jour', $_SESSION['general-stats-day']);
  $absenceHours->bindParam(':date_jour', $_SESSION['general-stats-day']);
}
else{
  $usersDelaysItemSelect->bindParam(':date_jour', $actualDate);
  $usersAbsencesItemSelect->bindParam(':date_jour', $actualDate);
  $ensDelaysItemSelect->bindParam(':date_jour', $actualDate);
  $ensAbsencesItemSelect->bindParam(':date_jour', $actualDate);
  $staffDelaysItemSelect->bindParam(':date_jour', $actualDate);
  $staffAbsencesItemSelect->bindParam(':date_jour', $actualDate);
  $lvlDelaysItemSelect->bindParam(':date_jour', $actualDate);
  $lvlAbsencesItemSelect->bindParam(':date_jour', $actualDate);
  $hoursSelect->bindParam(':date_jour', $actualDate);
  $absenceHours->bindParam(':date_jour', $actualDate);
}
$lvlSelect->execute();
$ensSelect->execute();
$staffSelect->execute();


$ensItem = $ensSelect->rowCount();
$staffItem = $staffSelect->rowCount();
$lvlItem = $lvlSelect->rowCount();

// Tableaux
// 1
$datesSelect = $connexion->prepare("SELECT DISTINCT date_jour FROM instants");
$datesSelect->execute();
  
$absencesChartItemSelect = $connexion->prepare("
  SELECT COUNT(*)
  FROM absences
  WHERE nbre_heures = '8'
  AND date_jour = :date_jour
");

// 2
$timeIn1 = $connexion->prepare("SELECT COUNT(*) FROM instants WHERE heure_arrivee < '07:00:00'");
$timeIn2 = $connexion->prepare("SELECT COUNT(*) FROM instants WHERE heure_arrivee BETWEEN '07:00:00' AND '07:29:59'");
$timeIn3 = $connexion->prepare("SELECT COUNT(*) FROM instants WHERE heure_arrivee BETWEEN '07:30:00' AND '07:59:59'");
$timeIn4 = $connexion->prepare("SELECT COUNT(*) FROM instants WHERE heure_arrivee BETWEEN '08:00:00' AND '08:29:59'");
$timeIn5 = $connexion->prepare("SELECT COUNT(*) FROM instants WHERE heure_arrivee >= '08:30:00'");

$timeIn1->execute();
$timeInResult1 = $timeIn1->fetch();

$timeIn2->execute();
$timeInResult2 = $timeIn2->fetch();

$timeIn3->execute();
$timeInResult3 = $timeIn3->fetch();

$timeIn4->execute();
$timeInResult4 = $timeIn4->fetch();

$timeIn5->execute();
$timeInResult5 = $timeIn5->fetch();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Statistiques generales</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="../../style/raleway.css">
    <link rel="stylesheet" href="../../style/w3schools.css">
    <link rel="stylesheet" href="../../style/personalized_styles.css">
    <script src="../../script/jquery-1.12.4.min.js"></script>
    <script src="../../script/Chart.js"></script>
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
      
      <?php
      $days = $totalAbs = [];
      $i = 0;
      while ($dateResult = $datesSelect->fetch()){
        $absencesChartItemSelect->bindParam(':date_jour', $dateResult['date_jour']);
        $absencesChartItemSelect->execute();
        $absencesChartItemResult = $absencesChartItemSelect->fetch();

        $year = substr($dateResult['date_jour'], 0, 4);
        $month = substr($dateResult['date_jour'], 5, 2);
        $day = substr($dateResult['date_jour'], 8, 2);

        $days[$i] = $year. '-'. $month. '-' .$day;
        $totalAbs[$i] = $absencesChartItemResult[0];

        echo '<p class="display-none date">'.$dateResult['date_jour'].'</p>';
        echo '<p class="display-none nb_absence">'.$absencesChartItemResult[0].'</p>';
      }
        echo '<p class="display-none nb_retard">'.$timeInResult1[0].'</p>';
        echo '<p class="display-none nb_retard">'.$timeInResult2[0].'</p>';
        echo '<p class="display-none nb_retard">'.$timeInResult3[0].'</p>';
        echo '<p class="display-none nb_retard">'.$timeInResult4[0].'</p>';
        echo '<p class="display-none nb_retard">'.$timeInResult5[0].'</p>';
      ?>
      
      <!-- Content -->
      <h5 class="w3-margin w3-text-blue-grey "><b>Statistiques générales</b></h5>
            <div class="w3-margin">
              <div class="w3-left" id="chart-left">
                <canvas id="basiclinechart"></canvas>
              </div>
              <div class="w3-right" id="chart-right">
                <canvas id="basiclinechart_delays"></canvas>
              </div>
            </div><br><br><br><br><br><br><br><br><br><br><br><br>
      <h5 class="w3-margin w3-text-blue-grey"><b>Détail journée</b></h5>
        <form method="post" action="../../models/actions.php" id="actions" class="w3-row-padding w3-padding-large w3-center">
          <span><b>Journée du</b></span>
          <input type="date" name="general-stats-day" id="general-stats-day" value="<?php echo (isset($_SESSION['general-stats-day']) && !empty($_SESSION['general-stats-day'])) ? $_SESSION['general-stats-day'] : $actualDate ?>" class="w3-button w3-hover-red w3-padding-small">  
        </form>
      
          <table class="w3-table-all w3-centered w3-hoverable w3-margin-top w3-margin-bottom">
            <tr>
              <th>Noms &amp; prénoms</th>
              <th>Heure d'entrée</th>
              <th>Heure de sortie</th>
              <th>Absences</th>
              <th>Motif</th>
            </tr>
            
            <?php
            unset($_SESSION['general-stats-day']);
            $i = 0;
            while($lvlResult = $lvlSelect->fetch()){
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
              <th colspan="5" class="w3-center" id="lvl-content<?php echo $i ?>">
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
              $lvlCount[$i] = 0;
              while($lvlStudentsResult = $lvlStudentsSelect->fetch()){
                $lvlCount[$i]++;
                
                $hoursSelect->bindParam(':id_pers', $lvlStudentsResult['id_pers']);
                $hoursSelect->execute();
                $hoursResult = $hoursSelect->fetch();
                
                $absenceHours->bindParam(':id_pers', $lvlStudentsResult['id_pers']);
                $absenceHours->execute();
                $absenceHoursResult = $absenceHours->fetch();
            ?>
            <tr class="std<?php echo $i ?>-list">
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
            <?php
            $i++;
            }
            ?>

            
            <tr>
              <th colspan="5" class="w3-center" id="ens-content">
                <?php
                  $ensDelaysItemSelect->execute();
                  $ensDelaysItemResult = $ensDelaysItemSelect->fetch();

                  $ensAbsencesItemSelect->execute();
                  $ensAbsencesItemResult = $ensAbsencesItemSelect->fetch();
                  
                  echo 'Enseignants - Retards (';
                  echo ($ensDelaysItemResult[0] == NULL) ? '0' : $ensDelaysItemResult[0];
                  echo ') - Absences (';
                  echo ($ensAbsencesItemResult[0] == NULL) ? '0' : $ensAbsencesItemResult[0];
                  echo 'H)';
                ?>
              </th>
            </tr>
            <?php
              while($ensResult = $ensSelect->fetch()){
                $hoursSelect->bindParam(':id_pers', $ensResult['id_pers']);
                $hoursSelect->execute();
                $hoursResult = $hoursSelect->fetch();
                
                $absenceHours->bindParam(':id_pers', $ensResult['id_pers']);
                $absenceHours->execute();
                $absenceHoursResult = $absenceHours->fetch();
            ?>
            <tr class="ens-list">
              <td><?php echo $ensResult['nom']. ' ' .$ensResult['prenom'] ?></td>
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
            
            
            <tr>
              <th colspan="5" class="w3-center" id="stf-content">
                <?php
                  $staffDelaysItemSelect->execute();
                  $staffDelaysItemResult = $staffDelaysItemSelect->fetch();

                  $staffAbsencesItemSelect->execute();
                  $staffAbsencesItemResult = $staffAbsencesItemSelect->fetch();
                  
                  echo 'Staff - Retards (';
                  echo ($staffDelaysItemResult[0] == NULL) ? '0' : $staffDelaysItemResult[0];
                  echo  ') - Absences (';
                  echo ($staffAbsencesItemResult[0] == NULL) ? '0' : $staffAbsencesItemResult[0];
                  echo 'H)';
                ?>
              </th>
            </tr>
            <?php
              while($staffResult = $staffSelect->fetch()){
                $hoursSelect->bindParam(':id_pers', $staffResult['id_pers']);
                $hoursSelect->execute();
                $hoursResult = $hoursSelect->fetch();
                
                $absenceHours->bindParam(':id_pers', $staffResult['id_pers']);
                $absenceHours->execute();
                $absenceHoursResult = $absenceHours->fetch();
            ?>
            <tr class="stf-list">
              <td><?php echo $staffResult['nom']. ' ' .$staffResult['prenom'] ?></td>
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
            </tr>  
            <?php } ?>
            
            <tr>
              <th colspan="5" class="w3-center">
                <?php
                  $usersDelaysItemSelect->execute();
                  $usersDelaysItemResult = $usersDelaysItemSelect->fetch();

                  $usersAbsencesItemSelect->execute();
                  $usersAbsencesItemResult = $usersAbsencesItemSelect->fetch();
                  
                  echo 'Total - Retards (';
                  echo ($usersDelaysItemResult[0] == NULL) ? '0' : $usersDelaysItemResult[0];
                  echo  ') - Absences (';
                  echo ($usersAbsencesItemResult[0] == NULL) ? '0' : $usersAbsencesItemResult[0];
                  echo 'H)';
                ?>
              </th>
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
      var generalStatsDay = document.getElementById("general-stats-day");
      generalStatsDay.addEventListener("change", function(){actions.submit()});
    </script>
    <script>
      var date = document.querySelectorAll(".date");
      var a = [];
      for (i=0; i<date.length ;i++) {
        a.push(date[i].textContent);
      }
      
      var nb_absence = document.querySelectorAll(".nb_absence");
      var b = [];
      for (i=0; i<nb_absence.length ;i++) {
        b.push(nb_absence[i].textContent);
      }
      
      var delays = document.querySelectorAll(".nb_retard");
      var c = [];
      for (i=0; i<delays.length ;i++) {
        c.push(delays[i].textContent);
      }
      
      
(function ($) {
	 /*----------------------------------------*/
	/*  1.  Basic Line Chart
	/*----------------------------------------*/
  

	var ctx = document.getElementById("basiclinechart");
	var basiclinechart = new Chart(ctx, {
		type: 'line',
		data: {
			labels: a,
			datasets: [{
				label: "Nombre d'absents",
				fill: false,
                backgroundColor: '#C12724',
				borderColor: '#C12724',
				data: b
            }]
		},
		options: {
			responsive: true,
			
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest',
				intersect: true
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: ''
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: ''
					}
				}]
			}
		}
	});
})(jQuery);



(function ($) {
	var ctx = document.getElementById("basiclinechart_delays");
	var basiclinechart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ["< 7H", "7H - 7H30","7H30 - 8H","8H - 8H30","> 8H30"],
			datasets: [{
				label: "Arrivées",
				fill: false,
                backgroundColor: '#C12724',
				borderColor: '#C12724',
				data: c
            }]
		},
		options: {
			responsive: true,
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest',
				intersect: true
			},
			scales: {
				xAxes: [{
					display: true,
					scaleLabel: {
						display: true,
					}
				}],
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
					}
				}]
			}
		}
	});
})(jQuery);
      
      // Deconnexion
      var logout = document.getElementById("logout");
      logout.addEventListener('click', function(event){
        if(!confirm('Souhaitez-vous vraiment vous deconnecter?')) event.preventDefault();
      });

  </script>
  </body>
</html>