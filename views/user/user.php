<?php
include '../../config/config.php';

sessionInit();

// Nombre de retards
$delaysItemSelect = $connexion->prepare("
    SELECT COUNT(*)
    FROM instants
    WHERE id_pers =  '" .$_SESSION['id']. "'
    AND heure_arrivee BETWEEN '07:46:00' AND '07:59:59'
");
$delaysItemSelect->execute();
$delaysItemResult = $delaysItemSelect->fetch();

// Nombre d'absences
$absencesItemSelect = $connexion->prepare('
    SELECT SUM(nbre_heures)
    FROM absences
    WHERE id_pers = ' .$_SESSION['id']
);
$absencesItemSelect->execute();
$absencesItemResult = $absencesItemSelect->fetch();

if(isset($_SESSION['delays-display']) && !empty($_SESSION['delays-display']) && ($_SESSION['delays-display'] == 1)){
    $datesSelect = $connexion->prepare("
          SELECT date_jour
          FROM instants
          WHERE id_pers = '" .$_SESSION['id']. "'
          AND heure_arrivee BETWEEN '07:46:00' AND '07:59:59'
    ");
    unset($_SESSION['delays-display']);
}
        
elseif(isset($_SESSION['absences-display']) && !empty($_SESSION['absences-display']) && ($_SESSION['absences-display'] == 1)){
    $datesSelect = $connexion->prepare("
        SELECT date_jour
        FROM absences
        WHERE id_pers = '" .$_SESSION['id']. "'
    ");
    unset($_SESSION['absences-display']);
}

else
    $datesSelect = $connexion->prepare('
      SELECT date_jour FROM instants WHERE id_pers = ' .$_SESSION['id']. '
      UNION
      SELECT date_jour FROM absences WHERE id_pers = ' .$_SESSION['id']. '
      ORDER BY date_jour
    ');

    
$hoursSelect = $connexion->prepare("
    SELECT *
    FROM instants
    WHERE id_pers = '" .$_SESSION['id']. "'
    AND date_jour = :date_jour;
");
$absencesSelect = $connexion->prepare("
    SELECT *
    FROM absences
    NATURAL JOIN motifs
    WHERE id_pers = '" .$_SESSION['id']. "'
    AND date_jour = :date_jour
");

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Espace utilisateur</title>
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
          <button type="submit" name="display-choice" value="delays" id="delays" class="w3-bar-item w3-button  w3-hover-red w3-padding"><i class="fas fa-clock w3-margin-right"></i>Mes retards (<?php echo $delaysItemResult[0] ?>)</button>
          <button type="submit" name="display-choice" value="absences" id="absences" class="w3-bar-item w3-button  w3-hover-red w3-padding"><i class="fas fa-calendar-times w3-margin-right"></i>Mes absences (<?php echo ($absencesItemResult[0] == NULL) ? '0' : $absencesItemResult[0] ?>H)</button>
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
      <div class="content">
      <?php
      $datesSelect->execute();
      $datesIndex = $datesSelect->rowCount();
      
      $viewsNumber = ceil($datesIndex/24);
      
      for($i = 0; $i < $viewsNumber; $i++){
      ?>
      <div class="w3-row-padding view<?php echo $i ?>">
      <?php
        $j = 1;
        while(($j <= 24) && ($resultDate = $datesSelect->fetch())){
          $j++;
          
          $year = substr($resultDate['date_jour'], 0, 4);
          $month = substr($resultDate['date_jour'], 5, 2);
          $day = substr($resultDate['date_jour'], 8, 2);
          
          $timestamp = mktime(0, 0, 0, $month, $day, $year);
 
          $date = date('D', $timestamp);
          
          switch ($date){
            case "Mon": $frDate = "Lun"; break;
            case "Tue": $frDate = "Mar"; break;
            case "Wed": $frDate = "Mer"; break;
            case "Thu": $frDate = "Jeu"; break;
            case "Fri": $frDate = "Ven"; break;
            case "Sat": $frDate = "Sam"; break;
            case "Sun": $frDate = "Dim"; break;
            default: break;
          }
          
          $hoursSelect->bindParam(':date_jour', $resultDate['date_jour']);
          $absencesSelect->bindParam(':date_jour', $resultDate['date_jour']);
          $hoursSelect->execute();
          $absencesSelect->execute();
          $resultHour = $hoursSelect->fetch();
          $resultAbsences = $absencesSelect->fetch();
        ?>
        <div class="w3-third w3-container w3-margin-bottom">
          <div class="w3-container infos-container w3-hover-shadow">
            <p class="w3-blue-grey"><?php echo $frDate. ' ' .$day. '-' .$month. '-' .$year ?></p>
          <?php
          if(!empty($resultHour)){
            $heureEntree = substr($resultHour['heure_arrivee'], 0, 5);
            $heureDepart = substr($resultHour['heure_depart'], 0, 5);
          ?>
            <p>
              In <?php echo $heureEntree ?>
              <?php
              if(($resultHour['heure_arrivee'] > '07:45:59') && ($resultHour['heure_arrivee'] < '08:00:00'))
                echo '<i title="Retard" class="fas fa-registered"></i>';
              ?>
              - Out <?php echo $heureDepart ?>
            </p>
          <?php
          }
          if(!empty($resultAbsences)){
          ?>
            <p>
              Absences <?php echo $resultAbsences['nbre_heures'] ?>H
              <?php
              if($resultAbsences['id_motif'] <= 2){
                echo '<i title="Non justifiee" class="fas fa-question"></i>';
              }
              else
                echo '<i title="Justifiee" class="fas fa-check"></i><br>Motif : ' .$resultAbsences['libelle'];
              ?>
            </p>
          <?php
          }
        ?>
          </div>
        </div>
        <?php
        }
      ?>
      </div>
      <?php
      }
      ?>
      
      <div class="w3-margin-left w3-margin-right w3-margin-top w3-bottom w3-clear">
        <!-- Pagination -->
        <div class="w3-left">
          <div class="w3-bar">
            <button class="w3-bar-item w3-button w3-hover-red" id="previous" title="previous">&laquo;</button>
            
            <?php
            for($i = 1; $i <= $viewsNumber; $i++){
              echo '<button class="w3-bar-item w3-button w3-hover-red view-item">' .$i. '</button>';
            }
            ?>
            
            <button class="w3-bar-item w3-button w3-hover-red" id="next" title="next">&raquo;</button>
          </div>
        </div>
        
        <!-- Infos journee -->
        <div class="w3-right">
          <span><b>Afficher journee du</b></span>
          <input type="date" name="date-search" id="date-search" value="<?php echo date('Y-m-d') ?>" class="w3-hover-red w3-button w3-padding-small">
        </div>
      </div>
    </div>
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
      
      // Afficher / Masquer bloc
      function showView(view, button){
        view.style.display = "block";
        button.style.backgroundColor = "RGBa(185, 13, 29, 0.7)";
        button.style.color = "#FFF";
      }
      function hideView(view, button){
        view.style.display = "none";
        button.style.backgroundColor = "";
        button.style.color = "";
      }
      
      // Quelques variables
      var datesIndex = <?php echo $datesIndex ?>;
      var viewsNumber = <?php echo $viewsNumber ?>;
      
      var views = document.querySelectorAll(".w3-row-padding");
      var buttons = document.querySelectorAll(".view-item");
      
      var lastView = viewsNumber - 1;
      
      // Afficher dernier slide au chargement
      showView(views[lastView], buttons[lastView]);
      
      // Deconnexion
      var logout = document.getElementById("logout");
      logout.addEventListener('click', function(event){
        if(!confirm('Souhaitez-vous vraiment vous deconnecter?')) event.preventDefault();
      });

      // Navigation (n'importe quel bouton)
      <?php for($i = 0; $i < $viewsNumber; $i++){ ?>
        buttons[<?php echo $i ?>].addEventListener("click", function(){
          <?php for($j = 0; $j < $viewsNumber; $j++) { ?>
            hideView(views[<?php echo $j ?>], buttons[<?php echo $j ?>]);
          <?php } ?>
          showView(views[<?php echo $i ?>], this);
        });
      <?php } ?>
      
      // Navigation (previous & next)      
      var previous = document.getElementById("previous");
      var next = document.getElementById("next");
      
      function move(start, limit, action){
        for(i = start; i < limit; i++)
          if(views[i].style.display == "block"){
            if(action == 'previous') var j = i - 1;
            else var j = i + 1;
            
            hideView(views[i], buttons[i]);
            showView(views[j], buttons[j]);
            
            i = viewsNumber;
          }
      }
      
      previous.addEventListener("click", function(){move(1, viewsNumber, 'previous')});
      next.addEventListener("click", function(){move(0, lastView, 'next')});

      // Rechercher date
      var infosContainer = document.querySelectorAll(".infos-container");
      var dateSearch = document.getElementById("date-search");
      
      dateSearch.addEventListener("change", function(){
        var year = dateSearch.value.substring(0, 4);
        var month = dateSearch.value.substring(5, 7);
        var day = dateSearch.value.substring(8, 10);
        var choosenDate = day + "-" + month + "-" + year;
        var found = false;
        
        for(i = 0; i < datesIndex; i++){
          var entireTestedDate = infosContainer[i].childNodes[1].textContent;
          var testedDate = entireTestedDate.substring(4, 14);

          if(testedDate == choosenDate){
            found = true;
            var concernedView = infosContainer[i].parentNode.parentNode.getAttribute('class');
            var item = concernedView.split('w3-row-padding view')[1];

            for(j = 0; j < viewsNumber; j++){
              hideView(views[j], buttons[j]);
            }
            showView(views[item], buttons[item]);

            setTimeout('infosContainer[' + i + '].style.boxShadow = "0px 0px 10px RGBa(185, 13, 29, 0.8)"', 100);
            setTimeout('infosContainer[' + i + '].style.boxShadow = ""', 400);
            
          }
        }
        if(!found) alert("Aucune information disponible pour cette date");
      });
    </script>
  </body>
</html>