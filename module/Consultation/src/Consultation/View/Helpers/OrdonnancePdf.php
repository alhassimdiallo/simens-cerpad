<?php
namespace Consultation\View\Helpers;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;
use Consultation\Model\Consultation;
use Facturation\View\Helper\DateHelper; 


class OrdonnancePdf
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
	protected $_id_cons;
	protected $_date;
	protected $_note;
	protected $_idPersonne;
	protected $_Medicaments;
	protected $_DonneesPatient;
	
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
		$this->getNoteMedicaments();
		$this->getPiedPage();
		
		$this->_page->restoreGS();
	}
	
	public function setEnTete(){
		$imageHeader = ZendPdf\Image::imageWithPath('C:\wamp\www\simens\public\img\logo_vert.png');
		$this->_page->drawImage($imageHeader, 425,
				$this->_pageHeight - 75,
				565,//largeur
				787);//hauteur
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('République du Sénégal',
				$this->_leftMargin,
				$this->_pageHeight - 50);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Ministère de la santé et de la prévention',
				$this->_leftMargin,
				$this->_pageHeight - 65);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Hopital Régional de Saint-Louis',
				$this->_leftMargin,
				$this->_pageHeight - 80);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Service Orthopédie et traumathologie',
				$this->_leftMargin,
				$this->_pageHeight - 95);
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE);
		$this->_page->setFont($font, 8);
		$today = new \DateTime ();
		$dateNow = $today->format ( 'd/m/Y' );
		$this->_page->drawText('Saint-Louis le ' . $dateNow,
				450,
				$this->_pageHeight - 50);
	}
	
	public function setDonneesPatient($donneesPatient){
		$this->_DonneesPatient = $donneesPatient;
	}
	
	public function setMedicaments($tab){
		$this->_Medicaments = $tab;
	}
	
	public function setIdCons($id_cons){
		$this->_id_cons = $id_cons;
	}
	
	public function getNewItalique(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTime(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
	}
	
	protected function nbAnnees($debut, $fin) {
		//60 secondes X 60 minutes X 24 heures dans une journee
		$nbSecondes = 60*60*24*365;
	
		$debut_ts = strtotime($debut);
		$fin_ts = strtotime($fin);
		$diff = $fin_ts - $debut_ts;
		return round($diff / $nbSecondes);
	}
	
	protected  function getNoteMedicaments(){
		$Control = new DateHelper();
		
		$this->_yPosition -= 35;
		$this->_page->setFont($this->_newTime, 15);
		$this->_page->setFillColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawText('ORDONNANCE',
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
			$this->_page->drawText('NOM :',
					$this->_leftMargin+175,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 9);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value['NOM']),
					$this->_leftMargin+210,
					$this->_yPosition);
			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
		    //----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('PRENOM :',
					$this->_leftMargin+156,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 9);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value['PRENOM']),
					$this->_leftMargin+210,
					$this->_yPosition);
// 			//-----------------------------------------------
    		$this->_yPosition -= 15;// allez a ligne suivante
// 			//----- -----------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('DATE DE NAISSANCE :',
					$this->_leftMargin+102,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 9);
			
			$today = new \DateTime(); 
			$date_actu = $today->format('Y-m-d');
			$dateNaissance = $Control->convertDate($value['DATE_NAISSANCE']);
			
			$this->_page->drawText($dateNaissance."  (".$this->nbAnnees($value['DATE_NAISSANCE'],$date_actu)." ans)",
					$this->_leftMargin+210,
					$this->_yPosition);
 			//-----------------------------------------------
			//-----------------------------------------------
			$this->_yPosition -= 15;// allez a ligne suivante
			//----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('ADRESSE :',
					$this->_leftMargin+155,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 9);
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
		
		$taille = count($this->_Medicaments);
		
		while($cpt <14) {
			
			while($d < $taille){
				$this->getNewItalique();
				$this->_page->drawText($l++.')',
						$this->_leftMargin,
						$this->_yPosition);
				$this->getNewTime();
				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1',$this->_Medicaments[$d++]),
						$this->_leftMargin+20,
						$this->_yPosition);
				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1',$this->_Medicaments[$d++]),
						$this->_leftMargin+180,
						$this->_yPosition);
				$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1',$this->_Medicaments[$d++]).' '.iconv ( 'UTF-8', 'ISO-8859-1',$this->_Medicaments[$d++]),
						$this->_leftMargin+350,
						$this->_yPosition);
			
				$this->_page->drawLine($this->_leftMargin,
						$this->_yPosition,
						$this->_pageWidth -
						$this->_leftMargin,
						$this->_yPosition);
				$this->_yPosition -= $noteLineHeight-10;
				$cpt++;
				$this->_page->drawLine($this->_leftMargin +20,
						$this->_yPosition ,
						$this->_pageWidth -
						$this->_leftMargin -320,
						$this->_yPosition );
				$this->_yPosition -= $noteLineHeight+5;
				$cpt++;
			} 
		
			$cpt++;
		
		$this->_page->drawLine($this->_leftMargin,
				$this->_yPosition,
				$this->_pageWidth -
				$this->_leftMargin,
				$this->_yPosition);
		$this->_yPosition -= $noteLineHeight;
		}
		//Ã©
		$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Cachet et Signature'),
				$this->_leftMargin+350,
				$this->_yPosition);
		
		
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
		$this->_page->drawText('Consultation numéro: ',
				$this->_leftMargin + 310,
				$this->_pageWidth - ( 100 + 390));
		$this->_page->setFont($this->_newTimeGras, 11);
		$this->_page->drawText($this->_id_cons,
				$this->_leftMargin + 405,
				$this->_pageWidth - ( 100 + 390));
	}
	
}