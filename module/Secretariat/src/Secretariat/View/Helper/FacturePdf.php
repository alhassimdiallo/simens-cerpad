<?php
namespace Facturation\View\Helper;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Facturation\View\Helper\DateHelper; 


class FacturePdf
{
	protected $_page;
	protected $_yPosition;
	protected $_leftMargin;
	protected $_pageWidth;
	protected $_pageHeight;
	protected $_normalFont;
	protected $_boldFont;
	protected $_newTime;
	protected $_newTimeGras;
	protected $_year;
	protected $_headTitle;
	protected $_introText;
	protected $_graphData;
	protected $_patient;
	protected $_date;
	protected $_note;
	protected $_idPersonne;
	protected $_Service;
	protected $_DonneesPatient;
	protected $_infos;
	
	
	public function __construct()
	{
		$this->_page = new Page(Page::SIZE_A4 );
		
 		$this->_yPosition = 750;
 		$this->_leftMargin = 50;
 		$this->_pageHeight = $this->_page->getHeight();
 		$this->_pageWidth = $this->_page->getWidth();
 		/**
 		 * Pas encore utilisé
 		 */
 		$this->_normalFont = Font::fontWithName( ZendPdf\Font::FONT_HELVETICA);
 		$this->_boldFont = Font::fontWithName( ZendPdf\Font::FONT_HELVETICA_BOLD);
 		/**
 		 ***************** 
 		 */
 		$this->_newTime = Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
 		$this->_newTimeGras = Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
	}
	
	public function getPage(){
		return $this->_page;
	}
	
	public function addNote(){
		$this->_page->saveGS();
		
		$this->setEnTete();
		$this->getNoteInformations();
		$this->getPiedPage();
		
		$this->_page->restoreGS();
	}
	
	public function setEnTete(){
		$imageHeader = ZendPdf\Image::imageWithPath('C:\wamp\www\simens\public\img\polycliniquelogo.png');
		$this->_page->drawImage($imageHeader, 445, //-x
				$this->_pageHeight - 130, //-y
				528, //+x
				787); //+y
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('République du Sénégal',
				$this->_leftMargin,
				$this->_pageHeight - 50);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Ministère de la santé et de l\'action sociale',
				$this->_leftMargin,
				$this->_pageHeight - 65);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Polyclinique de l\'UGB de Saint-Louis',
				$this->_leftMargin,
				$this->_pageHeight - 80);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Service: '.$this->_Service,
				$this->_leftMargin,
				$this->_pageHeight - 95);
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 8);
		$today = new \DateTime ();
		$dateNow = $today->format ( 'd/m/Y' );
		$this->_page->drawText('Saint-Louis le, ' . $dateNow,
				450,
				$this->_pageHeight - 50);
	}
	
	public function setDonneesPatient($donneesPatient){
		$this->_DonneesPatient = $donneesPatient;
	}
	
	public function setService($service){
		$this->_Service = $service;
	}
	
	public function setInformations($infos){
		$this->_infos = $infos;
	}
	
	public function getNewItalique(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTime(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 13);
	}
	
	public function getNewTimeBold(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
		$this->_page->setFont($font, 16);
	}
	
	public function getStyle(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC);
		$this->_page->setFont($font, 14);
	}
	
	protected function nbAnnees($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24*365;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return round($diff / $nbSecondes);
	}
	
	protected function scinderText($Text){
		$tab = array();
		$tab[1] = substr($Text, 0, 65).'.';
		$tab[2] = substr($Text, 65, 60);
		$tab[3] = substr($Text, 110, 60);
		return $tab;
	}
	
	protected  function getNoteInformations(){
		$Control = new DateHelper();
		
		$this->_yPosition -= 35;
		$this->_page->setFont($this->_newTime, 15);
		$this->_page->setFillColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawText('FACTURE',
				$this->_leftMargin+200,
				$this->_yPosition);
		$this->_yPosition -= 5;
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawLine($this->_leftMargin,
				$this->_yPosition,
				$this->_pageWidth -
				$this->_leftMargin,
				$this->_yPosition);
		$noteLineHeight = 30;
		$this->_yPosition -= 15;
		
		$this->_page->setFillColor(new ZendPdf\Color\Html('black')); //Pour le text
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); //Pour les ligne
		//$this->_page->setLineWidth(5);
		//$this->_page->setLineDashingPattern(array(1, 2));

		$l = 1;
		$i = 0;
		$d = 1;
		$cpt = 0;
		
		//-----------------------------------------------
		$value = $this->_DonneesPatient;
			//-----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('PRENOM & NOM :',
					$this->_leftMargin+123,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value['PRENOM'].'  '.$value['NOM']),
					$this->_leftMargin+210,
					$this->_yPosition);
			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
			//----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('SEXE :',
					$this->_leftMargin+173,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value['SEXE']),
					$this->_leftMargin+210,
					$this->_yPosition);
 			//-----------------------------------------------
    		$this->_yPosition -= 15;// allez a ligne suivante
 			//----- -----------------------------------------
			$date_naissance = $value['DATE_NAISSANCE'];
			if($date_naissance){ $date_naissance = $Control->convertDate($date_naissance); } else {$date_naissance = null; }
				
			if($date_naissance){
				$this->_page->setFont($this->_newTimeGras, 9);
				$this->_page->drawText('DATE DE NAISSANCE :',
						$this->_leftMargin+102,
						$this->_yPosition);
				$this->_page->setFont($this->_newTime, 10);
			
				$this->_page->drawText($date_naissance."  (".$value['AGE']." ans)",
						$this->_leftMargin+210,
						$this->_yPosition);
			}else {
				$this->_page->setFont($this->_newTimeGras, 9);
				$this->_page->drawText('AGE :',
						$this->_leftMargin+176,
						$this->_yPosition);
				$this->_page->setFont($this->_newTime, 10);
			
				$this->_page->drawText($value['AGE']." ans",
						$this->_leftMargin+210,
						$this->_yPosition);
			}
			
			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
			//----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('ADRESSE :',
					$this->_leftMargin+155,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value['ADRESSE']),
					$this->_leftMargin+210,
					$this->_yPosition);
			//-----------------------------------------------
			
			$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
			$this->_page->setLineWidth(0.2);
			$this->_page->setLineDashingPattern(array(0, 0));
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition-10,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition-10);

			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); //Pour les ligne
			$this->_page->setLineWidth(0.2);
			$this->_page->setLineDashingPattern(array(1, 2));
			
		//-----------------------------------------------
		$this->_yPosition -= $noteLineHeight+12;//aller a la ligne suivante
		
		
		//-----------------------------------------------
		//-----------------------------------------------
		$this->getStyle();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "NumÃ©ro : "),
				$this->_leftMargin+5,
				$this->_yPosition);
		
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_infos['numero']),
				$this->_leftMargin+80,
				$this->_yPosition);

		$this->_page->drawLine($this->_leftMargin,
				$this->_yPosition,
				$this->_pageWidth -
				$this->_leftMargin,
				$this->_yPosition);
		$this->_yPosition -= $noteLineHeight;
		
		//-----------------------------------------------
		//-----------------------------------------------
		$this->getStyle();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Service d'admission : "),
				$this->_leftMargin+5,
				$this->_yPosition);
		
		$this->getNewTime();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_infos['service']),
				$this->_leftMargin+140,
				$this->_yPosition);
		
		$this->_page->drawLine($this->_leftMargin,
				$this->_yPosition,
				$this->_pageWidth -
				$this->_leftMargin,
				$this->_yPosition);
		$this->_yPosition -= $noteLineHeight;
		
		//-----------------------------------------------
		//-----------------------------------------------
		$this->getStyle();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Tarif de la consultation : "),
				$this->_leftMargin+5,
				$this->_yPosition);
		
		$this->getNewTimeBold();
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_infos['montant']),
				$this->_leftMargin+160,
				$this->_yPosition);
		$this->getNewTime();
		$this->_page->drawText('  francs',
				$this->_leftMargin+200,
				$this->_yPosition);
		
		$this->_page->drawLine($this->_leftMargin,
				$this->_yPosition,
				$this->_pageWidth -
				$this->_leftMargin,
				$this->_yPosition);
		$this->_yPosition -= $noteLineHeight;
		
		//-----------------------------------------------
		//-----------------------------------------------
		
		if($this->_infos['type_facturation'] == 2){
			
			$this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff')); //Pour les ligne
			$this->_page->setLineWidth(0.1);
			$this->_page->setLineDashingPattern(array(0, 0));
			
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			$this->_yPosition -= $noteLineHeight;
			
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); //Pour les ligne
			$this->_page->setLineWidth(0.2);
			$this->_page->setLineDashingPattern(array(1, 2));
			//-----------------------------------------------
			//-----------------------------------------------
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Prise en charge par : "),
					$this->_leftMargin+5,
					$this->_yPosition);
			
			$this->getNewTime();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->scinderText($this->_infos['organisme'])[1]),
					$this->_leftMargin+160,
					$this->_yPosition);
			
			
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			$this->_yPosition -= $noteLineHeight;
			//-----------------------------------------------
			//-----------------------------------------------
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Taux de la majoration : "),
					$this->_leftMargin+5,
					$this->_yPosition);
				
			$this->getNewTime();
			
			if($this->_infos['taux']){
				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_infos['taux'].' %'),
						$this->_leftMargin+160,
						$this->_yPosition);
			}else {
				$this->_page->drawText( '00 %',
						$this->_leftMargin+160,
						$this->_yPosition);
			}

				
				
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			$this->_yPosition -= $noteLineHeight;
			
			//-----------------------------------------------
			//-----------------------------------------------
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "valeur de la majoration : "),
					$this->_leftMargin+5,
					$this->_yPosition);
			
			$this->getNewTime();
			$this->_page->drawText( ($this->_infos['montant']/100) * $this->_infos['taux'] .' francs',
					$this->_leftMargin+160,
					$this->_yPosition);
			
			
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			$this->_yPosition -= $noteLineHeight;
			
			//-----------------------------------------------
			//-----------------------------------------------
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Tarif avec majoration : "),
					$this->_leftMargin+5,
					$this->_yPosition);
			
			$this->getNewTimeBold();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $this->_infos['montant_avec_majoration']),
					$this->_leftMargin+160,
					$this->_yPosition);
			$this->getNewTime();
			$this->_page->drawText('  francs',
					$this->_leftMargin+200,
					$this->_yPosition);
			
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition);
			$this->_yPosition -= $noteLineHeight;
		}
		
		//-----------------------------------------------
		//-----------------------------------------------
	} 
	
	public function getPiedPage(){
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->setLineWidth(1.5);
		$this->_page->setLineDashingPattern(array(0, 0));
		$this->_page->drawLine($this->_leftMargin,
				120,
				$this->_pageWidth -
				$this->_leftMargin,
				120);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Téléphone: 33 726 25 36   BP: 24000',
				$this->_leftMargin,
				$this->_pageWidth - ( 100 + 390));
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('SIMENS+: ',
				$this->_leftMargin + 355,
				$this->_pageWidth - ( 100 + 390));
		$this->_page->setFont($this->_newTimeGras, 11);
		$this->_page->drawText('www.simens.sn',
				$this->_leftMargin + 405,
				$this->_pageWidth - ( 100 + 390));
	}
	
}