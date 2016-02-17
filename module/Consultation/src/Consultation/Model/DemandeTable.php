<?php
namespace Consultation\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Crypt\PublicKey\Rsa\PublicKey;
use Zend\View\Model\RetrievableChildrenInterface;
class DemandeTable{
	protected $tableGateway;
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function getDemande($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('d'=>'demande'));
		$select->where(array('d.idCons' => $id));
		$select->order('d.idDemande ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();

		return $result;
	}
	
	/**
	 * Recuperer la liste des examens Morphologiques
	 */
	public function getDemandeExamensMorphologiques($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('d'=>'demande'));
		$select->join( array(
				'e' => 'examens'
		), 'd.idExamen = e.idExamen' , array ( '*' ) );
		$select->where(array('d.idCons' => $id, 'idType' => 2));
		$select->order('d.idDemande ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
	
		return $result;
	}
	
	/**
	 * Recuperer la liste des examens Biologiques
	 */
	public function getDemandeExamensBiologiques($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('d'=>'demande'));
		$select->join( array(
				'e' => 'examens'
		), 'd.idExamen = e.idExamen' , array ( '*' ) );
		$select->where(array('d.idCons' => $id, 'idType' => 1));
		$select->order('d.idDemande ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
	
		return $result;
	}
	
	/**
	 * Recuperer la liste des examens Biologiques Effectués et Envoyés par le laborantion (biologiste)
	 */
	public function getDemandeExamensBiologiquesEffectuesEnvoyer($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('d'=>'demande'));
		$select->join( array(
				'e' => 'examens'
		), 'd.idExamen = e.idExamen' , array ( '*' ) );
		$select->join( array(
				'r' => 'resultats_examens'
		), 'd.idDemande = r.idDemande' , array ( '*' ) );
		$select->where(array('d.idCons' => $id, 'idType' => 1, 'appliquer' => 1, 'envoyer' => 1));
		$select->order('d.idDemande ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
	
		return $result;
	}
	
	/**
	 * Recuperer la liste des examens Biologiques Effectués par le laborantion (biologiste)
	 */
	public function getDemandeExamensBiologiquesEffectues($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('d'=>'demande'));
		$select->join( array(
				'e' => 'examens'
		), 'd.idExamen = e.idExamen' , array ( '*' ) );
		$select->join( array(
				'r' => 'resultats_examens'
		), 'd.idDemande = r.idDemande' , array ( '*' ) );
		$select->where(array('d.idCons' => $id, 'idType' => 1, 'appliquer' => 1));
		$select->order('d.idDemande ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
	
		return $result;
	}
	
	/**
	 * Recuperer la liste des examens Morphologiques Effectués et Envoyés par le laborantion (radiologue)
	 */
	public function getDemandeExamensMorphologiquesEffectues($id){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('d'=>'demande'));
		$select->join( array(
				'e' => 'examens'
		), 'd.idExamen = e.idExamen' , array ( '*' ) );
		$select->join( array(
				'r' => 'resultats_examens'
		), 'd.idDemande = r.idDemande' , array ( '*' ) );
		$select->where(array('d.idCons' => $id, 'idType' => 2, 'appliquer' => 1));
		$select->order('d.idDemande ASC');
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute();
	
		return $result;
	}
	
	public function updateDemande($examenDemande, $noteExamen)
	{
		$this->tableGateway->delete(array('idCons' => $examenDemande['id_cons']));

		$today = new \DateTime ();
		$date = $today->format ( 'Y-m-d H:i:s' );
		
		for($i=1; $i<14; $i++){
			if($examenDemande[$i]!= "null"){
				$donneesExamenDemande	 = array(
						'idCons' => $examenDemande['id_cons'],
						'idExamen' => $examenDemande[$i],
						'noteDemande' => $noteExamen[$i],
						'dateDemande' => $date,
				);
				$this->tableGateway->insert($donneesExamenDemande);
			}
		}
	}	
	
	/** RECUPERATION DES RESULTATS POUR L'INTERFACE DU MEDECIN **/
	/** RECUPERATION DES RESULTATS POUR L'INTERFACE DU MEDECIN **/
	/** RECUPERATION DES RESULTATS POUR L'INTERFACE DU MEDECIN **/
	public function resultatExamens($id_cons)
	{
		if($this->getDemande($id_cons)){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->select()
			->from(array('d'=>'demande'))->columns(array('*'))
			->join(array('result' => 'resultats_examens'), 'result.idDemande = d.idDemande', array('*'))
			->join(array('resul_Img' => 'resultats_image'), 'resul_Img.idResultat = result.idResultat' , array('NomImage' => 'nomImage'))
			->where(array('d.idCons' => $id_cons, 'result.envoyer' => 1))
			->order('resul_Img.idImage DESC');
			
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$Result = $stat->execute();
			
			return $Result;
		}
		
	}
	
	/** RECUPERATION DES RESULTATS POUR L'INTERFACE DU RADIOLOGUE (Examen morpho) **/
	/** RECUPERATION DES RESULTATS POUR L'INTERFACE DU RADIOLOGUE (Examen morpho) **/
	/** RECUPERATION DES RESULTATS POUR L'INTERFACE DU RADIOLOGUE (Examen morpho) **/
	public function resultatExamensMorpho($id_cons)
	{
		if($this->getDemande($id_cons)){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->select()
			->from(array('d'=>'demande'))->columns(array('*'))
			->join(array('result' => 'resultats_examens'), 'result.idDemande = d.idDemande', array('*'))
			->join(array('resul_Img' => 'resultats_image'), 'resul_Img.idResultat = result.idResultat' , array('NomImage' => 'nomImage'))
			->where(array('d.idCons' => $id_cons))
			->order('resul_Img.idImage DESC');
				
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$Result = $stat->execute();
				
			return $Result;
		}
	
	}
	
	public function ajouterImageMorpho($id_cons, $idExamen, $nomImage, $dateEnregistrement, $id_personne)
	{
		$demande = $this->verifierDemandeExiste($id_cons, $idExamen);
		if($demande){
			$resultat = $this->verifierResultatExiste($demande['idDemande']);
			if($resultat){
				//INSERTION DU RESULTAT DE LA DEMANDE
				$db = $this->tableGateway->getAdapter();
				$sql = new Sql($db);
				$sQuery = $sql->update('resultats_examens')
				->set(array('date_modifcation' => $dateEnregistrement))
				->where(array('idResultat' =>$resultat['idResultat']));
				$stat = $sql->prepareStatementForSqlObject($sQuery);
				$stat->execute();
				
				//INSERTION DES RESULTATS DES IMAGES
				$db = $this->tableGateway->getAdapter();
				$sql = new Sql($db);
				$sQuery = $sql->insert()
				->into('resultats_image')
				->columns(array('nomImage', 'dateEnregistrement', 'idResultat'))
				->values(array('nomImage' => $nomImage, 'dateEnregistrement'=>$dateEnregistrement, 'idResultat' =>$resultat['idResultat']));
				$stat = $sql->prepareStatementForSqlObject($sQuery);
				$result = $stat->execute();
				return $result;
			}else {
				//C'est ici qu'on met appliquer à 1
				$this->tableGateway->update(array('appliquer' => 1, 'responsable' => 0) , array('idCons' => $id_cons, 'idExamen' => $idExamen));
	
				//INSERTION DU RESULTAT DE LA DEMANDE
				$db = $this->tableGateway->getAdapter();
				$sql = new Sql($db);
				$sQuery = $sql->insert()
				->into('resultats_examens')
				->columns(array('idDemande', 'date_enregistrement', 'id_personne'))
				->values(array('idDemande' => $demande['idDemande'], 'id_personne' => $id_personne,  'date_enregistrement' => $dateEnregistrement));
				$stat = $sql->prepareStatementForSqlObject($sQuery);
				$stat->execute();
	
				//INSERTION DE L'IMAGE DU RESULTAT DE LA DEMANDE
				$resultat = $this->verifierResultatExiste($demande['idDemande']);
				if($resultat){
					$db = $this->tableGateway->getAdapter();
					$sql = new Sql($db);
					$sQuery = $sql->insert()
					->into('resultats_image')
					->columns(array('nomImage', 'dateEnregistrement', 'idResultat'))
					->values(array('nomImage' => $nomImage, 'dateEnregistrement'=>$dateEnregistrement, 'idResultat' =>$resultat['idResultat']));
					$stat = $sql->prepareStatementForSqlObject($sQuery);
					$result = $stat->execute();
					return $result;
				}
			}
		}
		return false;
	}
	
	
	/** FIN FIN RECUPERATION DES RESULTATS POUR L'INTERFACE DU RADIOLOGUE (Examen morpho) **/
	/** FIN FIN RECUPERATION DES RESULTATS POUR L'INTERFACE DU RADIOLOGUE (Examen morpho) **/
	/** FIN FIN RECUPERATION DES RESULTATS POUR L'INTERFACE DU RADIOLOGUE (Examen morpho) **/
	
	/**
	 * Verifier si la demande existe
	 */
	public function verifierDemandeExiste($id_cons, $idExamen)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('d'=>'demande'));
		$select->where(array('d.idCons' => $id_cons, 'idExamen' => $idExamen));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
		return $result;
	}
	/**
	 * Verifier si la demande a deja un resultat 
	 */
	public function verifierResultatExiste($idDemande)
	{
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns(array('*'));
		$select->from(array('re'=>'resultats_examens'));
		$select->where(array('re.idDemande' => $idDemande));
		$stat = $sql->prepareStatementForSqlObject($select);
		$result = $stat->execute()->current();
		return $result;
	}
	
	public function ajouterImage($id_cons, $idExamen, $nomImage, $dateEnregistrement, $id_personne)
	{
		$demande = $this->verifierDemandeExiste($id_cons, $idExamen);
		if($demande){
			$resultat = $this->verifierResultatExiste($demande['idDemande']);
			if($resultat){
				$db = $this->tableGateway->getAdapter();
				$sql = new Sql($db);
				$sQuery = $sql->insert()
				->into('resultats_image')
				->columns(array('nomImage', 'dateEnregistrement', 'idResultat'))
				->values(array('nomImage' => $nomImage, 'dateEnregistrement'=>$dateEnregistrement, 'idResultat' =>$resultat['idResultat']));
				$stat = $sql->prepareStatementForSqlObject($sQuery);
				$result = $stat->execute();
				return $result;
			}else {
				//C'est ici qu'on met appliquer à 1
				$this->tableGateway->update(array('appliquer' => 1, 'responsable' => 1) , array('idCons' => $id_cons, 'idExamen' => $idExamen));
				
				//INSERTION DU RESULTAT DE LA DEMANDE
				$db = $this->tableGateway->getAdapter();
				$sql = new Sql($db);
				$sQuery = $sql->insert()
				->into('resultats_examens')
				->columns(array('idDemande', 'id_personne', 'envoyer'))
				->values(array('idDemande' => $demande['idDemande'], 'id_personne' => $id_personne, 'envoyer' =>1));
				$stat = $sql->prepareStatementForSqlObject($sQuery);
				$stat->execute();
				
				//INSERTION DE L'IMAGE DU RESULTAT DE LA DEMANDE
				$resultat = $this->verifierResultatExiste($demande['idDemande']);
			    if($resultat){
			    	$db = $this->tableGateway->getAdapter();
			    	$sql = new Sql($db);
			    	$sQuery = $sql->insert()
			    	->into('resultats_image')
			    	->columns(array('nomImage', 'dateEnregistrement', 'idResultat'))
			    	->values(array('nomImage' => $nomImage, 'dateEnregistrement'=>$dateEnregistrement, 'idResultat' =>$resultat['idResultat']));
			    	$stat = $sql->prepareStatementForSqlObject($sQuery);
			    	$result = $stat->execute();
			    	return $result;
			    }
			}
		}
		return false;
	}
	
	/**
	 * $id_cons ---
	 * $id ---id de l'image;
	 * $typeExamen ---idExamen;
	 */
	public function recupererDonneesExamen($id_cons, $id, $typeExamen)
	{
		if($this->getDemande($id_cons)->current()){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->select()
			->from(array('d'=>'demande'))->columns(array('*'))
			->join(array('result' => 'resultats_examens'), 'result.idDemande = d.idDemande', array('*'))
			->join(array('resul_Img' => 'resultats_image'), 'resul_Img.idResultat = result.idResultat' , array('IdImage' => 'idImage', 'NomImage' => 'nomImage'))
			->where(array('d.idCons' => $id_cons, 'd.idExamen'=>$typeExamen, 'result.envoyer' => 1))
			->order('resul_Img.idImage DESC');
				
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$Result = $stat->execute();
		    $i = 1;
			foreach ($Result as $resultat){
				$tabIdImage[$i] = $resultat['IdImage'];
				$tabNomImage[$i] = $resultat['NomImage'];
				$i++;
			}
			
			$donnees = array('IdImage' => $tabIdImage[$id], 'NomImage'=> $tabNomImage[$id]);
			return $donnees;
		}
	
	}
	
	
	public function recupererDonneesExamenMorpho($id_cons, $id, $typeExamen)
	{
		if($this->getDemande($id_cons)->current()){
			$db = $this->tableGateway->getAdapter();
			$sql = new Sql($db);
			$sQuery = $sql->select()
			->from(array('d'=>'demande'))->columns(array('*'))
			->join(array('result' => 'resultats_examens'), 'result.idDemande = d.idDemande', array('*'))
			->join(array('resul_Img' => 'resultats_image'), 'resul_Img.idResultat = result.idResultat' , array('IdImage' => 'idImage', 'NomImage' => 'nomImage'))
			->where(array('d.idCons' => $id_cons, 'd.idExamen'=>$typeExamen))
			->order('resul_Img.idImage DESC');
	
			$stat = $sql->prepareStatementForSqlObject($sQuery);
			$Result = $stat->execute();
			$i = 1;
			foreach ($Result as $resultat){
				$tabIdImage[$i] = $resultat['IdImage'];
				$tabNomImage[$i] = $resultat['NomImage'];
				$i++;
			}
				
			$donnees = array('IdImage' => $tabIdImage[$id], 'NomImage'=> $tabNomImage[$id]);
			return $donnees;
		}
	
	}
	
	/**
	 * Supression de l'image
	 */
	public function supprimerImage($idImage)
	{
		$idImage = (int) $idImage;
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete('resultats_image')
		->where(array('idImage' => $idImage));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute();
		return $Result;
	}
	
	/**
	 * Supression toutes les images du resultat avec IdResultat
	 */
	public function supprimerImageAvecIdResultat($idResultat)
	{
		$idResultat = (int) $idResultat;
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete('resultats_image')
		->where(array('idResultat' => $idResultat));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute();
		return $Result;
	}
	
	/**
	 * Supression d'un resultat
	 */
	public function supprimerResultat($idResultat)
	{   
		$idResultat = (int) $idResultat;
		$db = $this->tableGateway->getAdapter();
		$sql = new Sql($db);
		$sQuery = $sql->delete('resultats_examens')
		->where(array('idResultat' => $idResultat));
		$stat = $sql->prepareStatementForSqlObject($sQuery);
		$Result = $stat->execute();
		return $Result;
	}

	/**
	 * 
	 */
	public function getExamen($id_cons, $idExamen){
		$id_cons = (String) $id_cons;
		$rowset = $this->tableGateway->select ( array (
				'idCons' => $id_cons,
				'idExamen' => $idExamen
		) );
		$row =  $rowset->current ();
		if (! $row) {
			return false;
		}
		return $row;
	}
	
	/**
	 * AJouter les demandes d'examens Morphologiques (Ajout ou modification)
	 */
	public function saveDemandesExamensMorphologiques($id_cons, $examens, $notes)
	{
		$today = new \DateTime ();
		$date = $today->format ( 'Y-m-d H:i:s' );
		
		if($this->getDemande($id_cons)->current()){

			//Suppression des examens qui ne sont pas selectionner dernierement
			foreach ($this->getDemande($id_cons) as $listeDemande) {
				if(!in_array($listeDemande['idExamen'], $examens)){
					if($listeDemande['idExamen'] > 7){
						$resultat = $this->verifierResultatExiste($listeDemande['idDemande']);
						if($resultat){
							//On supprime les images
							$this->supprimerImageAvecIdResultat($resultat['idResultat']);
							//On supprime le resultat
							$this->supprimerResultat($resultat['idResultat']);
							//On supprime la demande
							$this->tableGateway->delete(array('idDemande'=>$listeDemande['idDemande']));
						}else {
							$this->tableGateway->delete(array('idDemande'=>$listeDemande['idDemande']));
						}
					}
				}
			}
			
			//modification et insertion de nouveaux examens
			for($i=1 ; $i <= count($examens) ; $i++){
				if($this->getExamen($id_cons, $examens[$i])){
					$this->tableGateway->update(array('noteDemande' => $notes[$i]) , array('idCons' => $id_cons, 'idExamen' => $examens[$i]));
				} else {
					$this->tableGateway->insert(array(
							'idCons' => $id_cons,
							'idExamen'=> $examens[$i],
							'noteDemande'=> $notes[$i],
							'dateDemande' => $date
					));
				}
			}
			
			
		} else {
			for($i=1 ; $i <= count($examens) ; $i++){
				$examen = array(
						'idCons' => $id_cons,
						'idExamen'=> $examens[$i],
						'noteDemande'=> $notes[$i],
						'dateDemande' => $date
				);
				$this->tableGateway->insert($examen);
			}
		}
	}
	
	/**
	 * AJouter les demandes d'examens Biologiques (Ajout ou modification)
	 */
	public function saveDemandesExamensBiologiques($id_cons, $examens, $notes)
	{
		$today = new \DateTime ();
		$date = $today->format ( 'Y-m-d H:i:s' );
	
		if($this->getDemande($id_cons)->current()){
	
			//Suppression des examens qui ne sont pas selectionner dernierement
			foreach ($this->getDemande($id_cons) as $listeDemande) {
				if(!in_array($listeDemande['idExamen'], $examens)){
					if($listeDemande['idExamen'] < 7){
						$resultat = $this->verifierResultatExiste($listeDemande['idDemande']);
						if($resultat){
							//On supprime les images
							$this->supprimerImageAvecIdResultat($resultat['idResultat']);
							//On supprime le resultat
							$this->supprimerResultat($resultat['idResultat']);
							//On supprime la demande
							$this->tableGateway->delete(array('idDemande'=>$listeDemande['idDemande']));
						}else {
							$this->tableGateway->delete(array('idDemande'=>$listeDemande['idDemande']));
						}
					}
				}
			}
				
			//modification et insertion de nouveaux examens
			for($i=1 ; $i <= count($examens) ; $i++){
				if($this->getExamen($id_cons, $examens[$i])){
					$this->tableGateway->update(array('noteDemande' => $notes[$i]) , array('idCons' => $id_cons, 'idExamen' => $examens[$i]));
				} else {
					$this->tableGateway->insert(array(
							'idCons' => $id_cons,
							'idExamen'=> $examens[$i],
							'noteDemande'=> $notes[$i],
							'dateDemande' => $date
					));
				}
			}
				
				
		} else {
			for($i=1 ; $i <= count($examens) ; $i++){
				$examen = array(
						'idCons' => $id_cons,
						'idExamen'=> $examens[$i],
						'noteDemande'=> $notes[$i],
						'dateDemande' => $date
				);
				$this->tableGateway->insert($examen);
			}
		}
	}
	
}
