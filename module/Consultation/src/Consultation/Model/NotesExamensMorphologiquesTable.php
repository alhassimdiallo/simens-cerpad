<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
class NotesExamensMorphologiquesTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function getNotesExamensMorphologiques($id_cons){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('nem'=>'note_examen_morphologique'));
		$select->where(array('nem.id_cons' => $id_cons));
		$select->order('nem.code ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		
		$tab = array('radio'=>'','ecographie'=>'','fibroscopie'=>'','scanner'=>'','irm'=>'');
		
		foreach ($result as $donnes){
			if($donnes['id_examen'] == 8 ){ $tab['radio']       = $donnes['note'];}
			if($donnes['id_examen'] == 9 ){ $tab['ecographie']  = $donnes['note'];}
			if($donnes['id_examen'] == 10){ $tab['fibroscopie'] = $donnes['note'];}
			if($donnes['id_examen'] == 11){ $tab['scanner']     = $donnes['note'];}
			if($donnes['id_examen'] == 12){ $tab['irm']         = $donnes['note'];}
				
		}
		return $tab;
	}
	public function updateNotesExamensMorphologiques($donnees)
	{
		$this->tableGateway->delete(array('id_cons' => $donnees['id_cons']));
		
		for($i=8; $i<13; $i++){ 
        	if($donnees[$i]){
		      $dataNotesExamens	 = array(
			 	'id_cons'    => $donnees['id_cons'],
				'id_examen' => $i,
				'note'      => $donnees[$i],
		      );
		      $this->tableGateway->insert($dataNotesExamens);
        	}
        }
	}
}
