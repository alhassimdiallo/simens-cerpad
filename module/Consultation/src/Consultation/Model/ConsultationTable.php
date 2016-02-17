<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class ConsultationTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function getConsult($id){
		$id = (String) $id;
		$rowset = $this->tableGateway->select ( array (
				'ID_CONS' => $id
		) );
		$row =  $rowset->current ();
 		if (! $row) {
 			throw new \Exception ( "Could not find row $id" );
 		}
		return $row;
	}
	
	public function getConsultationPatient($id_pat, $id_cons){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array( '*' ));
		$select->from( array( 'c' => 'consultation' ));
		$select->join( array('e1' => 'employe'), 'e1.id_personne = c.ID_MEDECIN' , array());
		$select->join( array('e2' => 'employe'), 'e2.id_personne = c.ID_SURVEILLANT' , array());
		$select->join( array('p1' => 'personne'), 'e1.id_personne = p1.ID_PERSONNE' , array('*'));
		$select->join( array('p2' => 'personne'), 'e2.id_personne = p2.ID_PERSONNE' , array('NomSurveillant' => 'NOM', 'PrenomSurveillant' => 'PRENOM'));
		$select->join( array('s' => 'service'), 's.ID_SERVICE = c.ID_SERVICE' , array('nomService' => 'NOM', 'domaineService' => 'DOMAINE'));

		//On affiche toutes les consultations sauf celle ouverte
		$where = new Where();
		$where->equalTo('c.ID_PATIENT', $id_pat);
		$where->notEqualTo('c.ID_CONS', $id_cons);
		$select->where($where);
		$select->order('DATEONLY DESC');
		
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		
		return $result;
	}
	
	public function updateConsultation($values)
	{
		$donnees = array(
				'POIDS' => $values->get ( "poids" )->getValue (), 
				'TAILLE' => $values->get ( "taille" )->getValue (), 
				'TEMPERATURE' => $values->get ( "temperature" )->getValue (), 
				'PRESSION_ARTERIELLE' => $values->get ( "pressionarterielle" )->getValue (), 
				'POULS' => $values->get ( "pouls" )->getValue (), 
				'FREQUENCE_RESPIRATOIRE' => $values->get ( "frequence_respiratoire" )->getValue (), 
				'GLYCEMIE_CAPILLAIRE' => $values->get ( "glycemie_capillaire" )->getValue (), 
		);
		$this->tableGateway->update( $donnees, array('ID_CONS'=> $values->get ( "id_cons" )->getValue ()) );
	}
	
	public function validerConsultation($values){
		$donnees = array(
				'CONSPRISE' => $values['VALIDER'],
				'ID_MEDECIN' => $values['ID_MEDECIN']
		);
		$this->tableGateway->update($donnees, array('ID_CONS'=> $values['ID_CONS']));
	}
	
	public function addConsultation($values , $IdDuService){
		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
		try {
		
			$dataconsultation = array(
					'ID_CONS'=> $values->get ( "id_cons" )->getValue (), 
					'ID_SURVEILLANT'=> $values->get ( "id_surveillant" )->getValue (), 
					'ID_PATIENT'=> $values->get ( "id_patient" )->getValue (), 
					'DATE'=> $values->get ( "date_cons" )->getValue (), 
					'POIDS' => $values->get ( "poids" )->getValue (), 
					'TAILLE' => $values->get ( "taille" )->getValue (), 
					'TEMPERATURE' => $values->get ( "temperature" )->getValue (), 
					'PRESSION_ARTERIELLE' => $values->get ( "pressionarterielle" )->getValue (), 
					'POULS' => $values->get ( "pouls" )->getValue (), 
					'FREQUENCE_RESPIRATOIRE' => $values->get ( "frequence_respiratoire" )->getValue (), 
					'GLYCEMIE_CAPILLAIRE' => $values->get ( "glycemie_capillaire" )->getValue (), 
					'DATEONLY' => $values->get ( "dateonly" )->getValue (),
					'HEURECONS' => $values->get ( "heure_cons" )->getValue (),
					'ID_SERVICE' => $IdDuService
			);
			$this->tableGateway->insert($dataconsultation);

			$this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
		} catch (\Exception $e) {
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
		}
	}
	
	public function addConsultationEffective($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()
		->into('consultation_effective')
		->values(array('ID_CONS' => $id_cons));
		$requete = $sql->prepareStatementForSqlObject($sQuery);
		$requete->execute();
	}
	
	public function getInfoPatientMedecin($idcons){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array( '*' ));
		$select->from( array( 'c' => 'consultation' ));
		$select->join( array('s' => 'service'), 's.ID_SERVICE = c.ID_SERVICE' , array (
				'NomService' => 'NOM',
				'DomaineService' => 'DOMAINE'
		) );
		$select->join( array('p' => 'patient' ), 'p.ID_PERSONNE = c.ID_PATIENT' , array('*'));
		$select->join( array('pers' => 'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE' , array('*'));
		$select->join( array('m' => 'personne'), 'm.ID_PERSONNE = c.ID_MEDECIN' , array(
				'NomMedecin' => 'NOM', 
				'PrenomMedecin' => 'PRENOM', 
				'AdresseMedecin' => 'ADRESSE',
				'TelephoneMedecin' => 'TELEPHONE'
		));
		$select->where ( array( 'c.ID_CONS' => $idcons));
		
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		
		return $result;
	}
	
	public function addBandelette($bandelettes){
		$values = array();
		if($bandelettes['albumine'] == 1){
			$values[] = array('ID_TYPE_BANDELETTE'=>1, 'ID_CONS'=>$bandelettes['id_cons'], 'CROIX_BANDELETTE'=>(int)$bandelettes['croixalbumine']);
		}
		if($bandelettes['sucre'] == 1){
			$values[] = array('ID_TYPE_BANDELETTE'=>2, 'ID_CONS'=>$bandelettes['id_cons'], 'CROIX_BANDELETTE'=>(int)$bandelettes['croixsucre']);
		}
		if($bandelettes['corpscetonique'] == 1){
			$values[] = array('ID_TYPE_BANDELETTE'=>3, 'ID_CONS'=>$bandelettes['id_cons'], 'CROIX_BANDELETTE'=>(int)$bandelettes['croixcorpscetonique']);
		}
		
		for($i = 0 ; $i < count($values) ; $i++ ){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert()
			->into('bandelette')
			->columns(array('ID_TYPE_BANDELETTE', 'ID_CONS', 'CROIX_BANDELETTE'))
			->values($values[$i]);
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$stat->execute();
		}
	}
	
	public function getBandelette($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from('bandelette')
		->columns(array('*'))
		->where(array('id_cons' => $id_cons));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$result = $stat->execute();
		
		$donnees = array();
		$donnees['temoin'] = 0;
		foreach ($result as $resultat){
			if($resultat['ID_TYPE_BANDELETTE'] == 1){
				$donnees['albumine'] = 1; //C'est à coché
				$donnees['croixalbumine'] = $resultat['CROIX_BANDELETTE'];
			}
			if($resultat['ID_TYPE_BANDELETTE'] == 2){
				$donnees['sucre'] = 1; //C'est à coché
				$donnees['croixsucre'] = $resultat['CROIX_BANDELETTE'];
			}
			if($resultat['ID_TYPE_BANDELETTE'] == 3){
				$donnees['corpscetonique'] = 1; //C'est à coché
				$donnees['croixcorpscetonique'] = $resultat['CROIX_BANDELETTE'];
			}
			
			//temoin
			$donnees['temoin'] = 1;
		}
		
		return $donnees;
	}
	
	public function deleteBandelette($id_cons){
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete()
		->from('bandelette')
		->where(array('id_cons' => $id_cons));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$result = $stat->execute();
	}
	
	//Tous les patients consultes sauf ceux du jour
	public function tousPatientsCons($idService){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array () );
		$select->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE', array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Id' => 'ID_PERSONNE'
		));
		
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.ID_PATIENT', array('Id_cons' => 'ID_CONS', 'Dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE'));
		$select->join(array('s' => 'service'), 'c.ID_SERVICE = s.ID_SERVICE', array('Nomservice' => 'NOM'));
		$select->join(array('cons_eff' => 'consultation_effective'), 'cons_eff.ID_CONS = c.ID_CONS' , array('*'));
		$where = new Where();
		$where->equalTo('s.ID_SERVICE', $idService);
		$where->notEqualTo('DATEONLY', $date);
		$select->order('c.DATE DESC');
		$select->group('c.ID_PATIENT');
		$select->where($where);
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	
	}
	
	//liste des patients Ã  consulter par le medecin dans le service de ce dernier
	public function listePatientsConsParMedecin($idService){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array () );
		$select->join(array('pers'=>'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE', array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Id' => 'ID_PERSONNE'
		));
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.ID_PATIENT', array('Id_cons' => 'ID_CONS', 'dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE', 'date' => 'DATE'));
		$select->join(array('cons_eff' => 'consultation_effective'), 'cons_eff.ID_CONS = c.ID_CONS' , array('*'));
		$select->join(array('a' => 'admission'), 'c.ID_PATIENT = a.id_patient', array('Id_admission' => 'id_admission'));
		$select->where(array('c.ID_SERVICE' => $idService, 'DATEONLY' => $date, 'a.date_cons' => $date, 'c.ARCHIVAGE' => 0));
		$select->order('id_admission ASC');
	
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	}
	
	public function getPatientsRV($id_service){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
	
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql( $adapter );
		$select = $sql->select();
		$select->from( array(
				'rec' =>  'rendezvous_consultation'
		));
		$select->join(array('cons' => 'consultation'), 'cons.ID_CONS = rec.ID_CONS ', array('*'));
		$select->where( array(
				'rec.DATE' => $date,
				'cons.ID_SERVICE' => $id_service,
		) );
	
		$statement = $sql->prepareStatementForSqlObject( $select );
		$resultat = $statement->execute();
	
		$tab = array();
		foreach ($resultat as $result) {
			$tab[$result['ID_PATIENT']] = $result['HEURE'];
		}
	
		return $tab;
	}
	
	/**
	 * Recuperation de la liste des medicaments
	 */
	public function listeDeTousLesMedicaments(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql ( $adapter );
		$select = $sql->select('consommable');
		$select->columns(array('ID_MATERIEL','INTITULE'));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
	
		return $result;
	}
	
	/**
	 * RECUPERER LA FORME DES MEDICAMENTS
	 */
	
	public function formesMedicaments(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'forme' => 'forme_medicament' ));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result;
	}
	
	/**
	 * RECUPERER LES TYPES DE QUANTITE DES MEDICAMENTS
	 */
	
	public function typeQuantiteMedicaments(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from( array( 'typeQuantite' => 'quantite_medicament' ));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
	
		return $result;
	}
	
	public function getInfoPatient($id_personne) {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))
		->columns( array( '*' ))
		->join(array('pers' => 'personne'), 'pers.id_personne = pat.id_personne' , array('*'))
		->where(array('pat.ID_PERSONNE' => $id_personne));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$resultat = $stat->execute()->current();
	
		return $resultat;
	}
	
	public function getPhoto($id) {
		$donneesPatient =  $this->getInfoPatient( $id );
	
		$nom = null;
		if($donneesPatient){$nom = $donneesPatient['PHOTO'];}
		if ($nom) {
			return $nom . '.jpg';
		} else {
			return 'identite.jpg';
		}
	}
	
	//liste des patients deja consultÃ©s par le medecin pour l'espace recherche
	public function listePatientsConsMedecin($idService){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array () );
		$select->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = p.ID_PERSONNE', array(
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Id' => 'ID_PERSONNE'
		));
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.ID_PATIENT', array('Id_cons' => 'ID_CONS', 'Dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE', 'date' => 'DATE'));
		$select->join(array('cons_eff' => 'consultation_effective'), 'cons_eff.ID_CONS = c.ID_CONS' , array('*'));
		$select->join(array('s' => 'service'), 'c.ID_SERVICE = s.ID_SERVICE', array('Nomservice' => 'NOM'));
		$where = new Where();
		$where->equalTo('s.ID_SERVICE', $idService);
		$where->notEqualTo('DATEONLY', $date);
		$select->where($where);
		$select->order('c.DATE DESC');
		$select->group('c.ID_PATIENT');
	
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	
	}
	
 	public function addTraitementsInstrumentaux($traitement_instrumental){

 		
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()
 		->from('traitement_instrumental')
 		->where(array('id_cons' => $traitement_instrumental['id_cons']));
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 		
		if($traitement_instrumental['endoscopie_interventionnelle'] || $traitement_instrumental['radiologie_interventionnelle'] ||
		 $traitement_instrumental['cardiologie_interventionnelle'] || $traitement_instrumental['autres_interventions']){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->insert()
			->into('traitement_instrumental')
			->columns(array('id_cons', 'endoscopie_interventionnelle', 'radiologie_interventionnelle', 'cardiologie_interventionnelle', 'autres_interventions'))
			->values($traitement_instrumental);
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$stat->execute();
		}
 	}
 	
 	public function getTraitementsInstrumentaux($id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from('traitement_instrumental')
 		->where(array('id_cons' => $id_cons));
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute()->current();
 		return $result;
 	}
 	
 	
 	public function fetchConsommable(){
 		$adapter = $this->tableGateway->getAdapter();
 		$sql = new Sql ( $adapter );
 		$select = $sql->select('consommable');
 		$select->columns(array('ID_MATERIEL','INTITULE'));
 		$stat = $sql->prepareStatementForSqlObject($select);
 		$result = $stat->execute();
 		foreach ($result as $data) {
 			$options[$data['ID_MATERIEL']] = $data['INTITULE'];
 		}
 		return $options;
 	}
 	
 	
 	//GESTION DES FICHIER MP3
 	//GESTION DES FICHIER MP3
 	//GESTION DES FICHIER MP3
 	public function insererMp3($titre , $nom, $id_cons, $type){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()
 		->into('fichier_mp3')
 		->columns(array('titre', 'nom', 'id_cons', 'type'))
 		->values(array('titre' => $titre , 'nom' => $nom, 'id_cons'=>$id_cons, 'type'=>$type));
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		return $stat->execute();
 	}
 	
 	public function getMp3($id_cons, $type){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('f' => 'fichier_mp3'))->columns(array('*'))
 		->where(array('id_cons' => $id_cons, 'type' => $type))
 		->order('id DESC');
 	
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 		return $result;
 	}
 	
 	public function supprimerMp3($idLigne, $id_cons, $type){
 		$liste = $this->getMp3($id_cons, $type);
 	
 		$i=1;
 		foreach ($liste as $list){
 			if($i == $idLigne){
 				unlink('C:\wamp\www\simens\public\js\plugins\jPlayer-2.9.2\examples\\'.$list['nom']);
 	
 				$db = $this->tableGateway->getAdapter();
 				$sql = new Sql($db);
 				$sQuery = $sql->delete()
 				->from('fichier_mp3')
 				->where(array('id' => $list['id']));
 	
 				$stat = $sql->prepareStatementForSqlObject($sQuery);
 				$stat->execute();
 	
 				return true;
 			}
 			$i++;
 		}
 		return false;
 	}
 	
 	//COMPTE RENDU OPERATOIRE
 	//COMPTE RENDU OPERATOIRE
 	public function deleteCompteRenduOperatoire($id_cons, $type){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->delete()
 		->from('compte_rendu_operatoire')
 		->where(array('id_cons' => $id_cons, 'type' => $type));
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute();
 	}
 	
 	public function addCompteRenduOperatoire($note, $type, $id_cons){
 		$this->deleteCompteRenduOperatoire($id_cons, $type);
 		if($note) {
 			$db = $this->tableGateway->getAdapter();
 			$sql = new Sql($db);
 			$sQuery = $sql->insert()
 			->into('compte_rendu_operatoire')
 			->values(array('note' => $note , 'type' => $type, 'id_cons'=>$id_cons));
 				
 			$stat = $sql->prepareStatementForSqlObject($sQuery);
 			return $stat->execute();
 		}
 	}
 	
 	public function getCompteRenduOperatoire($type, $id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('c' => 'compte_rendu_operatoire'))->columns(array('*'))
 		->where(array('id_cons' => $id_cons, 'type' => $type));
 		
 		$stat = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $stat->execute()->current();
 		return $result;
 	}
 	
 	
 	
 	//GESTION DES EXAMENS DU JOUR LORS D'UNE HOSPITALISATION
 	//GESTION DES EXAMENS DU JOUR LORS D'UNE HOSPITALISATION
 	public function addConsultationExamenDuJour($codeExamen, $values , $IdDuService , $idMedecin){
 		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
 		$date = new \DateTime();
 		$aujourdhui = $date->format('Y-m-d H:i:s');
 		$dateonly = $date->format('Y-m-d');
 		
 		try {
 			$dataconsultation = array(
					'ID_CONS'=> $codeExamen,
 					'ID_MEDECIN'=> $idMedecin,
 					'ID_PATIENT'=> $values->id_personne,
 					'DATE'=> $aujourdhui,
 					'POIDS' => $values->poids,
 					'TAILLE' => $values->taille,
  					'TEMPERATURE' => $values->temperature,
  					'PRESSION_ARTERIELLE' => $values->pressionarterielle,
 					'POULS' => $values->pouls,
 					'FREQUENCE_RESPIRATOIRE' => $values->frequence_respiratoire,
 					'GLYCEMIE_CAPILLAIRE' => $values->glycemie_capillaire,
  					'DATEONLY' => $dateonly,
  					'ID_SERVICE' => $IdDuService
 			);
 			
 			$this->tableGateway->insert($dataconsultation);
 	
 			$this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
 		} catch (\Exception $e) {
 			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
 		}
 	}
 	
 	public function addExamenDuJour($id_cons, $id_hosp){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->insert()
 		->into('examen_du_jour')
 		->values(array('ID_CONS' => $id_cons, 'ID_HOSP' => $id_hosp));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		$requete->execute();
 	}
 	
 	public function getExamenDuJour($id_hosp){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('e' => 'examen_du_jour'))
 		->join(array('c' => 'consultation'), 'c.ID_CONS = e.ID_CONS' , array('*'))
 		->join(array('p' => 'personne'), 'p.ID_PERSONNE = c.ID_MEDECIN' , array('NomMedecin' => 'NOM', 'PrenomMedecin' => 'PRENOM'))
 		->where(array('ID_HOSP' => $id_hosp))
 		->order('DATE DESC');
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute();
 	}
 	
 	public function supprimerExamenDuJour($id_examen_jour){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('e' => 'examen_du_jour'))
 		->where(array('ID_EXAMEN_JOUR' => $id_examen_jour));
 		
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		$result = $requete->execute()->current();
 		
 		$db2 = $this->tableGateway->getAdapter();
 		$sql2 = new Sql($db2);
 		$sQuery2 = $sql2->delete()
 		->from('consultation')
 		->where(array('ID_CONS' => $result['ID_CONS']));
 			
 		$requete2 = $sql2->prepareStatementForSqlObject($sQuery2);
 		$requete2->execute();
 		
 		return $result['ID_HOSP'];
 	}
 	
 	public function getExamenDuJourParIdExamenJour($id_examen_jour){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('e' => 'examen_du_jour'))
 		->join(array('c' => 'consultation'), 'c.ID_CONS = e.ID_CONS' , array('*'))
 		->join(array('p' => 'personne'), 'p.ID_PERSONNE = c.ID_MEDECIN' , array('NomMedecin' => 'NOM', 'PrenomMedecin' => 'PRENOM'))
 		->where(array('ID_EXAMEN_JOUR' => $id_examen_jour));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute()->current();
 	}
 	
 	public function getConsultationExamenJour($id_cons){
 		$db = $this->tableGateway->getAdapter();
 		$sql = new Sql($db);
 		$sQuery = $sql->select()
 		->from(array('c' => 'consultation'))
 		->where(array('ID_CONS' => $id_cons));
 		$requete = $sql->prepareStatementForSqlObject($sQuery);
 		return $requete->execute()->current();
 	}
}