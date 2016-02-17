    var base_url = window.location.toString();
    var tabUrl = base_url.split("public");
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    $(function(){
    	setTimeout(function() {
    		infoBulle();
    	}, 1000);

    });
  
    function infoBulle(){
    	/***
    	 * INFO BULLE DE LA LISTE
    	 */
    	  var tooltips = $( 'table tbody tr td infoBulleVue' ).tooltip({show: {effect: 'slideDown', delay: 250}});
	      tooltips.tooltip( 'close' );
    	  $('table tbody tr td infoBulleVue').mouseenter(function(){
    	    var tooltips = $( 'table tbody tr td infoBulleVue' ).tooltip({show: {effect: 'slideDown', delay: 250}});
    	    tooltips.tooltip( 'open' );
    	  });
    }
    
    function initialisation(){
        var  oTable = $('#patient').dataTable
    	( {
    					"sPaginationType": "full_numbers",
    					"aLengthMenu": [5,7,10,15],
     					"aaSorting": [], //On ne trie pas la liste automatiquement
    					"oLanguage": {
    						"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
    						"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
    						"sInfoFiltered": "",
    						"sUrl": "",
    						"oPaginate": {
    							"sFirst":    "|<",
    							"sPrevious": "<",
    							"sNext":     ">",
    							"sLast":     ">|"
    							}
    					   },

    					"sAjaxSource": ""+tabUrl[0]+"public/hospitalisation/liste-demandes-examens-morpho-ajax", 
    					
    	}); 
        
        var asInitVals = new Array();
    
   	//le filtre du select
   	$('#filter_statut').change(function() 
   	{					
   		oTable.fnFilter( this.value );
   	});

   	//le filtre du select du type personnel
	$('#type_personnel').change(function() 
	{					
		oTable.fnFilter( this.value );
	});
   	
   	$("tfoot input").keyup( function () {
   		/* Filter on the column (the index) of this element */
   		oTable.fnFilter( this.value, $("tfoot input").index(this) );
   	} );
   	
   	/*
   	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
   	 * the footer
   	 */
   	$("tfoot input").each( function (i) {
   		asInitVals[i] = this.value;
   	} );
   	
   	$("tfoot input").focus( function () {
   		if ( this.className == "search_init" )
   		{
   			this.className = "";
   			this.value = "";
   		}
   		
   	} );
   	
   	$("tfoot input").blur( function (i) {
   		if ( this.value == "" )
   		{
   			this.className = "search_init";
   			this.value = asInitVals[$("tfoot input").index(this)];
   		}
   	} );

    $("#annuler").click(function(){
    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
	    $("#hospitaliser").fadeOut(function(){$("#contenu").fadeIn("fast"); $("#division").val(""); $("#salle,#lit").html("");});
	    return false;
	});
      
    }
    
    /********** POUR LES EXAMENS MORPHOLOGIQUES *** POUR LES EXAMENS MORPHOLOGIQUES *** POUR LES EXAMENS MORPHOLOGIQUES ***********/
    /********** POUR LES EXAMENS MORPHOLOGIQUES *** POUR LES EXAMENS MORPHOLOGIQUES *** POUR LES EXAMENS MORPHOLOGIQUES ***********/
    /********** POUR LES EXAMENS MORPHOLOGIQUES *** POUR LES EXAMENS MORPHOLOGIQUES *** POUR LES EXAMENS MORPHOLOGIQUES ***********/
    
    /** APPLICATION DE L'EXAMEN --- APPLICATION DE L'EXAMEN**/
    /** APPLICATION DE L'EXAMEN --- APPLICATION DE L'EXAMEN**/
    /** APPLICATION DE L'EXAMEN --- APPLICATION DE L'EXAMEN**/
    var indice = 1;
    function listeExamensMorpho(id_personne, idDemande){ 
    	var id_cons = $("#"+idDemande).val();
    	$("#Examen_id_cons").val(id_cons);

    	//Identification de la liste concernée
    	var id = $('#temoinListeConcerneePourVisualisation').val();
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/liste-examens-demander-morpho';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'id':id},
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> D&Eacute;TAILS DES R&Eacute;SULTATS DES EXAMENS </div>");
            	var result = jQuery.parseJSON(data);
            	if(indice == 1){
            		result = result+"<script>" +
            			            "getimagesExamensMorphologiques(); " +
            			            "getimagesEchographieExamensMorphologiques(); " +
            			            "getimagesIRMExamensMorphologiques(); " +
            			            "getimagesScannerExamensMorphologiques(); " +
            			            "getimagesFibroscopieExamensMorphologiques(); " +
            			            "</script>";
            		indice++;
            	}
            	$("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    }
    
    
    function vueExamen(idDemande){
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/verifier-si-resultat-existe';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:({'idDemande':idDemande}),
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);   
    			
    			var hauteur = 325;
				var largeur = 500;
    			if(result == 1) {
    				 hauteur = 540;
    				 largeur = 650;
    			}
    			
    			vueExamenAAppliquer(hauteur, largeur);
    	    	var chemin = tabUrl[0]+'public/hospitalisation/vue-examen-appliquer-morpho';
    	    	$.ajax({
    	    		type: 'POST',
    	    		url: chemin ,
    	    		data:({'idDemande':idDemande}),
    	    		success: function(data) {    
    	    			var result = jQuery.parseJSON(data);   
    	    			$("#info").html(result);
    	    			$("#informations").dialog('open'); 
    	    		},
    	            
    	    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    	    		dataType: "html"
    	    	});
    			
    	    	
    		},
            
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
    	});
    	
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function vueExamenAAppliquer(hauteur, largeur){
    	$( "#application_examen" ).dialog({
    		resizable: false,
    		height:hauteur,
    		width:largeur,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Terminer": function() {
    				$( this ).dialog( "close" );             	     
    				return false;
    			}
    		}
    	});
    }
    
    function vueExamenMorpho(idDemande){

    	$('#formulaireModif').toggle(false);
    	$('#info').toggle(true);
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/verifier-si-resultat-existe';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:({'idDemande':idDemande}),
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);   
    			
    			var hauteur = 655;
				var largeur = 650;
    			if(result == 1) {
    				 hauteur = 675;
    				 largeur = 650;
    			}
    			
    			vueExamenAAppliquer(hauteur, largeur);
    	    	var chemin = tabUrl[0]+'public/hospitalisation/vue-examen-appliquer-morpho';
    	    	$.ajax({
    	    		type: 'POST',
    	    		url: chemin ,
    	    		data:({'idDemande':idDemande}),
    	    		success: function(data) {    
    	    			var result = jQuery.parseJSON(data);   
    	    			$("#info").html(result); 
    	    			$("#AjoutImage, #AjoutImageEchographie, #AjoutImageIRM, #AjoutImageScanner, #AjoutImageFibroscopie").toggle(false);
    	    			$("#temoinPourSupression").val(0);
    	    			
    	    			/*CHOISIR L'IMAGE --- CHOISIR L'IMAGE --- CHOISIR L'IMAGE*/
    	    			/*CHOISIR L'IMAGE --- CHOISIR L'IMAGE --- CHOISIR L'IMAGE*/ 
    	    			if($('#typeExamen_tmp').val() == 8){
    	    				$("#libelleInformation").replaceWith("<td colspan='3' id='libelleInformation'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (Radiologie) </i> </td>");
    	    				$('.imageRadio').toggle(true);
    	    				$('.imageEchographie').toggle(false);
    	    				$('.imageIRM').toggle(false);
    	    				$('.imageScanner').toggle(false);
    	    				$('.imageFibroscopie').toggle(false);
    	    			}
    	    			if($('#typeExamen_tmp').val() == 9){
    	    				$("#libelleInformation2").replaceWith("<td colspan='3' id='libelleInformation2'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (Echographie) </i> </td>");
    	    				$('.imageEchographie').toggle(true);
    	    				$('.imageRadio').toggle(false);
    	    				$('.imageIRM').toggle(false);
    	    				$('.imageScanner').toggle(false);
    	    				$('.imageFibroscopie').toggle(false);
    	    			}
    	    			if($('#typeExamen_tmp').val() == 10){
    	    				$("#libelleInformation3").replaceWith("<td colspan='3' id='libelleInformation3'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (IRM) </i> </td>");
    	    				$('.imageIRM').toggle(true);
    	    				$('.imageRadio').toggle(false);
    	    				$('.imageEchographie').toggle(false);
    	    				$('.imageScanner').toggle(false);
    	    				$('.imageFibroscopie').toggle(false);
    	    			}
    	    			if($('#typeExamen_tmp').val() == 11){
    	    				$("#libelleInformation4").replaceWith("<td colspan='3' id='libelleInformation4'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (SCANNER) </i> </td>");
    	    				$('.imageScanner').toggle(true);
    	    				$('.imageIRM').toggle(false);
    	    				$('.imageRadio').toggle(false);
    	    				$('.imageEchographie').toggle(false);
    	    				$('.imageFibroscopie').toggle(false);
    	    			}
    	    			if($('#typeExamen_tmp').val() == 12){
    	    				$("#libelleInformation5").replaceWith("<td colspan='3' id='libelleInformation5'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (FIBROSCOPIE) </i> </td>");
    	    				$('.imageFibroscopie').toggle(true);
    	    				$('.imageScanner').toggle(false);
    	    				$('.imageIRM').toggle(false);
    	    				$('.imageRadio').toggle(false);
    	    				$('.imageEchographie').toggle(false);
    	    			}
    	    			
    	    	    	$("#application_examen").dialog('open'); 
    	    		},
    	    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    	    		dataType: "html"
    	    	});
    		},
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
    	});
    	
    }
    
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    function listeDesSoins() {
    	$('#listeDesExamens').dataTable
     	( {
     					"sPaginationType": "full_numbers",
     					"aLengthMenu": [3,5,7],
     					"iDisplayLength": 3,
     					"aaSorting": [], //On ne trie pas la liste automatiquement
     					"oLanguage": {
     						"sInfoFiltered": "",
     						"sUrl": "",
     						"oPaginate": {
     							"sFirst":    "|<",
     							"sPrevious": "<",
     							"sNext":     ">",
     							"sLast":     ">|"
     							}
     					   },
     					  "bDestroy": true,
     	});
    	
    }
    
    function listepatient(){
	    
    	$("#terminer").click(function(){
//    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
//	    	$("#vue_patient").fadeOut(function(){
//	    		$("#contenu").fadeIn("fast"); 

//	    	    $('div .dataTables_paginate').css({ 'margin-right' : '0'});
//	    		$('#listeDataTable').css({'margin-left' : '-10'});

    		    //setTimeout(function(){
		    	  //vart=tabUrl[0]+'public/hospitalisation/liste-examens-effectues-morpho';
				  //$(location).attr("href",vart);
	    		//}, 1500);
//	    	});
		
		  var id = $('#temoinListeConcerneePourVisualisation').val();
		  if(id == 2){
			  vart=tabUrl[0]+'public/hospitalisation/liste-examens-effectues-morpho';
			  $(location).attr("href",vart);
		  } else {
			  vart=tabUrl[0]+'public/hospitalisation/liste-demandes-examens-morpho';
			  $(location).attr("href",vart);
		  }
		  
	    });
    	
	    $("#terminerdetailhospi").click(function(){
	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
  	    	$("#vue_detail_hospi_patient").fadeOut(function(){
  	    		$("#contenu").fadeIn("fast"); 

  	    		$('div .dataTables_paginate').css({ 'margin-right' : '0'});
  	    		$('#listeDataTable').css({'margin-left' : '-10'});
  	    	});
	    });
	    
    }
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /****** GESTION DES DEPLIANTS DE L'AFFICHAGE DES INFORMATIONS APRES LIBERATION DU PATIENT******/
    
    /**INFO HOSPITALISATION**/
    function depliantPlus2() {
    	$('#titre_info_hospitalisation').click(function(){
    		$("#titre_info_hospitalisation").replaceWith(
    			"<span id='titre_info_hospitalisation' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Infos sur l'hospitalisation "+
    		    "</span>");
    		animationPliantDepliant2();
    		$('#info_hospitalisation').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant2() {
    	$('#titre_info_hospitalisation').click(function(){
    		$("#titre_info_hospitalisation").replaceWith(
    			"<span id='titre_info_hospitalisation' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Infos sur l'hospitalisation"+
    		    "</span>");
    		depliantPlus2();
    		$('#info_hospitalisation').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**INFO LISTE**/
    function depliantPlus3() {
    	$('#titre_info_liste').click(function(){
    		$("#titre_info_liste").replaceWith(
    			"<span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Liste des soins "+
    		    "</span>");
    		animationPliantDepliant3();
    		$('#info_liste').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant3() {
    	$('#titre_info_liste').click(function(){
    		$("#titre_info_liste").replaceWith(
    			"<span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Liste des soins "+
    		    "</span>");
    		depliantPlus3();
    		$('#info_liste').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**INFO DEMANDE**/
    function depliantPlus4() {
    	$('#titre_info_demande').click(function(){
    		$("#titre_info_demande").replaceWith(
    			"<span id='titre_info_demande' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> D&eacute;tails des infos sur la demande "+
    		    "</span>");
    		animationPliantDepliant4();
    		$('#info_demande').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant4() {
    	$('#titre_info_demande').click(function(){
    		$("#titre_info_demande").replaceWith(
    			"<span id='titre_info_demande' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> D&eacute;tails des infos sur la demande "+
    		    "</span>");
    		depliantPlus4();
    		$('#info_demande').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function initAnimation() {
    	$('#info_hospitalisation').toggle(false);
    	$('#info_demande').toggle(false);
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function EffectuerExamen(idDemande){
    	$( "#application_examen" ).dialog({
    		resizable: false,
    		height:675,
    		width:650,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Terminer": function() {
    				if( controleSaisi() ) {
    					
    				var technique_utilise = $('#technique_utilise').val();
    				var resultat = $('#resultat').val();
    				var conclusion = $('#conclusion').val();
    				var update = 0;
    				
    				if($("#temoinEnregistrementImage").val() == 12 || $("#temoinEnregistrementImage").val() == 11 || $("#temoinEnregistrementImage").val() == 10 || $("#temoinEnregistrementImage").val() == 9 || $("#temoinEnregistrementImage").val() == 8){
    					update = 1;
    				}
    				
    				var chemin = tabUrl[0]+'public/hospitalisation/appliquer-examen';
    		    	$.ajax({
    		    		type: 'POST',
    		    		url: chemin ,
    		    		data:({'idDemande':idDemande, 'techniqueUtiliser':technique_utilise, 'noteResultat':resultat, 'conclusion':conclusion, 'update':update}),
    		    		success: function() {    

    		    			var id_cons = $('#Examen_id_cons').val();
    		    			
    		    			var chemin = tabUrl[0]+'public/hospitalisation/raffraichir-liste-examens';
    	    		    	$.ajax({
    	    		    		type: 'POST',
    	    		    		url: chemin ,
    	    		    		data:({'id_cons':id_cons}),
    	    		    		success: function(data) {    
    	    		    			var result = jQuery.parseJSON(data);
    	    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
    	    		    			
    	    		    			$('#technique_utilise').val('');
    	    		    			$('#resultat').val('');
    	    		    			$('#conclusion').val('');
    	    		    		},
    	    		            
    	    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    	    		    		dataType: "html"
    	    		    	});
    		    			
    		    		},
    		            
    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		    		dataType: "html"
    		    	});
    				
    		    	$( this ).dialog( "close" );             	     
    				return false;
    				
    				}
    			},
    			
    			"Annuler": function() {
    				var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
    		    	tooltips.tooltip( "close" );
    				$( this ).dialog( "close" );             	     
    				return false;
    			}
    		}
    	});
    }
   
    function appliquerExamen(idDemande) { 
    	$("#technique_utilise, #resultat, #conclusion").css("border-color","");
    	$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
    	
    	$('#technique_utilise').val('');
		$('#resultat').val('');
		$('#conclusion').val('');
		
		$('#formulaireModif').toggle(true);
    	$('#info').toggle(false);
    	$("#AjoutImage, #AjoutImageEchographie, #AjoutImageIRM, #AjoutImageScanner, #AjoutImageFibroscopie").toggle(true);
    	$("#temoinPourSupression").val(1);
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/ajouter-examen';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:({'idDemande':idDemande}),
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);
    			$("#script_donnees").html(result);
    	    	
    			/*CHOISIR L'IMAGE --- CHOISIR L'IMAGE --- CHOISIR L'IMAGE*/
    			/*CHOISIR L'IMAGE --- CHOISIR L'IMAGE --- CHOISIR L'IMAGE*/ 
    			if($('#typeExamen_tmp').val() == 8){
    				$("#libelleInformation").replaceWith("<td colspan='3' id='libelleInformation'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (Radiologie) </i> </td>");
    				$('.imageRadio').toggle(true);
    				$('.imageEchographie').toggle(false);
    				$('.imageIRM').toggle(false);
    				$('.imageScanner').toggle(false);
    				$('.imageFibroscopie').toggle(false);
    			}
    			if($('#typeExamen_tmp').val() == 9){
    				$("#libelleInformation2").replaceWith("<td colspan='3' id='libelleInformation2'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (Echographie) </i> </td>");
    				$('.imageEchographie').toggle(true);
    				$('.imageRadio').toggle(false);
    				$('.imageIRM').toggle(false);
    				$('.imageScanner').toggle(false);
    				$('.imageFibroscopie').toggle(false);
    			}
    			if($('#typeExamen_tmp').val() == 10){
    				$("#libelleInformation3").replaceWith("<td colspan='3' id='libelleInformation3'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (IRM) </i> </td>");
    				$('.imageIRM').toggle(true);
    				$('.imageRadio').toggle(false);
    				$('.imageEchographie').toggle(false);
    				$('.imageScanner').toggle(false);
    				$('.imageFibroscopie').toggle(false);
    			}
    			if($('#typeExamen_tmp').val() == 11){
    				$("#libelleInformation4").replaceWith("<td colspan='3' id='libelleInformation4'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (SCANNER) </i> </td>");
    				$('.imageScanner').toggle(true);
    				$('.imageIRM').toggle(false);
    				$('.imageRadio').toggle(false);
    				$('.imageEchographie').toggle(false);
    				$('.imageFibroscopie').toggle(false);
    			}
    			if($('#typeExamen_tmp').val() == 12){
    				$("#libelleInformation5").replaceWith("<td colspan='3' id='libelleInformation5'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (FIBROSCOPIE) </i> </td>");
    				$('.imageFibroscopie').toggle(true);
    				$('.imageScanner').toggle(false);
    				$('.imageIRM').toggle(false);
    				$('.imageRadio').toggle(false);
    				$('.imageEchographie').toggle(false);
    			}
    	    	
    			EffectuerExamen(idDemande);
    			$("#application_examen").dialog('open'); 
    		},
            
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
    	});
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function modifierDonneesExamen(idDemande){
    	$( "#application_examen" ).dialog({
    		resizable: false,
    		height:675,
    		width:650,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Terminer": function() {
    				if( controleSaisi() ) {
    					
    				var technique_utilise = $('#technique_utilise').val();
    				var resultat = $('#resultat').val();
    				var conclusion = $('#conclusion').val();

    				var chemin = tabUrl[0]+'public/hospitalisation/appliquer-examen';
    		    	$.ajax({
    		    		type: 'POST',
    		    		url: chemin ,
    		    		data:({'idDemande':idDemande, 'techniqueUtiliser':technique_utilise, 'noteResultat':resultat, 'conclusion':conclusion, 'update':1}),
    		    		success: function() {    

    		    			var id_cons = $('#Examen_id_cons').val();
    		    			
    		    			var chemin = tabUrl[0]+'public/hospitalisation/raffraichir-liste-examens';
    	    		    	$.ajax({
    	    		    		type: 'POST',
    	    		    		url: chemin ,
    	    		    		data:({'id_cons':id_cons}),
    	    		    		success: function(data) {    
    	    		    			var result = jQuery.parseJSON(data);
    	    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
    	    		    			
    	    		    			$('#technique_utilise').val('');
    	    		    			$('#resultat').val('');
    	    		    			$('#conclusion').val('');
    	    		    		},
    	    		            
    	    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    	    		    		dataType: "html"
    	    		    	});
    		    			
    		    		},
    		            
    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		    		dataType: "html"
    		    	});
    				
    		    	$( this ).dialog( "close" );             	     
    				return false;
    				
    			    }
    			},
    			
    			"Annuler": function() {
    				
    		    	var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
    		    	tooltips.tooltip( "close" );
    		    		
    				$( this ).dialog( "close" );             	     
    				return false;
    			}
    		}
    	});
    }
    
    function modifierExamen(idDemande) {
    	$("#technique_utilise, #resultat, #conclusion").css("border-color","");
    	$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
    	
    	$('#formulaireModif').toggle(true);
    	$('#info').toggle(false);
    	$("#AjoutImage, #AjoutImageEchographie, #AjoutImageIRM, #AjoutImageScanner, #AjoutImageFibroscopie").toggle(true);
    	$("#temoinPourSupression").val(1);
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/modifier-examen';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:({'idDemande':idDemande}),
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);
    			$("#script_donnees").html(result);
    	    	
    			/*CHOISIR L'IMAGE --- CHOISIR L'IMAGE --- CHOISIR L'IMAGE*/
    			/*CHOISIR L'IMAGE --- CHOISIR L'IMAGE --- CHOISIR L'IMAGE*/ 
    			if($('#typeExamen_tmp').val() == 8){
    				$("#libelleInformation").replaceWith("<td colspan='3' id='libelleInformation'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (Radiologie) </i> </td>");
    				$('.imageRadio').toggle(true);
    				$('.imageEchographie').toggle(false);
    				$('.imageIRM').toggle(false);
    				$('.imageScanner').toggle(false);
    				$('.imageFibroscopie').toggle(false);
    			}
    			if($('#typeExamen_tmp').val() == 9){
    				$("#libelleInformation2").replaceWith("<td colspan='3' id='libelleInformation2'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (Echographie) </i> </td>");
    				$('.imageEchographie').toggle(true);
    				$('.imageRadio').toggle(false);
    				$('.imageIRM').toggle(false);
    				$('.imageScanner').toggle(false);
    				$('.imageFibroscopie').toggle(false);
    			}
    			if($('#typeExamen_tmp').val() == 10){
    				$("#libelleInformation3").replaceWith("<td colspan='3' id='libelleInformation3'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (IRM) </i> </td>");
    				$('.imageIRM').toggle(true);
    				$('.imageRadio').toggle(false);
    				$('.imageEchographie').toggle(false);
    				$('.imageScanner').toggle(false);
    				$('.imageFibroscopie').toggle(false);
    			}
    			if($('#typeExamen_tmp').val() == 11){
    				$("#libelleInformation4").replaceWith("<td colspan='3' id='libelleInformation4'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (SCANNER) </i> </td>");
    				$('.imageScanner').toggle(true);
    				$('.imageIRM').toggle(false);
    				$('.imageRadio').toggle(false);
    				$('.imageEchographie').toggle(false);
    				$('.imageFibroscopie').toggle(false);
    			}
    			if($('#typeExamen_tmp').val() == 12){
    				$("#libelleInformation5").replaceWith("<td colspan='3' id='libelleInformation5'> <i style='color: green; font-family: time new romans; font-size: 17px;'> Imagerie (FIBROSCOPIE) </i> </td>");
    				$('.imageFibroscopie').toggle(true);
    				$('.imageScanner').toggle(false);
    				$('.imageIRM').toggle(false);
    				$('.imageRadio').toggle(false);
    				$('.imageEchographie').toggle(false);
    			}
    	    	
    			modifierDonneesExamen(idDemande);
    			$("#application_examen").dialog('open'); 
    		},
            
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
    	});
    }
    
    /**
     * Lors d'un hover
     */
    function hover() {
    	$('#technique_utilise, #resultat, #conclusion').hover(function(){
    		$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
    		var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
    		tooltips.tooltip( "hide" );
    	});
    }
    
    /**
     * Lors d'un click
     */
    function click() {
    	$('#technique_utilise, #resultat, #conclusion').click(function(){
			var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
			tooltips.tooltip( "close" );
			$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
	    });
    }
    
    function controleSaisi() {
    	
    	hover();
    	click();
    	
    	$("#technique_utilise, #resultat, #conclusion").css("border-color","");
    	
    	if(!$('#technique_utilise').val()){
    		$("#technique_utilise").css("border-color","#FF0000");
    		$('#technique_utilise').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#technique_utilise" ).tooltip();
    			tooltips.tooltip( "open" );
    			
    	}else if(!$('#resultat').val()){
    		$("#resultat").css("border-color","#FF0000");
    		$('#resultat').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#resultat" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#technique_utilise").css("border-color","");
        		
    	}else if(!$('#conclusion').val()){
    		$("#conclusion").css("border-color","#FF0000");
    		$('#conclusion').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#conclusion" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#resultat").css("border-color","");
        		
    	}else {
    		
    		return true;
    	}
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function EnvoyerExamen(idDemande){
    	$( "#envoyer_examen" ).dialog({
    		resizable: false,
    		height:180,
    		width:460,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Terminer": function() {

    				var id_cons = $('#Examen_id_cons').val();
    				var chemin = tabUrl[0]+'public/hospitalisation/envoyer-examen';
    		    	$.ajax({
    		    		type: 'POST',
    		    		url: chemin ,
    		    		data:({'idDemande':idDemande, 'id_cons':id_cons}),
    		    		success: function(data) {    
    		    			var result = jQuery.parseJSON(data);
    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
    		    			
    		    		},
    		            
    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		    		dataType: "html"
    		    	});
    				
    		    	$( this ).dialog( "close" );             	     
    				return false;
    			},
    			
    			"Annuler": function() {
    				$( this ).dialog( "close" );             	     
    				return false;
    			}
    		}
    	});
    }
    function envoyer(idDemande){
    	EnvoyerExamen(idDemande);
		$("#envoyer_examen").dialog('open'); 
    }
    
    /** POUR LA LISTE DES EXAMENS DEJA EFFECTUES --- POUR LA LISTE DES EXAMENS DEJA EFFECTUES **/
    /** POUR LA LISTE DES EXAMENS DEJA EFFECTUES --- POUR LA LISTE DES EXAMENS DEJA EFFECTUES **/
    /** POUR LA LISTE DES EXAMENS DEJA EFFECTUES --- POUR LA LISTE DES EXAMENS DEJA EFFECTUES **/
    function initialisationListeRehercheExamensEffectuesMorpho(){
        
        var  oTable = $('#patient').dataTable
    	( {
    					"sPaginationType": "full_numbers",
    					"aLengthMenu": [5,7,10,15],
     					"aaSorting": [], //On ne trie pas la liste automatiquement
    					"oLanguage": {
    						"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
    						"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
    						"sInfoFiltered": "",
    						"sUrl": "",
    						"oPaginate": {
    							"sFirst":    "|<",
    							"sPrevious": "<",
    							"sNext":     ">",
    							"sLast":     ">|"
    							}
    					   },

    					"sAjaxSource": ""+tabUrl[0]+"public/hospitalisation/liste-recherche-examens-effectues-morpho-ajax", 
    					
    	}); 
        
        var asInitVals = new Array();
    
   	//le filtre du select
   	$('#filter_statut').change(function() 
   	{					
   		oTable.fnFilter( this.value );
   	});

   	//le filtre du select du type personnel
	$('#type_personnel').change(function() 
	{					
		oTable.fnFilter( this.value );
	});
   	
   	$("tfoot input").keyup( function () {
   		/* Filter on the column (the index) of this element */
   		oTable.fnFilter( this.value, $("tfoot input").index(this) );
   	} );
   	
   	/*
   	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
   	 * the footer
   	 */
   	$("tfoot input").each( function (i) {
   		asInitVals[i] = this.value;
   	} );
   	
   	$("tfoot input").focus( function () {
   		if ( this.className == "search_init" )
   		{
   			this.className = "";
   			this.value = "";
   		}
   		
   	} );
   	
   	$("tfoot input").blur( function (i) {
   		if ( this.value == "" )
   		{
   			this.className = "search_init";
   			this.value = asInitVals[$("tfoot input").index(this)];
   		}
   	} );

    $("#annuler").click(function(){
    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
	    $("#hospitaliser").fadeOut(function(){$("#contenu").fadeIn("fast"); $("#division").val(""); $("#salle,#lit").html("");});
	    return false;
	});
      
    }
