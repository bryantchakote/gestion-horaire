# Academic project / Projet académique
Une application web destinée à la gestion des heures d'arrivée et de depart, des retards et absences des étudiant.e.s et enseignant.e.s de l'ESSFAR. Projet académique réalisé en juillet-août 2020 avec des camarades d'école (Aelle Nanfah, Audrey Kamga, Dimitri Mbiemeni, Josias Tamo, Michelle Ngnibo, et Rith Fotso).


## Fonctionnalités
- Enregistrement des heures d'arrivée et de départ
- Calcul des retards et heures d'absence
- Justifications d'absences
- Synthèses individuelles et par classe

## Utilisation
- Téléchargez le dossier et stockez-le dans **xampp/htdocs**, démarrez Apache et MySQL (via Xampp par exemple)
- Créez la base de données, en exécutant le script `database/pointage_horaire-CREATION-TABLE.sql`. Vous pouvez aussi exécuter le script `database/pointage_horaire.sql` qui contient déjà un certain nombre de données, celles utilisées pour nos tests. Dans ce cas, la liste des utilisateurs et de leurs identifiants est disponible dans `images/login infos.jpg`, et vous pouvez vous passer de l'étape suivante
- Si vous avez juste initialisé la base de données, vous serez malheuresement obligés d'y insérer directement un.e étudiant.e représentatif.ve de chaque niveau pour que le système le rajoute à sa liste de niveaux. Cela implique la création d'instances de personnes (table `personnes`), puis la désignation de ces dernières en tant qu'étudiant.e.s (table `etudiants`) en précisant leur niveau
- Tapez ensuite *http://localhost/gestion-horaire* dans un navigateur et connectez-vous à l'aide de ces identifiants : **email** - ***tsamo@gmail.com***, **password** - ***tsamo***
- Vous êtes sur la page des administrateurs. Vous pouvez vous rendre sur l'onglet **Ajouter un utilisateur** pour créer autant d'utilisateurs que vous le souhaitez
- Une fois les utilisateurs créés, vous pouvez renseigner leurs heures d'arrivée et de départ de l'école pour différentes journées (onglet **Gestionnaire d'horaires**). Les retards et absences sont directement comptabilisés
- A chaque absence vous pouvez associer un motif via l'onglet **Gestionnaire d'absences**
- La visualisation des informations individuelles se fait à partir de **Infos uitilisateur**
- Des statistiques sont également disponibles (onglet **Statistiques**)

En outre, tout utilisateur a accès à ses propres informations, et les délégués de classe peuvent visualiser les données de leurs camarades.

### Note
Cette application présente certains incovénients : l'absence de synchronisation avec les différents emplois de temps fait que lorsque les données d'une journée sont renseignées, tous ceux qui n'ont aucune information enregistrée concernant cette journée sont considérés absents (pourtant il se pourrait juste qu'ils n'aient pas de cours programmés ce jour qui serait par exemple un samedi). De même, les heures de retard sont fixes alors qu'en realité elles dépendent de l'heure du début de chaque cours. Il n'est en outre pas possible de renseigner les informations relatives à une même journée en deux temps.
