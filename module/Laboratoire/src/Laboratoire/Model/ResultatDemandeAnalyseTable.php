<?php

namespace Laboratoire\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Predicate\In;

class ResultatDemandeAnalyseTable {
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
	
	public function getResultatDemandeAnalyse($iddemande) {
	    $rowset = $this->tableGateway->select ( array ( 'iddemande_analyse' => $iddemande ) );
	    $row =  $rowset->current ();
	    if (! $row) { $row = null; }
	    return $row;
	}
	
	public function addResultatDemandeAnalyse($iddemande, $idemploye) {

	    if(! $this->getResultatDemandeAnalyse($iddemande)){
	        $data = array(
	            'iddemande_analyse' => $iddemande,
	            'date' => (new \DateTime() ) ->format('Y-m-d H:i:s'),
	            'idemploye' => $idemploye,
	        );
	        
	        $this->tableGateway->insert($data);
	    }
	    
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	//****************************************************************************************************
	//****************************************************************************************************
	/**
	 * Indiquer que le résultat de la demande est effectué
	 */
	public function setResultDemandeEffectuee($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->update() ->table('demande_analyse') ->set( array('result' => 1) )
	    ->where(array('iddemande' => $iddemande ));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	/**
	 * Indiquer que le résultat de la demande n'est pas effectué
	 */
	public function setResultDemandeNonEffectuee($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->update() ->table('demande_analyse') ->set( array('result' => 0) )
	    ->where(array('iddemande' => $iddemande ));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	
	public function getValeursNfs($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('vs' => 'valeurs_nfs'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	     
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursNfs($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donnees = array();
	    $donneesExiste = 0;
	    
	    //Si les resultats n y sont pas on les ajoute 
	    if(!$this->getValeursNfs($iddemande)){
	        $donnees['idresultat_demande_analyse'] = $iddemande;
	        
	        for($i = 1 ; $i < count($tab)-1 ; $i++){
	            if($tab[$i]){ $donnees['champ'.$i] = $tab[$i]; $donneesExiste = 1; }else{ $donnees['champ'.$i] = null; }
	        }
	        $donnees['type_materiel'] = $tab[$i];
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->insert() ->into('valeurs_nfs') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	    } 
	    //Sinon on effectue des mises a jours
	    else {
	        for($i = 1 ; $i < count($tab)-1 ; $i++){
	            if($tab[$i]){ $donnees['champ'.$i] = $tab[$i]; $donneesExiste = 1; }else{ $donnees['champ'.$i] = null; }
	        }
	        $donnees['type_materiel'] = $tab[$i];
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_nfs') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	    }
	    
	    return $donneesExiste;
	}
	
	public function getValeursGsrhGroupage($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('vg' => 'valeurs_gsrh_groupage'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	     
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursGsrhGroupage($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['groupe'] = $tab[1];
	    $donnees['rhesus'] = $tab[2];
	    
	    if($donnees['groupe'] || $donnees['rhesus']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursGsrhGroupage($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_gsrh_groupage') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_gsrh_groupage') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	    }
	    
	    return $donneesExiste;
	}
	
	public function getValeursRechercheAntigene($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursRechercheAntigene($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    
	    if($donnees['valeur']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursRechercheAntigene($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTestCombsDirect($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTestCombsDirect($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTestCombsDirect($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTestCombsIndirect($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTestCombsIndirect($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTestCombsIndirect($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTestDemmel($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTestDemmel($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	    
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTestDemmel($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTestCompatibilite($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTestCompatibilite($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTestCompatibilite($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursVitesseSedimentation($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursVitesseSedimentation($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursVitesseSedimentation($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTauxReticulocyte($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTauxReticulocyte($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	    
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTauxReticulocyte($iddemande)){
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	        
	    }
	    //Sinon on effectue des mises a jours
	    else {
	        
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }

	    }
	    
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursGoutteEpaisse($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_goutte_epaisse'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursGoutteEpaisse($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	     
	    $donnees = array();
	    $donnees['goutte_epaisse'] = $tab[1];
	    if($tab[2]){ $donnees['densite_parasitaire'] = $tab[2]; } else { $donnees['densite_parasitaire'] = null; }
	     
	    if( $donnees['goutte_epaisse'] ){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursGoutteEpaisse($iddemande)){
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_goutte_epaisse') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	         
	    }
	    //Sinon on effectue des mises a jours
	    else {
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_goutte_epaisse') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	     
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTpInr($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_tp_inr'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTpInr($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    
	    if($tab[1]){ $donnees['temps_quick_temoin']        = $tab[1]; } else { $donnees['temps_quick_temoin']        = null; }
	    if($tab[2]){ $donnees['temps_quick_patient']       = $tab[2]; } else { $donnees['temps_quick_patient']       = null; }
	    if($tab[3]){ $donnees['taux_prothrombine_patient'] = $tab[3]; } else { $donnees['taux_prothrombine_patient'] = null; }
	    if($tab[4]){ $donnees['inr_patient']               = $tab[4]; } else { $donnees['inr_patient']               = null; }
	
	    if( $tab[1] || $tab[2] || $tab[3] || $tab[4] ){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTpInr($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_tp_inr') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_tp_inr') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTca($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('vt' => 'valeurs_tca'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTca($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	     
	    if($tab[1]){ $donnees['tca_patient'] = $tab[1]; } else { $donnees['tca_patient'] = null; }
	    if($tab[2]){ $donnees['temoin_patient']   = $tab[2]; } else { $donnees['temoin_patient']   = null; }
	
	    if( $tab[1] || $tab[2] ){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTca($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_tca') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_tca') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursFibrinemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursFibrinemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	     
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	     
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursFibrinemie($iddemande)){
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	     
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTempsSaignement($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTempsSaignement($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTempsSaignement($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursGlycemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('vt' => 'valeurs_glycemie'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursGlycemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['glycemie_1'] = $tab[1]; } else { $donnees['glycemie_1'] = null; }
	    if($tab[2]){ $donnees['glycemie_2'] = $tab[2]; } else { $donnees['glycemie_2'] = null; }
	
	    if( $tab[1] || $tab[2] ){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursGlycemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_glycemie') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_glycemie') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCreatininemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCreatininemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCreatininemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	

	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursAzotemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursAzotemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursAzotemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursAcideUrique($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursAcideUrique($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursAcideUrique($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCholesterolTotal($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_cholesterol_total'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCholesterolTotal($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    
	    if($tab[1]){ $donnees['cholesterol_total_1'] = $tab[1]; }else{ $donnees['cholesterol_total_1'] = null; }
	    if($tab[2]){ $donnees['cholesterol_total_2'] = $tab[2]; }else{ $donnees['cholesterol_total_2'] = null; }
	
	    if($donnees['cholesterol_total_1'] || $donnees['cholesterol_total_2']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCholesterolTotal($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_cholesterol_total') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_cholesterol_total') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTriglycerides($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_triglycerides'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTriglycerides($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	     
	    if($tab[1]){ $donnees['triglycerides_1'] = $tab[1]; }else{ $donnees['triglycerides_1'] = null; }
	    if($tab[2]){ $donnees['triglycerides_2'] = $tab[2]; }else{ $donnees['triglycerides_2'] = null; }
	
	    if($donnees['triglycerides_1'] || $donnees['triglycerides_2']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTriglycerides($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_triglycerides') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_triglycerides') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCholesterolHDL($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_cholesterol_hdl'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCholesterolHDL($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['cholesterol_HDL_1'] = $tab[1]; }else{ $donnees['cholesterol_HDL_1'] = null; }
	    if($tab[2]){ $donnees['cholesterol_HDL_2'] = $tab[2]; }else{ $donnees['cholesterol_HDL_2'] = null; }
	
	    if($donnees['cholesterol_HDL_1'] || $donnees['cholesterol_HDL_2']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCholesterolHDL($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_cholesterol_hdl') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_cholesterol_hdl') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCholesterolLDL($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_cholesterol_ldl'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCholesterolLDL($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['cholesterol_LDL_1'] = $tab[1]; }else{ $donnees['cholesterol_LDL_1'] = null; }
	    if($tab[2]){ $donnees['cholesterol_LDL_2'] = $tab[2]; }else{ $donnees['cholesterol_LDL_2'] = null; }
	
	    if($donnees['cholesterol_LDL_1'] || $donnees['cholesterol_LDL_2']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCholesterolLDL($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_cholesterol_ldl') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_cholesterol_ldl') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function addValeurs_Total_HDL_LDL_Triglycerides($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	    $cmpt = 0;
	
	    $donneesTotal = array();
	    $donneesHDL = array();
	    $donneesLDL = array();
	    $donneesTrigly = array();
	
	    if($tab[1]){ $donneesTotal['cholesterol_total_1'] = $tab[1]; }else{ $donneesTotal['cholesterol_total_1'] = null; $cmpt++;}
	    if($tab[2]){ $donneesTotal['cholesterol_total_2'] = $tab[2]; }else{ $donneesTotal['cholesterol_total_2'] = null; $cmpt++;}
	    
 	    if($tab[3]){ $donneesHDL['cholesterol_HDL_1'] = $tab[3]; }else{ $donneesHDL['cholesterol_HDL_1'] = null; $cmpt++;}
 	    if($tab[4]){ $donneesHDL['cholesterol_HDL_2'] = $tab[4]; }else{ $donneesHDL['cholesterol_HDL_2'] = null; $cmpt++;}
	    
 	    if($tab[5]){ $donneesLDL['cholesterol_LDL_1'] = $tab[5]; }else{ $donneesLDL['cholesterol_LDL_1'] = null; $cmpt++;}
 	    if($tab[6]){ $donneesLDL['cholesterol_LDL_2'] = $tab[6]; }else{ $donneesLDL['cholesterol_LDL_2'] = null; $cmpt++;}
	    
 	    if($tab[7]){ $donneesTrigly['triglycerides_1'] = $tab[7]; }else{ $donneesTrigly['triglycerides_1'] = null; $cmpt++;}
 	    if($tab[8]){ $donneesTrigly['triglycerides_2'] = $tab[8]; }else{ $donneesTrigly['triglycerides_2'] = null; $cmpt++;}
	
	    if($cmpt != 8){
	        $donneesExiste = 1; 
	        
	        /*** CHOLESTEROL TOTAL ----- CHOLESTEROL TOTAL ----- CHOLESTEROL TOTAL ***/
	        /*** CHOLESTEROL TOTAL ----- CHOLESTEROL TOTAL ----- CHOLESTEROL TOTAL ***/
	        if(!$this->getValeursCholesterolTotal($iddemande)){
	            if( $tab[1] || $tab[2] ){
	                $donneesTotal['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_cholesterol_total') ->values( $donneesTotal );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }
	        }else{
	            if( $tab[1] || $tab[2] ){
	                $sQuery = $sql->update() ->table('valeurs_cholesterol_total') ->set( $donneesTotal )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }else 
	                if( !$tab[1] && !$tab[2] ){
	                    $sQuery = $sql->delete() ->from('valeurs_cholesterol_total')
	                    ->where(array('idresultat_demande_analyse' => $iddemande));
	                    $sql->prepareStatementForSqlObject($sQuery)->execute();
	                    $this->setResultDemandeNonEffectuee($iddemande);
	                }
	        }
	    
	        /*** CHOLESTEROL HDL ----- CHOLESTEROL HDL ----- CHOLESTEROL HDL ***/
	        /*** CHOLESTEROL HDL ----- CHOLESTEROL HDL ----- CHOLESTEROL HDL ***/
	        if(!$this->getValeursCholesterolHDL($iddemande)){
	            if( $tab[3] || $tab[4] ){
	                $donneesHDL['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_cholesterol_hdl') ->values( $donneesHDL );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }
	        }else{
	            if( $tab[3] || $tab[4] ){
	                $sQuery = $sql->update() ->table('valeurs_cholesterol_hdl') ->set( $donneesHDL )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }else
	                if( !$tab[3] && !$tab[4] ){
	                    $sQuery = $sql->delete() ->from('valeurs_cholesterol_hdl')
	                    ->where(array('idresultat_demande_analyse' => $iddemande));
	                    $sql->prepareStatementForSqlObject($sQuery)->execute();
	                    $this->setResultDemandeNonEffectuee($iddemande);
	            }
	        }
	        
	        /*** CHOLESTEROL LDL ----- CHOLESTEROL LDL ----- CHOLESTEROL LDL ***/
	        /*** CHOLESTEROL LDL ----- CHOLESTEROL LDL ----- CHOLESTEROL LDL ***/
	        if(!$this->getValeursCholesterolLDL($iddemande)){
	            if( $tab[5] || $tab[6] ){
	                $donneesLDL['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_cholesterol_ldl') ->values( $donneesLDL );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }
	        }else{
	            if( $tab[5] || $tab[6] ){
	                $sQuery = $sql->update() ->table('valeurs_cholesterol_ldl') ->set( $donneesLDL )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }else
	                if( !$tab[5] && !$tab[6] ){
	                    $sQuery = $sql->delete() ->from('valeurs_cholesterol_ldl')
	                    ->where(array('idresultat_demande_analyse' => $iddemande));
	                    $sql->prepareStatementForSqlObject($sQuery)->execute();
	                    $this->setResultDemandeNonEffectuee($iddemande);
	            }
	        }
	        
	        /*** TRIGLYCERIDES ----- TRIGLYCERIDES ----- TRIGLYCERIDES ***/
	        /*** TRIGLYCERIDES ----- TRIGLYCERIDES ----- TRIGLYCERIDES ***/
	        if(!$this->getValeursTriglycerides($iddemande)){
	            if( $tab[7] || $tab[8] ){
	                $donneesTrigly['idresultat_demande_analyse'] = $iddemande;
	                $sQuery = $sql->insert() ->into('valeurs_triglycerides') ->values( $donneesTrigly );
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }
	        }else{
	            if( $tab[7] || $tab[8] ){
	                $sQuery = $sql->update() ->table('valeurs_triglycerides') ->set( $donneesTrigly )
	                ->where(array('idresultat_demande_analyse' => $iddemande ));
	                $sql->prepareStatementForSqlObject($sQuery)->execute();
	                $this->setResultDemandeEffectuee($iddemande);
	            }else
	                if( !$tab[7] && !$tab[8] ){
	                    $sQuery = $sql->delete() ->from('valeurs_triglycerides')
	                    ->where(array('idresultat_demande_analyse' => $iddemande));
	                    $sql->prepareStatementForSqlObject($sQuery)->execute();
	                    $this->setResultDemandeNonEffectuee($iddemande);
	            }
	        }
	    
	        
	        
	    }else{
	        $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) ); 
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursIonogramme($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_ionogramme'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursIonogramme($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	
	    if($tab[1]){ $donnees['sodium_sanguin']    = $tab[1]; }else{ $donnees['sodium_sanguin']    = null; }
	    if($tab[2]){ $donnees['potassium_sanguin'] = $tab[2]; }else{ $donnees['potassium_sanguin'] = null; }
	    if($tab[3]){ $donnees['chlore_sanguin']    = $tab[3]; }else{ $donnees['chlore_sanguin']    = null; }
	
	    if($donnees['sodium_sanguin'] || $donnees['potassium_sanguin'] || $donnees['chlore_sanguin']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursIonogramme($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_ionogramme') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_ionogramme') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursCalcemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursCalcemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	     
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	     
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursCalcemie($iddemande)){
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	     
	    return $donneesExiste;
	}
	
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursMagnesemie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursMagnesemie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursMagnesemie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursPhosphoremie($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursPhosphoremie($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursPhosphoremie($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTgoAsat($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTgoAsat($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTgoAsat($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursTgpAlat($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTgpAlat($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTgpAlat($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursPhosphatageAlcaline($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursPhosphatageAlcaline($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursPhosphatageAlcaline($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursGamaGtYgt($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_autre'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursGamaGtYgt($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    $donnees['valeur'] = $tab[1];
	
	    if($donnees['valeur']){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursGamaGtYgt($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_autre') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_autre') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	public function getValeursFerSerique($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_fer_serique'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursFerSerique($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $donneesExiste = 0;
	
	    $donnees = array();
	    
	    if($tab[1]){ $donnees['valeur_ug'] = $tab[1]; }else{ $donnees['valeur_ug'] = null; }
	    if($tab[2]){ $donnees['valeur_umol'] = $tab[2]; }else{ $donnees['valeur_umol'] = null; }
	
	    if($tab[1] || $tab[2]){ $donneesExiste = 1; }
	
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursFerSerique($iddemande)){
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_fer_serique') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_fer_serique') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	        }
	
	    }
	
	    return $donneesExiste;
	}
	
	//****************************************************************************************************
	//****************************************************************************************************
	
	public function addTypagePatientDepister($idpatient, $typage, $typepatient){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    
	    $sQuery = $sql->update() ->table('depistage') ->set( array('typage' => $typage, 'typepatient' => $typepatient ) ) 
	    ->where(array('idpatient' => $idpatient));
	    $sql->prepareStatementForSqlObject($sQuery)->execute();
	}
	
	public function getValeursTypageHemoglobine($iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    $sQuery = $sql->select()
	    ->from(array('va' => 'valeurs_typage_hemoglobine'))->columns(array('*'))
	    ->where(array('idresultat_demande_analyse' => $iddemande));
	
	    return $sql->prepareStatementForSqlObject($sQuery)->execute()->current();
	}
	
	public function addValeursTypageHemoglobine($tab, $iddemande){
	    $db = $this->tableGateway->getAdapter();
	    $sql = new Sql($db);
	    
	    $typesPathologiques = array('SS', 'SC', 'SB');
	    $typepatient = 0;
	    
	    $donneesExiste = 0;
	     
	    $donnees = array();
	    
	    if($tab[1]){ $donnees['type_materiel'] = $tab[1]; }else{ $donnees['type_materiel'] = null; }
	    if($tab[2]){ $donnees['valeur'] = $tab[2]; }else{ $donnees['valeur'] = null; }
	    
	    if($tab[2]){
	        $donneesExiste = 1; 
	        if(in_array($tab[2], $typesPathologiques)){ $typepatient = 1; }
	    }
	     
	    //Si les resultats n y sont pas on les ajoute
	    if(!$this->getValeursTypageHemoglobine($iddemande)){
	        $demande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	            
	            //Ajout des résultats dans la table dépistage lorsqu'il s'agit d'un patient dépisté
	            $this->addTypagePatientDepister($demande['idpatient'], null, $typepatient);
	        }else{
	            $donnees['idresultat_demande_analyse'] = $iddemande;
	            $sQuery = $sql->insert() ->into('valeurs_typage_hemoglobine') ->values( $donnees );
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	            
	            //Ajout des résultats dans la table dépistage lorsqu'il s'agit d'un patient dépisté
	            $this->addTypagePatientDepister($demande['idpatient'], $tab[2], $typepatient);
	        }
	
	    }
	    //Sinon on effectue des mises a jours
	    else {
	        $demande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	         
	        if($donneesExiste == 0){
	            $this->tableGateway->delete ( array ( 'iddemande_analyse' => $iddemande ) );
	            $this->setResultDemandeNonEffectuee($iddemande);
	            
	            //Ajout des résultats dans la table dépistage lorsqu'il s'agit d'un patient dépisté
	            $this->addTypagePatientDepister($demande['idpatient'], null, $typepatient);
	            
	        }else{
	            $sQuery = $sql->update() ->table('valeurs_typage_hemoglobine') ->set( $donnees )
	            ->where(array('idresultat_demande_analyse' => $iddemande ));
	            $sql->prepareStatementForSqlObject($sQuery)->execute();
	            $this->setResultDemandeEffectuee($iddemande);
	            
	            //Ajout des résultats dans la table dépistage lorsqu'il s'agit d'un patient dépisté
	            $this->addTypagePatientDepister($demande['idpatient'], $tab[2], $typepatient);
	        }
	
	    }
	     
	    return $donneesExiste;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//GESTION DES IMPRESSIONS DES RESULTATS DES ANALYSES
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
	public function getListeResultatsAnalysesDemandees($iddemande){
	    $dateDemande = $this->getDemandeAnalysesAvecIddemande($iddemande);
	
	    $adapter = $this->tableGateway->getAdapter ();
	    $sql = new Sql($adapter);
	    $select = $sql->select();
	    $select->from(array('d'=>'demande_analyse'));
	    $select->columns(array('*'));
	    $select->join(array('a'=>'analyse') ,'d.idanalyse = a.idanalyse', array('Designation'=>'designation', 'Tarif'=>'tarif'));
	    $select->join(array('t'=>'type_analyse') ,'t.idtype = a.idtype_analyse', array('Libelle'=>'libelle'));
	    $select->join(array('r'=>'resultat_demande_analyse'), 'd.iddemande = r.iddemande_analyse', array('DateEnregistrementResultat'=>'date'));
	    $select->join(array('p'=>'personne'), 'p.idpersonne = r.idemploye', array('Nom'=>'nom', 'Prenom'=>'prenom'));
	    $select->where(array('d.date' => $dateDemande['date'], 'd.idpatient' => $dateDemande['idpatient']));
	    $select->order(array('idanalyse' => 'ASC', 'idtype' =>'ASC'));
	    $result = $sql->prepareStatementForSqlObject($select)->execute();
	    
	    $resultats = array();
	    foreach ($result as $res){
	        $resultats [] = $res;
	    }
	    
	    return $resultats;
	}
	
}