<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Facturation\View\Helper\DateHelper;


class AntecedentPersonnelTable {

	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getAntecedentsPersonnels($id_personne){
		$rowset = $this->tableGateway->select ( array (
				'ID_PERSONNE' => $id_personne
		) );
		if (! $rowset) {
			return null;
		}
		return $rowset;
	}
	
	/**
	 * Recuperer dans un tableau tous les antecedents personnels du patient avec son id_personne
	 */
	public function getTableauAntecedentsPersonnels($id_personne){
		$rowset = $this->tableGateway->select ( array (
				'ID_PERSONNE' => $id_personne
		) );
		
		$Control = new DateHelper();
		$donnees = array();
		$donnees['DiabeteAM'] = 0;
		$donnees['drepanocytoseAM'] = 0;
		$donnees['htaAM'] = 0;
		$donnees['dislipidemieAM'] = 0;
		$donnees['asthmeAM'] = 0;
		
		$donnees['AlcooliqueHV'] = 0;
		$donnees['FumeurHV'] = 0;
		$donnees['DroguerHV'] = 0;
		$donnees['nbPaquetFumeurHV'] = 0;
		
		$donnees['MenarcheGO'] = 0;
		$donnees['GestiteGO'] = 0;
		$donnees['PariteGO'] = 0;
		$donnees['CycleGO'] = 0;
		
		foreach ($rowset as $rows){
			//ANTECEDENT MEDICAUX
			if($rows->id_antecedent == 1){
				$donnees['DiabeteAM'] = 1;
			}
			if($rows->id_antecedent == 2){
				$donnees['drepanocytoseAM'] = 1;
			}
			if($rows->id_antecedent == 3){
				$donnees['htaAM'] = 1;
			}
			if($rows->id_antecedent == 7){
				$donnees['dislipidemieAM'] = 1;
			}
			if($rows->id_antecedent == 8){
				$donnees['asthmeAM'] = 1;
			}
			
			//HABITUDE DE VIE
			if($rows->id_antecedent == 4){
				$donnees['AlcooliqueHV'] = 1;
				$donnees['DateDebutAlcooliqueHV'] = $Control->convertDate($rows->date_debut);
				$donnees['DateFinAlcooliqueHV'] = $Control->convertDate($rows->date_arret);
			}
			if($rows->id_antecedent == 5){
				$donnees['FumeurHV'] = 1;
				$donnees['DateDebutFumeurHV'] = $Control->convertDate($rows->date_debut);
				$donnees['DateFinFumeurHV'] = $Control->convertDate($rows->date_arret);
				$donnees['nbPaquetFumeurHV'] = $rows->nombre_paquet_jour;
			}
			if($rows->id_antecedent == 6){
				$donnees['DroguerHV'] = 1;
				$donnees['DateDebutDroguerHV'] = $Control->convertDate($rows->date_debut);
				$donnees['DateFinDroguerHV'] = $Control->convertDate($rows->date_arret);
			}
			
			//GYNECO-OBSTETRIQUE
			if($rows->id_antecedent == 9){
				$donnees['MenarcheGO'] = 1;
				$donnees['NoteMenarcheGO'] = $rows->note;
			}
			if($rows->id_antecedent == 10){
				$donnees['GestiteGO'] = 1;
				$donnees['NoteGestiteGO'] = $rows->note;
			}
			if($rows->id_antecedent == 11){
				$donnees['PariteGO'] = 1;
				$donnees['NotePariteGO'] = $rows->note;
			}
			if($rows->id_antecedent == 12){
				$donnees['CycleGO'] = 1;
				$donnees['DureeCycleGO'] = $rows->duree;
				$donnees['RegulariteCycleGO'] = $rows->regularite;
				$donnees['DysmenorrheeCycleGO'] = $rows->dysmenorrhee;
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
		$select->columns( array( 'ID_PERSONNE' => 'ID_PATIENT' ));
		$select->from( array( 'c' => 'consultation' ));
		$select->where(array('c.ID_CONS' =>$id_cons));

		$stat = $sql->prepareStatementForSqlObject ( $select );
		$result = $stat->execute ()->current();
		
		return $result['ID_PERSONNE'];
	}
	
	/**
	 * Ajouter et mettre à jour les antécédents personnels des patients
	 */
	public function addAntecedentsPersonnels($donneesDesAntecedents, $id_personne, $id_medecin){
		$this->tableGateway->getAdapter()->getDriver()->getConnection()->beginTransaction();
		$Control = new DateHelper();
		
		try {
			$this->tableGateway->delete(array('ID_PERSONNE' => $id_personne));
			//LES HABITUDES DE VIE DU PATIENTS
			//LES HABITUDES DE VIE DU PATIENTS
			if($donneesDesAntecedents['AlcooliqueHV'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 4,
						'DATE_DEBUT' => $Control->convertDateInAnglais($donneesDesAntecedents['DateDebutAlcooliqueHV']), 
						'DATE_ARRET' => $Control->convertDateInAnglais($donneesDesAntecedents['DateFinAlcooliqueHV']),
						'ID_EMPLOYE' => $id_medecin,
				);
				
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['FumeurHV'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 5,
						'DATE_DEBUT' => $Control->convertDateInAnglais($donneesDesAntecedents['DateDebutFumeurHV']),
						'DATE_ARRET' => $Control->convertDateInAnglais($donneesDesAntecedents['DateFinFumeurHV']),
						'NOMBRE_PAQUET_JOUR' => $donneesDesAntecedents['nbPaquetFumeurHV'],
						'ID_EMPLOYE' => $id_medecin,
				);
			
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['DroguerHV'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 6,
						'DATE_DEBUT' => $Control->convertDateInAnglais($donneesDesAntecedents['DateDebutDroguerHV']),
						'DATE_ARRET' => $Control->convertDateInAnglais($donneesDesAntecedents['DateFinDroguerHV']),
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			
			//LES ANTECEDENTS MEDICAUX
			//LES ANTECEDENTS MEDICAUX
			if($donneesDesAntecedents['DiabeteAM'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 1,
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['drepanocytoseAM'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 2,
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['htaAM'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 3,
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['dislipidemieAM'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 7,
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['asthmeAM'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 8,
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			
			//GYNECO-OBSTETRIQUE
			//GYNECO-OBSTETRIQUE
			if($donneesDesAntecedents['MenarcheGO'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 9,
						'NOTE' => $donneesDesAntecedents['NoteMenarcheGO'],
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['GestiteGO'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 10,
						'NOTE' => $donneesDesAntecedents['NoteGestiteGO'],
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['PariteGO'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 11,
						'NOTE' => $donneesDesAntecedents['NotePariteGO'],
						'ID_EMPLOYE' => $id_medecin,
				);
					
				$this->tableGateway->insert($donneesAntecedents);
			}
			if($donneesDesAntecedents['CycleGO'] == 1){
				$donneesAntecedents = array(
						'ID_PERSONNE' => $id_personne,
						'ID_ANTECEDENT' => 12,
						'DUREE' => $donneesDesAntecedents['DureeCycleGO'],
						'REGULARITE' => $donneesDesAntecedents['RegulariteCycleGO'],
						'DYSMENORRHEE' => $donneesDesAntecedents['DysmenorrheeCycleGO'],
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