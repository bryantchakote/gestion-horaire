# Academic project / Projet académique
Une application web destinée à la gestion des heures d'arrivée et de depart, des retards et absences des étudiants et enseignants de l'ESSFAR.


## Fonctionnalités
- Enregistrement des heures d'arrivée et de départ
- Calcul des retards et heures d'absence
- Justifications d'absences
- Synthèses individuelles et par classe

## Utilisation
- Téléchargez le dossier et stockez-le dans **xampp/htdocs**, démarrez Xampp (notamment Apache et MySQL)
- Créez la base de données, en exécutant le script `pointage_horaire-CREATION-TABLE.sql` (Vous pouvez aussi exécuter le script `pointage_horaire.sql` qui contient déjà un certain nombre de données que nous avions utilisé pour nos tests. Dans ce cas, la liste des utilisateurs et de leurs identifiants est disponible dans `images\login infos.jpg`)
- Vous serez malheuresement obligés de d'insérer directement dans la base de données un étudiant représentatif de chaque niveau pour que le système le rajoute à sa liste de niveaux. Cela implique la création d'une 'personne' (table `personnes`), puis la reconnaissance de ces personnes en tant qu'étudiants (table `etudiants`)
- Tapez ensuite **http://localhost/gestion-horaire** dans un navigateur et connectez-vous à l'aide de ces identifiants : **email** - ***tsamo@gmail.com***, **password** - ***tsamo***
- Vous êtes sur la page des administrateurs. Vous pouvez vous rendre sur l'onglet **Ajouter un utilisateur** pour créer autant d'utilisateurs que vous le souhaitez
- Une fois les utilisateurs créés, vous pouvez renseigner leurs heures d'arrivée et de départ de l'école pour différentes journées (onglet **Gestionnaire d'horaires**). Les retards et absences sont directement comptabilisés
- A chaque absence vous pouvez associer un motif via l'onglet **Gestionnaire d'absences**
- La visualisation des informations individuelles se fait à partir de **Infos uitilisateur**
- Des statistiques sont également disponibles (onglet **Statistiques**)

En outre, tout utilisateur a accès à ses propres informations, et les délégués de classe peuvent visualiser les données de leurs camarades.

### Note
Cette application présente un incovénient principal : l'absence de synchronisation avec les différents emplois de temps fait que lorsque les données d'une journée sont renseignées, tous ceux qui n'ont aucune information concernant cette journée sont considérés absents (pourtant il se pourrait juste qu'ils n'aient pas cours ce jour). De même, les heures de retard sont fixes alors qu'en realité elles dépendent de l'heure du début de chaque cours. Il n'est en outre pas possible de renseigner les informations relatives à une même journée en deux temps.