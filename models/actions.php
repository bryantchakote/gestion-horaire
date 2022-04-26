<?php
include "../config/config.php";

sessionInit();

// Afficher les absences et les retards
if(isset($_POST['display-choice']) && !empty($_POST['display-choice'])){
  if($_POST['display-choice'] == 'delays')
    $_SESSION['delays-display'] = 1;
  
  if($_POST['display-choice'] == 'absences')
    $_SESSION['absences-display'] = 1;
  
  if($_SESSION['status'] == 'user')
    header("location: ../views/user/user.php");
  
  elseif($_SESSION['status'] == 'class_prefect')
    header("location: ../views/class_prefect/class_prefect.php");
  
  elseif($_SESSION['status'] == 'admin')
    header("location: ../views/admin/admin.php");
}


// Afficher la carte d'un camarade de classe
if(isset($_POST['classmates-list']) && !empty($_POST['classmates-list'])){
  $_SESSION['classmates-list'] = $_POST['classmates-list'];
  header("location: ../views/class_prefect/class_prefect.php");
}

// Afficher la carte d'un utilisateur
if(isset($_POST['users-list']) && !empty($_POST['users-list'])){
  $_SESSION['users-list'] = $_POST['users-list'];
  header("location: ../views/admin/admin.php");
}


// Deconnexion
if(isset($_POST['logout']) && !empty($_POST['logout']))
  header("location: ../index.php");



// Gestionnaire d'absences
if(isset($_POST['absence-day']) && !empty($_POST['absence-day'])){
  if(!(isset($_POST['confirm-absences']) && !empty($_POST['confirm-absences']))){
    $_SESSION['absence-day'] = $_POST['absence-day'];
    echo "<script>location.assign('../views/admin/absences_manager.php')</script>";
  }
}
if(isset($_POST['confirm-absences']) && !empty($_POST['confirm-absences'])){
  $usersSelect = $connexion->prepare("SELECT * FROM personnes");
  $usersSelect->execute();
  
  $absencesSelect = $connexion->prepare("
    SELECT *
    FROM absences
    WHERE date_jour = :date_jour
    AND id_pers = :id_pers
  ");
  
  $absencesUpdate = $connexion->prepare("
    UPDATE absences
    SET id_motif = :id_motif
    WHERE id_abs = :id_abs
    AND id_motif = 1
  ");
  
  $i = 0;
  while($userResult = $usersSelect->fetch()){
    $IDs[$i] = $userResult['id_pers'];
    $i++;
  }
  
  for($j = 0; $j < $i; $j++){
    if(isset($_POST['hours'.$IDs[$j]]) && !empty($_POST['hours'.$IDs[$j]])){
      $date_jour = $_POST['absence-day'];
      $id_pers = $_POST['user-id'.$IDs[$j]];
      $id_motif = $_POST['motif'.$IDs[$j]];
      
      $absencesSelect->bindParam(':date_jour', $date_jour);
      $absencesSelect->bindParam(':id_pers', $id_pers);
      $absencesSelect->execute();
      $absenceResult = $absencesSelect->fetch();
      
      if(!empty($absenceResult)){
        // update
        $absencesUpdate->bindParam(':id_motif', $id_motif);
        $absencesUpdate->bindParam(':id_abs', $absenceResult['id_abs']);
        $absencesUpdate->execute();
      }
      echo '<script>alert("Operation effectuee"); location.assign("../views/admin/absences_manager.php")</script>';
    }
  }
}


// Insertion pointages
if(isset($_POST['in-out-day']) && !empty($_POST['in-out-day'])){
  if(!(isset($_POST['in-out-confirm']) && !empty($_POST['in-out-confirm']))){
    $dateVerification = $connexion->prepare("
      SELECT DISTINCT date_jour
      FROM instants
      WHERE date_jour = '" .$_POST['in-out-day']. "'
    ");
    $dateVerification->execute();
    $dateVerificationResult = $dateVerification->fetch();
    
    if(!empty($dateVerificationResult)){
      echo "<script>alert('Les informations concernant la date selectionnée existent déjà')</script>";
      $_SESSION['existing-date'] = true;
    }
    else $_SESSION['selected-date'] = $_POST['in-out-day'];
  }
  echo "<script>location.assign('../views/admin/in_out_manager.php')</script>";
}
if(isset($_POST['in-out-confirm']) && !empty($_POST['in-out-confirm'])){  
  $date_jour = $_POST['in-out-day'];
  
  $usersSelect = $connexion->prepare("SELECT * FROM personnes");
  $usersSelect->execute();
  
  $inOutInsert = $connexion->prepare("
    INSERT INTO instants(date_jour, heure_arrivee, heure_depart, id_pers) VALUES
    (:date_jour, :heure_arrivee, :heure_depart, :id_pers)
  ");
  $inOutInsert->bindParam(':date_jour', $date_jour);
  
  $absencesInsert = $connexion->prepare("
    INSERT INTO absences(date_jour, nbre_heures, id_pers, id_motif) VALUES
    (:date_jour, :nbre_heures, :id_pers, :id_motif)
  ");
  $absencesInsert->bindParam(':date_jour', $date_jour);
  $absencesInsert->bindValue(':id_motif', '1');
  
  $i = 0;
  while($userResult = $usersSelect->fetch()){
    $IDs[$i] = $userResult['id_pers'];
    $i++;
  }
  
  for($j = 0; $j < $i; $j++){
    $id_pers = $_POST['user-id'.$IDs[$j]];
    $inOutInsert->bindParam(':id_pers', $id_pers);
    $absencesInsert->bindParam(':id_pers', $id_pers);
    
    if((isset($_POST['in'.$IDs[$j]]) && !empty($_POST['in'.$IDs[$j]])) &&
       (isset($_POST['out'.$IDs[$j]]) && !empty($_POST['out'.$IDs[$j]]))
    ){
      $heure_arrivee = $_POST['in'.$IDs[$j]];
      $heure_depart = $_POST['out'.$IDs[$j]];
      
      $inOutInsert->bindParam(':heure_arrivee', $heure_arrivee);
      $inOutInsert->bindParam(':heure_depart', $heure_depart);
      $inOutInsert->execute();
      
      $nbre_heures = absencesCounter($heure_arrivee, $heure_depart);
      if($nbre_heures > 0){
        $absencesInsert->bindParam(':nbre_heures', $nbre_heures);
        $absencesInsert->execute();
      }
    }
    else{
      $absencesInsert->bindValue(':nbre_heures', '8');
      $absencesInsert->execute();
    }
  }
  echo "<script>alert('Informations de la journée ajoutées avec succès');location.assign('../views/user/user.php')</script>";
}


// Ajouter un utilisateur
if(isset($_POST['enreg-submit']) && !empty($_POST['enreg-submit'])){
  $nom = securisation($_POST['enreg-nom']);
  $prenom = securisation($_POST['enreg-prenom']);
  $sexe = securisation($_POST['enreg-sexe']);
  $email = securisation($_POST['enreg-email']);
  $mdp = password_hash($_POST['enreg-pwd2'], PASSWORD_BCRYPT);
          
  $userSelect = $connexion->prepare('SELECT * FROM personnes');
  $userSelect->execute();
  $userRedundancy = 0;
  $stopUsersFetch = false;
            
  // Le mail ne doit pas se repeter
  while(($userResult = $userSelect->fetch()) && ($stopUsersFetch == false))
    if($userResult['email'] == $_POST['enreg-email'])
      {$userRedundancy = 1; $stopUsersFetch = true;}
                
  if($userRedundancy == 1)
    echo "<script>alert('Adresse mail invalide'); location.assign('../views/admin/user_registration../views/')</script>";
  else{
    $addUser = $connexion->prepare('
      INSERT INTO personnes(nom, prenom, sexe, email, mdp)
      VALUES(:nom, :prenom, :sexe, :email, :mdp)
    ');
        
    $addUser->bindParam('nom', $nom);
    $addUser->bindParam('prenom', $prenom);
    $addUser->bindParam('sexe', $sexe);
    $addUser->bindParam('email', $email);
    $addUser->bindParam('mdp', $mdp);
    $addUser->execute();
  }
    
  $lastUserIdSelect = $connexion->prepare("SELECT id_pers FROM personnes ORDER BY id_pers DESC LIMIT 1");
  $lastUserIdSelect->execute();
  $lastUserIdResult = $lastUserIdSelect->fetch();
  
  if($_POST['qualite'] == 'etudiant'){
    $studentsSelect = $connexion->prepare('SELECT * FROM etudiants');
    $studentsSelect->execute();
    $studentRedundancy = 0;
    $stopStudentsFetch = false;

    // Le mail ne doit pas se repeter
    while(($studentResult = $studentsSelect->fetch()) && ($stopStudentsFetch == false))
      if($studentResult['matricule'] == $_POST['enreg-matricule'])
        {$studentRedundancy = 1; $stopStudentsFetch = true;}

    if($studentRedundancy == 1)
      echo "<script>alert('Matricule invalide'); location.assign('../views/admin/user_registration.php')</script>";
    else{
      $userType = $connexion->prepare("
        INSERT INTO etudiants(id_pers, matricule, niveau, est_delegue)VALUES
        (:id_pers, :matricule, :niveau, :est_delegue)
      ");

      $userType->bindParam(':matricule', $_POST['enreg-matricule']);
      $userType->bindParam(':niveau', $_POST['enreg-niveau']);
      if(isset($_POST['delegue']) && !empty($_POST['delegue'])) $userType->bindParam(':est_delegue', $_POST['delegue']);
      else $userType->bindValue(':est_delegue', '0');
    }
  }
  elseif($_POST['qualite'] == 'enseignant'){
    $userType = $connexion->prepare("INSERT INTO enseignants(id_pers) VALUES (:id_pers)");
  }
  elseif($_POST['qualite'] == 'staff'){
    $userType = $connexion->prepare("INSERT INTO staff(id_pers, est_admin) VALUES (:id_pers, :est_admin)");
    if(isset($_POST['admin']) && !empty($_POST['admin'])) $userType->bindParam(':est_admin', $_POST['admin']);
    else $userType->bindValue(':est_admin', '0');
  }
    
  $userType->bindParam(':id_pers', $lastUserIdResult['id_pers']);
  $userType->execute();
    
  echo "<script>alert('Utilisateur enregistré'); location.assign('../views/admin/user_registration.php')</script>";
}

// Statistiques
if(isset($_POST['general-stats-day']) && !empty($_POST['general-stats-day'])){
  $datesSelect = $connexion->prepare('
    SELECT DISTINCT date_jour FROM instants
    UNION
    SELECT DISTINCT date_jour FROM absences
  ');
  $datesSelect->execute();
  
  $_SESSION['general-stats-day'] = $_POST['general-stats-day'];
  $found = false;
  
  while($dateResult = $datesSelect->fetch()){
    if($dateResult['date_jour'] == $_POST['general-stats-day']) $found = true;
  }
    
  if(!$found){
    echo "<script>alert('Aucune information pour la date selectionnée')</script>";
    $_SESSION['general-stats-day'] = date('Y-m-d');
  }
  
  echo "<script>location.assign('../views/admin/general_stats.php')</script>";
}

if(isset($_POST['lvl-stats-day']) && !empty($_POST['lvl-stats-day'])){
  $datesSelect = $connexion->prepare('
    SELECT DISTINCT date_jour FROM instants
    UNION
    SELECT DISTINCT date_jour FROM absences
  ');
  $datesSelect->execute();
  
  $_SESSION['lvl-stats-day'] = $_POST['lvl-stats-day'];
  $found = false;
  
  while($dateResult = $datesSelect->fetch()){
    if($dateResult['date_jour'] == $_POST['lvl-stats-day']) $found = true;
  }
    
  if(!$found){
    echo "<script>alert('Aucune information pour la date selectionnée')</script>";
    $_SESSION['lvl-stats-day'] = date('Y-m-d');
  }
  
  echo "<script>location.assign('../views/class_prefect/level_stats.php')</script>";
}
?>