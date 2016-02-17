<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class ParametragesTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	/**
	 * Recuperer la liste des hopitaux
	 */
	public function getListeHopitaux()
	{
		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Nom','Region','Departement', 'Id');
		
		/* Indexed column (used for fast and accurate table cardinality) */
		$sIndexColumn = "id";
		
		/*
		 * Paging
		*/
		$sLimit = array();
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit[0] = $_GET['iDisplayLength'];
			$sLimit[1] = $_GET['iDisplayStart'];
		}
		
		/*
		 * Ordering
		*/
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = array();
			$j = 0;
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder[$j++] = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
								 	".$_GET['sSortDir_'.$i];
				}
			}
		}
		
		/*
		 * SQL queries
		*/
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('h' => 'hopital'))->columns(array('Nom'=>'NOM_HOPITAL','Id'=>'ID_HOPITAL'))
		->join(array('d' => 'departement') ,'d.id = h.ID_DEPARTEMENT' , array('Departement'=>'nom') )
		->join(array('r' => 'region') ,'r.id = d.id_region' , array('Region'=>'nom') )
		->order('h.ID_HOPITAL DESC');
		
		
		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
		
		$rResult = $rResultFt;
		
		$output = array(
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
		);
		
		/*
		 * $Control pour convertir la date en fran�ais
		*/
		$Control = new DateHelper();
		
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		
		/*
		 * Pr�parer la liste
		*/
		foreach ( $rResult as $aRow )
		{
			$row = array();
			  for ( $i=0 ; $i<count($aColumns) ; $i++ )
			  {
				if ( $aColumns[$i] != ' ')
				{ 
					/* General output */
					if ($aColumns[$i] == 'Id') {
						$html  ="<a href='javascript:visualiserDetails(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='détails'></a>";
		
						$html  .="<a href='javascript:modifier(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='modifier'></a>";
		
						$html .="<input id='".$aRow[ $aColumns[$i] ]."'   type='hidden' value='".$aRow[ 'Id' ]."'>";
		
						$row[] = $html;
					}
		
					else {
						$row[] = $aRow[ $aColumns[$i] ];
					}
				}
			  }
		
			  $output['aaData'][] = $row;
		}
		
		
		return $output;
		
	}
	
	/**
	 * Liste des regions
	 */
	public function getListeRegions()
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('r' => 'region'))->columns(array('*'));
		
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute();
		
		$region = array('' => '');
		foreach ($Result as $resultat){
			$region[$resultat['id']] = $resultat['nom'];
		} 
		return $region;
	}
	
	/**
	 * Liste des departements
	 */
	public function getListeDepartements($id)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('d' => 'departement'))->columns(array('*'))
		->where(array('id_region' => $id));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute();
	
		return $Result;
	}
	
	/**
	 * Ajout d'un hopital
	 */
	public function addHopital($nom, $id_departement, $id_personne, $directeur, $note)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->insert()
		->into('hopital')
		->values(array(
				'NOM_HOPITAL' => $nom, 
				'ID_DEPARTEMENT' => $id_departement, 
				'ID_PERSONNE' => $id_personne,
				'DIRECTEUR' => $directeur,
				'NOTE' => $note,
		));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$stat->execute();
	}
	
	/**
	 * Mis � jour d'un hopital
	 */
	public function updateHopital($updateHopital, $nom, $id_departement, $id_personne, $directeur, $note)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->update()
		->table('hopital')
		->set(array(
				'NOM_HOPITAL' => $nom,
				'ID_DEPARTEMENT' => $id_departement,
				'ID_PERSONNE' => $id_personne,
				'DIRECTEUR' => $directeur,
				'NOTE' => $note,
		))
		->where(array('ID_HOPITAL' => $updateHopital));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$stat->execute();
	}
	
	
	/**
	 * Informations d'un hopital
	 */
	public function getInfosHopital($id)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('h' => 'hopital'))->columns(array('Nom'=>'NOM_HOPITAL','Id'=>'ID_HOPITAL','Directeur'=>'DIRECTEUR','Note'=>'NOTE'))
		->join(array('d' => 'departement') ,'d.id = h.ID_DEPARTEMENT' , array('Departement'=>'nom', 'Id_departement'=>'id') )
		->join(array('r' => 'region') ,'r.id = d.id_region' , array('Region'=>'nom', 'Id_region'=>'id') )
		->where(array('h.ID_HOPITAL' => $id));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		return $stat->execute()->current();
	}
	
	
// 	const PASSWORD_HASH = 'MY_PASSWORD_HASH_WHICH_SHOULD_BE_SOMETHING_SECURE';
// 	protected function _encryptPassword($value) {
// 		for($i = 0; $i < 10; $i ++) {
// 			$value = md5 ( $value . self::PASSWORD_HASH );
// 		}
// 		return $value;
// 	}
	
	
// 	public function saveDonnees($donnees)
// 	{
// 		$date = new \DateTime ("now");
// 		$formatDate = $date->format ( 'Y-m-d H:i:s' );
// 		$data = array(
// 				'username' => $donnees->username,
// 				'password' => $this->_encryptPassword($donnees->password),
// 				'role' => $donnees->role,
// 				'fonction' => $donnees->fonction,
// 				'id_personne' => $donnees->idPersonne,
// 		);
		
// 		$id = (int)$donnees->id;
// 		//var_dump($id); exit();
// 		if($id == 0) {
// 			$data['date_enregistrement'] = $formatDate;
// 			$this->tableGateway->insert($data);
// 		}
// 		else {
// 			$data['date_de_modification'] = $formatDate;
// 			$this->tableGateway->update($data, array('id' => $id));
// 		}
// 	}
	
// 	public function modifierPassword($donnees)
// 	{
// 		$date = new \DateTime ("now");
// 		$formatDate = $date->format ( 'Y-m-d H:i:s' );
// 		$data = array(
// 				'username' => $donnees->username,
// 				'password' => $this->_encryptPassword($donnees->nouveaupassword),
// 				'date_de_modification' => $formatDate,
// 		);
	
// 		$this->tableGateway->update($data, array('id' => $donnees->id));
// 	}
	
// 	/**
// 	 * Encrypts a value by md5 + static token
// 	 * 10 times
// 	 */
// 	public function encryptPassword($value) {
// 		for($i = 0; $i < 10; $i ++) {
// 			$value = md5 ( $value . self::PASSWORD_HASH );
// 		}
	
// 		return $value;
// 	}
	
// 	//Réduire la chaine addresse
// 	function adresseText($Text){
// 		$chaine = $Text;
// 		if(strlen($Text)>36){
// 			$chaine = substr($Text, 0, 36);
// 			$nb = strrpos($chaine, ' ');
// 			$chaine = substr($chaine, 0, $nb);
// 			$chaine .=' ...';
// 		}
// 		return $chaine;
// 	}
	
// 	/**
// 	 * LISTE DE TOUTS LES AGENTS DU PERSONNEL
// 	 * @param unknown $id
// 	 * @return string
// 	 */
// 	public function getListeAgentPersonnelAjax(){
	
// 		$db = $this->tableGateway->getAdapter();
	
// 		$aColumns = array('Idpatient','Nom','Prenom','Datenaissance', 'NomService', 'id');
	
// 		/* Indexed column (used for fast and accurate table cardinality) */
// 		$sIndexColumn = "id";
	
// 		/*
// 		 * Paging
// 		*/
// 		$sLimit = array();
// 		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
// 		{
// 			$sLimit[0] = $_GET['iDisplayLength'];
// 			$sLimit[1] = $_GET['iDisplayStart'];
// 		}
	
// 		/*
// 		 * Ordering
// 		*/
// 		if ( isset( $_GET['iSortCol_0'] ) )
// 		{
// 			$sOrder = array();
// 			$j = 0;
// 			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
// 			{
// 				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
// 				{
// 					$sOrder[$j++] = $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
// 								 	".$_GET['sSortDir_'.$i];
// 				}
// 			}
// 		}
	
// 		/*
// 		 * SQL queries
// 		*/
	
// 		$sql = new Sql($db);
// 		$sQuery = $sql->select()
// 		->from(array('e' => 'employe'))->columns(array('*'))
// 		->join(array('p' => 'personne') ,'p.id_personne = e.id_personne' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE' , 'id'=>'id_personne','Idpatient'=>'id_personne' ) )
// 		->join(array('se' => 'service_employe') ,'se.id_employe = p.id_personne' , array() )
// 		->join(array('s' => 'service') ,'s.ID_SERVICE = se.id_service' , array('NomService' => 'NOM') )
// 		->order('p.id_personne ASC');

// 		/* Data set length after filtering */
// 		$stat = $sql->prepareStatementForSqlObject($sQuery);
// 		$rResultFt = $stat->execute();
// 		$iFilteredTotal = count($rResultFt);
	
// 		$rResult = $rResultFt;
	
// 		$output = array(
// 				//"sEcho" => intval($_GET['sEcho']),
// 				//"iTotalRecords" => $iTotal,
// 				"iTotalDisplayRecords" => $iFilteredTotal,
// 				"aaData" => array()
// 		);
	
// 		/*
// 		 * $Control pour convertir la date en fran�ais
// 		*/
// 		$Control = new DateHelper();
	
// 		/*
// 		 * ADRESSE URL RELATIF
// 		*/
// 		$baseUrl = $_SERVER['REQUEST_URI'];
// 		$tabURI  = explode('public', $baseUrl);
	
// 		/*
// 		 * Pr�parer la liste
// 		*/
// 		foreach ( $rResult as $aRow )
// 		{
// 			$row = array();
// 			for ( $i=0 ; $i<count($aColumns) ; $i++ )
// 			{
// 				if ( $aColumns[$i] != ' ' )
// 				{
// 					/* General output */
// 					if ($aColumns[$i] == 'Nom'){
// 						$row[] = "<khass id='nomMaj'>".$aRow[ $aColumns[$i]]."</khass>";
// 					}
	
// 					else if ($aColumns[$i] == 'Datenaissance') {
// 						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]);
// 					}
	
// 					else if ($aColumns[$i] == 'Adresse') {
// 						$row[] = $this->adresseText($aRow[ $aColumns[$i] ]);
// 					}
	
// 					else if ($aColumns[$i] == 'id') {
// 						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
// 						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
	
// 						$html .= "<infoBulleVue> <a href='javascript:nouvelUtilisateur(".$aRow[ $aColumns[$i] ].")' >";
// 						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/transfert_droite.png' title='suivant'></a> </infoBulleVue>";
	
// 						$row[] = $html;
// 					}
	
// 					else {
// 						$row[] = $aRow[ $aColumns[$i] ];
// 					}
	
// 				}
// 			}
// 			$output['aaData'][] = $row;
// 		}
// 		return $output;
// 	}
	
// 	public function getAgentPersonnel($id)
// 	{
// 		$db = $this->tableGateway->getAdapter();
// 		$sql = new Sql($db);
// 		$sQuery = $sql->select()
// 		->from(array('pers' => 'personne'))->columns(array('*'))
// 		->where(array('id_personne' => $id));
		
// 		$stat = $sql->prepareStatementForSqlObject($sQuery);
// 		$Resultat = $stat->execute()->current();
		
// 		return $Resultat;
// 	}
	
// 	public function getPhoto($id) {
// 		$donneesAgent =  $this->getAgentPersonnel ( $id );
	
// 		$nom = null;
// 		if($donneesAgent){$nom = $donneesAgent['PHOTO'];}
// 		if ($nom) {
// 			return $nom . '.jpg';
// 		} else {
// 			return 'identite.jpg';
// 		}
// 	}
	
// 	public function getServiceAgent($id)
// 	{
// 		$db = $this->tableGateway->getAdapter();
// 		$sql = new Sql($db);
// 		$sQuery = $sql->select()
// 		->from(array('e' => 'employe'))->columns(array('*'))
// 		->join(array('se' => 'service_employe') ,'se.id_employe = e.id_personne' , array() )
// 		->join(array('s' => 'service') ,'s.ID_SERVICE = se.id_service' , array('NomService' => 'NOM' ,'IdService' => 'ID_SERVICE') )
// 		->where(array('e.id_personne' => $id));
// 		$stat = $sql->prepareStatementForSqlObject($sQuery);
// 		return  $stat->execute()->current();
// 	}
}