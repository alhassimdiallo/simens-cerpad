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

    					"sAjaxSource": ""+tabUrl[0]+"public/hospitalisation/liste-patient-encours-ajax", 
    					
    	}); 
        
        var asInitVals = new Array();
    
   	//le filtre du select
   	$('#filter_statut').change(function() 
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
	    $("#hospitaliser").fadeOut(function(){$("#contenu").fadeIn("fast"); $("#division").val(""); $("#salle,#lit").html("");
	    $('div .dataTables_paginate').css({ 'margin-right' : '0'});
	    	
    	$('#listeDataTable').css({'margin-left' : '-10'});
    	
 		$("#id_soins, #duree, #date_recommandee, #heure_recommandee").css("border-color","");
 		$("#id_soins, #duree, #date_recommandee, #heure_recommandee").val('');
	    });
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
    	var chemin = tabUrl[0]+'public/consultation/info-patient';
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
	    
	    $('#date_recommandee, #date_recommandee_m').datepicker($.datepicker.regional['fr'] = {
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
	    	//$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES PATIENTS </div>");
  	    	//$("#vue_detail_hospi_patient").fadeOut(function(){
  	    		//$("#contenu").fadeIn("fast"); 
  	    	//});
  	    	
  	    	vart=tabUrl[0]+'public/hospitalisation/en-cours';
		    $(location).attr("href",vart);
  	    });
	    
    }
    
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    /*************************************************************************************************************************/
    function getsalle(id_batiment){
      var chemin = tabUrl[0]+'public/hospitalisation/salles';
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
    	var chemin = tabUrl[0]+'public/hospitalisation/lits';
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
    function administrerSoin(id_personne){
    	var id_hosp = $("#"+id_personne+"hp").val();
    	var chemin = tabUrl[0]+'public/hospitalisation/info-patient-hospi';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'administrerSoin':111},
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> ADMINISTRER DES SOINS </div>");
            	var result = jQuery.parseJSON(data);
            	$("#vue_patient_hospi").html(result);
            	$("#division,#salle,#lit").val("");
            	$("#code_demande").val($("#"+id_personne+"dh").val());
            	listeSoins(id_hosp);
            	$("#contenu").fadeOut(function(){
            		$("#hospitaliser").fadeIn("fast"); 
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
    
    function listeSoins(id_hosp) { 
    	var chemin = tabUrl[0]+'public/hospitalisation/liste-soin';
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
    	$('#date_recommandee, #heure_recommandee, #id_soins, #duree, #note, #motif').val('');
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
	            
	            	var chemin = tabUrl[0]+'public/hospitalisation/en-cours';
	            	var id_sh = $('#id_sh').val();
	            	var id_soins = $('#id_soins').val();
	            	var id_hosp = $('#id_hosp').val();
	            	var duree = $('#duree').val();
	            	var date_recommandee = $('#date_recommandee').val();
	            	var heure_recommandee = $('#heure_recommandee').val();
	            	var note = $('#note').val();
	            	var motif = $('#motif').val();
	            	$.ajax({
	                    type: 'POST',
	                    url: chemin ,
	                    data:{'id_sh':id_sh, 'id_soins':id_soins, 'id_hosp':id_hosp, 'duree':duree, 'date_recommandee':date_recommandee, 'heure_recommandee':heure_recommandee, 'note':note, 'motif':motif},
	                    success: function() {
	                    	listeSoins(id_hosp);
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
    	$('#id_soins, #duree, #date_recommandee, #heure_recommandee').hover(function(){
    		$("#id_soins, #duree, #date_recommandee, #heure_recommandee").attr({'title':''});
    		var tooltips = $( "#id_soins, #duree, #date_recommandee, #heure_recommandee" ).tooltip();
    		tooltips.tooltip( "hide" );
    	});
    }
    
    /**
     * Lors d'un click
     */
    function click() {
    	$('#id_soins, #duree, #date_recommandee, #heure_recommandee').click(function(){
			var tooltips = $( "#id_soins, #duree, #date_recommandee, #heure_recommandee" ).tooltip();
			tooltips.tooltip( "close" );
			$("#id_soins, #duree, #date_recommandee, #heure_recommandee").attr({'title':''});
	    });
    	
    	$('#date_recommandee').mouseover(function(){
    		var tooltips = $( "#date_recommandee" ).tooltip();
			tooltips.tooltip( "hide" );
    	});
    }
    
    function ajouter() {
    	
    	hover();
    	click();
    	
		$("#id_soins, #duree, #date_recommandee, #heure_recommandee").css("border-color","");
    	if(!$('#id_soins').val()){
    		$("#id_soins").css("border-color","#FF0000");
    		$('#id_soins').attr({'title': 'Veuillez selectionner un soin'});
    			var tooltips = $( "#id_soins" ).tooltip();
    			tooltips.tooltip( "open" );
    			
    	}else if(!$('#duree').val()){
    		$("#duree").css("border-color","#FF0000");
    		$('#duree').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#duree" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#id_soins").css("border-color","");
        		
    	}else if(!$('#date_recommandee').val()){
    		$("#date_recommandee").css("border-color","#FF0000");
    		$('#date_recommandee').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#date_recommandee" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#duree").css("border-color","");
        		
    	}else if(!$('#heure_recommandee').val()){
    		$("#heure_recommandee").css("border-color","#FF0000");
    		$('#heure_recommandee').attr({'title': 'Ce champ est requis'});
    			var tooltips = $( "#heure_recommandee" ).tooltip();
    			tooltips.tooltip( "open" );
    			
        		$("#date_recommandee").css("border-color","");
        		
    	}else {
    			confirmation();
    			$("#confirmation").dialog('open');
    	}

   	}
    

    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    	function vueSoinAppliquer(){
        	$( "#informations" ).dialog({
        	    resizable: false,
        	    height:325,
        	    width:700,
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
        	vueSoinAppliquer();
            var chemin = tabUrl[0]+'public/hospitalisation/vue-soin-appliquer';
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
            	        	
            	        	 var chemin = tabUrl[0]+'public/hospitalisation/supprimer-soin';
            	                $.ajax({
            	                    type: 'POST',
            	                    url: chemin ,
            	                    data:({'id_sh':id_sh}),
            	                    success: function() {    
            	                    	$('#'+id_sh).fadeOut(function(){
                        	        		listeSoins(id_hosp);
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
                	    height:360,
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
                	 var chemin = tabUrl[0]+'public/hospitalisation/modifier-soin';
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
                	
            		$("#id_soins_m, #duree_m, #date_recommandee_m, #heure_recommandee_m").css("border-color","");
                	if(!$('#id_soins_m').val()){
                		$("#id_soins_m").css("border-color","#FF0000");
                		$('#id_soins_m').attr({'title': 'Veuillez selectionner un soin'});
                			var tooltips = $( "#id_soins_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                	}else if(!$('#duree_m').val()){
                		$("#duree_m").css("border-color","#FF0000");
                		$('#duree_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#duree_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#id_soins_m").css("border-color","");
                    		
                	}else if(!$('#date_recommandee_m').val()){
                		$("#date_recommandee_m").css("border-color","#FF0000");
                		$('#date_recommandee_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#date_recommandee_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#duree_m").css("border-color","");
                    		
                	}else if(!$('#heure_recommandee_m').val()){
                		$("#heure_recommandee_m").css("border-color","#FF0000");
                		$('#heure_recommandee_m').attr({'title': 'Ce champ est requis'});
                			var tooltips = $( "#heure_recommandee_m" ).tooltip();
                			tooltips.tooltip( "open" );
                			
                    		$("#date_recommandee_m").css("border-color","");
                    		
                	}else {
                		/**
        	        	 * Enregistrer les modifications
        	        	 */
                		 var id_soins_m = $('#id_soins_m').val();
                		 var date_recommandee_m = $('#date_recommandee_m').val();
                		 var heure_recommandee_m = $('#heure_recommandee_m').val();
                		 var duree_m = $('#duree_m').val();
                		 var note_m = $('#note_m').val();
                		 var motif_m = $('#motif_m').val();
        	        	 var chemin = tabUrl[0]+'public/hospitalisation/en-cours';
     	                 $.ajax({
     	                    type: 'POST',
     	                    url: chemin ,
     	                    data:({
     	                    	'id_sh':id_sh, 'id_soins':id_soins_m, 'date_recommandee':date_recommandee_m,
     	                    	'heure_recommandee':heure_recommandee_m, 'duree':duree_m, 'note':note_m, 'motif':motif_m
     	                    }),
     	                    success: function() {  
                 	        		listeSoins(id_hosp);
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
                function liberer(id_personne) {
                	var id_cons = $("#"+id_personne).val();
                	var id_demande_hospi = $("#"+id_personne+"dh").val();
                	var chemin = tabUrl[0]+'public/hospitalisation/info-patient';
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
    function affichervuedetailhospi(id_personne){
    	var id_cons = $("#"+id_personne).val();
    	var id_demande_hospi = $("#"+id_personne+"dh").val();
    	var chemin = tabUrl[0]+'public/hospitalisation/detail-info-liberation-patient';
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
    
    function initAnimation() {
    	$('#info_liberation').toggle(false);
    	$('#info_hospitalisation').toggle(false);
    	$('#info_liste').toggle(false);
    	$('#info_demande').toggle(false);
    }