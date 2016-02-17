<?php

namespace Hospitalisation\Model;

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
	
	public function saveHeure($data, $id_sh)
	{
		for($i=0; $i<count($data->heure_recommandee) ; $i++ ){
			$adapter = $this->tableGateway->getAdapter();
			$sql = new Sql($adapter);
			$select = $sql->insert();
			$select->into('heures_soins');
			$select->columns(array('heure', 'id_sh'));
			$select->values(array('heure'=>$data->heure_recommandee[$i], 'id_sh'=>$id_sh));
			
			$stat = $sql->prepareStatementForSqlObject($select);
			$stat->execute();
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
	
	/*Enregistrer l'enregistrement du soin a cette heure*/
	/*Enregistrer l'enregistrement du soin a cette heure*/
	public function saveHeureSoinAppliquer($id_heure, $id_sh, $note, $id_personne)
	{
		$today = new \DateTime ();
		$date_enreg = $today->format ( 'Y-m-d H:i:s' );
		
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->update();
		$select->table('heures_soins');
		$select->set(array('note'=> $note, 'date_application'=>$date_enreg, 'id_personne_infirmier'=>$id_personne, 'applique'=> 1));
		$select->where(array('id_heure'=>$id_heure, 'id_sh'=>$id_sh));
		
		$stat = $sql->prepareStatementForSqlObject($select);
		$stat->execute();
	}
	
	public function saveSoinhospitalisation($SoinHospitalisation, $id_medecin)
	{
		$this->getDateHelper();
		$today = new \DateTime ();
		$date_enreg = $today->format ( 'Y-m-d H:i:s' );
		
		$data = array(
				'id_hosp' => $SoinHospitalisation->id_hosp,
				'date_enregistrement'=> $date_enreg,
				'date_application_recommandee' => $this->conversionDate->convertDateInAnglais($SoinHospitalisation->date_application),
				'medicament' => $SoinHospitalisation->medicament,
				'voie_administration' => $SoinHospitalisation->voie_administration,
				'frequence' => $SoinHospitalisation->frequence,
				'dosage' => $SoinHospitalisation->dosage,
				'motif' => $SoinHospitalisation->motif,
				'note' => $SoinHospitalisation->note,
				'id_personne' => $id_medecin,
		);
			
		$id_sh = (int)$SoinHospitalisation->id_sh;
		if($id_sh == 0){
			return($this->tableGateway->getLastInsertValue($this->tableGateway->insert($data)));
		} else {
			$data = array(
					'date_modifcation_medecin'=> $date_enreg,
					'date_application_recommandee' => $this->conversionDate->convertDateInAnglais($SoinHospitalisation->date_application),
					'medicament' => $SoinHospitalisation->medicament,
					'voie_administration' => $SoinHospitalisation->voie_administration,
					'frequence' => $SoinHospitalisation->frequence,
					'dosage' => $SoinHospitalisation->dosage,
					'motif' => $SoinHospitalisation->motif,
					'note' => $SoinHospitalisation->note,
			);
			if($this->getSoinhospitalisationWithId_sh($id_sh)) {
				$this->tableGateway->update($data, array('id_sh' => $id_sh));
			} 
		}
	}
	
	public function supprimerHospitalisation($id_sh) {
		
		if($this->getSoinhospitalisationWithId_sh($id_sh)){
			$this->tableGateway->delete(array('id_sh' => $id_sh));
		}
	}
	
	public function appliquerSoin($id_sh) {
		$this->getDateHelper();
		$today = new \DateTime ();
		$date_application = $today->format ( 'Y-m-d H:i:s' );
		
		if($this->getSoinhospitalisationWithId_sh($id_sh)){
			$this->tableGateway->update(array('appliquer'=>1), array('id_sh' => $id_sh));
		}
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
	
	
	public function getToutesDateDuSoinTableau($id_sh)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins')->columns(array('date'));
		$select->where(array('id_sh' => $id_sh));
		$select->order('id_heure ASC');
		$select->group('date');
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		$tab = array();
		foreach ($result as $res){
			$tab[] = $res['date'];
		}
		return $tab;
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
	
	/*Heure suivante*/
	/*Heure suivante*/
	public function getHeure($id_heure, $id_sh)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh' => $id_sh, 'id_heure' => $id_heure, 'applique' => 0, ));
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
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
	
	/**
	 * Liste des soins du jour a appliquer --- pour les soins non encore appliques
	 */
	public function getListeDesSoinsDuJourAAppliquer()
	{
		$today = new \DateTime();
		$date = $today->format ( 'Y-m-d' );
		$heure = $today->format ( 'H:00' );
		
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('hs' => 'heures_soins'))->columns(array('*'));
		$select->join(array('sh'=>'soins_hospitalisation'), 'sh.id_sh = hs.id_sh', array('*'));
		$select->join(array('h'=>'hospitalisation'), 'h.id_hosp = sh.id_hosp', array('*'));
		$select->join(array('dh'=>'demande_hospitalisation'), 'dh.id_demande_hospi = h.code_demande_hospitalisation', array('*'));
		$select->join(array('c'=>'consultation'), 'c.id_cons = dh.id_cons', array('*'));
		$select->join(array('p'=>'personne'), 'p.ID_PERSONNE = c.ID_PATIENT', array('*'));
		
		$select->join(array('hl'=>'hospitalisation_lit'), 'hl.id_hosp = h.id_hosp', array('*'));
		$select->join(array('l'=>'lit'), 'l.id_materiel = hl.id_materiel', array('intitule'));
		$select->join(array('s'=>'salle'), 's.id_salle = l.id_salle', array('numero_salle'));
		
		$select->where(array('hs.date' => $date, 'applique' =>0));
		$select->order(array('heure' =>'ASC'));
		
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		return $result;
	}
	
	/**
	 * Liste des soins du jour deja appliques
	 */
	public function getListeDesSoinsDuJourDejaAppliquer()
	{
		$today = new \DateTime();
		$date = $today->format ( 'Y-m-d' );
		$heure = $today->format ( 'H:00' );
	
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('hs' => 'heures_soins'))->columns(array('*'));
		$select->join(array('sh'=>'soins_hospitalisation'), 'sh.id_sh = hs.id_sh', array('*'));
		$select->join(array('h'=>'hospitalisation'), 'h.id_hosp = sh.id_hosp', array('*'));
		$select->join(array('dh'=>'demande_hospitalisation'), 'dh.id_demande_hospi = h.code_demande_hospitalisation', array('*'));
		$select->join(array('c'=>'consultation'), 'c.id_cons = dh.id_cons', array('*'));
		$select->join(array('p'=>'personne'), 'p.ID_PERSONNE = c.ID_PATIENT', array('*'));
	
		$select->join(array('hl'=>'hospitalisation_lit'), 'hl.id_hosp = h.id_hosp', array('*'));
		$select->join(array('l'=>'lit'), 'l.id_materiel = hl.id_materiel', array('intitule'));
		$select->join(array('s'=>'salle'), 's.id_salle = l.id_salle', array('numero_salle'));
	
		$select->where(array('hs.date' => $date, 'applique' =>1));
		$select->order(array('heure' =>'ASC'));
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
			
		return $result;
	}
	
	public function getHeureSoin($id_heure, $id_sh)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from('heures_soins');
		$select->where(array('id_sh' => $id_sh, 'id_heure' => $id_heure ));
	
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
			
		return $result;
	}
}