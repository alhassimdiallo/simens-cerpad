<?php
namespace Laboratoire\View\Helper;

use ZendPdf;
use ZendPdf\Page;
use ZendPdf\Font;

class ResultatsAnalysesDemandeesPdf
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
	protected $_analysesDemandees;
	protected $_resultatsAnalysesDemandees;
	protected $_depistage;
	
	
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
//  		$imageHeader = ZendPdf\Image::imageWithPath('C:\wamp\www\simensc\public\images_icons\cerpad-labo.png');
// 		$this->_page->drawImage($imageHeader, 445, //-x
// 				$this->_pageHeight - 120, //-y
// 				535, //+x
// 				787); //+y
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('République du Sénégal',
				$this->_leftMargin,
				$this->_pageHeight - 50);
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Ministère de la santé et de l\'action sociale',
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
	
	public function setDonneesPatient($donneesPatient){
		$this->_DonneesPatient = $donneesPatient;
	}
	
	public function setService($service){
		$this->_Service = $service;
	}
	
	public function setAnalysesDemandees($analysesDemandees){
	    $this->_analysesDemandees = $analysesDemandees;
	}
	
	public function setResultatsAnalysesDemandees($resultatsAnalysesDemandees){
	    $this->_resultatsAnalysesDemandees = $resultatsAnalysesDemandees;
	}
	
	public function setDepistage($depistage){
	    $this->_depistage = $depistage;
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
		$this->_page->setFont($font, 12);
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
		$this->_page->drawText('RESULTATS D\'ANALYSES',
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

		//-----------------------------------------------
		$value = $this->_DonneesPatient;
		$typepatient = "E";
		if($this->_depistage->current()){ 
		    if($this->_depistage->current()['typepatient'] == 1){ $typepatient = "I"; } 
		}
    
		//-----------------------------------------------
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText(iconv ('UTF-8' , 'ISO-8859-1' , 'NÂ°: '.$value->idpersonne.'-'.$typepatient),
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
			
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
	    
		$analyses = array();
		$idAnalyses = array();
		$typesAnalyses = array();
		$infosAnalyseDemande = array();
			
		$listeAnalysesDemandees = $this->_analysesDemandees;
		
		for($i = 0 ; $i < count($listeAnalysesDemandees) ; $i++){
		    $idanalyse = $listeAnalysesDemandees[$i]['idanalyse'];
		    
		    $analyses[$idanalyse]            = $listeAnalysesDemandees[$i]['Designation'];
		    $idAnalyses[$idanalyse]          = $idanalyse;
		    $typesAnalyses[$idanalyse]       = $listeAnalysesDemandees[$i]['Libelle'];
		    $infosAnalyseDemande[$idanalyse] = $listeAnalysesDemandees[$i];
		}
			
		//var_dump($analyses); exit();
		
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
		//-----------------------------------------------------------------------
		//-----------------------------------------------------------------------
		 		
		if(in_array('HEMATOLOGIE', $typesAnalyses)){
			
			for ($i = 1 ; $i <= count($analyses) ; $i++){
			    if($idAnalyses[$i] == 1){
			        $this->_page = $this->getNfs($noteLineHeight, $analyses[$i], $infosAnalyseDemande);
			    }
			}
			
 		}
		
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		//-----------------------------------------------------------------
 		
 		
 		
 		//-----------------------------------------------------------------------
 		//-----------------------------------------------------------------------
 		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
 		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
 		//-------- HEMATOLOGIE --------- HEMATOLOGIE ---------- HEMATOLOGIE------
 		//-----------------------------------------------------------------------
 		//-----------------------------------------------------------------------
 		
 		if(in_array('DEPISTAGE', $typesAnalyses)){
 		    $this->_page = $this->typage_hemoglobine($noteLineHeight, $analyses[68], $infosAnalyseDemande, $value);
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
// 		if(in_array('BIOCHIMIE', $typesAnalyses)){ }
 		
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
// 		if(in_array('PARASITOLOGIE', $typesAnalyses)){}
 		
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
// 		if(in_array('BACTERIOLOGIE', $typesAnalyses)){}
 		
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
				80,
				$this->_pageWidth -
				$this->_leftMargin,
				80);
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('Téléphone: 33 726 25 36   BP: 24000',
				$this->_leftMargin,
				$this->_pageWidth - ( 140 + 390));
		
		$this->_page->setFont($this->_newTime, 10);
		$this->_page->drawText('SIMENS+: ',
				$this->_leftMargin + 355,
				$this->_pageWidth - ( 140 + 390));
		$this->_page->setFont($this->_newTimeGras, 11);
		$this->_page->drawText('www.simens.sn',
				$this->_leftMargin + 405,
				$this->_pageWidth - ( 140 + 390));
	}
	
	//---   GESTION DES ANALYSES   ---   GESTION DES ANALYSES    
	//---   GESTION DES ANALYSES   ---   GESTION DES ANALYSES
	//---   GESTION DES ANALYSES   ---   GESTION DES ANALYSES
	
	public function getNfs($noteLineHeight, $Designation, $infosAnalyseDemande){
	    
	    $this->_page->setlineColor(new ZendPdf\Color\Html('green'));
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition-10,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition-10);
	     
	    //-----------------------------------------------------------------------
	    //-----------------------------------------------------------------------
	    //-----------------------------------------------------------------------
	    
	    $this->_yPosition -= $noteLineHeight;

	    $control = new DateHelper();
	    
	    $resultats = $this->_resultatsAnalysesDemandees;
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Enregistre le: '),
	        $this->_leftMargin+5,
	        $this->_yPosition+10);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $control->convertDateTime( $infosAnalyseDemande[1]['DateEnregistrementResultat'] )),
	        $this->_leftMargin+50,
	        $this->_yPosition+10);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'par: '),
	        $this->_leftMargin+135,
	        $this->_yPosition+10);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ''. $infosAnalyseDemande[1]['Prenom'] .' '.$infosAnalyseDemande[1]['Nom']),
	        $this->_leftMargin+150,
	        $this->_yPosition+10);
	    
	    $this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
	    $this->_page->setLineWidth(1);
	    $this->_page->drawLine($this->_leftMargin+190,
	        $this->_yPosition -3,
	        $this->_pageWidth -
	        $this->_leftMargin-202,
	        $this->_yPosition -3);
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'));
	    $this->_page->setLineWidth(0.5);
	     
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition -2,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition -2);
	    
	    $this->getNewTime();
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $Designation),
	        $this->_leftMargin+190,
	        $this->_yPosition);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'HÃ©matologie'),
	        $this->_leftMargin+5,
	        $this->_yPosition);
	    
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f2f2f2'));
	    $this->_page->setLineWidth(16);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +4,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +4);

	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Type de matÃ©riel: " ),
	        $this->_leftMargin+5,
	        $this->_yPosition);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 11);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[1]['type_materiel'] ),
	        $this->_leftMargin+85,
	        $this->_yPosition);
	    
	    	
	    $this->_yPosition -= $noteLineHeight-5; //aller a la ligne suivante
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ROMAN), 10);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "FORMULE LEUCOCYTAIRE "),
	        $this->_leftMargin+5,
	        $this->_yPosition);
	    	
	    //GESTION DES RESULTATS DE L'ANALYSE
	    //GESTION DES RESULTATS DE L'ANALYSE
	    //GESTION DES RESULTATS DE L'ANALYSE
	    /**
	     * CHAMP 1 -- Leucocytes ------ CHAMP 1 -- Leucocytes ------ CHAMP 1
	     */
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Leucocytes'),
	        $this->_leftMargin+110,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ1']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+280, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+300, $this->_yPosition +15);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(4 000 - 10 000)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 2 -- Polynucléaires neutrophiles ------ CHAMP 2 -- Polynucléaires neutrophiles ------ CHAMP 2
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'PolynuclÃ©aires neutrophiles'),
	        $this->_leftMargin+43,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ2']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+280, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+300, $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(2 000 - 7 000)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 3 -- Polynucléaire éosinophiles ------ CHAMP 3 -- Polynucléaires éosinophiles ------ CHAMP 3
	     */
	     
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'PolynuclÃ©aires Ã©osinophiles'),
	        $this->_leftMargin+43,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ3']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+280, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+300, $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(20 - 500)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 4 -- Polynucléaires basophiles ------ CHAMP 4 -- Polynucléaire basophiles ------ CHAMP 4
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'PolynuclÃ©aires basophiles'),
	        $this->_leftMargin+51,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ4']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+280, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+300, $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(0 - 100)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	     
	    /**
	     * CHAMP 5 -- Lymphocytes ------ CHAMP 5 -- Lymphocytes ------ CHAMP 5
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Lymphocytes'),
	        $this->_leftMargin+103,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ5']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+280, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+300, $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(800 - 4 000)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	     
	    /**
	     * CHAMP 6 -- Monocytes ------ CHAMP 6 -- Monocytes ------ CHAMP 6
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Monocytes'),
	        $this->_leftMargin+113,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ6']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+280, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+300, $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(120 - 1 200)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 7 -- Polynucléaires neutrophiles ------ CHAMP 7 -- Polynucléaires neutrophiles ------ CHAMP 7
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'PolynuclÃ©aires neutrophiles'),
	        $this->_leftMargin+43,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ7']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+280, $this->_yPosition +12);

	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(45 - 70)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	   
	    /**
	     * CHAMP 8 -- Polynucléaire éosinophiles ------ CHAMP 8 -- Polynucléaires éosinophiles ------ CHAMP 8
	     */
	     
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'PolynuclÃ©aires Ã©osinophiles'),
	        $this->_leftMargin+43,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ8']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+280, $this->_yPosition +12);
	    
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(0 - 5)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 9 -- Polynucléaires basophiles ------ CHAMP 9 -- Polynucléaire basophiles ------ CHAMP 9
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'PolynuclÃ©aires basophiles'),
	        $this->_leftMargin+51,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ9']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+280, $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(0 - 3)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 10 -- Lymphocytes ------ CHAMP 10 -- Lymphocytes ------ CHAMP 10
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Lymphocytes'),
	        $this->_leftMargin+103,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ10']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+280, $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(20 - 40)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 11 -- Monocytes ------ CHAMP 11 -- Monocytes ------ CHAMP 11
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Monocytes'),
	        $this->_leftMargin+113,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ11']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+280, $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(3 - 15)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 12 -- Erythrocytes ------ CHAMP 12 -- Erythrocytes ------ CHAMP 12
	     * ON DESCEND *** ON DESCEND *** ON DESCEND
	     */
	    
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Erythrocytes'),
	        $this->_leftMargin+101,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ12']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+280, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+300, $this->_yPosition +15);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(3 500 000 - 5 000 000)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /** 
	     * CHAMP 13 -- Hémoglobine  ------ CHAMP 13 -- Hémoglobine  ------ CHAMP 13
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'HÃ©moglobine'),
	        $this->_leftMargin+100,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ13']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('g/dl', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(11 - 15)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /** 
	     * CHAMP 14 -- Hématocrite ------ CHAMP 14 -- Hématocrite ------ CHAMP 14
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'HÃ©matocrite'),
	        $this->_leftMargin+107,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ14']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(37 - 47)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 15 -- V.G.M ------ CHAMP 15 -- V.G.M ------ CHAMP 15
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'V.G.M'),
	        $this->_leftMargin+129,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ15']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('fl', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(80 - 100)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 16 -- T.C.M.H ------ CHAMP 16 -- T.C.M.H ------ CHAMP 16
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'T.C.M.H'),
	        $this->_leftMargin+120,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ16']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('pg', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(27 - 34)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 17 -- C.C.M.H ------ CHAMP 17 -- C.C.M.H ------ CHAMP 17
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'C.C.M.H'),
	        $this->_leftMargin+120,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ17']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('g/dl', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(32 - 36)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 18 -- IDR-CV ------ CHAMP 18 -- IDR-CV ------ CHAMP 18
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'IDR-CV'),
	        $this->_leftMargin+122,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ18']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(11 - 16)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 19 -- IDR-DS ------ CHAMP 19 -- IDR-DS ------ CHAMP 19
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'IDR-DS'),
	        $this->_leftMargin+122,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ19']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('fl', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(35 - 56)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 20 -- Plaquettes ------ CHAMP 20 -- Plaquettes ------ CHAMP 20
	     * ON DESCEND *** ON DESCEND *** ON DESCEND
	     */
	    
	    $this->_yPosition -= $noteLineHeight; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Plaquettes'),
	        $this->_leftMargin+110,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ20']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('/mm', $this->_leftMargin+280, $this->_yPosition +12);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 8.5);
	    $this->_page->drawText('3', $this->_leftMargin+300, $this->_yPosition +15);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 10.5);
	    $this->_page->drawText('(100 000 - 400 000)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 21 -- VMP ------ CHAMP 21 -- VMP ------ CHAMP 21
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'VMP'),
	        $this->_leftMargin+133,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ21']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('fl', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(6,5 - 12,0)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    
	    /**
	     * CHAMP 22 -- IDP ------ CHAMP 22 -- IDP ------ CHAMP 22
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'IDP'),
	        $this->_leftMargin+135,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ22']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('g/dl', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(9,0 - 17,0)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	    
	    /**
	     * CHAMP 23 -- PCT ------ CHAMP 3 -- PCT ------ CHAMP 23
	     */
	    
	    $this->_yPosition -= $noteLineHeight-14; //aller a la ligne suivante
	    
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#f6f6f6'));
	    $this->_page->setLineWidth(15);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +15,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +15);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'PCT'),
	        $this->_leftMargin+135,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText($this->prixMill($resultats[1]['champ23']),
	        $this->_leftMargin+220,
	        $this->_yPosition +12);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('%', $this->_leftMargin+280, $this->_yPosition +12);
	    
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10.5);
	    $this->_page->drawText('(0,108 - 0,282)',
	        $this->_leftMargin+360,
	        $this->_yPosition +12);
	     
	    return $this->_page;
	}
	
	
	public function typage_hemoglobine($noteLineHeight, $Designation, $infosAnalyseDemande, $value){
	    
	    //-----------------------------------------------
	    $this->_yPosition -= 15;
	    //----------------------------------------------
	    $this->_page->setFont($this->_newTimeGras, 9);
	    $this->_page->drawText('ETHNIE :',
	        $this->_leftMargin+161,
	        $this->_yPosition);
	    $this->_page->setFont($this->_newTime, 10);
	    $this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $this->_depistage->current()['ethnie']),
	        $this->_leftMargin+210,
	        $this->_yPosition);
	    
	    //-----------------------------------------------
	    $this->_yPosition -= 15;
	    //----------------------------------------------
	    $this->_page->setFont($this->_newTimeGras, 9);
	    $this->_page->drawText('TELEPHONE :',
	        $this->_leftMargin+140,
	        $this->_yPosition);
	    $this->_page->setFont($this->_newTime, 10);
	    $this->_page->drawText(iconv ('UTF-8' ,'ISO-8859-1' , $value->telephone),
	        $this->_leftMargin+210,
	        $this->_yPosition);
	    
	    $this->_page->setlineColor(new ZendPdf\Color\Html('green'));
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition-10,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition-10);
	    
	    //-----------------------------------------------------------------------
	    //-----------------------------------------------------------------------
	    //-----------------------------------------------------------------------
	     
	    $this->_yPosition -= $noteLineHeight;
	
	    $control = new DateHelper();
	     
	    $resultats = $this->_resultatsAnalysesDemandees;
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Enregistre le: '),
	        $this->_leftMargin+5,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8.5);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $control->convertDateTime( $infosAnalyseDemande[68]['DateEnregistrementResultat'] )),
	        $this->_leftMargin+50,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 8);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'validÃ© par: '),
	        $this->_leftMargin+135,
	        $this->_yPosition+10);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', ''. $infosAnalyseDemande[68]['Prenom'] .' '.$infosAnalyseDemande[68]['Nom']),
	        $this->_leftMargin+173,
	        $this->_yPosition+10);
	     
	    $this->_yPosition -= $noteLineHeight-20; //Allez à la ligne
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#999999'));
	    $this->_page->setLineWidth(1);
	    $this->_page->drawLine($this->_leftMargin+190,
	        $this->_yPosition -3,
	        $this->_pageWidth -
	        $this->_leftMargin-165,
	        $this->_yPosition -3);
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#ffffff'));
	    $this->_page->setLineWidth(0.5);
	
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition -2,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition -2);
	     
	    $this->getNewTime();
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $Designation),
	        $this->_leftMargin+190,
	        $this->_yPosition);
	     
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_HELVETICA_OBLIQUE), 9);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'DÃ©pistage nÃ©onatal'),
	        $this->_leftMargin+5,
	        $this->_yPosition);
	     
	    $this->_yPosition -= $noteLineHeight+15; //aller a la ligne suivante
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#eeeeee'));
	    $this->_page->setLineWidth(16);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +9,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +9);
	
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 10);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', "Technique utilisÃ©e: " ),
	        $this->_leftMargin+5,
	        $this->_yPosition+5);
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_ITALIC), 11);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', $resultats[68]['type_materiel'] ),
	        $this->_leftMargin+85,
	        $this->_yPosition+5);
	    
	    $this->_yPosition -= $noteLineHeight+25; //aller a la ligne suivante
	     
	    $this->_page->setLineColor(new ZendPdf\Color\Html('#e1e1e1'));
	    $this->_page->setLineWidth(19);
	    $this->_page->drawLine($this->_leftMargin,
	        $this->_yPosition +23,
	        $this->_pageWidth -
	        $this->_leftMargin,
	        $this->_yPosition +23);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES), 12);
	    $this->_page->drawText(iconv ( 'UTF-8', 'ISO-8859-1', 'Typage de l\'hÃ©moglobine: '),
	        $this->_leftMargin+85,
	        $this->_yPosition +18);
	    
	    $this->_page->setFont(Font::fontWithName(ZendPdf\Font::FONT_TIMES_BOLD), 13);
	    $this->_page->drawText($resultats[68]['valeur'],
	        $this->_leftMargin+220,
	        $this->_yPosition +18);
	    
	    
	    return $this->_page;
	}
	
	
}