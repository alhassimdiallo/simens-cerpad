<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Facturation\View\Helper\DateHelper;


class AntecedentsFamiliauxTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getAntecedentsFamiliaux($id_personne){
		$rowset = $this->tableGateway->select ( array (
				'ID_PERSONNE' => $id_personne
		) );
		if (! $rowset) {
			return null;
		}
		return $rowset;
	}
	
	/**
	 * Recuperer dans un tableau tous les antecedents familiaux du patient avec son id_personne
	 */
	public function getTableauAntecedentsFamiliaux($id_personne){
		$rowset = $this->tableGateway->select ( array (
				'ID_PERSONNE' => $id_personne
		) );
		
		$Control = new DateHelper();
		$donnees = array();
		$donnees['DiabeteAF'] = 0;
		$donnees['DrepanocytoseAF'] = 0;
		$donnees['htaAF'] = 0;
		
		foreach ($rowset as $rows){
			if($rows->id_antecedent == 1){
				$donnees['DiabeteAF'] = 1;
				$donnees['NoteDiabeteAF'] = $rows->note;
			}
			if($rows->id_antecedent == 2){
				$donnees['DrepanocytoseAF'] = 1;
				$donnees['NoteDrepanocytoseAF'] = $rows->note;
			}
			if($rows->id_antecedent == 3){
				$donnees['htaAF'] = 1;
				$donnees['NoteHtaAF'] = $rows->note;
			}
		}
		
		return $donnees;
	}
	
	
	/**
	 * Recuperer l'id du patient avec son id_cons
	 */
	public function getIdPersonneParIdCons($id_cons){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->columns( array( 'ID_PERSONNE' => 'PAT_ID_PERSONNE' ));
		$select->from( array( 'c' => 'consultation' ));
		$select->where(array('c.ID_CONS' =>$id_cons));

		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ()->current();
		
		return $result['ID_PERSONNE'];
	}
	
	/**
	 * Ajouter et mettre à jour les antécédents familiaux des patients
	 */
	public function addAntecedentsFamiliaux($donneesDesAntecedents, $id_personne, $id_medecin){
		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
		$Control = new DateHelper();
		
		try {
			$this->tableGateway->delete(array('ID_PERSONNE' => $id_personne));
			//LES ANTECEDANTS FAMILIAUX
			//LES ANTECEDANTS FAMILIAUX
			/*Diabète*/
			if($donneesDesAntecedents['DiabeteAF'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 1,
						'NOTE' => $donneesDesAntecedents['NoteDiabeteAF'], 
						'ID_EMPLOYE' => $id_medecin,
				);
				
				$this->tableGateway->insert($donneesAntecedents);
			}
			/*Drépanocytose*/
			if($donneesDesAntecedents['DrepanocytoseAF'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 2,
						'NOTE' => $donneesDesAntecedents['NoteDrepanocytoseAF'],
						'ID_EMPLOYE' => $id_medecin,
				);
			
				$this->tableGateway->insert($donneesAntecedents);
			}
			/*HTA*/
			if($donneesDesAntecedents['htaAF'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 3,
						'NOTE' => $donneesDesAntecedents['NoteHtaAF'],
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}

	
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->commit();
		} catch (\Exception $e) {
			$this->tableGateway->getAdapter()->getDriver()->getConnection()->rollback();
		}
	}

}