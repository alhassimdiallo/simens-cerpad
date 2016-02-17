<?php
namespace EspacePatient\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EspacePatientController extends AbstractActionController{
	public function rechercheAction(){
		$this->layout()->setTemplate('layout/espace-patient');
		$view = new ViewModel();
		return $view;
	}
	public function priseRendezVousAction(){
		return new ViewModel();
	}
	public function listeMessagesAction(){
		return new ViewModel();
	}
}