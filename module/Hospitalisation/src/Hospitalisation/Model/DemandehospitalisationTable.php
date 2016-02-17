<?php

namespace Hospitalisation\Model;

use Zend\Db\TableGateway\TableGateway;
use Facturation\View\Helper\DateHelper;
use Zend\Db\Sql\Sql;

class DemandehospitalisationTable {
	protected $tableGateway;
	protected $conversionDate;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function getConversionDate(){
		$this->conversionDate = new DateHelper();
		
		return $this->conversionDate;
	}
	
	public function getDemandehospitalisation($data)
	{
		$rowset = $this->tableGateway->select(array(
				'id_personne' => (int) $data['id_personne'],
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function getDemandehospitalisationWithIdDemandeHospi($idDemandeHospi)
	{
		$rowset = $this->tableGateway->select(array(
				'id_demande_hospi' => (int) $idDemandeHospi,
		));
		$row = $rowset->current();
		if (!$row) {
			$row = null;
		}
		return $row;
	}
	
	public function saveDemandehospitalisation()
	{
		$today = new \DateTime ( 'now' );
		$date = $today->format ( 'Y-m-d' );
	}
	
	public function deleteDemandehospitalisation($id_demande_hospi){
		$id_demande_hospi = (int) $id_demande_hospi;
		$this->tableGateway->delete( array('id_demande_hospi' => $id_demande_hospi));
	}
	
	public function getDemandeHospitalisationWithIdcons($id_cons){
		
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))->columns(array())
		->join(array('pers' => 'personne'), 'pers.ID_PERSONNE = pat.ID_PERSONNE', array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','id'=>'id_personne'))
		->join(array('cons' => 'consultation'), 'cons.ID_PATIENT= pat.ID_PERSONNE', array('Datedemandehospi'=>'date', 'Idcons'=>'id_cons'))
		->join(array('dh' => 'demande_hospitalisation'), 'dh.id_cons = cons.id_cons' , array('*'))
		->join(array('med' => 'personne') , 'med.ID_PERSONNE = cons.id_medecin' , array('NomMedecin' =>'nom', 'PrenomMedecin' => 'prenom'))
		->where(array('cons.id_cons' => $id_cons));
		
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute()->current();
		
		return $Result;
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
	 * Recuperation de la liste des demandes d'hospitalisation
	 */
	public function getListeDemandeHospitalisation()
	{

		$db = $this->tableGateway->getAdapter();
		
		$aColumns = array('Nom','Prenom','Datenaissance','Sexe', 'date_demande_hospi', 'Prenom&NomMedecin' , 'id');
		
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
		->join(array('pers' => 'personne'), 'pat.ID_PERSONNE = pers.ID_PERSONNE', array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','id'=>'ID_PERSONNE'))
		->join(array('cons' => 'consultation'), 'cons.ID_PATIENT = pat.ID_PERSONNE', array('Datedemandehospi'=>'DATE', 'Idcons'=>'ID_CONS'))
		->join(array('dh' => 'demande_hospitalisation'), 'dh.id_cons = cons.ID_CONS' , array('*'))
		->join(array('med' => 'personne') , 'med.ID_PERSONNE = cons.ID_MEDECIN' , array('NomMedecin' =>'NOM', 'PrenomMedecin' => 'PRENOM'))
		->where(array('dh.valider_demande_hospi' => 0 , 'cons.ARCHIVAGE' => 0))
		->order('id_demande_hospi asc');
		
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
						$html  ="<infoBulleVue><a href='javascript:affichervue(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/voir.png' title='détails'></a></infoBulleVue>";
		
						$html  .="<infoBulleVue><a href='javascript:hospitaliser(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline; margin-right: 5%;' src='".$tabURI[0]."public/images_icons/details.png' title='Hospitaliser'></a></infoBulleVue>";
		
						$html .="<input id='".$aRow[ $aColumns[$i] ]."'   type='hidden' value='".$aRow[ 'Idcons' ]."'>";
						$html .="<input id='".$aRow[ $aColumns[$i] ]."dh' type='hidden' value='".$aRow[ 'id_demande_hospi' ]."'>";
						
						$row[] = $html;
					}
						
					else if ($aColumns[$i] == 'Prenom&NomMedecin') {
						$row[] = $aRow[ 'PrenomMedecin' ]." ".$aRow[ 'NomMedecin' ];
					}
					
					else if ($aColumns[$i] == 'date_demande_hospi') {
						$row[] = $Control->convertDateTime($aRow[ 'date_demande_hospi' ]);
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
	 * Recuperation de la liste des patients en cours d'hospitalisation et deja hospitaliser
	 */
	public function getListePatientEncoursHospitalisation()
	{
	
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('Nom','Prenom','Datenaissance','Sexe', 'Datedebut', 'date_fin_prevue_hospi' , 'id');
	
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
		->from(array('pat' => 'patient'))->columns(array('Nom'=>'nom','Prenom'=>'prenom','Datenaissance'=>'date_naissance','Sexe'=>'sexe','Adresse'=>'adresse','id'=>'id_personne'))
		->join(array('cons' => 'consultation'), 'cons.pat_id_personne = pat.id_personne', array('Datedemandehospi'=>'date', 'Idcons'=>'id_cons'))
		->join(array('dh' => 'demande_hospitalisation2'), 'dh.id_cons = cons.id_cons' , array('*'))
		->join(array('med' => 'medecin') , 'med.id_personne = cons.id_personne' , array('NomMedecin' =>'nom', 'PrenomMedecin' => 'prenom'))
		->join(array('h' => 'hospitalisation'), 'h.code_demande_hospitalisation = dh.id_demande_hospi' , array('Datedebut'=>'date_debut', 'Idhosp'=>'id_hosp', 'Terminer'=>'terminer'))
		->where(array('dh.valider_demande_hospi'=>1))
		->order('h.terminer ASC');
	
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
						
						if($aRow[ 'Terminer' ] == 0) {
							$html  ="<infoBulleVue><a href='javascript:affichervue(".$aRow[ 'id_demande_hospi' ].")'>";
							$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir.png' title='détails'></a></infoBulleVue>";
							
							$html  .="<infoBulleVue><a href='javascript:administrerSoin(".$aRow[ 'id_demande_hospi' ].")'>";
							$html .="<img style='display: inline; margin-right: 14%;' src='".$tabURI[0]."public/images_icons/details.png' title='Administrer'></a></infoBulleVue>";
	
							$html  .="<infoBulleVue><a href='javascript:liberer(".$aRow[ 'id_demande_hospi' ].")'>";
							$html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/edit_item_btn.png' title='Libérer'></a></infoBulleVue>";
						
						}else {
							$html  ="<infoBulleVue><a href='javascript:affichervuedetailhospi(".$aRow[ 'id_demande_hospi' ].")'>";
							$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir.png' title='détails'></a></infoBulleVue>";
								
							$html  .="<infoBulleVue><a>";
							$html .="<img style='color: white; opacity: 0.15; margin-right: 14%;' src='".$tabURI[0]."public/images_icons/details.png' ></a></infoBulleVue>";
							
							$html  .="<infoBulleVue><a>";
							$html .="<img style='color: white; opacity: 0.15;' src='".$tabURI[0]."public/images_icons/edit_item_btn.png' ></a></infoBulleVue>";
							
						}
						
						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."'   type='hidden' value='".$aRow[ 'Idcons' ]."'>";
						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."hp' type='hidden' value='".$aRow[ 'Idhosp' ]."'>";
						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."idPers' type='hidden' value='".$aRow[ $aColumns[$i] ]."'>";
						
						$row[] = $html;
					}
	
					else if ($aColumns[$i] == 'date_fin_prevue_hospi') {
						$row[] = $Control->convertDate($aRow[ 'date_fin_prevue_hospi' ]);
					}
						
					else if ($aColumns[$i] == 'Datedebut') {
						$row[] = $Control->convertDateTime($aRow[ 'Datedebut' ]);
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
	
	public function validerDemandeHospitalisation($code_demande){
		$this->tableGateway->update(array('valider_demande_hospi'=>1), array('id_demande_hospi'=>$code_demande));
	}
	
	
	/**
	 * Recuperation de la liste des patients en cours d'hospitalisation pour le suivi des patients
	 */
	public function getListePatientSuiviHospitalisation()
	{
	
		$db = $this->tableGateway->getAdapter();
	
		$aColumns = array('Nom','Prenom','Datenaissance','Sexe', 'Datedebut', 'date_fin_prevue_hospi' , 'id');
	
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
		->join(array('pers' => 'personne'), 'pat.ID_PERSONNE = pers.ID_PERSONNE', array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','id'=>'ID_PERSONNE'))
		->join(array('cons' => 'consultation'), 'cons.ID_PATIENT = pat.ID_PERSONNE', array('Datedemandehospi'=>'DATE', 'Idcons'=>'ID_CONS'))
		->join(array('dh' => 'demande_hospitalisation'), 'dh.id_cons = cons.ID_CONS' , array('*'))
		->join(array('med' => 'personne') , 'med.ID_PERSONNE = cons.ID_MEDECIN' , array('NomMedecin' =>'NOM', 'PrenomMedecin' => 'PRENOM'))
		->join(array('h' => 'hospitalisation'), 'h.code_demande_hospitalisation = dh.id_demande_hospi' , array('Datedebut'=>'date_debut', 'Idhosp'=>'id_hosp', 'Terminer'=>'terminer'))
		->where(array('dh.valider_demande_hospi'=>1 , 'cons.ARCHIVAGE'=>0))
		->where(array('h.terminer' => 0));
	
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
	
						$html  ="<infoBulleVue><a href='javascript:affichervue(".$aRow[ 'id_demande_hospi' ].")'>";
						$html .="<img style='display: inline; margin-right: 10%;' src='".$tabURI[0]."public/images_icons/voir2.png' title='détails'></a></infoBulleVue>";
								
						$html  .="<infoBulleVue><a href='javascript:administrerSoin(".$aRow[ 'id_demande_hospi' ].")'>";
						$html .="<img style='display: inline; margin-right: 0%;' src='".$tabURI[0]."public/img/dark/blu-ray.png' title='Appliquer un soin'></a></infoBulleVue>";

						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."'   type='hidden' value='".$aRow[ 'Idcons' ]."'>";
						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."hp' type='hidden' value='".$aRow[ 'Idhosp' ]."'>";
						$html .="<input id='".$aRow[ 'id_demande_hospi' ]."idPers' type='hidden' value='".$aRow[ $aColumns[$i] ]."'>";
	
						$row[] = $html;
					}
	
					else if ($aColumns[$i] == 'date_fin_prevue_hospi') {
						$row[] = $Control->convertDate($aRow[ 'date_fin_prevue_hospi' ]);
					}
	
					else if ($aColumns[$i] == 'Datedebut') {
						$row[] = $Control->convertDateTime($aRow[ 'Datedebut' ]);
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
	
}