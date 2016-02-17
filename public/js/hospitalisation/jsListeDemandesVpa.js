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
    	 * INFO BULLE FE LA LISTE
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

    					"sAjaxSource": ""+tabUrl[0]+"public/hospitalisation/liste-demandes-vpa-ajax", 
    					
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
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function details(id_personne, idVpa){ 
    	var id_cons = $("#"+idVpa).val();
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/details-demande-visite';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'idVpa':idVpa},
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> AJOUTER LES R&Eacute;SULTATS </div>");
            	var result = jQuery.parseJSON(data);
            	$("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    }
    
    function scriptTerminer(){

    	$("#annuler").click(function(){
        	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
    	    	$("#vue_patient").fadeOut(function(){
    	    		$("#contenu").fadeIn("fast"); 
    	    	});
    	    	return false;
    	});
    	
    }
    
    function vider(){
    	$('#numero_vpa').val('');
    	$('#type_intervention').val('');
    	$('#form_patient_vpa input[name=aptitude]').attr('checked', false);
    	$('#Coche').toggle(false);  $('#DeCoche').toggle(false);
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /*** PARTIE RECHERCHE ***/
    /*** PARTIE RECHERCHE ***/
    /*** PARTIE RECHERCHE ***/
    
    function initialisationRecherche(){
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

    					"sAjaxSource": ""+tabUrl[0]+"public/hospitalisation/liste-recherche-vpa-ajax", 
    					
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

    $("#annuler2").click(function(){
    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
	    $("#hospitaliser").fadeOut(function(){$("#contenu").fadeIn("fast"); $("#division").val(""); $("#salle,#lit").html("");});
	    return false;
	});
      
    }
    
    function vuedetails(id_personne, idVpa){
    	var id_cons = $("#"+idVpa).val();
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/details-recherche-visite';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'idVpa':idVpa},
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> D&Eacute;TAILS DES R&Eacute;SULTATS </div>");
            	var result = jQuery.parseJSON(data);
            	$("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    }
    
    function scriptAnnulerVisualisation(){

    	$("#terminer2").click(function(){
        	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
    	    	$("#vue_patient").fadeOut(function(){
    	    		$("#contenu").fadeIn("fast"); 
    	    	});
    	    	return false;
    	});
    	
    }
//    function vueExamenAAppliquer(hauteur, largeur){
//    	$( "#informations" ).dialog({
//    		resizable: false,
//    		height:hauteur,
//    		width:largeur,
//    		autoOpen: false,
//    		modal: true,
//    		buttons: {
//    			"Terminer": function() {
//    				$( this ).dialog( "close" );             	     
//    				return false;
//    			}
//    		}
//    	});
//    }
//    
//    function vueExamen(idDemande){
//    	
//    	var chemin = tabUrl[0]+'public/hospitalisation/verifier-si-resultat-existe';
//    	$.ajax({
//    		type: 'POST',
//    		url: chemin ,
//    		data:({'idDemande':idDemande}),
//    		success: function(data) {    
//    			var result = jQuery.parseJSON(data);   
//    			
//    			var hauteur = 325;
//				var largeur = 500;
//    			if(result == 1) {
//    				 hauteur = 540;
//    				 largeur = 650;
//    			}
//    			
//    			vueExamenAAppliquer(hauteur, largeur);
//    	    	var chemin = tabUrl[0]+'public/hospitalisation/vue-examen-appliquer';
//    	    	$.ajax({
//    	    		type: 'POST',
//    	    		url: chemin ,
//    	    		data:({'idDemande':idDemande}),
//    	    		success: function(data) {    
//    	    			var result = jQuery.parseJSON(data);   
//    	    			$("#info").html(result);
//    	    			$("#informations").dialog('open'); 
//    	    		},
//    	            
//    	    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    	    		dataType: "html"
//    	    	});
//    			
//    	    	
//    		},
//            
//    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		dataType: "html"
//    	});
//    	
//    }
//    
//    /*************************************************************************************************************************/
//    /*************************************************************************************************************************/
//    /*************************************************************************************************************************/
//    function listeDesSoins() {
//    	$('#listeDesExamens').dataTable
//     	( {
//     					"sPaginationType": "full_numbers",
//     					"aLengthMenu": [3,5,7],
//     					"iDisplayLength": 3,
//     					"aaSorting": [], //On ne trie pas la liste automatiquement
//     					"oLanguage": {
//     						"sInfoFiltered": "",
//     						"sUrl": "",
//     						"oPaginate": {
//     							"sFirst":    "|<",
//     							"sPrevious": "<",
//     							"sNext":     ">",
//     							"sLast":     ">|"
//     							}
//     					   },
//     					  "bDestroy": true,
//     	});
//    	
//    }
//    
//    function listepatient(){
//	    
//    	$("#terminer").click(function(){
//    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
//	    	$("#vue_patient").fadeOut(function(){
//	    		$("#contenu").fadeIn("fast"); 
//	    	
//	    	    $('div .dataTables_paginate').css({ 'margin-right' : '0'});
//	    		$('#listeDataTable').css({'margin-left' : '-10'});
//	    		
//	    		//setTimeout(function(){
//		    	  //vart=tabUrl[0]+'public/hospitalisation/liste-demandes-examens';
//				  //$(location).attr("href",vart);
//	    		//}, 1500);
//	    	});
//	    });
//    	
//	    $("#terminerdetailhospi").click(function(){
//	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
//  	    	$("#vue_detail_hospi_patient").fadeOut(function(){
//  	    		$("#contenu").fadeIn("fast"); 
//
//  	    		$('div .dataTables_paginate').css({ 'margin-right' : '0'});
//  	    		$('#listeDataTable').css({'margin-left' : '-10'});
//  	    	});
//	    });
//	    
//    }
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    function administrerSoin(id_personne){ 
//    	var id_cons = $("#"+id_personne).val();
//    	var id_demande_hospi = $("#"+id_personne+"dh").val();
//    	var chemin = tabUrl[0]+'public/hospitalisation/administrer-soin-patient';
//    	$.ajax({
//    		type: 'POST',
//    		url: chemin ,
//    		data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'id_demande_hospi':id_demande_hospi},
//    		success: function(data) {
//    			$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> APPLICATION DES SOINS </div>");
//    			var result = jQuery.parseJSON(data);
//    			$("#contenu").fadeOut(function(){$("#vue_detail_hospi_patient").html(result).fadeIn("fast"); }); 
//    		},
//    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		dataType: "html"
//    	});
//    }
//    
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    /****** GESTION DES DEPLIANTS DE L'AFFICHAGE DES INFORMATIONS APRES LIBERATION DU PATIENT******/
//    
//    /**INFO HOSPITALISATION**/
//    function depliantPlus2() {
//    	$('#titre_info_hospitalisation').click(function(){
//    		$("#titre_info_hospitalisation").replaceWith(
//    			"<span id='titre_info_hospitalisation' style='margin-left:-10px; cursor:pointer;'>" +
//    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Infos sur l'hospitalisation "+
//    		    "</span>");
//    		animationPliantDepliant2();
//    		$('#info_hospitalisation').animate({
//    			height : 'toggle'
//    		},1000);
//    		return false;
//    	});
//    }
//    
//    function animationPliantDepliant2() {
//    	$('#titre_info_hospitalisation').click(function(){
//    		$("#titre_info_hospitalisation").replaceWith(
//    			"<span id='titre_info_hospitalisation' style='margin-left:-10px; cursor:pointer;'>" +
//    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Infos sur l'hospitalisation"+
//    		    "</span>");
//    		depliantPlus2();
//    		$('#info_hospitalisation').animate({
//    			height : 'toggle'
//    		},1000);
//    		return false;
//    	});
//    }
//    
//    /**INFO LISTE**/
//    function depliantPlus3() {
//    	$('#titre_info_liste').click(function(){
//    		$("#titre_info_liste").replaceWith(
//    			"<span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>" +
//    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Liste des soins "+
//    		    "</span>");
//    		animationPliantDepliant3();
//    		$('#info_liste').animate({
//    			height : 'toggle'
//    		},1000);
//    		return false;
//    	});
//    }
//    
//    function animationPliantDepliant3() {
//    	$('#titre_info_liste').click(function(){
//    		$("#titre_info_liste").replaceWith(
//    			"<span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>" +
//    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Liste des soins "+
//    		    "</span>");
//    		depliantPlus3();
//    		$('#info_liste').animate({
//    			height : 'toggle'
//    		},1000);
//    		return false;
//    	});
//    }
//    
//    /**INFO DEMANDE**/
//    function depliantPlus4() {
//    	$('#titre_info_demande').click(function(){
//    		$("#titre_info_demande").replaceWith(
//    			"<span id='titre_info_demande' style='margin-left:-10px; cursor:pointer;'>" +
//    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> D&eacute;tails des infos sur la demande "+
//    		    "</span>");
//    		animationPliantDepliant4();
//    		$('#info_demande').animate({
//    			height : 'toggle'
//    		},1000);
//    		return false;
//    	});
//    }
//    
//    function animationPliantDepliant4() {
//    	$('#titre_info_demande').click(function(){
//    		$("#titre_info_demande").replaceWith(
//    			"<span id='titre_info_demande' style='margin-left:-10px; cursor:pointer;'>" +
//    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> D&eacute;tails des infos sur la demande "+
//    		    "</span>");
//    		depliantPlus4();
//    		$('#info_demande').animate({
//    			height : 'toggle'
//    		},1000);
//    		return false;
//    	});
//    }
//    
//    function initAnimation() {
//    	$('#info_hospitalisation').toggle(false);
//    	$('#info_demande').toggle(false);
//    }
//    
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    function EffectuerExamen(idDemande){
//    	$( "#application_examen" ).dialog({
//    		resizable: false,
//    		height:350,
//    		width:600,
//    		autoOpen: false,
//    		modal: true,
//    		buttons: {
//    			"Terminer": function() {
//    				if( controleSaisi() ) {
//    					
//    				var technique_utilise = $('#technique_utilise').val();
//    				var resultat = $('#resultat').val();
//    				var conclusion = $('#conclusion').val();
//
//    				var chemin = tabUrl[0]+'public/hospitalisation/appliquer-examen';
//    		    	$.ajax({
//    		    		type: 'POST',
//    		    		url: chemin ,
//    		    		data:({'idDemande':idDemande, 'techniqueUtiliser':technique_utilise, 'noteResultat':resultat, 'conclusion':conclusion, 'update':0}),
//    		    		success: function() {    
//
//    		    			var id_cons = $('#Examen_id_cons').val();
//    		    			
//    		    			var chemin = tabUrl[0]+'public/hospitalisation/raffraichir-liste-examens';
//    	    		    	$.ajax({
//    	    		    		type: 'POST',
//    	    		    		url: chemin ,
//    	    		    		data:({'id_cons':id_cons}),
//    	    		    		success: function(data) {    
//    	    		    			var result = jQuery.parseJSON(data);
//    	    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
//    	    		    			
//    	    		    			$('#technique_utilise').val('');
//    	    		    			$('#resultat').val('');
//    	    		    			$('#conclusion').val('');
//    	    		    		},
//    	    		            
//    	    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    	    		    		dataType: "html"
//    	    		    	});
//    		    			
//    		    		},
//    		            
//    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		    		dataType: "html"
//    		    	});
//    				
//    		    	$( this ).dialog( "close" );             	     
//    				return false;
//    				
//    				}
//    			},
//    			
//    			"Annuler": function() {
//    				var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
//    		    	tooltips.tooltip( "close" );
//    				$( this ).dialog( "close" );             	     
//    				return false;
//    			}
//    		}
//    	});
//    }
//   
//    function appliquerExamen(idDemande) {
//    	$("#technique_utilise, #resultat, #conclusion").css("border-color","");
//    	$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
//    	
//    	$('#technique_utilise').val('');
//		$('#resultat').val('');
//		$('#conclusion').val('');
//    	EffectuerExamen(idDemande);
//		$("#application_examen").dialog('open'); 
//    }
//    
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    function modifierDonneesExamen(idDemande){
//    	$( "#application_examen" ).dialog({
//    		resizable: false,
//    		height:350,
//    		width:600,
//    		autoOpen: false,
//    		modal: true,
//    		buttons: {
//    			"Terminer": function() {
//    				if( controleSaisi() ) {
//    					
//    				var technique_utilise = $('#technique_utilise').val();
//    				var resultat = $('#resultat').val();
//    				var conclusion = $('#conclusion').val();
//
//    				var chemin = tabUrl[0]+'public/hospitalisation/appliquer-examen';
//    		    	$.ajax({
//    		    		type: 'POST',
//    		    		url: chemin ,
//    		    		data:({'idDemande':idDemande, 'techniqueUtiliser':technique_utilise, 'noteResultat':resultat, 'conclusion':conclusion, 'update':1}),
//    		    		success: function() {    
//
//    		    			var id_cons = $('#Examen_id_cons').val();
//    		    			
//    		    			var chemin = tabUrl[0]+'public/hospitalisation/raffraichir-liste-examens';
//    	    		    	$.ajax({
//    	    		    		type: 'POST',
//    	    		    		url: chemin ,
//    	    		    		data:({'id_cons':id_cons}),
//    	    		    		success: function(data) {    
//    	    		    			var result = jQuery.parseJSON(data);
//    	    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
//    	    		    			
//    	    		    			$('#technique_utilise').val('');
//    	    		    			$('#resultat').val('');
//    	    		    			$('#conclusion').val('');
//    	    		    		},
//    	    		            
//    	    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    	    		    		dataType: "html"
//    	    		    	});
//    		    			
//    		    		},
//    		            
//    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		    		dataType: "html"
//    		    	});
//    				
//    		    	$( this ).dialog( "close" );             	     
//    				return false;
//    				
//    			    }
//    			},
//    			
//    			"Annuler": function() {
//    				
//    		    	var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
//    		    	tooltips.tooltip( "close" );
//    		    		
//    				$( this ).dialog( "close" );             	     
//    				return false;
//    			}
//    		}
//    	});
//    }
//    
//    function modifierExamen(idDemande) {
//    	$("#technique_utilise, #resultat, #conclusion").css("border-color","");
//    	$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
//    	
//    	var chemin = tabUrl[0]+'public/hospitalisation/modifier-examen';
//    	$.ajax({
//    		type: 'POST',
//    		url: chemin ,
//    		data:({'idDemande':idDemande}),
//    		success: function(data) {    
//    			var result = jQuery.parseJSON(data);
//    			$("#script_donnees").html(result);
//    			
//    			modifierDonneesExamen(idDemande);
//    			$("#application_examen").dialog('open'); 
//    		},
//            
//    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		dataType: "html"
//    	});
//    }
//    
//    /**
//     * Lors d'un hover
//     */
//    function hover() {
//    	$('#technique_utilise, #resultat, #conclusion').hover(function(){
//    		$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
//    		var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
//    		tooltips.tooltip( "hide" );
//    	});
//    }
//    
//    /**
//     * Lors d'un click
//     */
//    function click() {
//    	$('#technique_utilise, #resultat, #conclusion').click(function(){
//			var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
//			tooltips.tooltip( "close" );
//			$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
//	    });
//    }
//    
//    function controleSaisi() {
//    	
//    	hover();
//    	click();
//    	
//    	$("#technique_utilise, #resultat, #conclusion").css("border-color","");
//    	
//    	if(!$('#technique_utilise').val()){
//    		$("#technique_utilise").css("border-color","#FF0000");
//    		$('#technique_utilise').attr({'title': 'Ce champ est requis'});
//    			var tooltips = $( "#technique_utilise" ).tooltip();
//    			tooltips.tooltip( "open" );
//    			
//    	}else if(!$('#resultat').val()){
//    		$("#resultat").css("border-color","#FF0000");
//    		$('#resultat').attr({'title': 'Ce champ est requis'});
//    			var tooltips = $( "#resultat" ).tooltip();
//    			tooltips.tooltip( "open" );
//    			
//        		$("#technique_utilise").css("border-color","");
//        		
//    	}else if(!$('#conclusion').val()){
//    		$("#conclusion").css("border-color","#FF0000");
//    		$('#conclusion').attr({'title': 'Ce champ est requis'});
//    			var tooltips = $( "#conclusion" ).tooltip();
//    			tooltips.tooltip( "open" );
//    			
//        		$("#resultat").css("border-color","");
//        		
//    	}else {
//    		
//    		return true;
//    	}
//    }
//    
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    function EnvoyerExamen(idDemande){
//    	$( "#envoyer_examen" ).dialog({
//    		resizable: false,
//    		height:180,
//    		width:460,
//    		autoOpen: false,
//    		modal: true,
//    		buttons: {
//    			"Terminer": function() {
//
//    				var id_cons = $('#Examen_id_cons').val();
//    				var chemin = tabUrl[0]+'public/hospitalisation/envoyer-examen';
//    		    	$.ajax({
//    		    		type: 'POST',
//    		    		url: chemin ,
//    		    		data:({'idDemande':idDemande, 'id_cons':id_cons}),
//    		    		success: function(data) {    
//    		    			var result = jQuery.parseJSON(data);
//    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
//    		    			
//    		    		},
//    		            
//    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		    		dataType: "html"
//    		    	});
//    				
//    		    	$( this ).dialog( "close" );             	     
//    				return false;
//    			},
//    			
//    			"Annuler": function() {
//    				$( this ).dialog( "close" );             	     
//    				return false;
//    			}
//    		}
//    	});
//    }
//    function envoyer(idDemande){
//    	EnvoyerExamen(idDemande);
//		$("#envoyer_examen").dialog('open'); 
//    }
//    
//    
//    /********** POUR LES EXAMENS BIOLOGIQUES *** POUR LES EXAMENS BIOLOGIQUES *** POUR LES EXAMENS BIOLOGIQUES **************/
//    /********** POUR LES EXAMENS BIOLOGIQUES *** POUR LES EXAMENS BIOLOGIQUES *** POUR LES EXAMENS BIOLOGIQUES **************/
//    /********** POUR LES EXAMENS BIOLOGIQUES *** POUR LES EXAMENS BIOLOGIQUES *** POUR LES EXAMENS BIOLOGIQUES **************/
//    
//    /** APPLICATION DE L'EXAMEN --- APPLICATION DE L'EXAMEN**/
//    /** APPLICATION DE L'EXAMEN --- APPLICATION DE L'EXAMEN**/
//    /** APPLICATION DE L'EXAMEN --- APPLICATION DE L'EXAMEN**/
//    function listeExamensBio(id_personne, idDemande){ 
//    	var id_cons = $("#"+idDemande).val();
//    	var chemin = tabUrl[0]+'public/hospitalisation/liste-examens-demander';
//        $.ajax({
//            type: 'POST',
//            url: chemin ,
//            data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'examensBio': 1},
//            success: function(data) {
//           	         
//            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> AJOUTER LES R&Eacute;SULTATS DES EXAMENS </div>");
//            	var result = jQuery.parseJSON(data);
//            	$("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
//            	     
//            },
//            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//            dataType: "html"
//        });
//    }
//    
//    function EffectuerExamenBio(idDemande){
//    	$( "#application_examen" ).dialog({
//    		resizable: false,
//    		height:350,
//    		width:600,
//    		autoOpen: false,
//    		modal: true,
//    		buttons: {
//    			"Terminer": function() {
//    				if( controleSaisi() ) {
//    					
//    				var technique_utilise = $('#technique_utilise').val();
//    				var resultat = $('#resultat').val();
//    				var conclusion = $('#conclusion').val();
//
//    				var chemin = tabUrl[0]+'public/hospitalisation/appliquer-examen';
//    		    	$.ajax({
//    		    		type: 'POST',
//    		    		url: chemin ,
//    		    		data:({'idDemande':idDemande, 'techniqueUtiliser':technique_utilise, 'noteResultat':resultat, 'conclusion':conclusion, 'update':0}),
//    		    		success: function() {    
//
//    		    			var id_cons = $('#Examen_id_cons').val();
//    		    			
//    		    			var chemin = tabUrl[0]+'public/hospitalisation/raffraichir-liste-examens-bio';
//    	    		    	$.ajax({
//    	    		    		type: 'POST',
//    	    		    		url: chemin ,
//    	    		    		data:({'id_cons':id_cons}),
//    	    		    		success: function(data) {    
//    	    		    			var result = jQuery.parseJSON(data);
//    	    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
//    	    		    			
//    	    		    			$('#technique_utilise').val('');
//    	    		    			$('#resultat').val('');
//    	    		    			$('#conclusion').val('');
//    	    		    		},
//    	    		            
//    	    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    	    		    		dataType: "html"
//    	    		    	});
//    		    			
//    		    		},
//    		            
//    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		    		dataType: "html"
//    		    	});
//    				
//    		    	$( this ).dialog( "close" );             	     
//    				return false;
//    				
//    				}
//    			},
//    			
//    			"Annuler": function() {
//    				var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
//    		    	tooltips.tooltip( "close" );
//    				$( this ).dialog( "close" );             	     
//    				return false;
//    			}
//    		}
//    	});
//    }
//    
//    function appliquerExamenBio(idDemande) {
//    	$("#technique_utilise, #resultat, #conclusion").css("border-color","");
//    	$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
//    	
//    	$('#technique_utilise').val('');
//		$('#resultat').val('');
//		$('#conclusion').val('');
//		
//		//*** CACHER L'ICONE INSERTION D'IMAGES ***//
//		$('#imagesExamensMorphologiques').toggle(false);
//		
//    	EffectuerExamenBio(idDemande);
//		$("#application_examen").dialog('open'); 
//    }
//    
//    
//    /** ENVOIE --- ENVOIE --- ENVOIE **/
//    /** ENVOIE --- ENVOIE --- ENVOIE **/
//    /** ENVOIE --- ENVOIE --- ENVOIE **/
//    function EnvoyerExamenBio(idDemande){
//    	$( "#envoyer_examen" ).dialog({
//    		resizable: false,
//    		height:180,
//    		width:460,
//    		autoOpen: false,
//    		modal: true,
//    		buttons: {
//    			"Terminer": function() {
//
//    				var id_cons = $('#Examen_id_cons').val();
//    				var chemin = tabUrl[0]+'public/hospitalisation/envoyer-examen-bio';
//    		    	$.ajax({
//    		    		type: 'POST',
//    		    		url: chemin ,
//    		    		data:({'idDemande':idDemande, 'id_cons':id_cons}),
//    		    		success: function(data) {    
//    		    			var result = jQuery.parseJSON(data);
//    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
//    		    			
//    		    		},
//    		            
//    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		    		dataType: "html"
//    		    	});
//    				
//    		    	$( this ).dialog( "close" );             	     
//    				return false;
//    			},
//    			
//    			"Annuler": function() {
//    				$( this ).dialog( "close" );             	     
//    				return false;
//    			}
//    		}
//    	});
//    }
//    function envoyerBio(idDemande){
//    	EnvoyerExamenBio(idDemande);
//		$("#envoyer_examen").dialog('open'); 
//    }
//    
//    
//    /** MODIFICATION --- MODIFICATION --- MODIFICATION **/
//    /** MODIFICATION --- MODIFICATION --- MODIFICATION **/
//    /** MODIFICATION --- MODIFICATION --- MODIFICATION **/
//    function modifierDonneesExamenBio(idDemande){
//    	$( "#application_examen" ).dialog({
//    		resizable: false,
//    		height:350,
//    		width:600,
//    		autoOpen: false,
//    		modal: true,
//    		buttons: {
//    			"Terminer": function() {
//    				if( controleSaisi() ) {
//    					
//    				var technique_utilise = $('#technique_utilise').val();
//    				var resultat = $('#resultat').val();
//    				var conclusion = $('#conclusion').val();
//
//    				var chemin = tabUrl[0]+'public/hospitalisation/appliquer-examen';
//    		    	$.ajax({
//    		    		type: 'POST',
//    		    		url: chemin ,
//    		    		data:({'idDemande':idDemande, 'techniqueUtiliser':technique_utilise, 'noteResultat':resultat, 'conclusion':conclusion, 'update':1}),
//    		    		success: function() {    
//
//    		    			var id_cons = $('#Examen_id_cons').val();
//    		    			
//    		    			var chemin = tabUrl[0]+'public/hospitalisation/raffraichir-liste-examens-bio';
//    	    		    	$.ajax({
//    	    		    		type: 'POST',
//    	    		    		url: chemin ,
//    	    		    		data:({'id_cons':id_cons}),
//    	    		    		success: function(data) {    
//    	    		    			var result = jQuery.parseJSON(data);
//    	    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
//    	    		    			
//    	    		    			$('#technique_utilise').val('');
//    	    		    			$('#resultat').val('');
//    	    		    			$('#conclusion').val('');
//    	    		    		},
//    	    		            
//    	    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    	    		    		dataType: "html"
//    	    		    	});
//    		    			
//    		    		},
//    		            
//    		    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		    		dataType: "html"
//    		    	});
//    				
//    		    	$( this ).dialog( "close" );             	     
//    				return false;
//    				
//    			    }
//    			},
//    			
//    			"Annuler": function() {
//    				
//    		    	var tooltips = $( "#technique_utilise, #resultat, #conclusion" ).tooltip();
//    		    	tooltips.tooltip( "close" );
//    		    		
//    				$( this ).dialog( "close" );             	     
//    				return false;
//    			}
//    		}
//    	});
//    }
//    
//    function modifierExamenBio(idDemande) {
//    	$("#technique_utilise, #resultat, #conclusion").css("border-color","");
//    	$("#technique_utilise, #resultat, #conclusion").attr({'title':''});
//    	
//    	//*** CACHER L'ICONE INSERTION D'IMAGES ***//
//		$('#imagesExamensMorphologiques').toggle(false);
//		
//    	var chemin = tabUrl[0]+'public/hospitalisation/modifier-examen';
//    	$.ajax({
//    		type: 'POST',
//    		url: chemin ,
//    		data:({'idDemande':idDemande}),
//    		success: function(data) {    
//    			var result = jQuery.parseJSON(data);
//    			$("#script_donnees").html(result);
//    			
//    			modifierDonneesExamenBio(idDemande);
//    			$("#application_examen").dialog('open'); 
//    		},
//            
//    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		dataType: "html"
//    	});
//    }
//    
//
//    /** VISUALISATION --- VISUALISATION --- VISUALISATION **/
//    /** VISUALISATION --- VISUALISATION --- VISUALISATION **/
//    /** VISUALISATION --- VISUALISATION --- VISUALISATION **/
//    function vueExamenBio(idDemande){
//    	var chemin = tabUrl[0]+'public/hospitalisation/verifier-si-resultat-existe';
//    	$.ajax({
//    		type: 'POST',
//    		url: chemin ,
//    		data:({'idDemande':idDemande}),
//    		success: function(data) {    
//    			var result = jQuery.parseJSON(data);   
//    			
//    			var hauteur = 345;
//				var largeur = 500;
//    			if(result == 1) {
//    				 hauteur = 560;
//    				 largeur = 650;
//    			}
//    			
//    			vueExamenAAppliquer(hauteur, largeur);
//    	    	var chemin = tabUrl[0]+'public/hospitalisation/vue-examen-appliquer';
//    	    	$.ajax({
//    	    		type: 'POST',
//    	    		url: chemin ,
//    	    		data:({'idDemande':idDemande}),
//    	    		success: function(data) {    
//    	    			var result = jQuery.parseJSON(data);   
//    	    			$("#info").html(result);
//    	    			//CECHER L'ICONE IMAGE 
//    	    			$('#visualisationImageResultats').toggle(false);
//    	    			$("#informations").dialog('open'); 
//    	    		},
//    	            
//    	    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    	    		dataType: "html"
//    	    	});
//    			
//    	    	
//    		},
//            
//    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//    		dataType: "html"
//    	});
//    	
//    }
//    
//    
//    /** POUR LA LISTE DES EXAMENS DEJA EFFECTUES --- POUR LA LISTE DES EXAMENS DEJA EFFECTUES **/
//    /** POUR LA LISTE DES EXAMENS DEJA EFFECTUES --- POUR LA LISTE DES EXAMENS DEJA EFFECTUES **/
//    /** POUR LA LISTE DES EXAMENS DEJA EFFECTUES --- POUR LA LISTE DES EXAMENS DEJA EFFECTUES **/
//    function initialisationListeRehercheExamensEffectues(){
//        var  oTable = $('#patient').dataTable
//    	( {
//    					"sPaginationType": "full_numbers",
//    					"aLengthMenu": [5,7,10,15],
//     					"aaSorting": [], //On ne trie pas la liste automatiquement
//    					"oLanguage": {
//    						"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
//    						"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
//    						"sInfoFiltered": "",
//    						"sUrl": "",
//    						"oPaginate": {
//    							"sFirst":    "|<",
//    							"sPrevious": "<",
//    							"sNext":     ">",
//    							"sLast":     ">|"
//    							}
//    					   },
//
//    					"sAjaxSource": ""+tabUrl[0]+"public/hospitalisation/liste-recherche-examens-effectues-ajax", 
//    					
//    	}); 
//        
//        var asInitVals = new Array();
//    
//   	//le filtre du select
//   	$('#filter_statut').change(function() 
//   	{					
//   		oTable.fnFilter( this.value );
//   	});
//
//   	//le filtre du select du type personnel
//	$('#type_personnel').change(function() 
//	{					
//		oTable.fnFilter( this.value );
//	});
//   	
//   	$("tfoot input").keyup( function () {
//   		/* Filter on the column (the index) of this element */
//   		oTable.fnFilter( this.value, $("tfoot input").index(this) );
//   	} );
//   	
//   	/*
//   	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
//   	 * the footer
//   	 */
//   	$("tfoot input").each( function (i) {
//   		asInitVals[i] = this.value;
//   	} );
//   	
//   	$("tfoot input").focus( function () {
//   		if ( this.className == "search_init" )
//   		{
//   			this.className = "";
//   			this.value = "";
//   		}
//   		
//   	} );
//   	
//   	$("tfoot input").blur( function (i) {
//   		if ( this.value == "" )
//   		{
//   			this.className = "search_init";
//   			this.value = asInitVals[$("tfoot input").index(this)];
//   		}
//   	} );
//
//    $("#annuler").click(function(){
//    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
//	    $("#hospitaliser").fadeOut(function(){$("#contenu").fadeIn("fast"); $("#division").val(""); $("#salle,#lit").html("");});
//	    return false;
//	});
//      
//    }