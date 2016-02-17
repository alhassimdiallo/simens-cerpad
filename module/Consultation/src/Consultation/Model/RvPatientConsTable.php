<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\XmlRpc\Value\String;
use Doctrine\Tests\Common\Annotations\Null;

class RvPatientConsTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getRendezVous($id){
		$rowset = $this->tableGateway->select ( array (
				'ID_CONS' => $id
		) );
		$row =  $rowset->current ();
		if (! $row) { 
 			//throw new \Exception ( "Could not find row $id" );
 			return $row;
 		}
		return $row;
	}
	
	public function updateRendezVous($infos_rv){
		$this->tableGateway->delete(array('ID_CONS'=>$infos_rv['ID_CONS']));
		
		if($infos_rv['DATE'] && $infos_rv['HEURE']){
			$this->tableGateway->insert($infos_rv);
		}
	}
}