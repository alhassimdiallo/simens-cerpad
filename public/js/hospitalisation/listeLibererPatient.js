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

    					"sAjaxSource": ""+tabUrl[0]+"public/hospitalisation/liste-liberer-patient-ajax", 
    					
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
  
    //FILTRE POUR AFFICHER LES PATIENTS LIBERER PAR LE MAJOR  
    //FILTRE POUR AFFICHER LES PATIENTS LIBERER PAR LE MAJOR  
    $('#afficherPatientsALiberer').css({'font-weight':'bold', 'font-size': '17px' });
    oTable.fnFilter( 'Patientslibre0' );
    $('#afficherPatientsALiberer').click(function(){
    	oTable.fnFilter( 'Patientslibre0' );
    	$('#afficherPatientsALiberer').css({'font-weight':'bold', 'font-size': '17px' });
    	$('#afficherPatientsLibres').css({'font-weight':'normal', 'font-size': '14px' });
    });

    $('#afficherPatientsLibres').click(function(){
    	oTable.fnFilter( 'Patientslibre1' );
    	$('#afficherPatientsALiberer').css({'font-weight':'normal', 'font-size': '14px'});
    	$('#afficherPatientsLibres').css({'font-weight':'bold', 'font-size': '17px' });
    });
    
    
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    
    function vuedetails(id_demande_hospi){
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	var id_cons = $("#"+id_demande_hospi+"idCons").val();
    	var id_hosp = $("#"+id_demande_hospi+"hp").val();
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/liberation-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'id_cons':id_cons, 'id_hosp':id_hosp, 'id_demande_hospi':id_demande_hospi},
            success: function(data) {
           	         
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> LIB&Eacute;RATION DU PATIENT </div>");
            	var result = jQuery.parseJSON(data);
            	$("#contenu").fadeOut(function(){
            		$("#vue_patient").html(result).fadeIn("fast"); 
            		$("#annulerLiberer").click(function(){
                    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> HOSPITALISATION </div>");
                    	$("#vue_patient").fadeOut(function(){
                    		$("#contenu").fadeIn("fast");
                    	});
                    	return false;
            		});
            		
            		$("#terminerLiberer").click(function(){ 
            			if(click_info_pat == 1){
            				$("#titre_info_liste").trigger('click');
            			}
            			else{
            				ConfirmationLiberationPatient();
                			$("#Confirmation").dialog('open');
            			}
            			return false;
            		});
            	}); 
            	     
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
        });
    	
    }
    
    function vueDetailsLiberation(id_demande_hospi){
    	var id_personne = $("#"+id_demande_hospi+"idPers").val();
    	var id_cons = $("#"+id_demande_hospi+"idCons").val();
    	var id_hosp = $("#"+id_demande_hospi+"hp").val();
    	
    	var chemin = tabUrl[0]+'public/hospitalisation/info-patient-liberer';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:{'id_personne':id_personne, 'id_cons':id_cons, 'id_hosp':id_hosp, 'id_demande_hospi':id_demande_hospi},
            success: function(data) {
            	$("#titre").replaceWith("<div id='titre2' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> INFORMATIONS </div>");
            	var result = jQuery.parseJSON(data);
            	$("#vue_patient").html('');
            	$("#contenu").fadeOut(function(){
            		$("#vue_patient_liberer").html(result).fadeIn("fast"); 
            		
            		$("#terminerVisualisationLiberation").click(function(){
                    	$("#titre2").replaceWith("<div id='titre' style='font-family: police2; color: green; font-size: 20px; font-weight: bold; padding-left:20px;'><iS style='font-size: 25px;'>&curren;</iS> HOSPITALISATION </div>");
                    	$("#vue_patient_liberer").fadeOut(function(){
                    		$("#contenu").fadeIn("fast");
                    	});
                    	return false;
            		});
            		
            	});
            },
            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
            dataType: "html"
            
        });
        
    }
    
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    /************************************************************************************************************************/
    function ConfirmationLiberationPatient(){
    	$( "#Confirmation" ).dialog({
    		resizable: false,
    		height:180,
    	    width:390,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Oui": function() {
    				$( this ).dialog( "close" );
    				
    				setTimeout(function(){
        				LiberationLit();
        				$( "#ConfirmationLiberationLit" ).dialog('open');    					
    				},300);

    				return false;
    			},
    			
    			"Non": function() {
    				$( this ).dialog( "close" );             	     
    				return false;
    			}
    		}
    	});
    }
    
    function LiberationLit(){
    	$( "#ConfirmationLiberationLit" ).dialog({
    		resizable: false,
    		height:180,
    	    width:390,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Oui": function() {
    				$( this ).dialog( "close" );
    				$('#liberer_lit').val(1);
    				var formulaireLibererPatient = document.getElementById("Formulaire_Liberer_Patient_Major");
    				formulaireLibererPatient.submit();
    				
    				return false;
    			},
    			
    			"Non": function() {
    				$( this ).dialog( "close" );    
    				$('#liberer_lit').val(0);
    				var formulaireLibererPatient = document.getElementById("Formulaire_Liberer_Patient_Major");
    				formulaireLibererPatient.submit();
    				
    				return false;
    			}
    		}
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
    	$('#titre_info_liste').click(function(){ click_info_pat = 0;
    		$("#titre_info_liste").replaceWith(
    			"<span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> lib&eacute;ration du patient "+
    		    "</span>");
    		animationPliantDepliant3();
    		$('#info_liste').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant3() {
    	$('#titre_info_liste').click(function(){ click_info_pat = 1;
    		$("#titre_info_liste").replaceWith(
    			"<span id='titre_info_liste' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> lib&eacute;ration du patient "+
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
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> Infos sur la demande d'hospitalisation"+
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
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> Infos sur la demande d'hospitalisation"+
    		    "</span>");
    		depliantPlus4();
    		$('#info_demande').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    /**INFO LISTE**/
    function depliantPlus5() {
    	$('#titre_info_liberation_liste').click(function(){
    		$("#titre_info_liberation_liste").replaceWith(
    			"<span id='titre_info_liberation_liste' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/minus.png' /> infos sur la lib&eacute;ration du patient "+
    		    "</span>");
    		animationPliantDepliant5();
    		$('#info_liste').animate({
    			height : 'toggle'
    		},1000);
    		return false;
    	});
    }
    
    function animationPliantDepliant5() {
    	$('#titre_info_liberation_liste').click(function(){
    		$("#titre_info_liberation_liste").replaceWith(
    			"<span id='titre_info_liberation_liste' style='margin-left:-10px; cursor:pointer;'>" +
    			"<img src='"+tabUrl[0]+"public/img/light/plus.png' /> infos sur la lib&eacute;ration du patient "+
    		    "</span>");
    		depliantPlus5();
    		$('#info_liste').animate({
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
