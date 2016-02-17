<?php

namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class SoinhospitalisationTable {
	protected $tableGateway;
	protected $conversionDate;
	
	public function getDateHelper() {
		$this->conversionDate = new DateHelper();
	}
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getSoinhospitalisationWithId_sh($id_sh)
	{
		$rowset = $this->tableGateway->select(array(
				'id_sh' => (int) $id_sh,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function getSoinhospitalisation($id_hosp)
	{
		$rowset = $this->tableGateway->select(array(
				'id_hosp' => (int) $id_hosp,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function getAllSoinhospitalisation($id_hosp)
	{ 
		$rowset = $this->tableGateway->select(array(
				'id_hosp' => (int) $id_hosp,
		));
		if (!$rowset) {
			$row = null;
		}
		$row = $rowset->toArray();
		return $row;
	}
	
	public function saveHeure($donnees, $id_sh)
	{
		$duree = (int)$donnees->duree;
		$date_debut_application = $this->conversionDate->convertDateInAnglais($donnees->date_application);
		
		for ($d = 0 ; $d < $duree ; $d++){
			$date = date("Y-m-d", strtotime($date_debut_application.'+'.$d.' day' ));
			
			for($i=0; $i<count($donnees->heure_recommandee) ; $i++ ){
				$values = array('date'=> $date, 'heure'=>$donnees->heure_recommandee[$i], 'id_sh'=>$id_sh);
				
				$adapter = $this->tableGateway->getAdapter();
				$sql = new Sql($adapter);
				$select = $sql->insert();
				$select->into('heures_soins');
				$select->values($values);
					
				$stat = $sql->prepareStatementForSqlObject($select);
				$stat->execute();
			}
		}
	}
	
	public function getHeures($id_sh)
	{
			$adapter = $this->tableGateway->getAdapter();
			$sql = new Sql($adapter);
			$select = $sql->select();
			$select->from('heures_soins');
			$select->where(array('id_sh'=>$id_sh));
				
			$stat = $sql->prepareStatementForSqlObject($select);
			$result = $stat->execute();
			
			$tab = array();
			foreach ($result as $resultat){
				$tab[] = $resultat['heure'];
			}
			return $tab;
	}
	
	public function getHeuresGroup($id_sh)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh'=>$id_sh));
		$select->group('heure');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		$tab = array();
		foreach ($result as $resultat){
			$tab[] = $resultat['heure'];
		}
		return $tab;
	}
	
	public function saveSoinhospitalisation($SoinHospitalisation, $id_medecin)
	{
		$this->getDateHelper();
		$today = new \DateTime ();
		$date_enreg = $today->format ( 'Y-m-d H:i:s' );
		
		$data = array(
				'id_hosp' => $SoinHospitalisation->id_hosp,
				'date_enregistrement'=> $date_enreg,
				'date_debut_application' => $this->conversionDate->convertDateInAnglais($SoinHospitalisation->date_application),
				'medicament' => $SoinHospitalisation->medicament,
				'voie_administration' => $SoinHospitalisation->voie_administration,
				'frequence' => $SoinHospitalisation->frequence,
				'dosage' => $SoinHospitalisation->dosage,
				'motif' => $SoinHospitalisation->motif,
				'note' => $SoinHospitalisation->note,
				'duree' => (int)$SoinHospitalisation->duree,
				'id_personne' => $id_medecin,
		);
			
		$id_sh = (int)$SoinHospitalisation->id_sh;
		if($id_sh == 0){
			return($this->tableGateway->getLastInsertValue($this->tableGateway->insert($data)));
		} else {
			$data = array(
					'date_modifcation_medecin'=> $date_enreg,
					'date_debut_application' => $this->conversionDate->convertDateInAnglais($SoinHospitalisation->date_application),
					'medicament' => $SoinHospitalisation->medicament,
					'voie_administration' => $SoinHospitalisation->voie_administration,
					'frequence' => $SoinHospitalisation->frequence,
					'dosage' => $SoinHospitalisation->dosage,
					'motif' => $SoinHospitalisation->motif,
					'note' => $SoinHospitalisation->note,
					'duree' => (int)$SoinHospitalisation->duree,
			);
			if($this->getSoinhospitalisationWithId_sh($id_sh)) {
				$this->tableGateway->update($data, array('id_sh' => $id_sh));
			} 
		}
	}
	
	public function supprimerHospitalisation($id_sh) {
		
		//La suppression du soin implique automatiquement celle des heures recommandées  
		if($this->getSoinhospitalisationWithId_sh($id_sh)){
			$this->tableGateway->delete(array('id_sh' => $id_sh)); 
		}
	}
	
	public function appliquerSoin($id_sh, $note) {
		$this->getDateHelper();
		$today = new \DateTime ();
		$date_application = $today->format ( 'Y-m-d H:i:s' );
		
		if($this->getSoinhospitalisationWithId_sh($id_sh)){
			$this->tableGateway->update(array('note_application' => $note, 'date_application'=> $date_application , 'appliquer'=>1), array('id_sh' => $id_sh));
		}
	}
	
	
	public function getInfosInfirmiers($id_personne)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('e' => 'employe'));
		$select->join(array('pers'=>'personne'), 'pers.ID_PERSONNE = e.id_personne', array('*'));
		$select->where(array('e.id_personne' => $id_personne));
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
	
	
	/*Heure suivante*/
	/*Heure suivante*/
	public function getHeureSuivante($id_sh)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh'=>$id_sh, 'applique'=>0));
		$select->order('heure ASC');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
	
	/*Toutes les heures*/
	/*Toutes les heures*/
    public function getToutesHeures($id_sh, $date)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh'=>$id_sh, 'date'=>$date));
		$select->order('id_heure ASC');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		return $result;
	}
	
	/**
	 * Recuperer au moin une heure ou le soin est appliquer 
	 **/
	public function getHeureAppliquer($id_sh)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh'=>$id_sh, 'applique'=>1));
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->count();
			
		return $result;
	}
	
	
	//GESTION DES HEURE POUR LES SOINS SUIVANT PLUSIEURS JOURS
	//GESTION DES HEURE POUR LES SOINS SUIVANT PLUSIEURS JOURS
	//GESTION DES HEURE POUR LES SOINS SUIVANT PLUSIEURS JOURS
	/*Heure suivante*/
	/*Heure suivante*/
	public function getHeureSuivantePourAujourdhui($id_sh)
	{
		$today = new \DateTime();
		$date = $today->format ( 'Y-m-d' );
		$heure = $today->format ( 'H:i:s' );
		
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh' => $id_sh, 'applique' => 0, 'date' => $date, 'heure > ?' => $heure));
		$select->order('heure ASC');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
	
	/*Heure precedente*/
	/*Heure precedente*/
	public function getHeurePrecedentePourAujourdhui($id_sh)
	{
		$today = new \DateTime();
		$date = $today->format ( 'Y-m-d' );
		$heure = $today->format ( 'H:i:s' );
	
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh' => $id_sh, 'applique' => 0, 'date' => $date, 'heure < ?' => $heure));
		$select->order('heure DESC');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
	
	public function getHeuresPourAujourdhui($id_sh)
	{
		$today = new \DateTime();
		$date = $today->format ( 'Y-m-d' );
		
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh'=>$id_sh, 'date'=>$date));
		$select->order('id_heure ASC');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		$tab = array();
		foreach ($result as $resultat){
			$tab[] = $resultat['heure'];
		}
		return $tab;
	}
	
	public function getToutesHeuresPourAujourdhui($id_sh)
	{
		$today = new \DateTime();
		$date = $today->format ( 'Y-m-d' );
		
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh' => $id_sh, 'date' => $date));
		$select->order('id_heure ASC');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		return $result;
	}
	
	public function getHeureAppliqueePourAujourdhui($id_sh, $heure)
	{
		$today = new \DateTime();
		$date = $today->format ( 'Y-m-d' );
		
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh' => $id_sh, 'date' => $date, 'heure' => $heure));
		
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
	

	//RECUPERER LES INFOS POUR UNE DATE DONNEES
	//RECUPERER LES INFOS POUR UNE DATE DONNEES
	public function getHeuresPourUneDate($id_sh, $date)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh'=>$id_sh, 'date'=>$date));
		$select->order('id_heure ASC');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		$tab = array();
		foreach ($result as $resultat){
			$tab[] = $resultat['heure'];
		}
		return $tab;
	}
	
	
	public function getHeureSuivantePourUneDate($id_sh, $date)
	{
		$today = new \DateTime();
		$heure = $today->format ( 'H:i:s' );
	
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh' => $id_sh, 'applique' => 0, 'date' => $date, 'heure > ?' => $heure));
		$select->order('heure ASC');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
	
	public function getHeureAppliqueePourUneDate($id_sh, $heure, $date)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh' => $id_sh, 'date' => $date, 'heure' => $heure));
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
	
	/**
	 * Toutes les dates inférieures a la date donnee
	 */
	public function getToutesDateDuSoinPourUneDate($id_sh, $date)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins')->columns(array('date'));
		$select->where(array('id_sh' => $id_sh, 'date <= ?' => $date));
		$select->order('date ASC');
		$select->group('date');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		return $result;
	}
	
	public function getToutesDateDuSoin($id_sh)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins')->columns(array('date'));
		$select->where(array('id_sh' => $id_sh));
		$select->order('date ASC');
		$select->group('date');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		return $result;
	}
	
	/**
	 * La date superieure a la date donnee
	 */
	public function getDateApresDateDonnee($id_sh, $date)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins')->columns(array('*'));
		$select->where(array('id_sh' => $id_sh, 'date > ?' => $date));
		$select->order('date ASC');
		$select->group('date');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
	
}