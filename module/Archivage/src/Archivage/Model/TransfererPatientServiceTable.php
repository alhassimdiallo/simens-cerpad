<?php
namespace Archivage\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
class TransfererPatientServiceTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function fetchHopital(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select('hopital');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		foreach ($result as $data) {
			$options[$data['ID_HOPITAL']] = $data['NOM_HOPITAL'];
		}
		return $options;
	}
	public function fetchServiceWithHopital($idHopital){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select(array('hs' =>'hopital_service'));
		$select->where(array('hs.ID_HOPITAL' => $idHopital));
		$select->columns(array('hs_IdService'=>'ID_SERVICE', 'hs_IdHopital'=>'ID_HOPITAL'));
		$select->join(array('s'=>'service'), 's.ID_SERVICE = hs.ID_SERVICE' , array('*'));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		foreach ($result as $data) {
			$options[$data['ID_SERVICE']] = $data['NOM'];
		}
		return $options;
	}
	public function getPatientMedecinDonnees($idcons) {
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array(
				'IdService' => 'ID_SERVICE',
				'IdPersonne' => 'ID_PERSONNE',
				'Date' => 'DATE',
				'MotifTransfert' => 'MOTIF_TRANSFERT',
				'IdCons' => 'ID_CONS',
		));
		$select->from( array( 'tps' => 'transferer_patient_service' ));
		$select->join( array( 
				's' => 'service'
		), 's.ID_SERVICE = tps.ID_SERVICE' , array (
				'NomService' => 'NOM',
				'DomaineService' => 'DOMAINE'
		) );
		$select->join( array( 
				'p' => 'patient'
		), 'p.ID_PERSONNE = tps.ID_PERSONNE' , array('*'));
		$select->join( array(
				'c' => 'consultation'
		), 'c.ID_CONS = tps.ID_CONS' , array('*'));
		$select->where ( array( 'tps.ID_CONS' => $idcons));
		
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ();
		
		return $result;
	}
	/**
	 * Recuperer le service ou le patient est transfere
	 */
	public function getServicePatientTransfert ($idcons) {
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from(array('tps' => 'transferer_patient_service'));
		$select->where(array('tps.ID_CONS' => $idcons));
		
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ()->current();

		return $result;
	}
	
	/**
	 * Recuperer l'hopital du service ou le patient est transfere
	 */
	public function getHopitalPatientTransfert ($idService) {
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array('*'));
		$select->from(array('hs' => 'hopital_service'));
		$select->where(array('hs.ID_SERVICE' => $idService));
	
		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ()->current();
	
		return $result;
	}
	
	/**
	 * Modification des donnees du transfert
	 */
    public function updateTransfertPatientService($info_transfert){
		$this->tableGateway->delete(array('ID_CONS'=> $info_transfert['ID_CONS']));
		$today = new \DateTime ();
		$dateAujourdhui = $today->format ( 'Y-m-d H:i:s' );
		$info_transfert['DATE'] = $dateAujourdhui;
		if($info_transfert['MOTIF_TRANSFERT'] && $info_transfert['ID_MEDECIN'] && $info_transfert['ID_SERVICE']){
			$this->tableGateway->insert($info_transfert);
		}
	}
	
	
	
	public function fetchServiceWithHopitalNotServiceActual($idHopital, $idDuService){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select(array('hs' =>'hopital_service'));
		$select->where(array('hs.ID_HOPITAL' => $idHopital));
		$select->columns(array('hs_IdService'=>'ID_SERVICE', 'hs_IdHopital'=>'ID_HOPITAL'));
		$select->join(array('s'=>'service'), 's.ID_SERVICE = hs.ID_SERVICE' , array('*'));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		
		$options = array();
		foreach ($result as $data) {
			if($data['ID_SERVICE'] != $idDuService){
				$options[$data['ID_SERVICE']] = $data['NOM'];
			}
		}
		return $options;
	}
}
