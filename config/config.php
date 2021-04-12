<?php
    // Connexion a la bade de donnees
    $DB_DSN = 'mysql:host=localhost;dbname=pointage_horaire';
    $DB_USER = 'root';
    $DB_PASS = '';
    
    try{
        $options =
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        $connexion = new PDO($DB_DSN, $DB_USER, $DB_PASS, $options);
    }
    catch(PDOException $e){
        echo 'Erreur de connexion : ' .$e->getMessage();
    }
    
    // GMT + 1
    date_default_timezone_set('Africa/Brazzaville');
    
    // Petite securite
    function securisation($donnee){
      $donnee = trim($donnee);
      $donnee = stripslashes($donnee);
      $donnee = strip_tags ($donnee);
        return $donnee;
    }
    
    // Initialisation de session
    function sessionInit(){
        if(!session_id()){
            session_start();
            session_regenerate_id();
        }
    }

  // Comptabilisation des absences
  function absencesCounter($inHour, $outHour){
    $inAbsences = 0;
    if(($inHour > '07:59:59') && ($inHour <= '09:59:59')) $inAbsences = 2;
    elseif(($inHour > '09:59:59') && ($inHour <= '12:59:59')) $inAbsences = 4;
    elseif(($inHour > '12:59:59') && ($inHour <= '14:59:59')) $inAbsences = 6;
    elseif(($inHour > '14:59:59') && ($inHour <= '16:59:59')) $inAbsences = 8;
      
    $outAbsences = 0;
    if(($outHour > '07:59:59') && ($outHour < '09:59:59')) $outAbsences = 8;
    elseif(($outHour > '09:59:59') && ($outHour <= '12:59:59')) $outAbsences = 6;
    elseif(($outHour > '12:59:59') && ($outHour <= '14:59:59')) $outAbsences = 4;
    elseif(($outHour > '14:59:59') && ($outHour <= '16:59:59')) $outAbsences = 2;

    $hours = $inAbsences + $outAbsences;
    if($hours > 8) $hours = 8;
    
    return $hours;
  }
?>