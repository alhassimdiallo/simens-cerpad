    var base_url = window.location.toString();
    var tabUrl = base_url.split("public");
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    $(function(){
    	initialisation();
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

    					"sAjaxSource": ""+tabUrl[0]+"public/archivage/liste-patient-suivi-ajax", 
    					
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
    function affichervue(id_demande_hospi){ 
    	var id_cons = $("#"+id_demande_hospi).val();
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	var chemin = tabUrl[0]+'public/archivage/info-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'id_demande_hospi':id_demande_hospi},
            success: function(data) {
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> INFORMATIONS </div>");
            	var result = jQuery.parseJSON(data); 
            	$("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
     }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function vueSoinAppliquer(x, y){
    	$( "#informations" ).dialog({
    		resizable: false,
    		width:x,
    		height:y,
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
    
    function vuesoin(id_sh){
    	vueSoinAppliquer(750, 650);
    	var chemin = tabUrl[0]+'public/archivage/vue-soin-appliquer';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:({'id_sh':id_sh}),
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);   
    			$("#info").html(result);
    			$("#informations").dialog('open'); 
    		},
            
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
    	});
    }
    
    function vuesoinApp(id_sh){
    	vueSoinAppliquer(750, 640);
    	var chemin = tabUrl[0]+'public/archivage/vue-soin-appliquer';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:({'id_sh':id_sh}),
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);   
    			$("#info").html(result);
    			$("#informations").dialog('open'); 
    		},
            
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
    	});
    }
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    function listeDesSoins() {
    	$('#listeSoin').dataTable
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
    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
	    	$("#vue_patient").fadeOut(function(){$("#contenu").fadeIn("fast"); });
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
    function administrerSoin(id_demande_hospi){ 
    	var id_cons = $("#"+id_demande_hospi).val();
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	var chemin = tabUrl[0]+'public/archivage/administrer-soin-patient';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'id_demande_hospi':id_demande_hospi},
    		success: function(data) {
    			$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> APPLICATION DES SOINS </div>");
    			var result = jQuery.parseJSON(data);
    			$("#contenu").fadeOut(function(){$("#vue_detail_hospi_patient").html(result).fadeIn("fast"); }); 
    		},
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
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
    function ApplicationSoin(id_sh, id_hosp, id_heure){
    	$( "#application_soin" ).dialog({
    		resizable: false,
    		height:275,
    		width:300,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Terminer": function() {
    				
    				var note = $('#note').val();
    				var chemin = tabUrl[0]+'public/archivage/application-soin';
    		    	$.ajax({
    		    		type: 'POST',
    		    		url: chemin ,
    		    		data:({'id_sh':id_sh, 'note':note, 'id_heure':id_heure}),
    		    		success: function() {    

    		    			var chemin = tabUrl[0]+'public/archivage/raffraichir-liste';
    	    		    	$.ajax({
    	    		    		type: 'POST',
    	    		    		url: chemin ,
    	    		    		data:({'id_hosp':id_hosp}),
    	    		    		success: function(data) {    
    	    		    			var result = jQuery.parseJSON(data);
    	    		    			$("#info_liste").fadeOut(function(){$("#info_liste").html(result).fadeIn("fast"); });
    	    		    			$('#note').val('');
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
    			},
    			
    			"Annuler": function() {
    				$( this ).dialog( "close" );             	     
    				return false;
    			}
    		}
    	});
    }
   
    function appliquerSoin(id_sh, id_hosp, id_heure) {
    	ApplicationSoin(id_sh, id_hosp, id_heure);
		
		var chemin = tabUrl[0]+'public/archivage/heure-suivante';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:({'id_hosp':id_hosp , 'id_sh':id_sh, 'id_heure':id_heure}),
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);
    			$('#HeureActu').html(result);
    			$("#application_soin").dialog('open');
    		},
            
    		error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
    		dataType: "html"
    	});
    }