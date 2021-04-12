#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: personnes
#------------------------------------------------------------

CREATE TABLE personnes(
        id_pers Int  Auto_increment  NOT NULL ,
        nom     Varchar (50) NOT NULL ,
        prenom  Varchar (50) ,
        sexe    Varchar (1) NOT NULL ,
        mdp     Varchar (60) NOT NULL ,
        email   Varchar (70) NOT NULL
	,CONSTRAINT personnes_AK UNIQUE (email)
	,CONSTRAINT personnes_PK PRIMARY KEY (id_pers)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: etudiants
#------------------------------------------------------------

CREATE TABLE etudiants(
        id_pers   Int NOT NULL ,
        niveau    Varchar (50) NOT NULL ,
        nom       Varchar (50) NOT NULL ,
        prenom    Varchar (50) ,
        sexe      Varchar (1) NOT NULL ,
        mdp       Varchar (60) NOT NULL ,
        matricule Varchar (15) NOT NULL ,
        email     Varchar (70) NOT NULL,
        est_delegue BOOL NOT NULL DEFAULT 0
	,CONSTRAINT etudiants_AK UNIQUE (matricule,email)
	,CONSTRAINT etudiants_PK PRIMARY KEY (id_pers)

	,CONSTRAINT etudiants_personnes_FK FOREIGN KEY (id_pers) REFERENCES personnes(id_pers) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: enseignants
#------------------------------------------------------------

CREATE TABLE enseignants(
        id_pers Int NOT NULL ,
        nom     Varchar (50) NOT NULL ,
        prenom  Varchar (50) ,
        sexe    Varchar (1) NOT NULL ,
        mdp     Varchar (60) NOT NULL ,
        email   Varchar (70) NOT NULL
	,CONSTRAINT enseignants_AK UNIQUE (email)
	,CONSTRAINT enseignants_PK PRIMARY KEY (id_pers)

	,CONSTRAINT enseignants_personnes_FK FOREIGN KEY (id_pers) REFERENCES personnes(id_pers) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: staff
#------------------------------------------------------------

CREATE TABLE staff(
        id_pers Int NOT NULL ,
        est_admin   Bool NOT NULL DEFAULT 0 ,
        nom     Varchar (50) NOT NULL ,
        prenom  Varchar (50) ,
        sexe    Varchar (1) NOT NULL ,
        mdp     Varchar (60) NOT NULL ,
        email   Varchar (70) NOT NULL
	,CONSTRAINT staff_AK UNIQUE (email)
	,CONSTRAINT staff_PK PRIMARY KEY (id_pers)

	,CONSTRAINT staff_personnes_FK FOREIGN KEY (id_pers) REFERENCES personnes(id_pers) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: instants
#------------------------------------------------------------

CREATE TABLE instants(
        id_instant    Int  Auto_increment  NOT NULL ,
        date_jour     Date NOT NULL ,
        heure_arrivee Time NOT NULL ,
        heure_depart  Time ,
        id_pers       Int NOT NULL
	,CONSTRAINT instants_PK PRIMARY KEY (id_instant)

	,CONSTRAINT instants_personnes_FK FOREIGN KEY (id_pers) REFERENCES personnes(id_pers) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB;

