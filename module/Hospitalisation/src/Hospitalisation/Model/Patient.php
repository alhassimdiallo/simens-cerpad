<?php

namespace Hospitalisation\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Patient implements InputFilterAwareInterface {
	public $id_personne;
	public $civilite;
	public $nom;
	public $prenom;
	public $date_naissance;
	public $lieu_naissance;
	public $adresse;
	public $sexe;
	public $nationalite_actuelle;
	public $nationalite_origine;
	public $telephone;
	public $email;
	public $profession;
	public $taille;
	public $poids;
	public $groupe_sanguin;
	public $photo;
	public $date_enregistrement;
	protected $inputFilter;
	public function exchangeArray($data) {
		$this->id_personne = (! empty ( $data ['ID_PERSONNE'] )) ? $data ['ID_PERSONNE'] : null;
		$this->civilite = (! empty ( $data ['CIVILITE'] )) ? $data ['CIVILITE'] : null;
		$this->lieu_naissance = (! empty ( $data ['LIEU_NAISSANCE'] )) ? $data ['LIEU_NAISSANCE'] : null;
		$this->nom = (! empty ( $data ['NOM'] )) ? $data ['NOM'] : null;
		$this->prenom = (! empty ( $data ['PRENOM'] )) ? $data ['PRENOM'] : null;
		$this->date_naissance = (! empty ( $data ['DATE_NAISSANCE'] )) ? $data ['DATE_NAISSANCE'] : null;
		$this->adresse = (! empty ( $data ['ADRESSE'] )) ? $data ['ADRESSE'] : null;
		$this->sexe = (! empty ( $data ['SEXE'] )) ? $data ['SEXE'] : null;
		$this->nationalite_actuelle = (! empty ( $data ['NATIONALITE_ACTUELLE'] )) ? $data ['NATIONALITE_ACTUELLE'] : null;
		$this->nationalite_origine = (! empty ( $data ['NATIONALITE_ORIGINE'] )) ? $data ['NATIONALITE_ORIGINE'] : null;
		$this->telephone = (! empty ( $data ['TELEPHONE'] )) ? $data ['TELEPHONE'] : null;
		$this->email = (! empty ( $data ['EMAIL'] )) ? $data ['EMAIL'] : null;
		$this->profession = (! empty ( $data ['PROFESSION'] )) ? $data ['PROFESSION'] : null;
		$this->taille = (! empty ( $data ['TAILLE'] )) ? $data ['TAILLE'] : null;
		$this->poids = (! empty ( $data ['POIDS'] )) ? $data ['POIDS'] : null;
		$this->groupe_sanguin = (! empty ( $data ['GROUPE_SANGUIN'] )) ? $data ['GROUPE_SANGUIN'] : null;
		$this->photo = (! empty ( $data ['PHOTO'] )) ? $data ['PHOTO'] : null;
		$this->date_enregistrement = (! empty ( $data ['date_enregistrement'] )) ? $data ['date_enregistrement'] : null;
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	public function getInputFilter() {
		if (! $this->inputFilter) {
			$inputFilter = new InputFilter ();
			$factory = new InputFactory ();

// 			$inputFilter->add ($factory->createInput( array (
// 					'name' => 'id_personne',
// 					'filters' => array (
// 							array (
// 									'name' => 'Int'
// 							)
// 					)
// 			 ) ));

// 			$inputFilter->add (array (
// 					'name' => 'nom',
// 					'required' => true,
// 					'filters' => array (
// 							array (
// 									'name' => 'StripTags'
// 							),
// 							array (
// 									'name' => 'StringTrim'
// 							)
// 					),
// 					'validators' => array (
// 							array (
// 									'name' => 'StringLength',
// 									'options' => array (
// 											'encoding' => 'UTF-8',
// 											'min' => 1,
// 											'max' => 100
// 									)
// 							)
// 					)
// 			 ) );

// 			$inputFilter->add (array (
// 					'name' => 'prenom',
// 					'required' => true,
// 					'filters' => array (
// 							array (
// 									'name' => 'StripTags'
// 							),
// 							array (
// 									'name' => 'StringTrim'
// 							)
// 					),
// 					'validators' => array (
// 							array (
// 									'name' => 'StringLength',
// 									'options' => array (
// 											'encoding' => 'UTF-8',
// 											'min' => 1,
// 											'max' => 100
// 									)
// 							)
// 					)
// 			 ) );

			$this->inputFilter = $inputFilter;
		}

		return $this->inputFilter;
	}
}