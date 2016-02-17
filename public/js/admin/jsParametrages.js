 var base_url = window.location.toString();
 var tabUrl = base_url.split("public");
    
 /************************************************************************************************************************/
 /************************************************************************************************************************/
 /************************************************************************************************************************/
 var compteur = 0; 
 var updateHopital = 0;
 function ajouterDonneesParametrages(){
	 $('#ajouterHopitaux').hover(function(){
		  $(this).css({'font-weight':'bold', 'color':'green', 'font-size': '17px'});
		},function(){
		  $(this).css({'font-weight':'normal', 'color':'black', 'font-size': '14px'});
		});
	 
	 $('#ajouterHopitaux').click(function(){
		 $(this).css({'font-weight':'normal', 'color':'black', 'font-size': '14px'});
	 });
	 
	 $('#ajouterHopitaux').click(function(){
		 updateHopital = 0;
		 
		 $.ajax({
           type: 'POST',
           url: tabUrl[0]+'public/admin/gestion-des-hopitaux' ,
           data:{'id':1},
           success: function(data) {
        	   var result = jQuery.parseJSON(data);  
            
        	   $('#contenuTable').html(result); 
        	   $('#wait').toggle(false);
        	   $('#VueDetailsHopital').toggle(false);
        	   
        	   $('#contenu').fadeOut(function(){
        		   $("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><img src='/simens/public/images_icons/Table16X16.png' /> GESTION DES H&Ocirc;PITAUX </div>");
        		   $('#contenuTable').fadeIn('fast');
        		   ListeDesHopitaux();
        		   
        		   //Au click sur terminer
        		   $('#terminer').click(function(){
        			   $('#contenuTable').fadeOut(function(){
        				   $("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> PARAM&Eacute;TRAGES </div>");
        				   $('#contenu').fadeIn('fast'); 
        			   });
        		   });
        		   
        		   //Au click sur annuler
        		   $('#annuler').click(function(){
        			   $('#nom').val('');
        			   $('#region').val('');
        		       $('#departement').val('');
        		       $('#directeur').val('');
	    			   $('#note').val('');
        		       
        			   return false;
        		   });
        		   
        		 //Au click sur enregistrer
        		   $('#enregistrer').click(function(){
        			   var nom = $('#nom').val();
        			   var region = $('#region').val();
        		       var departement = $('#departement').val();
        		       var directeur = $('#directeur').val();
        		       var note = $('#note').val();
        		       
        		       if(nom == '' || region == '' || departement == null || departement == '' || directeur == ''){
        		    	   return true;
        		       } else {
        		    	   //alert(updateHopital); return false;
        		    	   $.ajax({
        		    		   type: 'POST',
        		    		   url: tabUrl[0]+'public/admin/ajouter-hopital' ,
        		    		   data:{'nom': nom, 'departement':departement , 'directeur':directeur, 'note':note, 'updateHopital': updateHopital},
        		    		   success: function() {
        		    	        	 
        		    			   $('#nom').val('');
        		    			   $('#region').val('');
        		    			   $('#departement').val('');$('#departement').html('');
        		    			   $('#directeur').val('');
        		    			   $('#note').val('');
       		    				   $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouvel h&ocirc;pital </span>');
       		    				   updateHopital = 0;

        	            		   
        		    			   //POUR LE RAFFRAICHISSEMENT DU FORMULAIRE
        		    			   if(compteur == 9){ 
        		    				   $('#contenuTable').toggle(false);
        		    				   $('#wait').toggle(true);
        		    				   $('#ajouterHopitaux').trigger('click');
        		    				   compteur = 0;
        		    			   }else{
        		    				   ListeDesHopitaux(); compteur++;
        		    			   }
        		    		   },
        		    	   
        		    	   });
        		    	   
            			   return false;
        		       }
         		   });
        		   
        	   });
           
           },
           error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
           dataType: "html"
		 
		 });
		
	 }); 
 }

 //*********************************** AFFICHAGE DE LA LISTE DES HOPITAUX *************************************************/
 //*********************************** AFFICHAGE DE LA LISTE DES HOPITAUX *************************************************/
 //*********************************** AFFICHAGE DE LA LISTE DES HOPITAUX *************************************************/
 function ListeDesHopitaux(){
     var  oTable = $('#listeDesHopitauxAjax').dataTable
 	 ({
 		"bDestroy":true,
 		 "sPaginationType": "full_numbers",
 		 "aLengthMenu": [3,5],
 		"iDisplayLength": 5,
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
 		 "sAjaxSource": ""+tabUrl[0]+"public/admin/liste-hopitaux-ajax", 
 	 }); 
     
     var asInitVals = new Array();
	
     //le filtre du select
     $('#filter_statut').change(function() {					
    	 oTable.fnFilter( this.value );
     });
	
     $("tfoot input").keyup( function () {
    	 /* Filter on the column (the index) of this element */
    	 oTable.fnFilter( this.value, $("tfoot input").index(this) );
     });
	
     /*
	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
	 * the footer
	 */
     $("tfoot input").each( function (i) {
    	 asInitVals[i] = this.value;
     });
	
     $("tfoot input").focus( function () {
    	 if ( this.className == "search_init" )
    	 {
    		 this.className = "";
    		 this.value = "";
    	 }
     });
	
     $("tfoot input").blur( function (i) {
    	 if ( this.value == "" )
    	 {
    		 this.className = "search_init";
    		 this.value = asInitVals[$("tfoot input").index(this)];
    	 }
     });
	
 }
 
 function getDepartement(id){
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-departements' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 $('#departement').html(result);
         },
	 });
 }
 
 function visualiserDetails(id){
	 $('#FormulaireAjouterHopitaux').toggle(false);
	 $('#VueDetailsHopital').toggle(true);
	 
	 //Afficher l'interface (formulaire) de saisie des hopitaux
	 $('#PlusFormulaireAjouterHopitaux').click(function(){
		 updateHopital = 0; 
		 $('#labelInfos').replaceWith('<span id="labelInfos"> Cr&eacute;ation d\'un nouvel h&ocirc;pital </span>');

		 $('#VueDetailsHopital').fadeOut(function(){
			 $('#nom').val('');
			 $('#region').val('');
		     $('#departement').val(''); $('#departement').html('');
		     $('#directeur').val('');
			 $('#note').val('');
			   
			 $('#FormulaireAjouterHopitaux').toggle(true);
		 });
	 });
	 
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-infos-hopital' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 $('#scriptVue').html(result);
         },
	 });
	 
 }

 function modifier(id){
	 updateHopital = id;
	 $('#labelInfos').replaceWith('<span id="labelInfos"> Modification des infos de l\'h&ocirc;pital </span>');
	 $('#VueDetailsHopital').toggle(false);
	 $('#FormulaireAjouterHopitaux').toggle(true);
	 
	 $.ajax({
         type: 'POST',
         url: tabUrl[0]+'public/admin/get-infos-modification-hopital' ,
         data:{'id': id},
         success: function(data) {
        	 var result = jQuery.parseJSON(data); 
        	 $('#scriptVue').html(result);
         },
	 });
 }
 
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    /************************************************************************************************************************/
//    function modifier(id){
//         
//         $.ajax({
//            type: 'POST',
//            url: tabUrl[0]+'public/admin/modifier-utilisateur' ,
//            data:{'id':id},
//            success: function(data) {
//           	         
//            	var result = jQuery.parseJSON(data);  
//            	$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> MODIFICATION DES DONNEES </div>");
//            	$("#scriptFormUtilisationPopulate").html(result);
//            	$("#contenu").fadeOut(function(){
//            		$("#FormUtilisateur").fadeIn("fast"); 
//            		$("#previous").toggle(false);
//            	}); 
//            	     
//            },
//            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//            dataType: "html"
//        });
//         
//         
//   	}
//    
//    function viderChamp(){
//    	$("#nomUtilisateur").val('');
//    	$("#prenomUtilisateur").val('');
//    	$("#username").val('');
//    	$("#password").val('');
//    	$("#fonction").val('');
//    	$("#service").val('');
//    }
//    
//    function initialisationListePersonnel(){
//    	var asInitValss = new Array();
//    	var  oTablee = $('#personnel').dataTable
//    	( {
//    		"sPaginationType": "full_numbers",
//    		"aLengthMenu": [5,7,10,15],
//    			"aaSorting": [],
//    		"oLanguage": {
//    			"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
//    			"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
//    			"sInfoFiltered": "",
//    			"sUrl": "",
//    			"oPaginate": {
//    				"sFirst":    "|<",
//    				"sPrevious": "<",
//    				"sNext":     ">",
//    				"sLast":     ">|"
//    				}
//    		   },
//
//    		"sAjaxSource": ""+tabUrl[0]+"public/admin/liste-agent-personnel-ajax", 
//    						
//    	} );
//
//    	$("thead input").keyup( function () {
//    		/* Filter on the column (the index) of this element */
//    		oTablee.fnFilter( this.value, $("thead input").index(this) );
//    	} );
//
//    	/*
//    	* Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
//    	* the footer
//    	*/
//    	$("thead input").each( function (i) {
//    		asInitValss[i] = this.value;
//    	} );
//
//    	$("thead input").focus( function () {
//    		if ( this.className == "search_init" )
//    		{
//    			this.className = "";
//    			this.value = "";
//    		}
//    	} );
//
//    	$("thead input").blur( function (i) {
//    		if ( this.value == "" )
//    		{
//    			this.className = "search_init";
//    			this.value = asInitValss[$("thead input").index(this)];
//    		}
//    	} );
//       
//    }
//    
//    function ajouterUtilisateur(){
//    	$("#previous").toggle(true);
//    	viderChamp();
//    	$('#id').val("");
//    	var role = $('#RoleSelect').val();
//    	$('input[type=radio][name=role][value='+role+']').attr('checked', false);
//    	$("#contenu").fadeOut(function(){
//    		$("#listeRecherche").fadeIn("fast"); 
//    		$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> RECHERCHE DU NOUVEL UTILISATEUR A AJOUTER </div>");
//    		
//    		$('div .dataTables_paginate').css({ 'margin-right' : '40px'});
//	        $('#listeDataTable').css({'padding-left' : '-20px'});
//    	});
//    }
//   
//    function listeUtilisateurs(){
//    	$("#listeRecherche").fadeOut(function(){
//    		$("#contenu").fadeIn("fast"); 
//    		$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES UTILISATEURS </div>");
//    		
//    		$('div .dataTables_paginate').css({ 'margin-right' : '0px'});
//    	});
//    }
//    
//    function confirmation(id){
//    	$( "#visualisation" ).dialog({
//    	    resizable: false,
//    	    height:400,
//    	    width:485,
//    	    autoOpen: false,
//    	    modal: true,
//    	    buttons: {
//    	        "Terminer": function() {
//    	            $( this ).dialog( "close" );             	     
//    	            return false;
//    	        }
//    	   }
//    	  });
//    }
//    
//    function visualiser(id){
//   	 confirmation(id);
//   	 var cle = id;
//        var chemin = tabUrl[0]+'public/admin/visualisation';
//        $.ajax({
//            type: 'POST',
//            url: chemin ,
//            data: $(this).serialize(),  
//            data:'id='+cle,
//            success: function(data) {    
//            	    var result = jQuery.parseJSON(data);  
//            	     $("#info").html(result);
//            	     
//            	     $("#visualisation").dialog('open'); 
//            	       
//            },
//            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//            dataType: "html"
//        });
//   }
//    
//    
//   function nouvelUtilisateur(id){ 
//	
//		$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> NOUVEL UTILISATEUR</div>");
//	    var cle = id;
//	    var chemin = tabUrl[0]+'public/admin/nouvel-utilisateur';
//	    $.ajax({
//	        type: 'POST',
//	        url: chemin ,
//	        data:'id='+cle,
//	        success: function(data) {    
//	        	    var result = jQuery.parseJSON(data);  //alert(result);
//	        	     $("#scriptFormUtilisationPopulate").html(result);
//	        	     //PASSER A SUIVANT
//	        	     $('#FormUtilisateur').animate({
//	        	         height : 'toggle'
//	        	      },1000);
//	        	     $('#listeRecherche').animate({
//	        	         height : 'toggle'
//	        	     },1000);
//	        },
//	        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
//	        dataType: "html"
//	    });
//   }
//   
//   function listePrecedent(){
//		$("#titre").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> RECHERCHE DU NOUVEL UTILISATEUR A AJOUTER </div>");
//
//		$('#FormUtilisateur').animate({
//	         height : 'toggle'
//	      },1000);
//	     $('#listeRecherche').animate({
//	         height : 'toggle'
//	     },1000);
//	     
//	     return false;
//   }