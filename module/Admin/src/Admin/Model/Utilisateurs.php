<?php
namespace Admin\Model;

class Utilisateurs
{
	public $id;
	public $username;
	public $password;
	public $nom;
	public $prenom;
	public $role;
	public $id_service;
	public $fonction;
	public $id_personne;
	

	public function exchangeArray($data)
	{
		$this->id     = (!empty($data['id'])) ? $data['id'] : null;
		$this->username = (!empty($data['username'])) ? $data['username'] : null;
		$this->password  = (!empty($data['password'])) ? $data['password'] : null;
		$this->nom  = (!empty($data['nom'])) ? $data['nom'] : null;
		$this->prenom  = (!empty($data['prenom'])) ? $data['prenom'] : null;
		$this->role  = (!empty($data['role'])) ? $data['role'] : null;
		$this->id_service  = (!empty($data['id_service'])) ? $data['id_service'] : null;
		$this->fonction  = (!empty($data['fonction'])) ? $data['fonction'] : null;
		$this->id_personne  = (!empty($data['id_personne'])) ? $data['id_personne'] : null;
	}
}