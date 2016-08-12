<?php
namespace Secretariat\View\Helper;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;

class DemandeAnalysePdf
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
	protected $_analyses;
	protected $_typesAnalyses;
	protected $_tarifs;
	protected $_depistage;
	
	
	public function __construct()
	{
		$this->_page = new Page(Page::SIZE_A4 );
		
 		$this->_yPosition = 750;
 		$this->_leftMargin = 50;
 		$this->_pageHeight = $this->_page->getHeight();
 		$this->_pageWidth = $this->_page->getWidth();
 		/**
 		 * Pas encore utilis�
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
		$this->_page->drawText('R�publique du S�n�gal',
				$this->_leftMargin,
				$this->_pageHeight - 50);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Minist�re de la sant� et de l\'action sociale',
				$this->_leftMargin,
				$this->_pageHeight - 65);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('CERPAD de l\'UGB de Saint-Louis',
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
	
	public function setDepistage($depistage){
	    $this->_depistage = $depistage;
	}
	
	public function setDonneesPatient($donneesPatient){
		$this->_DonneesPatient = $donneesPatient;
	}
	
	public function setService($service){
		$this->_Service = $service;
	}
	
	public function setAnalyses($analyses){
		$this->_analyses = $analyses;
	}
	
	public function setTypesAnalyses($typesAnalyses){
		$this->_typesAnalyses = $typesAnalyses;
	}
	
	public function setTarifs($tarifs){
		$this->_tarifs = $tarifs;
	}
	
	public function getNewItalique(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTime(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 10);
	}
	
	public function getNewTime2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN);
		$this->_page->setFont($font, 12);
	}
	
	public function getNewTimeBold(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
		$this->_page->setFont($font, 16);
	}
	
	public function getNewTimeBold2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD);
		$this->_page->setFont($font, 14);
	}
	
	public function getStyle(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC);
		$this->_page->setFont($font, 14);
	}
	
	public function getStyle2(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_ITALIC);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle3(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle4(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle5(){
		$font = ZendPdf\Font::fontWithName(ZendPdf\Font::FONT_HELVETICA);
		$this->_page->setFont($font, 12);
	}
	
	public function getStyle6(){
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
	
	protected function scinderText($Text){
		$tab = array();
		$tab[1] = substr($Text, 0, 65).'.';
		$tab[2] = substr($Text, 65, 60);
		$tab[3] = substr($Text, 110, 60);
		return $tab;
	}
	
	public function prixMill($prix) {
		$str="";
		$long =strlen($prix)-1;
	
		for($i = $long ; $i>=0; $i--)
		{
		$j=$long -$i;
		if( ($j%3 == 0) && $j!=0)
		{ $str= " ".$str;   }
		$p= $prix[$i];
	
		$str = $p.$str;
		}
	
		if(!$str){ $str = $prix; }
	
		return($str);
	}
	
		
	protected  function getNoteInformations(){
		$Control = new DateHelper();
		
		$this->_yPosition -= 35;
		$this->_page->setFont($this->_newTime, 15);
		$this->_page->setFillColor(new ZendPdf\Color\Html('green'));
		$this->_page->drawText('DEMANDE D\'ANALYSES',
				$this->_leftMargin+150,
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
		
		$this->_page->setFillColor(new ZendPdf\Color\Html('black')); 
		
		$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); 

		$l = 1;
		$i = 0;
		$d = 1;
		$cpt = 0;
		
		//-----------------------------------------------
		$value = $this->_DonneesPatient;
	    //-----------------------------------------------
		$typepatient = "E";
		if($this->_depistage->current()){
		    if($this->_depistage->current()['typepatient'] == 1){ $typepatient = "I"; }
		}

		//-----------------------------------------------
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ('UTF-8' , 'ISO-8859-1' , 'N°: '.$value->idpersonne.'-'.$typepatient),
		    $this->_leftMargin,
		    $this->_yPosition);
		
		
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('PRENOM & NOM :',
					$this->_leftMargin+123,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value->prenom.'  '.$value->nom),
					$this->_leftMargin+210,
					$this->_yPosition);
			//-----------------------------------------------
			$this->_yPosition -= 15;
			//----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('SEXE :',
					$this->_leftMargin+173,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value->sexe),
					$this->_leftMargin+210,
					$this->_yPosition);
 			//-----------------------------------------------
    		$this->_yPosition -= 15;
 			//----- -----------------------------------------
			
			$date_naissance = $value->date_naissance;
			if($date_naissance){ $date_naissance = $Control->convertDate($date_naissance); } else {$date_naissance = null; }
			
			if($date_naissance){
				$this->_page->setFont($this->_newTimeGras, 9);
				$this->_page->drawText('DATE DE NAISSANCE :',
						$this->_leftMargin+102,
						$this->_yPosition);
				$this->_page->setFont($this->_newTime, 10);
				
				$this->_page->drawText($date_naissance."  (".$value->age." ans)",
						$this->_leftMargin+210,
						$this->_yPosition);
			}else {
				$this->_page->setFont($this->_newTimeGras, 9);
				$this->_page->drawText('AGE :',
						$this->_leftMargin+176,
						$this->_yPosition);
				$this->_page->setFont($this->_newTime, 10);
				
				$this->_page->drawText($value->age." ans",
						$this->_leftMargin+210,
						$this->_yPosition);
			}

			//-----------------------------------------------
			$this->_yPosition -= 15;
			//----------------------------------------------
			$this->_page->setFont($this->_newTimeGras, 9);
			$this->_page->drawText('ADRESSE :',
					$this->_leftMargin+155,
					$this->_yPosition);
			$this->_page->setFont($this->_newTime, 10);
			$this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value->adresse),
					$this->_leftMargin+210,
					$this->_yPosition);
			//-----------------------------------------------
			
			$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
			$this->_page->setLineWidth(0.2);
			//$this->_page->setLineDashingPattern(array(0, 0));
			$this->_page->drawLine($this->_leftMargin,
					$this->_yPosition-10,
					$this->_pageWidth -
					$this->_leftMargin,
					$this->_yPosition-10);

			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999')); 
			$this->_page->setLineWidth(0.2);
			//$this->_page->setLineDashingPattern(array(1, 2));
			
		//-----------------------------------------------
		$this->_yPosition -= $noteLineHeight+5;//aller a la ligne suivante
		
		//-----------------------------------------------
		//-----------------------------------------------
		$analyses = $this->_analyses;
		$typesAnalyses = $this->_typesAnalyses;
		$tarifs = $this->_tarifs;
		
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		if(in_array('HEMATOLOGIE', $typesAnalyses)){
			
			$this->_page->setLineColor(new ZendPdf\Color\Html('#cfcfcf'));
			$this->_page->setLineWidth(20);
			$this->_page->drawLine($this->_leftMargin, //180
					$this->_yPosition +5,
					$this->_pageWidth -
					$this->_leftMargin, //220
					$this->_yPosition +5);
			
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "HEMATOLOGIE"),
					$this->_leftMargin+180,
					$this->_yPosition);
			
			
			//-----------------------------------------------
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
			
			$this->getStyle6();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "DESIGNATION "),
					$this->_leftMargin+15,
					$this->_yPosition);
			
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin+15,
					$this->_yPosition -5,
					$this->_pageWidth -
					$this->_leftMargin-400,
					$this->_yPosition -5);
			
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TARIF (frs)"),
					$this->_leftMargin+325,
					$this->_yPosition);
			
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin+325,
					$this->_yPosition -5,
					$this->_pageWidth -
					$this->_leftMargin-115,
					$this->_yPosition -5);
			
			
			$this->_yPosition -= $noteLineHeight;
			
			$j = 0;
			
			for ($i = 1 ; $i < count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'HEMATOLOGIE'){

					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0.5);
						
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
			
					$this->getNewTime();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.') '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
			
					$this->getNewTime2();
					$this->_page->drawText($tarifs[$i],
							$this->_leftMargin+330,
							$this->_yPosition);
			
					$this->_yPosition -= -10;
					$this->_yPosition -= $noteLineHeight;
				}
			
			}
		}
		
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		
 		
 		
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-------- BIOCHIMIE --------- BIOCHIMIE ---------- BIOCHIMIE------
 		//-------- BIOCHIMIE --------- BIOCHIMIE ---------- BIOCHIMIE------
 		//-------- BIOCHIMIE --------- BIOCHIMIE ---------- BIOCHIMIE------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
		if(in_array('BIOCHIMIE', $typesAnalyses)){
			$this->_yPosition -= -5;
			$this->_yPosition -= $noteLineHeight;
				
			$this->_page->setLineColor(new ZendPdf\Color\Html('#cfcfcf'));
			$this->_page->setLineWidth(20);
			$this->_page->drawLine($this->_leftMargin, //180
					$this->_yPosition +5,
					$this->_pageWidth -
					$this->_leftMargin, //220
					$this->_yPosition +5);
				
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "BIOCHIMIE" ),
					$this->_leftMargin+190,
					$this->_yPosition);
				
				
			//-----------------------------------------------
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
				
			$this->getStyle6();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "DESIGNATION "),
					$this->_leftMargin+15,
					$this->_yPosition);
				
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin+15,
					$this->_yPosition -5,
					$this->_pageWidth -
					$this->_leftMargin-400,
					$this->_yPosition -5);
				
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TARIF (frs)"),
					$this->_leftMargin+325,
					$this->_yPosition);
				
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin+325,
					$this->_yPosition -5,
					$this->_pageWidth -
					$this->_leftMargin-115,
					$this->_yPosition -5);
				
				
			$this->_yPosition -= $noteLineHeight;
				
			$j = 0;
			//-----------------------------------------------
			//-----------------------------------------------
			for ($i = 1 ; $i < count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'BIOCHIMIE'){
			
					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0.5);
						
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
						
					$this->getNewTime();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.') '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
						
					$this->getNewTime2();
					$this->_page->drawText($tarifs[$i],
							$this->_leftMargin+330,
							$this->_yPosition);
						
					$this->_yPosition -= -10;
					$this->_yPosition -= $noteLineHeight;
				}
					
			}
		}
 		
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		
 		
 		//-----------------------------------------------------------------------------
 		//-----------------------------------------------------------------------------
 		//-------- PARASITOLOGIE --------- PARASITOLOGIE ---------- PARASITOLOGIE------
 		//-------- PARASITOLOGIE --------- PARASITOLOGIE ---------- PARASITOLOGIE------
 		//-------- PARASITOLOGIE --------- PARASITOLOGIE ---------- PARASITOLOGIE------
 		//-----------------------------------------------------------------------------
 		//-----------------------------------------------------------------------------
		if(in_array('PARASITOLOGIE', $typesAnalyses)){
			$this->_yPosition -= -5;
			$this->_yPosition -= $noteLineHeight;
			
			$this->_page->setLineColor(new ZendPdf\Color\Html('#cfcfcf'));
			$this->_page->setLineWidth(20);
			$this->_page->drawLine($this->_leftMargin, //180
					$this->_yPosition +5,
					$this->_pageWidth -
					$this->_leftMargin, //220
					$this->_yPosition +5);
			
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "PARASITOLOGIE"),
					$this->_leftMargin+180,
					$this->_yPosition);
			
			
			//-----------------------------------------------
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
			
			$this->getStyle6();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "DESIGNATION "),
					$this->_leftMargin+15,
					$this->_yPosition);
			
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin+15,
					$this->_yPosition -5,
					$this->_pageWidth -
					$this->_leftMargin-400,
					$this->_yPosition -5);
			
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TARIF (frs)"),
					$this->_leftMargin+325,
					$this->_yPosition);
			
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin+325,
					$this->_yPosition -5,
					$this->_pageWidth -
					$this->_leftMargin-115,
					$this->_yPosition -5);
			
			
			$this->_yPosition -= $noteLineHeight;
			
			$j = 0;
			//-----------------------------------------------
			//-----------------------------------------------
			for ($i = 1 ; $i < count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'PARASITOLOGIE'){
						
					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0.5);
						
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
			
					$this->getNewTime();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.') '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
			
					$this->getNewTime2();
					$this->_page->drawText($tarifs[$i],
							$this->_leftMargin+330,
							$this->_yPosition);
			
					$this->_yPosition -= -10;
					$this->_yPosition -= $noteLineHeight;
				}
					
			}
		}
 		
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		
 		
 		
 		//-----------------------------------------------------------------------------
 		//-----------------------------------------------------------------------------
 		//-------- BACTERIOLOGIE --------- BACTERIOLOGIE ---------- BACTERIOLOGIE------
 		//-------- BACTERIOLOGIE --------- BACTERIOLOGIE ---------- BACTERIOLOGIE------
 		//-------- BACTERIOLOGIE --------- BACTERIOLOGIE ---------- BACTERIOLOGIE------
 		//-----------------------------------------------------------------------------
 		//-----------------------------------------------------------------------------
		if(in_array('BACTERIOLOGIE', $typesAnalyses)){
			$this->_yPosition -= -5;
			$this->_yPosition -= $noteLineHeight;
				
			$this->_page->setLineColor(new ZendPdf\Color\Html('#cfcfcf'));
			$this->_page->setLineWidth(20);
			$this->_page->drawLine($this->_leftMargin, //180
					$this->_yPosition +5,
					$this->_pageWidth -
					$this->_leftMargin, //220
					$this->_yPosition +5);
				
			$this->getStyle();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "BACTERIOLOGIE"),
					$this->_leftMargin+180,
					$this->_yPosition);
				
				
			//-----------------------------------------------
			$this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
				
			$this->getStyle6();
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "DESIGNATION "),
					$this->_leftMargin+15,
					$this->_yPosition);
				
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin+15,
					$this->_yPosition -5,
					$this->_pageWidth -
					$this->_leftMargin-400,
					$this->_yPosition -5);
				
			$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TARIF (frs)"),
					$this->_leftMargin+325,
					$this->_yPosition);
				
			$this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
			$this->_page->setLineWidth(1);
			$this->_page->drawLine($this->_leftMargin+325,
					$this->_yPosition -5,
					$this->_pageWidth -
					$this->_leftMargin-115,
					$this->_yPosition -5);
				
				
			$this->_yPosition -= $noteLineHeight;
				
			$j = 0;
			//-----------------------------------------------
			//-----------------------------------------------
			for ($i = 1 ; $i < count($analyses) ; $i++){
					
				if( $typesAnalyses[$i] == 'BACTERIOLOGIE'){
			
					$this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
					$this->_page->setLineWidth(0.5);
						
					$this->_page->drawLine($this->_leftMargin,
							$this->_yPosition -2,
							$this->_pageWidth -
							$this->_leftMargin,
							$this->_yPosition -2);
						
					$this->getNewTime();
					$this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.') '.$analyses[$i]),
							$this->_leftMargin+10,
							$this->_yPosition);
						
					$this->getNewTime2();
					$this->_page->drawText($tarifs[$i],
							$this->_leftMargin+330,
							$this->_yPosition);
						
					$this->_yPosition -= -10;
					$this->_yPosition -= $noteLineHeight;
				}
					
			}
		}
 		
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		//---------------------------------------------------------------------------
 		
		
		//-----------------------------------------------------------------------------
		//-----------------------------------------------------------------------------
		//------------ DEPISTAGE ------------ DEPISTAGE ------------- DEPISTAGE--------
		//------------ DEPISTAGE ------------ DEPISTAGE ------------- DEPISTAGE--------
		//------------ DEPISTAGE ------------ DEPISTAGE ------------- DEPISTAGE--------
		//-----------------------------------------------------------------------------
		//-----------------------------------------------------------------------------
		if(in_array('DEPISTAGE', $typesAnalyses)){
		    $this->_yPosition -= -5;
		    $this->_yPosition -= $noteLineHeight;
		
		    $this->_page->setLineColor(new ZendPdf\Color\Html('#cfcfcf'));
		    $this->_page->setLineWidth(20);
		    $this->_page->drawLine($this->_leftMargin, //180
		        $this->_yPosition +5,
		        $this->_pageWidth -
		        $this->_leftMargin, //220
		        $this->_yPosition +5);
		
		    $this->getStyle();
		    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "DEPISTAGE"),
		        $this->_leftMargin+180,
		        $this->_yPosition);
		
		
		    //-----------------------------------------------
		    $this->_yPosition -= $noteLineHeight;//aller a la ligne suivante
		
		    $this->getStyle6();
		    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "DESIGNATION "),
		        $this->_leftMargin+15,
		        $this->_yPosition);
		
		    $this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		    $this->_page->setLineWidth(1);
		    $this->_page->drawLine($this->_leftMargin+15,
		        $this->_yPosition -5,
		        $this->_pageWidth -
		        $this->_leftMargin-400,
		        $this->_yPosition -5);
		
		    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "TARIF (frs)"),
		        $this->_leftMargin+325,
		        $this->_yPosition);
		
		    $this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
		    $this->_page->setLineWidth(1);
		    $this->_page->drawLine($this->_leftMargin+325,
		        $this->_yPosition -5,
		        $this->_pageWidth -
		        $this->_leftMargin-115,
		        $this->_yPosition -5);
		
		
		    $this->_yPosition -= $noteLineHeight;
		
		    $j = 0;
		    //-----------------------------------------------
		    //-----------------------------------------------
		    for ($i = 1 ; $i < count($analyses) ; $i++){
		        	
		        if( $typesAnalyses[$i] == 'DEPISTAGE'){
		            	
		            $this->_page->setLineColor(new ZendPdf\Color\Html('#efefef'));
		            $this->_page->setLineWidth(0.5);
		
		            $this->_page->drawLine($this->_leftMargin,
		                $this->_yPosition -2,
		                $this->_pageWidth -
		                $this->_leftMargin,
		                $this->_yPosition -2);
		
		            $this->getNewTime();
		            $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ++$j.') '.$analyses[$i]),
		                $this->_leftMargin+10,
		                $this->_yPosition);
		
		            $this->getNewTime2();
		            $this->_page->drawText($tarifs[$i],
		                $this->_leftMargin+330,
		                $this->_yPosition);
		
		            $this->_yPosition -= -10;
		            $this->_yPosition -= $noteLineHeight;
		        }
		        	
		    }
		}
			
		//---------------------------------------------------------------------------
		//---------------------------------------------------------------------------
		//---------------------------------------------------------------------------
		//---------------------------------------------------------------------------
		//---------------------------------------------------------------------------
		//---------------------------------------------------------------------------
 		
		$this->_yPosition -= $noteLineHeight;
		
		
	} 
	
	public function getPiedPage(){
		$this->_page->setlineColor(new ZendPdf\Color\Html('green'));
		$this->_page->setLineWidth(1.5);
		$this->_page->drawLine($this->_leftMargin,
				120,
				$this->_pageWidth -
				$this->_leftMargin,
				120);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('T�l�phone: 33 726 25 36   BP: 24000',
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