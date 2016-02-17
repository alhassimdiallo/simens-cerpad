<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class UtilisateursTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}

	public function getUtilisateurs($id)
	{
		$id  = (int) $id;
		$rowset = $this->tableGateway->select(array('id' => $id));
		$row = $rowset->current();
		if (!$row) {
			return null;
		}
		return $row;
	}
	
	
	public function getUtilisateursWithUsername($username)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('u' => 'utilisateurs')) -> columns(array('*') )
		->join(array('e' => 'employe'), 'e.id_personne = u.id_personne' , array('*'))
		->join(array('p' => 'personne') ,'p.id_personne = e.id_personne' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE' ) )
		->join(array('se' => 'service_employe') ,'se.id_employe = p.id_personne' , array('*') )
		->join(array('s' => 'service') ,'s.ID_SERVICE = se.id_service' , array('NomService' => 'NOM', 'IdService' => 'ID_SERVICE') )
		->where(array('username'=>$username));
		
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute()->current();
		return $Result;
	}
	
	/**
	 * Recuperer la liste des utilisateurs
	 */
	public function getListeUtilisateurs()
	{
		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Username','Nom','Prenom','NomService','Fonction','Role', 'Id');
		
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
		->from(array('u' => 'utilisateurs'))->columns(array('Username'=>'username','Role'=>'role','Fonction'=>'fonction','Id'=>'id'))
		->join(array('e' => 'employe') ,'e.id_personne = u.id_personne' , array('*') )
		->join(array('p' => 'personne') ,'p.id_personne = e.id_personne' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE') )
		->join(array('se' => 'service_employe') ,'se.id_employe = p.id_personne' , array('*') )
		->join(array('s' => 'service') ,'s.ID_SERVICE = se.id_service' , array('NomService' => 'NOM') )
		->order('p.id_personne ASC');
		
		
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
			if($aRow['Id'] != 1) { //SI C'EST LE superAdmin on ne l'affiche pas
			  for ( $i=0 ; $i<count($aColumns) ; $i++ )
			  {
				if ( $aColumns[$i] != ' ')
				{ 
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<khass id='nomMaj'>".$aRow[ $aColumns[$i]]."</khass>";
					}
		
					else if ($aColumns[$i] == 'Id') {
						$html  ="<a href='javascript:modifier(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 30%;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='détails'></a>";
		
						$html  .="<a href='javascript:supprimer(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/delete_16.png' title='Hospitaliser'></a>";
		
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
		}
		
		
		return $output;
		
	}
	
	/**
	 * Crypter le mot de passe
	 * @param unknown $donnees
	 */
	const PASSWORD_HASH = 'MY_PASSWORD_HASH_WHICH_SHOULD_BE_SOMETHING_SECURE';
	protected function _encryptPassword($value) {
		for($i = 0; $i < 10; $i ++) {
			$value = md5 ( $value . self::PASSWORD_HASH );
		}
		return $value;
	}
	
	
	public function saveDonnees($donnees)
	{
		$date = new \DateTime ("now");
		$formatDate = $date->format ( 'Y-m-d H:i:s' );
		$data = array(
				'username' => $donnees->username,
				'password' => $this->_encryptPassword($donnees->password),
				'role' => $donnees->role,
				'fonction' => $donnees->fonction,
				'id_personne' => $donnees->idPersonne,
		);
		
		$id = (int)$donnees->id;
		//var_dump($id); exit();
		if($id == 0) {
			$data['date_enregistrement'] = $formatDate;
			$this->tableGateway->insert($data);
		}
		else {
			$data['date_de_modification'] = $formatDate;
			$this->tableGateway->update($data, array('id' => $id));
		}
	}
	
	public function modifierPassword($donnees)
	{
		$date = new \DateTime ("now");
		$formatDate = $date->format ( 'Y-m-d H:i:s' );
		$data = array(
				'username' => $donnees->username,
				'password' => $this->_encryptPassword($donnees->nouveaupassword),
				'date_de_modification' => $formatDate,
		);
	
		$this->tableGateway->update($data, array('id' => $donnees->id));
	}
	
	/**
	 * Encrypts a value by md5 + static token
	 * 10 times
	 */
	public function encryptPassword($value) {
		for($i = 0; $i < 10; $i ++) {
			$value = md5 ( $value . self::PASSWORD_HASH );
		}
	
		return $value;
	}
	
	//Réduire la chaine addresse
	function adresseText($Text){
		$chaine = $Text;
		if(strlen($Text)>36){
			$chaine = substr($Text, 0, 36);
			$nb = strrpos($chaine, ' ');
			$chaine = substr($chaine, 0, $nb);
			$chaine .=' ...';
		}
		return $chaine;
	}
	
	/**
	 * LISTE DE TOUTS LES AGENTS DU PERSONNEL
	 * @param unknown $id
	 * @return string
	 */
	public function getListeAgentPersonnelAjax(){
	
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('Idpatient','Nom','Prenom','Datenaissance', 'NomService', 'id');
	
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
		->from(array('e' => 'employe'))->columns(array('*'))
		->join(array('p' => 'personne') ,'p.id_personne = e.id_personne' , array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE' , 'id'=>'id_personne','Idpatient'=>'id_personne' ) )
		->join(array('se' => 'service_employe') ,'se.id_employe = p.id_personne' , array() )
		->join(array('s' => 'service') ,'s.ID_SERVICE = se.id_service' , array('NomService' => 'NOM') )
		->order('p.id_personne ASC');

		/* Data set length after filtering */
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$rResultFt = $stat->execute();
		$iFilteredTotal = count($rResultFt);
	
		$rResult = $rResultFt;
	
		$output = array(
				//"sEcho" => intval($_GET['sEcho']),
				//"iTotalRecords" => $iTotal,
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
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
					if ($aColumns[$i] == 'Nom'){
						$row[] = "<khass id='nomMaj'>".$aRow[ $aColumns[$i]]."</khass>";
					}
	
					else if ($aColumns[$i] == 'Datenaissance') {
						$row[] = $Control->convertDate($aRow[ $aColumns[$i] ]);
					}
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = $this->adresseText($aRow[ $aColumns[$i] ]);
					}
	
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
	
						$html .= "<infoBulleVue> <a href='javascript:nouvelUtilisateur(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/transfert_droite.png' title='suivant'></a> </infoBulleVue>";
	
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
	
	public function getAgentPersonnel($id)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pers' => 'personne'))->columns(array('*'))
		->where(array('id_personne' => $id));
		
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Resultat = $stat->execute()->current();
		
		return $Resultat;
	}
	
	public function getPhoto($id) {
		$donneesAgent =  $this->getAgentPersonnel ( $id );
	
		$nom = null;
		if($donneesAgent){$nom = $donneesAgent['PHOTO'];}
		if ($nom) {
			return $nom . '.jpg';
		} else {
			return 'identite.jpg';
		}
	}
	
	public function getServiceAgent($id)
	{
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('e' => 'employe'))->columns(array('*'))
		->join(array('se' => 'service_employe') ,'se.id_employe = e.id_personne' , array() )
		->join(array('s' => 'service') ,'s.ID_SERVICE = se.id_service' , array('NomService' => 'NOM' ,'IdService' => 'ID_SERVICE') )
		->where(array('e.id_personne' => $id));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		return  $stat->execute()->current();
	}
	
	public function modifierPasswordAjax($ancienUsername, $nouveauUsername, $nouveaupassword)
	{
		$date = new \DateTime ("now");
		$formatDate = $date->format ( 'Y-m-d H:i:s' );
		$data = array(
				'username' => $nouveauUsername,
				'password' => $this->_encryptPassword($nouveaupassword),
				'date_de_modification' => $formatDate,
		);
	
		$this->tableGateway->update($data, array('username' => $ancienUsername));
	}
	
}