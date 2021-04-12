#------------------------------------------------------------
#   1. ACTIONS ETUDIANTS
#------------------------------------------------------------

# 1.1.1 Visualiser tous ses pointages
SELECT *
FROM instants
WHERE id_pers = [id_etudiant_concerne];

# 1.1.2 Visualiser ses pointages d'un jour particulier
SELECT *
FROM instants
WHERE id_pers = [id_etudiant_concerne]
AND date_jour = [jour_concerne];

# 1.1.3 Visualiser ses pointages d'une semaine particuliere
SELECT *
FROM instants
WHERE id_pers = [id_etudiant_concerne]
AND date_jour BETWEEN [lundi_de_la_semaine] AND [samedi_de_la_semaine];

# 1.2 Retards
SELECT *
FROM instants
WHERE id_pers = [id_etudiant_concerne]
AND (
	(heure_arrivee BETWEEN '07:46:00' AND '08:00:00')
	OR
    (heure_arrivee BETWEEN '12:46:00' AND '13:00:00')
);

# 1.3.1 Absences (quelques heures)
SELECT *
FROM instants
WHERE id_pers = [id_etudiant_concerne]
AND heure_arrivee BETWEEN '08:00:01' AND '16:59:59';

# 1.3.2 Absences (toute la journee)
SELECT *
FROM personnes
WHERE id_pers = [id_etudiant_concerne]
AND id_pers NOT IN
(
	SELECT id_pers
    FROM instants
    WHERE date_jour = [date_jour_concerne]
);





#------------------------------------------------------------
#   2. ACTIONS ETUDIANTS (DELEGUES)
#------------------------------------------------------------

# 2.1 Selectionner tous les eleves de sa classe
SELECT *
FROM etudiants
NATURAL JOIN personnes
WHERE niveau = [niveau_concerne];

# 2.2.1 Visualiser les pointages de ses camarades
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers IN
(
	SELECT id_pers
    FROM etudiants
    WHERE niveau = [niveau_concerne]
)

# 2.2.2 Visualiser les pointages d'un camarade en particulier 
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers = [id_etudiant_concerne];

# 2.2.3 Visualiser les pointages de ses camarades un jour particulier
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers IN
(
	SELECT id_pers
    FROM etudiants
    WHERE niveau = [niveau_concerne]
)
AND date_jour = [date_jour_concerne];

# 2.3 Retards de ses camarades
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers IN
(
    SELECT id_pers
    FROM etudiants
    WHERE niveau = [niveau_concerne]
)
AND (
	(heure_arrivee BETWEEN '07:46:00' AND '08:00:00')
	OR
    (heure_arrivee BETWEEN '12:46:00' AND '13:00:00')
);

# 2.4.1 Absences de ses camarades (quelques heures)
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers IN
(
    SELECT id_pers
    FROM etudiants
    WHERE niveau = [niveau_concerne]
)
AND heure_arrivee BETWEEN '08:00:01' AND '16:59:59';

# 2.4.2 Absences de ses camarades (toute la journee)
SELECT *
FROM personnes
WHERE id_pers IN
(
    SELECT id_pers
    FROM etudiants
    WHERE niveau = [niveau_concerne]
)
AND id_pers NOT IN
(
	SELECT id_pers
    FROM instants
    WHERE date_jour = [date_jour_concerne]
);





#------------------------------------------------------------
#   3. ACTIONS ENSEIGNANTS
#------------------------------------------------------------

# 3.1 Visualiser tous ses pointages
SELECT *
FROM instants
WHERE id_pers = [id_enseignant_concerne];

# 3.2 Visualiser ses pointages d'un jour particulier
SELECT *
FROM instants
WHERE id_pers = [id_enseignant_concerne]
AND date_jour = [jour_concerne];

# 3.3 Visualiser ses pointages d'une semaine particuliere
SELECT *
FROM instants
WHERE id_pers = [id_enseignant_concerne]
AND date_jour BETWEEN [lundi_de_la_semaine] AND [samedi_de_la_semaine];





#------------------------------------------------------------
#   4. ACTIONS MEMBRES DU STAFF
#------------------------------------------------------------

# 4.1.1 Visualiser tous ses pointages
SELECT *
FROM instants
WHERE id_pers = [id_membre_concerne];

# 4.1.2 Visualiser ses pointages d'un jour particulier
SELECT *
FROM instants
WHERE id_pers = [id_membre_concerne]
AND date_jour = [jour_concerne];

# 4.1.3 Visualiser ses pointages d'une semaine particuliere
SELECT *
FROM instants
WHERE id_pers = [id_membre_concerne]
AND date_jour BETWEEN [lundi_de_la_semaine] AND [samedi_de_la_semaine];

# 4.2 Retards
SELECT *
FROM instants
WHERE id_pers = [id_membre_concerne]
AND heure_arrivee > '08:00:00';

# 4.3 Absences
SELECT *
FROM personnes
WHERE id_pers = [id_membre_concerne]
AND id_pers NOT IN
(
	SELECT id_pers
    FROM instants
    WHERE date_jour = [date_jour_concerne]
);





#------------------------------------------------------------
#   5. ACTIONS MEMBRES DU STAFF (ADMINISTRATEURS)
#------------------------------------------------------------

# 5.1.1.1 Selectionner tous les niveaux
SELECT DISTINCT niveau
FROM etudiants

# 5.1.1.2 Selectionner tous les etudiants d'un niveau
SELECT *
FROM etudiants
NATURAL JOIN personnes
WHERE niveau = [niveau_concerne];

# 5.1.2 Selectionner les enseignants
SELECT *
FROM personnes
NATURAL JOIN enseignants;

# 5.1.3 Selectionner les membres de staff
SELECT *
FROM personnes
NATURAL JOIN staff;

# 5.2.1 Releve des arrivees/departs
SELECT *
FROM instants
NATURAL JOIN personnes

# 5.2.2.1 Releve des arrivees/departs des etudiants
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers IN
(
    SELECT id_pers
    FROM etudiants
)

# 5.2.2.2 Releve des arrivees/departs des etudiants d'un niveau
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers IN
(
    SELECT id_pers
    FROM etudiants
    WHERE niveau = [niveau_concerne]
)

# 5.2.3 Releve des arrivees/departs des enseignants
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers IN
(
    SELECT id_pers
    FROM enseignants
);

# 5.2.4 Releve des arrivees/departs des membres du staff
SELECT *
FROM instants
NATURAL JOIN personnes
WHERE id_pers IN
(
    SELECT id_pers
    FROM staff
);