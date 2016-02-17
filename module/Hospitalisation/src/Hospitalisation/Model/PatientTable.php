<?php

namespace Hospitalisation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\NotIn;
use Facturation\View\Helper\DateHelper;

class PatientTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	public function getPatient($id_personne) {
		$id_personne = ( int ) $id_personne;
		$rowset = $this->tableGateway->select ( array (
				'ID_PERSONNE' => $id_personne
		) );
		$row =  $rowset->current ();
		if (! $row) {
			return null;
		}
		return $row;
	}
	public function savePatient(Patient $patient) {
		$control = new DateHelper();
		$data = array (
				'civilite' => $patient->civilite,
				'prenom' => $patient->prenom,
				'nom' => $patient->nom,
				'date_naissance' => $control->convertDateInAnglais($patient->date_naissance),
				'lieu_naissance' => $patient->lieu_naissance,
				'adresse' => $patient->adresse,
				'sexe' => $patient->sexe,
				'nationalite_actuelle' => $patient->nationalite_actuelle,
				'nationalite_origine' => $patient->nationalite_origine,
				'telephone' => $patient->telephone,
				'email' => $patient->email,
				'profession' => $patient->profession,
				'photo' => $patient->photo,
				'date_enregistrement' => $patient->date_enregistrement,

		);

		$id = ( int ) $patient->id_personne;
		if ($id == 0) {
			$this->tableGateway->insert ( $data );
		} else {
			if ($this->getPatient ( $id )) {
				$this->tableGateway->update ( $data, array (
						'ID_PERSONNE' => $id
				) );
			} else {
				throw new \Exception ( 'Patient id does not exist' );
			}
		}
	}
	
	public function addPatient($donnees){
		$this->tableGateway->insert ( $donnees ); 
	}
	
	public function addPatientNe($donnees){
 		return($this->tableGateway->getLastInsertValue($this->tableGateway->insert ( $donnees )));
	}
	
	public  function updatePatient($donnees, $id_personne){
		$this->tableGateway->update( $donnees, array('ID_PERSONNE' => $id_personne) );
	}
	
	public function deletePatient($id) {
		$this->tableGateway->delete ( array (
				'ID_PERSONNE' => $id
		) );
	}
	function quoteInto($text, $value, $platform, $count = null)
	{
		if ($count === null) {
			return str_replace('?', $platform->quoteValue($value), $text);
		} else {
			while ($count > 0) {
				if (strpos($text, '?') !== false) {
					$text = substr_replace($text, $platform->quoteValue($value), strpos($text, '?'), 1);
				}
				--$count;
			}
			return $text;
		}
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
	public function getListePatient(){

		$db = $this->tableGateway->getAdapter();

		$aColumns = array('Nom','Prenom','Datenaissance','Sexe', 'Adresse', 'Nationalite', 'id');

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
		->from(array('pat' => 'patient'))->columns(array('Nom'=>'NOM','Prenom'=>'PRENOM','Datenaissance'=>'DATE_NAISSANCE','Sexe'=>'SEXE','Adresse'=>'ADRESSE','Nationalite'=>'NATIONALITE_ACTUELLE','Taille'=>'TAILLE','id'=>'ID_PERSONNE'));
	
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
						$html ="<a href='".$tabURI[0]."public/facturation/info-patient/id_patient/".$aRow[ $aColumns[$i] ]."'>";
						$html .="<img style='display: inline; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/vue.PNG' title='d&eacute;tails'></a>";

						$html .= "<a href='".$tabURI[0]."public/facturation/modifier/id_patient/".$aRow[ $aColumns[$i] ]."'>";
						$html .="<img style='display: inline; margin-right: 15%;' src='".$tabURI[0]."public/images_icons/modifier.PNG' title='Modifier'></a>";

						$html .= "<a id='".$aRow[ $aColumns[$i] ]."' href='javascript:envoyer(".$aRow[ $aColumns[$i] ].")'>";
						$html .="<img style='display: inline;' src='".$tabURI[0]."public/images_icons/trash_16.PNG' title='Supprimer'></a>";

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
	
	public function tousPatientsAdmis($service) {
		//var_dump($service);exit();
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select1 = $sql->select ();
		$select1->from ( array (
				'p' => 'patient'
		) );
		$select1->columns(array (
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		) );
		$select1->join(array('f' => 'facturation'), 'p.ID_PERSONNE = f.id_patient', array('Id_facturation' => 'id_facturation'));
		$select1->join(array('s' => 'service'), 'f.id_service = s.id_service', array('Nomservice' => 'NOM'));
		$select1->where(array('date_cons' => $date, 's.NOM' => $service));
		$select1->order('id_facturation ASC');
		$statement1 = $sql->prepareStatementForSqlObject ( $select1 );
		$result1 = $statement1->execute ();
		
		//var_dump($result1);exit();
		$select2 = $sql->select ('consultation');
		$select2->columns(array('Id' => 'PAT_ID_PERSONNE',
				'Id_cons' => 'ID_CONS',
				'Date_cons' => 'DATEONLY',));
		$select2->where(array('DATEONLY' => $date));
		$statement2 = $sql->prepareStatementForSqlObject ( $select2 );
		$result2 = $statement2->execute ();
		$tab = array($result1,$result2);
		return $tab;
	}
	public function listePatients() {
		$adapter = $this->tableGateway->getAdapter ();
		$sql1 = new Sql ( $adapter );
		$subselect = $sql1->select ();
		$subselect->from ( array (
				'd' => 'deces'
		) );
		$subselect->columns ( array (
				'id_personne'
		) );
		$sql2 = new Sql ( $adapter );
		$rowset = $sql2->select ();
		$rowset->from(array (
				'p' => 'patient'
		) );
		$rowset->columns( array (
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		) );
		$rowset->where(array (
				'ID_PERSONNE > 800',
				'SEXE =\'FEMININ\'',
				new NotIn ( 'ID_PERSONNE', $subselect )
		) );
		$rowset->order( 'ID_PERSONNE ASC' );
		$statement = $sql2->prepareStatementForSqlObject ( $rowset );
		$result = $statement->execute ();
		return $result;
	}
	
	// LISTE DES PATIENTS SAUF LES PATIENTS DECEDES
	public function laListePatients() {
		$date = new \DateTime ("now");
		$formatDate = $date->format ( 'Y-m-d' );
		$adapter = $this->tableGateway->getAdapter ();
		$sql1 = new Sql ($adapter );
		$subselect1 = $sql1->select ();
		$subselect1->from ( array (
				'd' => 'deces'
		) );
		$subselect1->columns (array (
				'id_personne'
		) );
		$sql2 = new Sql ($adapter);
		$subselect2 = $sql2->select ();
		$subselect2->from ('facturation');
		$subselect2->columns ( array (
				'id_patient'
		) );
		$subselect2->where ( array (
				'date_cons' => $formatDate
		) );
		$sql3 = new Sql($adapter);
		$rowset = $sql3->select ();
		$rowset->from ( array (
				'p' => 'patient'
		) );
		$rowset->columns(array (
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		) );
		$rowset->where( array (
				'ID_PERSONNE > 800',
				new NotIn ( 'ID_PERSONNE', $subselect1 ),
				new NotIn ( 'ID_PERSONNE', $subselect2 )
		) );
		$rowset->order('ID_PERSONNE ASC');
		//$req = $sql3->getSqlStringForSqlObject($rowset);
		//var_dump($req); exit();
		$statement = $sql3->prepareStatementForSqlObject($rowset);
		$result = $statement->execute();
		return $result;
	}
	//Modification des donnees du bebe
	public function updatePatientBebe($data)
	{
		$donnees = array(
				'PRENOM' => $data['prenom'],
				'NOM' => $data['nom'],
				'DATE_NAISSANCE' => $data['date_naissance'],
				'LIEU_NAISSANCE' => $data['lieu_naissance'],
				'SEXE' =>$data['sexe'],
				'CIVILITE' =>$data['civilite'],
		);
		$this->tableGateway->update($donnees, array('ID_PERSONNE'=> $data['id_bebe']));
	}

	//Tous les patients qui ont pour ID_PESONNE > 900
	public function tousPatients(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array (
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		) );
		$select->where( array (
				'ID_PERSONNE > 900'
		) );
		$select->order('ID_PERSONNE DESC');

		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	}

	//le nombre de patients qui ont pour ID_PESONNE > 900
	public function nbPatientSUP900(){
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ('patient');
		$select->columns(array ('ID_PERSONNE'));
		$select->where( array (
				'ID_PERSONNE > 900'
		) );
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result->count();
	}
	public function listePays()
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('nation'=>'nationalite'));
		$select->columns(array ('PAYS', 'PAYS'));
		$select->order('PAYS ASC');
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		foreach ($result as $data) {
			$options[$data['PAYS']] = $data['PAYS'];
		}
		return $options;
	}
	
	public function listeServices()
	{
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from(array('serv'=>'service'));
		$select->columns(array ('ID_SERVICE', 'NOM'));
		$select->order('ID_SERVICE ASC');
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		foreach ($result as $data) {
			$options[$data['ID_SERVICE']] = $data['NOM'];
		}
		return $options;
	}
	
	public function listeHopitaux()
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select('hopital');
		$select->order('ID_HOPITAL ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
		foreach ($result as $data) {
			$options[$data['ID_HOPITAL']] = $data['NOM_HOPITAL'];
		}
		return $options;
	}
	
	//Tous les patients consultes sauf ceux du jour
	public function tousPatientsCons($service){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array (
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		) );
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.PAT_ID_PERSONNE', array('Id_cons' => 'ID_CONS', 'Dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE'));
		$select->join(array('s' => 'service'), 'c.ID_SERVICE = s.ID_SERVICE', array('Nomservice' => 'NOM'));
		$where = new Where();
		$where->equalTo('s.NOM', $service);
		$where->notEqualTo('DATEONLY', $date);
		$select->where($where);
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;

	}
	//liste des patients à consulter par le medecin dans ce service
	public function listePatientsConsParMedecin($idDuService){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array (
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		) );
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.PAT_ID_PERSONNE', array('Id_cons' => 'ID_CONS', 'dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE', 'date' => 'DATE'));
		$where = new Where();
		$where->equalTo('c.ID_SERVICE', $idDuService);
		$where->equalTo('DATEONLY', $date);
		$select->where($where);
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;
	}
	//liste des patients consultés par le medecin pour l'espace recherche
	public function listePatientsConsMedecin($service){
		$today = new \DateTime();
		$date = $today->format('Y-m-d');
		$adapter = $this->tableGateway->getAdapter ();
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( array (
				'p' => 'patient'
		) );
		$select->columns(array (
				'Nom' => 'NOM',
				'Prenom' => 'PRENOM',
				'Datenaissance' => 'DATE_NAISSANCE',
				'Sexe' => 'SEXE',
				'Adresse' => 'ADRESSE',
				'Nationalite' => 'NATIONALITE_ACTUELLE',
				'Taille' => 'TAILLE',
				'Id' => 'ID_PERSONNE'
		) );
		$select->join(array('c' => 'consultation'), 'p.ID_PERSONNE = c.PAT_ID_PERSONNE', array('Id_cons' => 'ID_CONS', 'Dateonly' => 'DATEONLY', 'Consprise' => 'CONSPRISE', 'date' => 'DATE'));
		$select->join(array('s' => 'service'), 'c.ID_SERVICE = s.ID_SERVICE', array('Nomservice' => 'NOM'));
		$where = new Where();
		$where->equalTo('s.NOM', $service);
		$where->notEqualTo('DATEONLY', $date);
		$select->where($where);
		$stmt = $sql->prepareStatementForSqlObject($select);
		$result = $stmt->execute();
		return $result;

	}
	
	public function getInfoPatient($id_personne) {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('pat' => 'patient'))
		->columns( array( '*' ))
		->join(array('pers' => 'personne'), 'pers.id_personne = pat.id_personne' , array('*'))
		->where(array('pat.ID_PERSONNE' => $id_personne));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$resultat = $stat->execute()->current();
	
		return $resultat;
	}
	
	public function getPhoto($id) {
		$donneesPatient =  $this->getInfoPatient( $id );
	
		$nom = null;
		if($donneesPatient){$nom = $donneesPatient['PHOTO'];}
		if ($nom) {
			return $nom . '.jpg';
		} else {
			return 'identite.jpg';
		}
	}
	
	
	public function getInfoMedecin($id_medecin) {
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->select()
		->from(array('e' => 'employe'))
		->columns( array( '*' ))
		->join(array('pers' => 'personne'), 'pers.id_personne = e.id_personne' , array('*'))
		->where(array('pers.id_personne' => $id_medecin));
	
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$resultat = $stat->execute()->current();
	
		return $resultat;
	}
}