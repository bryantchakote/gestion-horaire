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

// Differents niveaux
$lvlSelect = $connexion->prepare('SELECT DISTINCT niveau FROM etudiants');
$lvlSelect->execute();

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Enregistrer utilisateur</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="../../style/raleway.css">
    <link rel="stylesheet" href="../../style/w3schools.css">
    <link rel="stylesheet" href="../../style/personalized_styles.css">
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
	<script>
      // Activation / Desactivation des champs en fonction du usertype
      function activeEtudiant(){
        admin.checked = false;
        
        etExtensions.style.display = "block";
        stfExtensions.style.display = "";
      }
      
      function activeEnseignant(){
        delegue.checked = false;
        admin.checked = false;
        
        enregMatricule.value = "";
        enregNiveau.value = "Niveau";
        
        matValidation.innerHTML = "";
        nivValidation.innerHTML = "";
        
        etExtensions.style.display = "";
        stfExtensions.style.display = "";
      }
      
      function activeStaff(){
        delegue.checked = false;
        
        enregMatricule.value = "";
        enregNiveau.value = 'Niveau';
        
        matValidation.innerHTML = "";
        nivValidation.innerHTML = "";
        
        etExtensions.style.display = "";
        stfExtensions.style.display = "block";
      }
      // Verification en local de l'integrite des champs d'enregistrement
      function reset_focus(element){
        element.value = '';
        element.focus();
      }
      
      function namesEmailVerification(regex, element, elementName, span, event){
        reset();
        try{
          if(element.value == '') throw 'Veuillez entrer ' + elementName;
          else if(!regex.test(element.value)) throw 'Mauvais format';
          else throw '';
        }
        catch(e){
          span.innerHTML = e;
        }
        
        if(span.innerHTML != ''){
          reset_focus(element);
          event.preventDefault();
        }
        else return 124;
      }
      
      function sexeVerification(event){
        reset();
        try{
          if(enregSexe.value == '') throw 'Veuillez entrer une valeur';
          if((enregSexe.value != 'M') && (enregSexe.value != 'F')) throw 'Valeur invalide';
          else throw '';
        }
        catch(sexe){
          sexeValidation.innerHTML = sexe;
        }
        
        if(sexeValidation.innerHTML != ''){
          reset_focus(enregSexe);
          event.preventDefault();
        }
        else return 3;
      }
      
      function pwdLengthVerification(event){
        reset();
        try{
          if(enregPwd1.value == '') throw 'Veuillez entrer un mot de passe';
          if(enregPwd1.value.length < 6) throw 'Mot de passe trop court';
          else throw '';
        }
        catch(e){
          pwdLengthValidation.innerHTML = e;
        }
        
        if(pwdLengthValidation.innerHTML != ''){
          reset_focus(enregPwd1);
          event.preventDefault();
        }
        else return 5;
      }
      
      function pwdVerification(event){
        reset();
        try{
          if(enregPwd2.value == '') throw 'Veuillez confirmer le mot de passe';
          if(enregPwd2.value != enregPwd1.value) throw 'Les mots de passe ne correspondent pas';
          else throw '';
        }
        catch(e){
          pwdValidation.innerHTML = e;
        }
        if(pwdValidation.innerHTML != ''){
          reset_focus(enregPwd2);
          event.preventDefault();
        }
        else return 6;
      }
      
      function reset(){
        if(($('#etudiant').is(':checked')) || ($('#enseignant').is(':checked')) || ($('#staff').is(':checked'))){userTypeValidation.innerHTML = ''}
        
        if(enregMatricule.value != ''){matValidation.innerHTML = ''}
        
        if(enregNiveau.value != 'Niveau'){nivValidation.innerHTML = ''}
      }
      
      function userTypeVerification(event){
        try{
          if(!($('#etudiant').is(':checked')) && !($('#enseignant').is(':checked')) && !($('#staff').is(':checked')))
            throw 'Selectionnez "Etudiant", "Enseignant" ou "Staff" ci-dessous';
          else throw '';
        }
        catch(userType){
          userTypeValidation.innerHTML = userType;
        }
        
        if(userTypeValidation.innerHTML != '') event.preventDefault();
        else return 1;
      }
      
      function matVerification(event){
        try{
          if(($('#etudiant').is(':checked')) && (enregMatricule.value == '')) throw 'Veuillez entrer un matricule';
          
          if(enregMatricule.value != '')
            if((/^\d+$/.test(enregMatricule.value)) == false || (enregMatricule.value.length < 11))
              throw 'Matricule invalide';
        }
        catch(mat){
          matValidation.innerHTML = mat;
        }
        
        if(matValidation.innerHTML != ''){
          reset_focus(enregMatricule);
          event.preventDefault();
        }
        else return 1;
      }
      
      function nivVerification(event){
        try{
          if(($('#etudiant').is(':checked')) && (enregNiveau.value == 'Niveau')) throw 'Veuillez choisir un niveau';
           else throw '';
        }
        catch(niv){
          nivValidation.innerHTML = niv;
        }
        
        if(nivValidation.innerHTML != '') event.preventDefault();
        else return 1;
      }
      
      // Verifications a l'envoi des donnees - Se fait de maniere sequentielle, un champ apres l'autre, d'ou les if repetes
      function enregValidation(){
        var n=0;
        
        if(n == 0){
          m = namesEmailVerification(regNoms, enregNom, 'un nom', name1Validation, event);
          if(m == 124)
            n++;
        }
        
        if(n == 1){
          if(enregPrenom.value != ''){
            m = namesEmailVerification(regNoms, enregPrenom, 'un prénom', name2Validation, event);
            if(m == 124)
              n++;
          }
          else
            n++;
        }
        
        if(n == 2){
          m = sexeVerification(event);
          if(m == 3)
          n++;
        }
        
        if(n == 3){
          m = namesEmailVerification(regEmail, enregEmail, 'une adresse mail', emailValidation, event);
          if (m == 124)
            n++;
        }
        
        if(n == 4){
          m = pwdLengthVerification(event);
          if(m == 5)
            n++;
        }
        
        if(n == 5){
          m = pwdVerification(event);
          if(m == 6)
            n++;
        }
        
        if(
          enregNom.value != '' &&
          enregSexe.value != '' &&
          enregEmail.value != '' &&
          enregPwd1.value != '' &&
          enregPwd2.value != ''
        )
        {
          userTypeVerification(event);
          matVerification(event);
          nivVerification(event);
        }
      }
    </script>
  </head>
  
  <body class="w3-content w3-light-grey">
    
    <!-- Sidebar/menu -->
    <nav class="w3-blue-grey w3-sidebar w3-collapse w3-hover-shadow w3-animate-left" id="mySidebar">
      <div class="w3-container">
        <a href="#" onclick="w3_close()" class="w3-hide-large w3-right w3-jumbo w3-padding w3-hover-white" title="close menu">
          <i class="fa fa-times"></i>
        </a>
        <img src="../../images/img.jpg" id='user-img' class="w3-circle w3-margin-top"><br><br>
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
          <button type="submit" name="display-choice" value="today" id="today" class="w3-bar-item w3-button w3-hover-red w3-hover-red w3-padding"><i class="fas fa-calendar-day w3-margin-right"></i>Aujourd'hui</button>
          
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
      <div class='w3-table-all' id='enreg-content'>
        <form method='post' action='../../models/actions.php'>
          <div class='enreg-left w3-left'>
            <input class='w3-input w3-button w3-hover-white w3-margin-top w3-margin-bottom' type='text' name='enreg-nom' id='enreg-nom' required='required' placeholder='Nom'>
            <span id='name1-validation'></span>

            <input class='w3-input w3-button w3-hover-white w3-margin-top w3-margin-bottom' type='text' name='enreg-prenom' id='enreg-prenom' placeholder='Prénom'>
            <span id='name2-validation'></span>

            <input class='w3-input w3-button w3-hover-white w3-margin-top w3-margin-bottom' type='text' name='enreg-sexe' id='enreg-sexe' required='required' placeholder='Sexe (M ou F)'>
            <span id='sexe-validation'></span>

            <input class='w3-input w3-button w3-hover-white w3-margin-top w3-margin-bottom' type='email' name='enreg-email' id='enreg-email' required='required' placeholder='Adresse mail'>
            <span id='email-validation'></span>

            <input class='w3-input w3-button w3-hover-white w3-margin-top w3-margin-bottom' type='password' name='enreg-pwd1' id='enreg-pwd1' maxlength='16' required='required' placeholder='Mot de passe (6 - 16 caractères)'>
            <span id='pwd-length-validation'></span>

            <input class='w3-input w3-button w3-hover-white w3-margin-top w3-margin-bottom' type='password' name='enreg-pwd2' id='enreg-pwd2' maxlength='16' required='required' placeholder='Confirmation du mot de passe'>
            <span id='pwd-validation'></span>
          </div>

          <div class='enreg-right w3-right'>
            <span id='user-type-validation'></span>
            <div class='w3-center w3-margin-top w3-margin-bottom' id='et-enreg'>
              <input class='w3-margin-top w3-margin-bottom' type='radio' name='qualite' value='etudiant' id='etudiant'>
              <label for='etudiant' class='radio-lbl'>Etudiant</label>

              <div id='et-extensions'>
                <input class='w3-input w3-button w3-hover-white w3-margin-bottom' type='text' name='enreg-matricule' id='enreg-matricule' maxlength='11'  placeholder='Matricule'>
                <span id='mat-validation'></span>

                <select class='w3-button w3-hover-white w3-margin-top w3-margin-bottom w3-margin-right' name='enreg-niveau' id='enreg-niveau'>
                  <option value='Niveau' disabled='disabled' selected='selected'>Niveau</option>
                  <option value='L1'>L1</option>
                  <option value='L2'>L2</option>
                  <option value='L3 ME'>L3 ME</option>
                  <option value='L3 IO'>L3 IO</option>
                  <option value='M1 ACT'>M1 ACT</option>
                  <option value='MI FIN'>M1 FIN</option>
                  <option value='MI SI'>M1 SI</option>
                  <option value='MI SBD'>M1 SBD</option>
                  <option value='M2 ACT'>M2 ACT</option>
                  <option value='M2 FIN'>M2 FIN</option>
                  <option value='M2 SI'>M2 SI</option>
                  <option value='M2 SBD'>M2 SBD</option>
                </select>

                <input class='w3-button w3-hover-white w3-margin-top w3-margin-bottom w3-margin-left' type='checkbox' name='delegue' value='1' id='delegue'>
                <label id='delegue-label' for='delegue'>Délégué</label>
                <span id='niv-validation'></span>
              </div>
            </div>
            
            <div class='w3-center w3-margin-top w3-margin-bottom' id='ens-enreg'>
              <input class='w3-margin-top w3-margin-bottom' type='radio' name='qualite' value='enseignant' id='enseignant'>
              <label for='enseignant' class='radio-lbl'>Enseignant</label>
            </div>

            <div class='w3-center w3-margin-top w3-margin-bottom' id='admin-enreg'>
              <input class=' w3-margin-top w3-margin-bottom' type='radio' name='qualite' value='staff' id='staff'>
              <label for='staff' class='radio-lbl w3'>Staff</label>

              <div id='stf-extensions'>
                  <input class='w3-button w3-hover-white w3-margin-top w3-margin-bottom' type='checkbox' name='admin' value='1' id='admin'>
                  <label id='admin-label' for='admin'>Admin</label>
              </div>
            </div>
          </div>

          <div class='w3-center w3-clear w3-margin-top w3-margin-bottom'>
            <button class='w3-button w3-blue-grey w3-hover-red w3-margin-top' type='submit' name='enreg-submit' value='valider' id='enreg-submit'>Valider</button>
            <button class='w3-button w3-blue-grey w3-hover-red w3-margin-top' type='reset' value='annuler' id='enreg-reset'>Annuler</button>
          </div>
        </form>
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
      
      // Registration manager
      var enregNom = document.getElementById('enreg-nom');
      var name1Validation = document.getElementById('name1-validation');
      var regNoms = /^[A-Z][a-zéèïî']+([-'\s[A-Z][a-zéèïî']+)?/;
      
      var enregPrenom = document.getElementById('enreg-prenom');
      var name2Validation = document.getElementById('name2-validation');
      
      var enregSexe = document.getElementById('enreg-sexe');
      var sexeValidation = document.getElementById('sexe-validation');
      
      var enregEmail = document.getElementById('enreg-email');
      var emailValidation = document.getElementById('email-validation');
      var regEmail = /^[a-z]+\w@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
      
      var enregPwd1 = document.getElementById('enreg-pwd1');
      var pwdLengthValidation = document.getElementById('pwd-length-validation');
      
      var enregPwd2 = document.getElementById('enreg-pwd2');
      var pwdValidation = document.getElementById('pwd-validation');
      
      var userTypeValidation = document.getElementById('user-type-validation');
      
      var etudiant = document.getElementById('etudiant');
      var etExtensions = document.getElementById('et-extensions');
      
      var enregMatricule = document.getElementById('enreg-matricule');
      var matValidation = document.getElementById('mat-validation');
      
      var enregNiveau = document.getElementById('enreg-niveau');
      var nivValidation = document.getElementById('niv-validation');
      
      var delegue = document.getElementById('delegue');
      var delegueLabel = document.getElementById('delegue-label');
      
      var enseignant = document.getElementById('enseignant');
      
      var staff = document.getElementById('staff');
      var stfExtensions = document.getElementById('stf-extensions');
      
      var admin = document.getElementById('admin');
      var adminLabel = document.getElementById('admin-label');
      
      var enregSubmit = document.getElementById('enreg-submit');
      
      
      etudiant.addEventListener('click', activeEtudiant);
      enseignant.addEventListener('click', activeEnseignant);
      staff.addEventListener('click', activeStaff);
      enregSubmit.addEventListener('click', enregValidation);
      
      // Deconnexion
      var logout = document.getElementById("logout");
      logout.addEventListener('click', function(event){
        if(!confirm('Souhaitez-vous vraiment vous deconnecter?')) event.preventDefault();
      });

    </script>
  </body>
</html>