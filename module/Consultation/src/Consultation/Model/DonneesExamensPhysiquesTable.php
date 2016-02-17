<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
class DonneesExamensPhysiquesTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function getExamensPhysiques($id_cons){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('dep'=>'donnees_examen_physique'));
		$select->where(array('dep.id_cons' => $id_cons));
		$select->order('dep.code_examen ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();

		return $result;
	}
	
	public function updateExamenPhysique($donnees)
	{
		$this->tableGateway->delete(array('id_cons' => $donnees['id_cons']));

 		for($i=1 ; $i<=5; $i++){ // 5 car on s'arrete a 5 champs de données
 			if($donnees['donnee'.$i]){
 				$datadonnee	 = array(
 						'libelle_examen' => $donnees['donnee'.$i],
 						'id_cons' => $donnees['id_cons'],
 				);
 				$this->tableGateway->insert($datadonnee);
 			}
	
		}
	}
	
	public function addExamenPhysiqueExamenJour($donnees)
	{
		$this->tableGateway->delete(array('id_cons' => $donnees['id_cons']));
	
		for($i=1 ; $i<=5; $i++){ // 5 car on s'arrete a 5 champs de données
			if($donnees['donnee'.$i]){
				$datadonnee	 = array(
						'libelle_examen' => $donnees['donnee'.$i],
						'id_cons' => $donnees['id_cons'],
				);
				$this->tableGateway->insert($datadonnee);
			}
	
		}
	}
}
