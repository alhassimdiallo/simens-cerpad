    var nb="_TOTAL_";
    var asInitVals = new Array();
    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
	//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    function confirmation(id){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:435,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );
	            
	            var cle = id;
	            var chemin = tabUrl[0]+'public/facturation/supprimer-naissance';
	            $.ajax({
	                type: 'POST',
	                url: chemin ,
	                data: $(this).serialize(),  
	                data:'id='+cle,
	                success: function(data) {
	                	
	                	     var result = jQuery.parseJSON(data);  
	                	     $("#"+cle).fadeOut(function(){$("#"+cle).empty();});
	                	     $("#compteur").html(result);
	                	     
	                },
	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	                dataType: "html"
	            });
	    	   //  return false;
	    	     
	        },
	        "Annuler": function() {
                $( this ).dialog( "close" );
            }
	   }
	  });
    }
    
    function envoyer(id){
   	   confirmation(id);
       $("#confirmation").dialog('open');
   	}
    
    /**********************************************************************************/
    
    function affichervue(id){
    	
    	var cle = id;
        var chemin = tabUrl[0]+'public/facturation/vue-naissance';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data: $(this).serialize(),  
            data:'id='+cle,
            success: function(data) {
            	
            	     $("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 18px; font-weight: bold; padding-left: 20px;'><iS style='font-size: 25px;'>&curren;</iS> INFORMATIONS SUR LE PATIENT </div>");
            	     var result = jQuery.parseJSON(data);  
            	     $("#contenu").fadeOut(function(){$("#vue_patient").html(result).fadeIn("fast"); }); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
	   // return false;
    }
    
    function listepatient(){
    	//Lorsqu'on clique sur terminer �a ram�ne la liste des aptients d�c�d�s 
	    $("#terminer").click(function(){
	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 18px; font-weight: bold; padding-left: 20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES NAISSANCES</div>");
  	    	$("#vue_patient").fadeOut(function(){$("#contenu").fadeIn("fast"); });
  	    });
    }
    
    /*********************************************************************************/
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
    	
     var asInitVals = new Array();
	 var  oTable = $('#patientdeces').dataTable
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
					   
			   "sAjaxSource": ""+tabUrl[0]+"public/facturation/liste-naissance-ajax", 
					   
	} );

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
	

    }
    
  //AFFICHER LES INFORMATIONS SUR LA MAMAN  
 /**********************************************************************************/
    
    function information(id){
    	$( "#information" ).dialog({
    	    resizable: false,
    	    height:330,
    	    width:685,
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
    
    
    function infomaman(id){   
    	information(id);
    	
    	var cle = id;
        var chemin = tabUrl[0]+'public/facturation/vue-info-maman';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data: $(this).serialize(),  
            data:'id='+cle,
            success: function(data) {        
            	var result = jQuery.parseJSON(data);  
                $("#info").html(result);
            	     
                $("#information").dialog('open');       
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    }
    
    
    
    //POUR LA MODIFICATION DES INFORMATIONS D'UNE NAISSANCE 
    /***********************************************************************************************/
    function modifier(id){

    	var cle = id;
        var chemin = tabUrl[0]+'public/facturation/modifier-naissance';
        $.ajax({
            type: 'GET',
            url: chemin ,
            data:'id='+cle,
            success: function(data) {
            	     $("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 18px; font-weight: bold; padding-left: 20px;'><iS style='font-size: 25px;'>&curren;</iS> MODIFIER LES INFORMATIONS SUR LA NAISSANCE </div>");
            	     var result = jQuery.parseJSON(data); 
            	     /****EFFACER LE CONTENU ET DEPLIER L'INTERFACE DE MODIFICATION****/
            	     $("#contenu").fadeOut(function(){
            	    	 $("#modifier_donnees").html(result); 
            	     
            	    	 /********************ON PREPARE LA TOUCHE 'P�c�dent' *******************/
            	    	 $('#precedent').click(function(){
            	    		$('#precedent').remove();
            	 	    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 18px; font-weight: bold; padding-left: 20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES NAISSANCES </div>");	
            	 	    
            	 	         $('#modifier_donnees').animate({
            	 	            height : 'toggle',
            	 	         },1000).queue(function(){
            	 	        	$('#contenu').fadeIn(500),
            	 	            $(this).dequeue();
            	 	         });
	
            	 	         $("#terminer").replaceWith("<button id='terminer' style='height:35px;'>Terminer</button>");
            	 	         
            	 	         return false;
            	 	     });
            	    	 /***********************************************************************/
            	    	 
            	    	 
            	    	 /****************ON PREPARE LA TOUCHE 'Terminer'*****************/
       	 	              modifierDonnees(id);
       	 	             /****************************************************************/
            	    	 
            	    	 
            	    	 /****DEPLIER L'INTERFACE****/
            	         $('#modifier_donnees').animate({
            	            height : 'toggle'
            	         },1000).queue(function(){$("#modifier_donnees").stop(true); $(this).dequeue();}); //POUR EVITER L'EFFET SUR LE DOUBLE CLICK DE L'ICONE 'Modifier' 
            	     });
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
        
	    //return false;
    }
    
    function modifierDonnees(id){
    	
//    	$("#terminer_modif").click(function(){ 
//    		$("#terminer_modif").css({"border-color":"#ccc", "background":"-webkit-linear-gradient( #555, #CCC)", "box-shadow":"1px 1px 10px black inset,0 1px 0 rgba( 255, 255, 255, 0.4)"});
//        	var taille = $("#taille").val();
//        	var poids  = $("#poids").val();
//        	var groupe_sanguin  = $("#groupe_sanguin").val();
//        	var heure_naissance = $("#heure_naissance").val();
//        	var date_naissance  = $("#date_naissance").val();
//        	var nom = $('#nom').val();
//        	var prenom = $("#prenom").val();
//        	var lieu_naissance = $("#lieu_naissance").val();
//        	var sexe = $("#sexe").val(); if(!sexe){sexe="";}
//        	
//        	$.ajax({
//                type: 'POST',  
//                url: tabUrl[0]+'public/facturation/modifier-naissance' ,  
//                data: ({'id':id , 'nom':nom , 'prenom':prenom , 'date_naissance':date_naissance , 'lieu_naissance':lieu_naissance , 'heure_naissance':heure_naissance , 'poids':poids , 'groupe_sanguin':groupe_sanguin , 'taille':taille , 'sexe':sexe}),
//        	    success: function(data) {    
//        	    	
//                  vart=tabUrl[0]+'public/facturation/liste-naissance';
//                  $(location).attr("href",vart);
//                  
//                },
//                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//                dataType: "html"
//        	});
//    	});
    	
    	//Annuler l'enregistrement d'une naissance
        $("#annuler_modif").click(function(){
        	$("#annuler_modif").css({"border-color":"#ccc", "background":"-webkit-linear-gradient( #555, #CCC)", "box-shadow":"1px 1px 10px black inset,0 1px 0 rgba( 255, 255, 255, 0.4)"});
        	vart=tabUrl[0]+'public/facturation/liste-naissance';
            $(location).attr("href",vart);
            return false;
        });
        
        
        $('#date_naissance').datepicker(
    			$.datepicker.regional['fr'] = {
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
    			}
    	);
        
        //MENU GAUCHE
      	//MENU GAUCHE
      	$("#vider").click(function(){
      		$('#nom').val('');
      		$('#prenom').val('');
      		$('#date_naissance').val('');
      		$('#heure_naissance').val('');
      		$('#lieu_naissance').val('');
      		$('#poids').val('');
      		$('#sexe').val('');
      		$('#taille').val('');
      		$('#groupe_sanguin').val('');
      		return false;
      	});
        
        $('#vider_champ').hover(function(){
  			
 			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/annuler2.png") no-repeat right top');
 		},function(){
 			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/annuler1.png") no-repeat right top');
 	    });

 		$('#div_supprimer_photo').hover(function(){
 			
 			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/mod2.png") no-repeat right top');
 		},function(){
 			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/mod.png") no-repeat right top');
 	    });

 		$('#div_ajouter_photo').hover(function(){
 			
 			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/ajouterphoto2.png") no-repeat right top');
 		},function(){
 			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/ajouterphoto.png") no-repeat right top');
 	    });

 		$('#div_modifier_donnees').hover(function(){
 			
 			 $(this).css('background','url("'+tabUrl[0]+'public/images_icons/modifier2.png") no-repeat right top');
 		},function(){
 			  $(this).css('background','url("'+tabUrl[0]+'public/images_icons/modifier.png") no-repeat right top');
 	   });
    }