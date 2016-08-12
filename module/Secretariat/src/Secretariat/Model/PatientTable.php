<?php

namespace Secretariat\Model;

use Zend\Db\TableGateway\TableGateway;
use Secretariat\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Predicate\In;

class PatientTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	public function getPatient($idpersonne) {
		$idpersonne = ( int ) $idpersonne;
		$rowset = $this->tableGateway->select ( array (
				'idpersonne' => $idpersonne
		) );
		$row =  $rowset->current ();
		if (! $row) {
			$row = null;
		}
		return $row;
	}
	
	public function savePatient($idpersonne, $idemploye)
	{
		$data = array( 'idpersonne' => $idpersonne, 'date_enregistrement' => (new \DateTime() ) ->format('Y-m-d H:i:s'), 'idemploye' => $idemploye );
		$this->tableGateway->insert($data);
	}
	
	public function updatePatient($idpersonne, $idemploye)
	{
	    $data = array( 'idemploye' => $idemploye );
	    $this->tableGateway->update($data, array('idpersonne' => $idpersonne) );
	}

	public function saveDepistagePatient($idpatient, $ethnie, $idemploye)
	{
	    $data = array( 'idpatient' => $idpatient, 'ethnie' => $ethnie, 'date_enregistrement' => (new \DateTime() ) ->format('Y-m-d H:i:s'), 'idemploye' => $idemploye);
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->insert() ->into('depistage') ->values( $data );
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function updateDepistagePatient($idpatient, $ethnie, $typepatient, $idemploye)
	{
	    $data = array( 'ethnie' => $ethnie, 'typepatient' => $typepatient, 'idemploye' => $idemploye);
	    	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->update() ->table('depistage') ->set( $data ) ->where( array('idpatient' => $idpatient ) );
	    
	    $sql->prepareStatementForSqlObject($sQuery) ->execute();
	}
	
	public function getDepistagePatient($idpatient)
	{
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('dep' => 'depistage'))->columns(array('*'))
	    ->where(array('dep.idpatient' => $idpatient));
	    return $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	//La liste des patients dépistés de type (SS ou SC ou SB) pour lesquels le typepatient n'est pas mis à 1
	public function getListeDepistagePatient()
	{
	    $typagesPathologiques = array('SS','SC','SB');
	    
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('dep' => 'depistage'))->columns(array('*'))
	    ->where(array('dep.typepatient' => 0, new In('typage', $typagesPathologiques) ));
	    $result = $sql->prepareStatementForSqlObject($sQuery)->execute();
	    
	    $resultat = array();
	    foreach ($result as $res){
	        $resultat[] = $res['idpatient'];
	    }
	    
	    return $resultat;
	}
	
	public function listePatientsAjax()
	{
		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Nom','Prenom','Datenaissance', 'Sexe', 'Adresse', 'Nationalite', 'id', 'id2');
		
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
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle','id'=>'idpersonne','id2'=>'idpersonne'))
		->order('pat.idpersonne DESC');
		
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
		 * $Control pour convertir la date en franï¿½ais
		*/
		$Control = new DateHelper();
		
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		
		/*
		 * Prï¿½parer la liste
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
					    $nom = "";
					    if(in_array($aRow[ 'id' ], $this->getListeDepistagePatient())){
					        $nom  .= "<div id='alert_depistage_positif_".$aRow[ 'id' ]."' style='width: 10px; height: 22px; margin-left: -7px; margin-right: 0.5px; float:left; display:non' >
				                         <div style='background: red; height: 6px; width: 6px; margin-top: 6px; border-radius: 50%;'></div>
				                      </div>";
					    }
					    $nom .= "<div id='nomMaj'>".$aRow[ $aColumns[$i]]."</div>";
						$row[] = $nom;
					}
					
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Datenaissance') {
						$date_naissance = $aRow[ $aColumns[$i] ];
						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
					}
		
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
		
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
		
						$html .= "<infoBulleVue> <a href='javascript:modifierPatient(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/pencil_16.png' title='Modifier'></a> </infoBulleVue>";
		
						$depistage = $this->getDepistagePatient( $aRow[ $aColumns[$i] ] );
						
						$depister = 0;
						$typepatient = 0;
						if( $depistage->current() ){
						    $depister = 1;
						    $typepatient = $depistage->current()['typepatient'];
						} 
						
						if($depister == 1){
						    $html .="<span style='display: none;'> patient_depister </span>";
						}
						if($typepatient == 0){
						    $html .="<span style='display: none;'> patient_externe </span>";
						}else if($typepatient == 1){
						    $html .="<span style='display: none;'> patient_interne </span>";
						}
						
						
						
						$row[] = $html;
					}
					
					else if ($aColumns[$i] == 'Nationalite'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
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
	
	
	public function listeRecherchePatientAjax()
	{
		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Idpatient','Nom','Prenom','Datenaissance', 'Adresse', 'id');
		
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
		
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array())
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','id'=>'idpersonne','Idpatient'=>'idpersonne'))
		->order('pat.idpersonne DESC');
		
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
		 * $Control pour convertir la date en franï¿½ais
		*/
		$Control = new DateHelper();
		
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		
		/*
		 * Prï¿½parer la liste
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
						$row[] = "<div id='nomMaj'>".$aRow[ $aColumns[$i]]."</div>";
					}
					
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Datenaissance') {
						$date_naissance = $aRow[ $aColumns[$i] ];
						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
					}
		
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
		
					else if ($aColumns[$i] == 'id') {
						$html ="<a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='Visualiser'></a>";
		
						$html .= "<a href='javascript:demandesAnalyses(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/transfert_droite.png' title='Suivant'></a>";
		
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
	
	//Recupere la liste des actes
	//Recupere la liste des actes
	public function getListeDesTypesAnalyses(){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('t'=>'type_analyse'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function getListeDesAnalyses($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('a'=>'analyse'));
		$select->columns(array('*'));
		$select->where(array('a.idtype_analyse' => $id));
		$select->order('idanalyse ASC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function getTarifAnalyse($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('a'=>'analyse'));
		$select->columns(array('*'));
		$select->where(array('idanalyse' => $id));
		return $sql->prepareStatementForSqlObject($select)->execute()->current()['tarif'];
	}
	
	
	public function listeDemandesAjax()
	{
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('Nom','Prenom','Datenaissance', 'Sexe', 'Adresse', 'Date', 'id', 'id2');
	
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
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle','id'=>'idpersonne','id2'=>'idpersonne'))
		->join(array('dem_an' => 'demande_analyse'), 'pat.idpersonne = dem_an.idpatient', array('Date'=>'date', 'Time'=>'time')) 
		->order(array('dem_an.date' => 'DESC', 'dem_an.time' => 'DESC'))
		->group('idpatient');
	
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
		 * $Control pour convertir la date en franï¿½ais
		*/
		$Control = new DateHelper();
	
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
	
		/*
		 * Prï¿½parer la liste
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
						$row[] = "<div id='nomMaj'>".$aRow[ $aColumns[$i]]."</div>";
					}
						
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
	
					else if ($aColumns[$i] == 'Datenaissance') {
						$date_naissance = $aRow[ $aColumns[$i] ];
						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
					}
	
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
	
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='d&eacute;tails'></a> </infoBulleVue>";
	
						$html .= "<infoBulleVue> <a href='javascript:listeDemandes(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='liste des demandes'></a> </infoBulleVue>";
	
						$row[] = $html;
					}
						
					else if ($aColumns[$i] == 'Date'){
						$row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' '.$aRow['Time']."</div>";
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
	
	
	public function listeDemandesFiltreAjax()
	{
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('Iddemande', 'Date', 'Id');
	
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
		->from(array('dem' => 'demande_analyse'))->columns(array('Date'=>'date', 'Time'=>'time', 'Iddemande'=>'iddemande', 'Id'=>'iddemande'))
		->order('dem.date DESC') ->group('date');
	
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
		 * $Control pour convertir la date en franï¿½ais
		*/
		$Control = new DateHelper();
	
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
	
		/*
		 * Prï¿½parer la liste
		*/
		foreach ( $rResult as $aRow )
		{
			$row = array();
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				if ( $aColumns[$i] != ' ' )
				{
					/* General output */
	
					$html = "";
					if ($aColumns[$i] == 'Id') {
	
						$html .= "<infoBulleVue> <a href='javascript:listeDemandes(".$aRow[ $aColumns[$i] ].")' >";
						$html .= "<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='liste des demandes'></a> </infoBulleVue>";
						$row[] = $html;
					}
	
					else if ($aColumns[$i] == 'Date'){
						$row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' '.$aRow['Time']."</div>";
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
	

	/*Liste des demandes d'analyses*/
	/*Liste des demandes d'analyses*/
	public function getDemandesAnalyses($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->where(array('idpatient' => $id));
		$select->group('date');
		$select->order('date DESC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	public function getInfosSecretaire($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('p'=>'personne'));
		$select->columns(array('*'));
		$select->where(array('idpersonne' => $id));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	
	//Recuperer les analyses de la dernière demande du patient $id
	public function getAnalysesDemandees($id){
		$demande = $this->getDemandesAnalyses($id)->current();
		
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $demande['date'], 'idpatient' => $id));
		$select->order('idanalyse ASC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	
	public function getDemandeAnalysesAvecIddemande($iddemande){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->where(array('iddemande' => $iddemande));
		return $sql->prepareStatementForSqlObject($select)->execute()->current();
	}
	
	//Recuperer la liste des analyses demandees pour la demande $iddemande
	public function getListeAnalysesDemandees($iddemande){
		$dateDemande = $this->getDemandeAnalysesAvecIddemande($iddemande);
		
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
		$select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
		$select->where(array('date' => $dateDemande['date'], 'idpatient' => $dateDemande['idpatient']));
		$select->order(array('idanalyse' => 'ASC', 'idtype' =>'ASC'));
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
	/**
	 * Recuperer le montant total des analyses de la demande
	 */
	public function getMontantTotalAnalyses($iddemande){
		$listeAnalyse = $this->getListeAnalysesDemandees($iddemande);
	
		$somme = 0;
		foreach ($listeAnalyse as $resultat){
			$somme += $resultat['Tarif'];
		}
	
		return $somme;
	}
	
	
	/*La liste des demandes afficher sur l'interface du laborantin*/
	public function listeDemandesAnalyses(){

		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Nom','Prenom','Age', 'Sexe', 'Adresse', 'Date', 'id', 'id2');
		
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
		->from(array('pat' => 'patient'))->columns(array('*'))
		->join(array('pers' => 'personne'), 'pat.idpersonne = pers.idpersonne', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance', 'Age'=>'age', 'Sexe'=>'sexe','Adresse'=>'adresse','Nationalite'=>'nationalite_actuelle','id'=>'idpersonne','id2'=>'idpersonne'))
		->join(array('dem_an' => 'demande_analyse'), 'pat.idpersonne = dem_an.idpatient', array('Date'=>'date', 'Time'=>'time'))
		->order(array('dem_an.date' => 'ASC', 'dem_an.time' => 'ASC'))
		->group('idpatient');
		
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
		 * $Control pour convertir la date en franï¿½ais
		*/
		$Control = new DateHelper();
		
		/*
		 * ADRESSE URL RELATIF
		*/
		$baseUrl = $_SERVER['REQUEST_URI'];
		$tabURI  = explode('public', $baseUrl);
		
		/*
		 * Prï¿½parer la liste
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
						$row[] = "<div id='nomMaj'>".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Prenom'){
						$row[] = "<div>".$aRow[ $aColumns[$i]]."</div>";
					}
		
					else if ($aColumns[$i] == 'Datenaissance') {
						$date_naissance = $aRow[ $aColumns[$i] ];
						if($date_naissance){ $row[] = $Control->convertDate($aRow[ $aColumns[$i] ]); }else{ $row[] = null;}
					}
		
					else if ($aColumns[$i] == 'Adresse') {
						$row[] = "<div class='adresseText' >".$aRow[ $aColumns[$i] ]."</div>";
					}
					
					else if ($aColumns[$i] == 'Age') {
						$row[] = "<div style='text-align: center;'>".$aRow[ $aColumns[$i] ]."</div>";
					}
		
					else if ($aColumns[$i] == 'id') {
						$html ="<infoBulleVue> <a href='javascript:visualiser(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='margin-left: 5%; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='Visualiser'></a> </infoBulleVue>";
		
						$html .= "<infoBulleVue> <a href='javascript:listeDemandes(".$aRow[ $aColumns[$i] ].")' >";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='Liste demandes'></a> </infoBulleVue>";
		
						$row[] = $html;
					}
		
					else if ($aColumns[$i] == 'Date'){
						$row[] = "<div>".$Control->convertDate($aRow[ $aColumns[$i] ]).' - '.$aRow['Time']."</div>";
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
	
	/*Liste des demandes d'analyses sans résultat*/
	/*Liste des demandes d'analyses sans résultat*/
	public function getDemandesAnalysesSansResultat($id){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->from(array('d'=>'demande_analyse'));
		$select->columns(array('*'));
		$select->where(array('idpatient' => $id));
		$select->group('date');
		$select->order('date DESC');
		return $sql->prepareStatementForSqlObject($select)->execute();
	}
	
}