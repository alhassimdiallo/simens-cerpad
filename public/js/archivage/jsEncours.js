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

    					"sAjaxSource": ""+tabUrl[0]+"public/archivage/liste-patient-encours-ajax", 
    					
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

    $("#terminersoin").click(function(){
    	var tooltips = $( "#id_soins, #duree, #date_recommandee, #heure_recommandee" ).tooltip();
    	tooltips.tooltip( "close" );
    	$("#id_soins, #duree, #date_recommandee, #heure_recommandee").attr({'title':''});
    	
    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
    	
    	$("#hospitaliser").css({'visibility':'hidden'});
        $("#contenu").fadeIn("fast"); $("#division").val(""); $("#salle,#lit").html("");
	    $('div .dataTables_paginate').css({ 'margin-right' : '0'});
	    	
    	$('#listeDataTable').css({'margin-left' : '-10'});
    	
 		$("#id_soins, #duree, #date_recommandee, #heure_recommandee").css("border-color","");
 		$("#id_soins, #duree, #date_recommandee, #heure_recommandee").val('');
	    return false;
	});
    
    listepatient();
  
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
    
    function listepatient(){
	    $("#terminer").click(function(){
	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
  	    	$("#vue_patient").fadeOut(function(){
  	    		$("#contenu").fadeIn("fast"); 
  	    	});
  	    });
	    
	    $('#date_recommandee, #date_recommandee_m, #date_application, #date_application_m').datepicker($.datepicker.regional['fr'] = {
				closeText: 'Fermer',
				changeYear: true,
				yearRange: 'c-80:c',
				prevText: '&#x3c;Préc',
				nextText: 'Suiv&#x3e;',
				currentText: 'Courant',
				monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
				'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
				monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
				'Jul','Aout','Sep','Oct','Nov','Dec'],
				dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
				dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
				dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
				weekHeader: 'Sm',
				dateFormat: 'dd/mm/yy',
				firstDay: 1,
				isRTL: false,
				showMonthAfterYear: false,
				yearRange: '1990:2015',
				showAnim : 'bounce',
				changeMonth: true,
				changeYear: true,
				yearSuffix: ''
		});
	    
	    $("#terminerLiberer").click(function(){
	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
  	    	$("#vue_liberer_patient").fadeOut(function(){
  	    		$("#contenu").fadeIn("fast"); 
  	    	});
  	    	return false;
  	    });
	    
	    $("#terminerdetailhospi").click(function(){
  	    	vart=tabUrl[0]+'public/archivage/administrer-soin';
		    $(location).attr("href",vart);
  	    });
	    
    }
    
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    function getsalle(id_batiment){
      var chemin = tabUrl[0]+'public/archivage/salles';
      $.ajax({
        type: 'POST',
        url: chemin ,
        data:'id_batiment='+id_batiment,
        success: function(data) {
        	     var result = jQuery.parseJSON(data);  
        	     $("#salle").html(result); 
        },
        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        dataType: "html"
      });
    }
    
    function getlit(id_salle){ 
    	var chemin = tabUrl[0]+'public/archivage/lits';
    	$.ajax({
            type: 'POST',
            url: chemin ,
            data:'id_salle='+id_salle,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);  
            	     $("#lit").html(result); 
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function administrerSoin(id_demande_hospi){
    	var id_hosp = $("#"+id_demande_hospi+"hp").val(); 
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	var chemin = tabUrl[0]+'public/archivage/info-patient-hospi';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'administrerSoin':111},
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> ADMINISTRER DES SOINS </div>");
            	var result = jQuery.parseJSON(data);
            	$("#vue_patient_hospi").html(result);
            	$("#division,#salle,#lit").val("");
            	$("#code_demande").val($("#"+id_demande_hospi+"dh").val());
            	listeSoinsPrescrits(id_hosp);
            	$("#contenu").fadeOut(function(){
            		$("#hospitaliser").css({'visibility':'visible'});
            	}); 
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        $('#id_soins').val('');
        $('#id_hosp').val(id_hosp);
	    $('#date_recommandee, #heure_recommandee, #id_soins, #duree, #note, #motif').css({'font-weight':'bold','color':'#065d10','font-family': 'Times  New Roman','font-size':'16px'});
	    controle_saisie();
    }
    
    function listeSoinsPrescrits(id_hosp) { 
    	var chemin = tabUrl[0]+'public/archivage/liste-soins-prescrits';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_hosp': id_hosp},
            success: function(data) {
            	var result = jQuery.parseJSON(data);
            	$("#liste_soins").html(result); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        
    }
    
    function listeSoins(id_hosp) { 
    	var chemin = tabUrl[0]+'public/archivage/liste-soin';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_hosp': id_hosp},
            success: function(data) {
            	var result = jQuery.parseJSON(data);
            	$("#liste_soins").html(result); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        
    }
    
    function listeDesSoins() {
    	$('#listeSoin').dataTable
     	( {
     					"sPaginationType": "full_numbers",
     					"aLengthMenu": [3,5,7],
     					"iDisplayLength": 3,
     					"aaSorting": [], //On ne trie pas la liste automatiquement
     					"oLanguage": {
     						//"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
     						//"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
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
    
    function vider_tout() {
    	$('#medicament, #voie_administration, #frequence, #dosage, #date_application, #motif_, #note_').val('');

    	//POUR LA SUPPRESSION DES ELEMENTS SELECTIONNES SUR LA LISTE
    	for(var j = 0; j < 24; j++){
    		$('.SlectBox')[0].sumo.unSelectItem(j);
    	}
    	//POUR LA SUPPRESSION DES ICONES COCHES SUR LA LISTE
    	$(function(){
            $('select.SlectBox')[0].sumo.unload();
            $('.SlectBox').SumoSelect({ csvDispCount: 6 });
           });
    }
    
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    function confirmation(){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:370,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );
	            
	            	var chemin = tabUrl[0]+'public/archivage/administrer-soin';
	            	var id_sh = $('#id_sh').val();
	            	var id_hosp = $('#id_hosp').val();
	            	
	            	var medicament = $('#medicament').val();
	            	var voie_administration = $('#voie_administration').val();
	            	var frequence = $('#frequence').val();
	            	var dosage = $('#dosage').val();
	            	var date_application = $('#date_application').val();
	            	var heure_recommandee = $('#heure_recommandee_').val();
	            	var motif = $('#motif_').val();
	            	var note = $('#note_').val();
	            	
	            	$.ajax({
	                    type: 'POST',
	                    url: chemin ,
	                    data:{'id_sh':id_sh, 'id_hosp':id_hosp, 
	                    	  'medicament':medicament, 'voie_administration':voie_administration, 
	                    	  'frequence':frequence, 'dosage':dosage, 'date_application':date_application,
	                    	  'heure_recommandee':heure_recommandee, 'motif': motif, 'note':note
	                    	  },
	                    success: function() {
	                    	 listeSoinsPrescrits(id_hosp);
	                    	 vider_tout();
	                    },
	                    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	                    dataType: "html"
	                });
	            	
	        },
	        "Annuler": function() {
                $( this ).dialog( "close" );
            }
	   }
	  });
    }
    
    /**
     * Lors d'un hover
     */
    function hover() {
    	$('#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_').hover(function(){
    		$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_").attr({'title':''});
    		var tooltips = $( "#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_" ).tooltip();
    		tooltips.tooltip( "hide" );
    	});
    }
    
    /**
     * Lors d'un click
     */
    function click() {
    	$('#titre_info_admis, #medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_').click(function(){
			var tooltips = $( "#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_" ).tooltip();
			tooltips.tooltip( "close" );
			$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_").attr({'title':''});
	    });
    	
    	$('#date_application').mouseover(function(){
    		var tooltips = $( "#date_application" ).tooltip();
			tooltips.tooltip( "hide" );
    	});
    }
    
    function ajouter() {
    	
    	hover();
    	click();
    	
		$("#medicament, #voie_administration, #frequence, #dosage, #date_application, #heure_recommandee_").css("border-color","");
    	if(!$('#medicament').val()){
    		$("#medicament").css("border-color","#FF0000");
    		$('#medicament').attr({'title': 'Veuillez ajouter un médicament'});
    			var tooltips = $( "#medicament" ).tooltip();
    			tooltips.tooltip( "open" );
    			
    	}else if(!$('#voie_administration').val()){
    		$("#voie_administration").css("border-color","#FF0000");
    		$('#voie_administration').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#voie_administration" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#medicament").css("border-color","");
        		
    	}else if(!$('#frequence').val()){
    		$("#frequence").css("border-color","#FF0000");
    		$('#frequence').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#frequence" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#voie_administration").css("border-color","");
        		
    	}else if(!$('#dosage').val()){
    		$("#dosage").css("border-color","#FF0000");
    		$('#dosage').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#dosage" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#frequence").css("border-color","");
        		
    	}else if(!$('#date_application').val()){
    		$("#date_application").css("border-color","#FF0000");
    		$('#date_application').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#date_application" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#dosage").css("border-color","");
        		
    	}else if(!$('#heure_recommandee_').val()){
    		$("#heure_recommandee_").css("border-color","#FF0000");
    		$('#heure_recommandee_').attr({'title': 'Ce champ est requis'});
    			//var tooltips = $( "#heure_recommandee_" ).tooltip();
    			//tooltips.tooltip( "open" );
    			
        		$("#date_application").css("border-color","");
        		
    	}else {
    			confirmation();
    			$("#confirmation").dialog('open');
    	}

   	}
    

    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    	function vueSoinAppliquer(x, y){
        	$( "#informations" ).dialog({
        	    resizable: false,
        	    width: x, 
        	    height: y, 
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
            var chemin = tabUrl[0]+'public/consultation/vue-soin-appliquer';
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
        
        
        /************************************************************************************************************************/
        /************************************************************************************************************************/
        /************************************************************************************************************************/
        	function supprimersoinConfirm(id_sh,id_hosp){
            	$( "#confirmationSup" ).dialog({
            	    resizable: false,
            	    height:170,
            	    width:380,
            	    autoOpen: false,
            	    modal: true,
            	    buttons: {
            	        "Oui": function() {
            	        	
            	        	 var chemin = tabUrl[0]+'public/consultation/supprimer-soin';
            	                $.ajax({
            	                    type: 'POST',
            	                    url: chemin ,
            	                    data:({'id_sh':id_sh}),
            	                    success: function() {    
            	                    	$('#'+id_sh).fadeOut(function(){
            	                    		listeSoinsPrescrits(id_hosp);
                        	        	});
            	                    },
            	                    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            	                    dataType: "html"
            	                });
            	        	
            	            $( this ).dialog( "close" );             	     
            	            return false;
            	        },
            	   
           	            "Non": function() {
           	            	$( this ).dialog( "close" );             	     
           	            	return false;
           	            }
            	       
            	    }
            	   
            	});
        	}
            
            function supprimersoin(id_sh,id_hosp){
            	supprimersoinConfirm(id_sh,id_hosp);
            	$("#confirmationSup").dialog('open'); 
            }
            /************************************************************************************************************************/
            /************************************************************************************************************************/
            /************************************************************************************************************************/
            	function modifiersoinPopup(id_sh,id_hosp){
                	$( "#modification" ).dialog({
                	    resizable: false,
                	    height:430,
                	    width:930,
                	    autoOpen: false,
                	    modal: true,
                	    buttons: {
                	        "Enregistrer": function() {
                	        	
                	        	result = controleSaisiPopup(id_sh,id_hosp);
                	        	
                	        	if(result == true) {
                	        		$( this ).dialog( "close" );             	     
                    	            return false;
                	        	}
                	        	
                	        },
                	   
               	        "Annuler": function() {
               	        	/**
                    		 * On enleve l'info bulle actif au cas ou
                    		 */
                    		 var tooltips = $( "#id_soins_m, #duree_m, #date_recommandee_m, #heure_recommandee_m" ).tooltip();
                			 tooltips.tooltip( "close" );
                			 
               	            $( this ).dialog( "close" );             	     
               	            return false;
               	        }
                	       
                	    }
                	});
            	}
                
                function modifiersoin(id_sh , id_hosp){ 
                	modifiersoinPopup(id_sh,id_hosp);
                	 var chemin = tabUrl[0]+'public/consultation/modifier-soin';
 	                $.ajax({
 	                    type: 'POST',
 	                    url: chemin ,
 	                    data:({'id_sh':id_sh, 'id_hosp':id_hosp}),
 	                    success: function(data) {    
 	                    	var result = jQuery.parseJSON(data);   
 	                	    $("#info_modif").html(result);
 	                    	$("#modification").dialog('open'); 
 	                    },
 	                    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
 	                    dataType: "html"
 	                });
                }

                /************************************************************************************************************************/
                /************************************************************************************************************************/
                /************************************************************************************************************************/
                function controleSaisiPopup(id_sh,id_hosp) {
                	
            		$("#medicament_m, #voie_administration_m, #frequence_m, #dosage_m, #date_application_m, #heure_recommandee_m, #motif_m, #note_m").css("border-color","");
                	if(!$('#medicament_m').val()){
                		$("#medicament_m").css("border-color","#FF0000");
                		$('#medicament_m').attr({'title': 'Veuillez séléctionner un médicament'});
                			var tooltips = $( "#medicament_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                	}else if(!$('#voie_administration_m').val()){
                		$("#voie_administration_m").css("border-color","#FF0000");
                		$('#voie_administration_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#voie_administration_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#medicament_m").css("border-color","");
                    		
                	}else if(!$('#frequence_m').val()){
                		$("#frequence_m").css("border-color","#FF0000");
                		$('#frequence_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#frequence_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#voie_administration_m").css("border-color","");
                    		
                	}else if(!$('#dosage_m').val()){
                		$("#dosage_m").css("border-color","#FF0000");
                		$('#dosage_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#dosage_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#frequence_m").css("border-color","");
                    		
                	}else if(!$('#date_application_m').val()){
                		$("#date_application_m").css("border-color","#FF0000");
                		$('#date_application_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#date_application_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#dosage_m").css("border-color","");
                    		
                	}
                	else if(!$('#heure_recommandee_m').val()){
                		$("#heure_recommandee_m").css("border-color","#FF0000");
                		$('#heure_recommandee_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#heure_recommandee_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#date_application_m").css("border-color","");
                    		
                	}
                	else if(!$('#motif_m').val()){
                		$("#motif_m").css("border-color","#FF0000");
                		$('#motif_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#motif_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#heure_recommandee_m").css("border-color","");
                    		
                	}
                	else if(!$('#note_m').val()){
                		$("#note_m").css("border-color","#FF0000");
                		$('#note_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#note_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#motif_m").css("border-color","");
                    		
                	}
                	else {
                		/**
        	        	 * Enregistrer les modifications
        	        	 */
                		 var medicament_m = $('#medicament_m').val();
                		 var voie_administration_m = $('#voie_administration_m').val();
                		 var frequence_m = $('#frequence_m').val();
                		 var dosage_m = $('#dosage_m').val();
                		 var date_application_m = $('#date_application_m').val();
                		 var heure_recommandee_m = $('#heure_recommandee_m').val();
                		 var motif_m = $('#motif_m').val();
                		 var note_m = $('#note_m').val();

        	        	 var chemin = tabUrl[0]+'public/consultation/en-cours';
     	                 $.ajax({
     	                    type: 'POST',
     	                    url: chemin ,
     	                    data:({
     	                    	'id_sh':id_sh, 'medicament':medicament_m, 'voie_administration':voie_administration_m, 
     	                    	'frequence':frequence_m, 'dosage':dosage_m, 'date_application':date_application_m, 
     	                    	'heure_recommandee':heure_recommandee_m, 'motif':motif_m, 'note':note_m, 
     	                    }),
     	                    success: function() {  
     	                    	listeSoinsPrescrits(id_hosp);
     	                    },
     	                    error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
     	                    dataType: "html",
     	                 });
     	        	
     	                /**
     	                 * *******************************
     	                 * *******************************
     	                 */
        	            return true;
                	}

               	}

                /************************************************************************************************************************/
                /************************************************************************************************************************/
                /************************************************************************************************************************/
                function liberer(id_demande_hospi) {
                	var id_cons = $("#"+id_demande_hospi).val();
                	var id_personne = $("#"+id_demande_hospi+"idPers").val();
                	var chemin = tabUrl[0]+'public/archivage/info-patient';
                    $.ajax({
                        type: 'POST',
                        url: chemin ,
                        data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'terminer':111, 'id_demande_hospi':id_demande_hospi},
                        success: function(data) {
                       	         
                        	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LIBERATION DU PATIENT </div>");
                        	var result = jQuery.parseJSON(data);
                        	$("#contenu").fadeOut(function(){$("#vue_liberer_patient").html(result).fadeIn("fast"); }); 
                        	     
                        },
                        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                        dataType: "html"
                    });
                }
                
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function affichervuedetailhospi(id_demande_hospi){
    	var id_cons = $("#"+id_demande_hospi).val();
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	
    	var chemin = tabUrl[0]+'public/archivage/detail-info-liberation-patient';
    	$.ajax({
    		type: 'POST',
    		url: chemin ,
    		data:{'id_personne':id_personne, 'id_cons':id_cons, 'encours':111, 'id_demande_hospi':id_demande_hospi},
    		success: function(data) {
    			$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> INFORMATIONS D&Eacute;TAILL&Eacute;ES </div>");
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
    
    /**INFO LIBERATION DU PATIENT**/
    function depliantPlus() {
    	$('#titre_info_liberation').click(function(){
    		$("#titre_info_liberation").replaceWith(
    			"<span id='titre_info_liberation' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Infos sur la lib&eacute;ration du patient"+
    		    "</span>");
    		animationPliantDepliant();
    		$('#info_liberation').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant() {
    	$('#titre_info_liberation').click(function(){
    		$("#titre_info_liberation").replaceWith(
    			"<span id='titre_info_liberation' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Infos sur la lib&eacute;ration du patient"+
    		    "</span>");
    		depliantPlus();
    		$('#info_liberation').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
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
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Liste des soins "+
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
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Liste des soins "+
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
    
    /** CETTE PARTIE CONCERNE LES SOINS PRESCRITS PAR LE MEDECIN LORS DE L'HOSPITALISATION DU PATIENT**/
    /** CETTE PARTIE CONCERNE LES SOINS PRESCRITS PAR LE MEDECIN LORS DE L'HOSPITALISATION DU PATIENT**/
    /** CETTE PARTIE CONCERNE LES SOINS PRESCRITS PAR LE MEDECIN LORS DE L'HOSPITALISATION DU PATIENT**/
    /** CETTE PARTIE CONCERNE LES SOINS PRESCRITS PAR LE MEDECIN LORS DE L'HOSPITALISATION DU PATIENT**/
    /**FORMULAIRE AJOUT D'UN SOIN**/ 
    function depliantPlus5() {
    	$('#titre_info_admis').click(function(){
    		$("#titre_info_admis").replaceWith(
    			"<span id='titre_info_admis' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Ajout d'un soin "+
    		    "</span>");
    		animationPliantDepliant5();
    		$('#form_ajout_soins').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant5() {
    	$('#titre_info_admis').click(function(){
    		$("#titre_info_admis").replaceWith(
    			"<span id='titre_info_admis' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Ajout d'un soin "+
    		    "</span>");
    		depliantPlus5();
    		$('#form_ajout_soins').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**LISTE DES SOINS **/
    function depliantPlus6() {
    	$('#titre_info_liste_soin').click(function(){
    		$("#titre_info_liste_soin").replaceWith(
    			"<span id='titre_info_liste_soin' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Liste des soins "+
    		    "</span>");
    		animationPliantDepliant6();
    		$('#Liste_soins_deja_prescrit').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant6() {
    	$('#titre_info_liste_soin').click(function(){
    		$("#titre_info_liste_soin").replaceWith(
    			"<span id='titre_info_liste_soin' style='margin-left:-5px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Liste des soins "+
    		    "</span>");
    		depliantPlus6();
    		$('#Liste_soins_deja_prescrit').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function initAnimation() {
    	$('#info_liberation').toggle(false);
    	$('#info_hospitalisation').toggle(false);
    	$('#info_liste').toggle(false);
    	$('#info_demande').toggle(false);
    }
    
    
    
    $(function(){
    /****** CONTROLE APRES VALIDATION ********/ 
	 /****** CONTROLE APRES VALIDATION ********/ 

	 	 var valid = true;  // VARIABLE GLOBALE utilis�e dans 'VALIDER LES DONNEES DU TABLEAU DES CONSTANTES'

	 	     $("#terminer,#bouton_constantes_valider").click(function(){

	 	     	valid = true;
	 	         if( $("#taille").val() == ""){
	 	             $("#taille").css("border-color","#FF0000");
	 	             $("#erreur_taille").fadeIn().text("Max: 250cm").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 	             valid = false;
	 	         }
	 	         else{
	 	         	$("#taille").css("border-color","");
	 	         	$("#erreur_taille").fadeOut();
	 	         }

	 	         if( $("#poids").val() == ""){
	 	         	 $("#poids").css("border-color","#FF0000");
	 	             $("#erreur_poids").fadeIn().text("Max: 300kg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 	             valid = false;
	 	         }
	 	         else{
	 	         	$("#poids").css("border-color", "");
	 	             $("#erreur_poids").fadeOut();
	 	         }
	 	         if( $('#temperature').val() == ""){
	 	         	$("#temperature").css("border-color","#FF0000");
	 	             $("#erreur_temperature").fadeIn().text("Min: 34°C, Max: 45°C").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 	             valid = false;
	 	         }
	 	         else{
	 	         	$("#temperature").css("border-color", "");
	 	             $("#erreur_temperature").fadeOut();
	 	         }
	 	         
	 	         if( $("#pouls").val() == ""){
	 	         	 $("#pouls").css("border-color","#FF0000");
	 	             $("#erreur_pouls").fadeIn().text("Max: 150 battements").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 	             valid = false;
	 	         }
	 	         else{
	 	         	 $("#pouls").css("border-color", "");
	 	             $("#erreur_pouls").fadeOut();
	 	         }
	 	         
	 	         
	 	         if( $("#frequence_respiratoire").val() == ""){
	 	         	 $("#frequence_respiratoire").css("border-color","#FF0000");
	 	             $("#erreur_frequence").fadeIn().text("Ce champs est requis").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 	             valid = false;
	 	         }
	 	         else{
	 	        	 $("#frequence_respiratoire").css("border-color", "");
	 	             $("#erreur_frequence").fadeOut();
	 	         }
	 	         
	 	         
	 	         if( $("#pressionarterielle").val() == ""){
	 	        	 $("#pressionarterielle").css("border-color","#FF0000");
	 	        	 $("#erreur_pressionarterielle").fadeIn().text("Max: 300mmHg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 	        	 valid = false;
	 	         }
	 	         else{
	 	        	 $("#pressionarterielle").css("border-color", "");
	 	        	 $("#erreur_pressionarterielle").fadeOut();
	 	         }
	 	         return valid;
	 	 	}); 
 	 	 	
			   //******************* VALIDER LES DONNEES DU TABLEAU DES MOTIFS ******************************** 
			   //******************* VALIDER LES DONNEES DU TABLEAU DES MOTIFS ******************************** 
			   
	 	    $("#pouls").keyup(function(){
	 	    	var valeur = $('#pouls').val();
	 			if(isNaN(valeur/1) || valeur > 150){
	 				$('#pouls').val("");
	 				valeur = null;
	 				$("#pouls").css("border-color","#FF0000");
	 	             $("#erreur_pouls").fadeIn().text("Max: 150 battements").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 			}else{
	 				$("#pouls").css("border-color","");
	 				$("#erreur_pouls").fadeOut();
	 			}
	 	    });
	 	    
	 	    $("#frequence_respiratoire").keyup(function(){
	 	    	var valeur = $('#frequence_respiratoire').val();
	 			if(isNaN(valeur/1) || valeur > 150){
	 				$('#frequence_respiratoire').val("");
	 				valeur = null;
	 				$("#frequence_respiratoire").css("border-color","#FF0000");
	 	             $("#erreur_frequence").fadeIn().text("Ce champs est requis").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 			}else{
	 				$("#frequence_respiratoire").css("border-color","");
	 				$("#erreur_frequence").fadeOut();
	 			}
	 	    });
	 	    
	 	   $("#temperature").blur(function(){
	 	    	if($("#temperature").val() > 45 || $("#temperature").val() < 34){
	 	    		$("#temperature").val('');
	 	    		$("#temperature").mask("49");
	 	    		$("#temperature").css("border-color","#FF0000");
	 	    		$("#erreur_temperature").fadeIn().text("Min: 34°C, Max: 45°C").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 	    	} else 
	 	    		if($("#temperature").val() <= 45 && $("#temperature").val() >= 34){
	 	    			$("#temperature").css("border-color","");
	 	    			$("#erreur_temperature").fadeOut();
	 	    		}
	 	    	return false;
	 	    });
	 	    
	 	    $("#poids").keyup(function(){
	 	    	var valeur = $('#poids').val();
	 			if(isNaN(valeur/1) || valeur > 300){
	 				$('#poids').val("");
	 				valeur = null;
	 				$("#poids").css("border-color","#FF0000");
	 	             $("#erreur_poids").fadeIn().text("Max: 300kg").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 			}else{
	 				$("#poids").css("border-color","");
	 				$("#erreur_poids").fadeOut();
	 			}
	 	    });
	 	    
	 	   $("#taille").keyup(function(){
	 	    	var valeur = $('#taille').val();
	 			if(isNaN(valeur/1) || valeur > 300){
	 				$('#taille').val("");
	 				valeur = null;
	 				$("#taille").css("border-color","#FF0000");
	 	             $("#erreur_taille").fadeIn().text("Max: 250cm").css({"color":"#ff5b5b","padding":" 0 10px 0 10px","margin-top":"-18px","font-size":"13px","font-style":"italic"});
	 			}else{
	 				$("#taille").css("border-color","");
	 				$("#erreur_taille").fadeOut();
	 			}
	 	    });
	 	 //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
	 	 //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 

	 	     //Au debut on d�sactive le code cons et la date de consultation qui sont non modifiables
	 	    	var id_cons = $("#id_cons");
	 	    	var date_cons = $("#date_cons");
	 	    	id_cons.attr('readonly',true);
	 	    	date_cons.attr('readonly',true);

	 	    	var poids = $('#poids');
	 	    	var taille = $('#taille');
	 	    	var tension = $('#tension');
	 	    	var bu = $('#bu');
	 	    	var temperature = $('#temperature');
	 	    	var glycemie_capillaire = $('#glycemie_capillaire');
	 	    	var pouls = $('#pouls');
	 	    	var frequence_respiratoire = $('#frequence_respiratoire');
	 	    	var pressionarterielle = $("#pressionarterielle");
	 	    	
	 	  	  //Au debut on cache le bouton modifier et on affiche le bouton valider
	 	    	$( "#bouton_constantes_valider" ).toggle(true);
	 	    	$( "#bouton_constantes_modifier" ).toggle(false);

	 	    	//Au debut on active tous les champs
	 	    	poids.attr( 'readonly', false ).css({'background':'#fff'});
	 	    	taille.attr( 'readonly', false ).css({'background':'#fff'});
	 	    	tension.attr( 'readonly', false).css({'background':'#fff'}); 
	 	    	bu.attr( 'readonly', false).css({'background':'#fff'});  
	 	    	temperature.attr( 'readonly', false).css({'background':'#fff'}); 
	 	    	glycemie_capillaire.attr( 'readonly', false).css({'background':'#fff'});
	 	    	pouls.attr( 'readonly', false).css({'background':'#fff'});
	 	    	frequence_respiratoire.attr( 'readonly', false).css({'background':'#fff'});
	 	    	pressionarterielle.attr( 'readonly', false ).css({'background':'#fff'});

	 	    	$( "#bouton_constantes_valider" ).click(function(){
	 	    		if(valid == true){
	 	  	   		poids.attr( 'readonly', true ).css({'background':'#f8f8f8'});    
	 	  	   		taille.attr( 'readonly', true ).css({'background':'#f8f8f8'});
	 	  	   		tension.attr( 'readonly', true).css({'background':'#f8f8f8'});
	 	  	   		bu.attr( 'readonly', true).css({'background':'#f8f8f8'});
	 	  	   		temperature.attr( 'readonly', true).css({'background':'#f8f8f8'});
	 	  	   		glycemie_capillaire.attr( 'readonly', true).css({'background':'#f8f8f8'});
	 	  	   		pouls.attr( 'readonly', true).css({'background':'#f8f8f8'});
	 	  	   		frequence_respiratoire.attr( 'readonly', true).css({'background':'#f8f8f8'});
	 	  	   		pressionarterielle.attr( 'readonly', true ).css({'background':'#f8f8f8'});
	 	  	   		
	 	    		    $("#bouton_constantes_modifier").toggle(true);  //on affiche le bouton permettant de modifier les champs
	 	    		    $("#bouton_constantes_valider").toggle(false); //on cache le bouton permettant de valider les champs
	 	    		}
	 	    		return false; 
	 	    	});
	 	    	
	 	    	$( "#bouton_constantes_modifier" ).click(function(){
	 	    		poids.attr( 'readonly', false ).css({'background':'#fff'});
	 	    		taille.attr( 'readonly', false ).css({'background':'#fff'}); 
	 	    		tension.attr( 'readonly', false).css({'background':'#fff'}); 
	 	    		bu.attr( 'readonly', false).css({'background':'#fff'});
	 	    		temperature.attr( 'readonly', false).css({'background':'#fff'});
	 	    		glycemie_capillaire.attr( 'readonly', false).css({'background':'#fff'});
	 	    		pouls.attr( 'readonly', false).css({'background':'#fff'});
	 	    		frequence_respiratoire.attr( 'readonly', false).css({'background':'#fff'});
	 	    		pressionarterielle.attr( 'readonly', false ).css({'background':'#fff'});
	 	    		
	 	    	 	$("#bouton_constantes_modifier").toggle(false);   //on cache le bouton permettant de modifier les champs
	 	    	 	$("#bouton_constantes_valider").toggle(true);    //on affiche le bouton permettant de valider les champs
	 	    	 	return  false;
	 	    	});


	 	   //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
	 	   //******************* VALIDER LES DONNEES DU TABLEAU DES CONSTANTES ******************************** 
	 	    
    });

       // ******************* Tranfert ******************************** 
	   // ******************* Tranfert ******************************** 
	    $(function(){
	    	var motif_transfert = $("#motif_transfert");
	    	var hopital_accueil = $("#hopital_accueil");
	    	var service_accueil = $("#service_accueil");

	    	$( "#bouton_transfert_valider" ).toggle(true);
	    	$( "#bouton_transfert_modifier" ).toggle(false);

	    	$( "#bouton_transfert_valider" ).click(function(){
	    		motif_transfert.attr( 'disabled', true ).css({'background':'#f8f8f8'});     
	    		hopital_accueil.attr( 'disabled', true ).css({'background':'#f8f8f8'});     
	    		service_accueil.attr( 'disabled', true ).css({'background':'#f8f8f8'});   
	    		$("#bouton_transfert_modifier").toggle(true);  
	    		$("#bouton_transfert_valider").toggle(false); 
	    		return false; 
	    	});
	    	
	    	$( "#bouton_transfert_modifier" ).click(function(){
	    		motif_transfert.attr( 'disabled', false ).css({'background':'#fff'});
	    		hopital_accueil.attr( 'disabled', false ).css({'background':'#fff'});
	    		service_accueil.attr( 'disabled', false ).css({'background':'#fff'});
	    	 	$("#bouton_transfert_modifier").toggle(false);  
	    	 	$("#bouton_transfert_valider").toggle(true);    
	    	 	return  false;
	    	});
	    });
	    
    /**** RECUPEARTION DE LA LISTE DES SERVIVES ****/
	  var base_url = window.location.toString();
	  var tabUrl = base_url.split("public");
		
    var theHREF = tabUrl[0]+"public/consultation/services";
	  function getservices(cle)
	  {
	       $.ajax({
	          type: 'POST',
	          url: theHREF,
	          data: 'id='+cle,
	          success: function(data) {
	              var result = jQuery.parseJSON(data);
	              $("#service_accueil").html(result);
	        },
	        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	          dataType: "html"
	      });

	      return false;
	  }