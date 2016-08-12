    var nb="_TOTAL_";
    var asInitVals = new Array();
    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
   
    var oTable;
    function initialisation(){
    	 $( "#accordions" ).accordion();
    	 $( "button" ).button();
    	    
   
    	 oTable = $('#patient').dataTable
    	 ( {
    	
    		 "sPaginationType": "full_numbers",
    		 "aLengthMenu": [5,7,10,15],
    		 "aaSorting": [],
    		 "oLanguage": {
    			 "sInfo": "_START_ &agrave; _END_ sur _TOTAL_ patients",
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

    		 "sAjaxSource":  tabUrl[0]+"public/laboratoire/liste-demandes-analyses-ajax",
    		 "fnDrawCallback": function() 
    	
    		 {
    			 clickRowHandler();
    		 }
    	 } );
    	
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

    $(".boutonTerminer").html('<button type="submit" id="terminer" style=" font-family: police2; font-size: 17px; font-weight: bold;"> Terminer </button>');

   }
    

 
   function clickRowHandler() 
   {
    	var id;
    	$('#patient tbody tr').contextmenu({
    		target: '#context-menu',
    		onItem: function (context, e) {
    			
    			if($(e.target).text() == 'Visualiser' || $(e.target).is('#visualiserCTX')){
    				visualiser(id);
    			} else 
    				if($(e.target).text() == 'Liste demandes' || $(e.target).is('#listeDemandesCTX')){
    					listeDemandes(id);
    				}
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[7];
    	});
    	
    	
    	
    	$("#patient tbody tr").bind('dblclick', function (event) {
    		var aData = oTable.fnGetData( this );
    		var id = aData[7];
    		visualiser(id);
    	});
    	
    }
    
    function visualiser(id){
    	var cle = id;
        var chemin = tabUrl[0]+'public/laboratoire/infos-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data: $(this).serialize(),  
            data:'id='+cle,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);  
            	     
            	     $('#vue_patient').html(result);
            	     $('#contenu').fadeOut(function(){
            	    	 $('#titre span').html('ETAT CIVIL DU PATIENT'); 
            	    	 $('#vue_patient').fadeIn();
            	     });
            	     
            	     $('#terminer').click(function(){
            	    	 $('#vue_patient').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES PATIENTS'); 
                	    	 $('#contenu').fadeIn();
                	    	 $('#vue_patient').html("");
                	     });
            	     });
            }
        
        });
    	
    }
    
    function listeDemandes(id){
    	
    	var cle = id;
        var chemin = tabUrl[0]+'public/laboratoire/get-informations-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'id='+cle,
            success: function(data) {
            	     var result = jQuery.parseJSON(data); 
            	     $('#info_patient').html(result[0]);
            	     $('#listeAnalysesParDemande').html(result[1]);
            	     $('.visualiser'+result[2]).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");
            	     $('.dateAffichee_'+result[2]).css({'color' : 'green'});
            	     
            	     $('#contenu').fadeOut(function(){
            	    	 $('#titre span').html('LISTE DES DEMANDES D\'ANALYSE'); 
            	    	 $('#liste_demandes').fadeIn(100);
            	     });
        	            	 
            	     $('.boutonTerminer').click(function(){
            	    	 $('#liste_demandes').fadeOut(function(){
            	    		 $('#titre span').html('LISTE DES DEMANDES PAR PATIENT'); 
            	    		 $('#contenu').fadeIn(300);
            	    	 });
            	    		 
            	     });
            }
        });
    	
    }
    
    function listeDemandesAnalyses()
    {
        var oTable2 = $('#listeDemandesFiltre').dataTable
        ( {
        	"bDestroy":true,
    		"sPaginationType": "full_numbers",
    		"aLengthMenu": [3,5],
    		"iDisplayLength": 3,
        	"aaSorting": [],
        	"oLanguage": {
        		"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ ",
        		"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
        		"sInfoFiltered": "",
        		"sUrl": "",
        		"oPaginate": {
        			"sFirst":    "|<",
        			"sPrevious": "<",
        			"sNext":     ">",
        			"sLast":     ">|",
        		},
        		
        	},

        } );
        
        var asInitVals = new Array();
    	
    	//le filtre du select
    	$('#filter_statut').change(function() 
    	{					
    		oTable2.fnFilter( this.value );
    	});
    	
    	$(".foot_style_demande input").keyup( function () {
    		/* Filter on the column (the index) of this element */
    		oTable2.fnFilter( this.value, $(".foot_style_demande input").index(this) );
    	} );
    	
    	/*
    	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
    	 * the footer
    	 */
    	$(".foot_style_demande input").each( function (i) {
    		asInitVals[i] = this.value;
    	} );
    	
    	$(".foot_style_demande input").focus( function () {
    		if ( this.className == "search_init" )
    		{
    			this.className = "";
    			this.value = "";
    		}
    	} );
    	
    	$(".foot_style_demande input").blur( function (i) {
    		if ( this.value == "" )
    		{
    			this.className = "search_init";
    			this.value = asInitVals[$(".foot_style_demande input").index(this)];
    		}
    	} );
    	
    }
    
    

    function listeAnalysesDemandes()
    {
        var oTable2 = $('#listeAnalyseFiltre').dataTable
        ( {
        	"bDestroy":true,
    		"sPaginationType": "full_numbers",
    		"aLengthMenu": [3,5],
    		"iDisplayLength": 3,
        	"aaSorting": [],
        	"oLanguage": {
        		"sInfo": "_START_ &agrave; _END_ sur _TOTAL_ analyses",
        		"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
        		"sInfoFiltered": "",
        		"sUrl": "",
        		"oPaginate": {
        			"sFirst":    "|<",
        			"sPrevious": "<",
        			"sNext":     ">",
        			"sLast":     ">|",
        		},
        		
        	},

        } );
        
        var asInitVals = new Array();
    	
    	//le filtre du select
    	$('#filter_statut').change(function() 
    	{					
    		oTable2.fnFilter( this.value );
    	});
    	
    	$(".foot_style_analyse input").keyup( function () {
    		/* Filter on the column (the index) of this element */
    		oTable2.fnFilter( this.value, $(".foot_style_analyse input").index(this) );
    	} );
    	
    	/*
    	 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
    	 * the footer
    	 */
    	$(".foot_style_analyse input").each( function (i) {
    		asInitVals[i] = this.value;
    	} );
    	
    	$(".foot_style_analyse input").focus( function () {
    		if ( this.className == "search_init" )
    		{
    			this.className = "";
    			this.value = "";
    		}
    	} );
    	
    	$(".foot_style_analyse input").blur( function (i) {
    		if ( this.value == "" )
    		{
    			this.className = "search_init";
    			this.value = asInitVals[$(".foot_style_analyse input").index(this)];
    		}
    	} );
    	
    }
    
    function vueListeAnalyses(iddemande){
    	var chemin = tabUrl[0]+'public/laboratoire/get-liste-analyses-demandees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'iddemande='+iddemande,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);
            	     //Ici on modifie les icones 
            	     $('.iconeListeAffichee').html("<img style='padding-left: 3px; cursor: pointer;' src='../images_icons/transfert_droite2.png' />");
            	     $('.visualiser'+iddemande).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");
            	     $('.dateAffichee').css({'color' : 'black'});
            	     $('.dateAffichee_'+iddemande).css({'color' : 'green'});
            	     
            	     
            	     $('#liste_analyses_demandes').html(result);
            	     listeAnalysesDemandes();
            }
        });
    }
    
    function gestionFormuleLeucocytaire(){
    	
    	//Polynucléaires neutrophiles
    	//Polynucléaires neutrophiles
    	$("#champ1, #champ7").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ7 = $("#champ7").val();
    		if( champ1 && champ7 ){
    			var resultatChamp2 = (champ1*champ7)/100;
    			$("#champ2").val(resultatChamp2);
    		}
    		else { $("#champ2").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ7 = $("#champ7").val();
    		if( champ1 && champ7 ){
    			var resultatChamp2 = (champ1*champ7)/100;
    			$("#champ2").val(resultatChamp2);
    		}
    		else { $("#champ2").val(null); }
    	} );
    	
    	//Polynucléaires eosinophiles
    	//Polynucléaires eosinophiles
    	$("#champ1, #champ8").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ8 = $("#champ8").val();
    		if( champ1 && champ8 ){
    			var resultatChamp3 = (champ1*champ8)/100;
    			$("#champ3").val(resultatChamp3);
    		}
    		else { $("#champ3").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ8 = $("#champ8").val();
    		if( champ1 && champ8 ){
    			var resultatChamp3 = (champ1*champ8)/100;
    			$("#champ3").val(resultatChamp3);
    		}
    		else { $("#champ3").val(null); }
    	} );
    	
    	//Polynucléaires basophiles
    	//Polynucléaires basophiles
    	$("#champ1, #champ9").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ9 = $("#champ9").val();
    		if( champ1 && champ9 ){
    			var resultatChamp4 = (champ1*champ9)/100;
    			$("#champ4").val(resultatChamp4);
    		}
    		else { $("#champ4").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ9 = $("#champ9").val();
    		if( champ1 && champ9 ){
    			var resultatChamp4 = (champ1*champ9)/100;
    			$("#champ4").val(resultatChamp4);
    		}
    		else { $("#champ4").val(null); }
    		
    	} );
    	
    	//Lymphocytes Lymphocytes
    	//Lymphocytes Lymphocytes
    	$("#champ1, #champ10").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ10 = $("#champ10").val();
    		if( champ1 && champ10 ){
    			var resultatChamp5 = (champ1*champ10)/100;
    			$("#champ5").val(resultatChamp5);
    		}
    		else { $("#champ5").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ10 = $("#champ10").val();
    		if( champ1 && champ10 ){
    			var resultatChamp5 = (champ1*champ10)/100;
    			$("#champ5").val(resultatChamp5);
    		}
    		else { $("#champ5").val(null); }
    	} );
    	
    	//Monocytes Monocytes
    	//Monocytes Monocytes
    	$("#champ1, #champ11").keyup( function () {
    		var champ1 = $("#champ1").val();
    		var champ11 = $("#champ11").val();
    		if( champ1 && champ11 ){
    			var resultatChamp6 = (champ1*champ11)/100;
    			$("#champ6").val(resultatChamp6);
    		}
    		else { $("#champ6").val(null); }
    		
    	} ).change( function () {
    		var champ1 = $("#champ1").val();
    		var champ11 = $("#champ11").val();
    		if( champ1 && champ11 ){
    			var resultatChamp6 = (champ1*champ11)/100;
    			$("#champ6").val(resultatChamp6);
    		}
    		else { $("#champ6").val(null); }
    	} );
    }
    
    function getChampsNfs(){
    	var tab = new Array();
    	var i;
    	for(i = 1 ; i <= 23 ; i++){
    		if($('#champ'+i).val()){ tab[i] = $('#champ'+i).val(); }
    		else { tab[i] = null; }
    	}
    	tab[i] = $('#type_materiel_nfs').val();
    	return tab;
    }
    
    
    function getTypageHemoglobine(){
    	var tab = [];
    	tab[1] = $('#type_materiel_typage_hemoglobine').val();
    	tab[2] = $('#typage_hemoglobine').val();
    	
    	return tab;
    }
    
    function getElectroHemo(){
    	$('#electro_hemo_moins').toggle(false);
	    
    	$('#electro_hemo_plus').click(function(){
	    	var nbLigne = $("#electro_hemo tr").length;
	    	$('#electro_hemo_moins').toggle(true);
	    	
	    	if(nbLigne < 10){
	    		var html ="<tr id='electro_hemo_"+nbLigne+"' class='ligneAnanlyse' style='width: 100%;'>"+
                            "<td style='width: 45%;'><label class='lab1'><span style='font-weight: bold; '>  <input id='electro_hemo_label_"+nbLigne+"' type='text' style='font-weight: bold; padding-right: 5px; margin-right: 30px;'> </span></label></td>"+
                            "<td style='width: 35%;'><label class='lab2' style='padding-top: 5px;'> <input id='electro_hemo_valeur_"+nbLigne+"' type='number' step='any'> % </label></td>"+
                            "<td style='width: 20%;'><label class='lab3' style='padding-top: 5px; width: 80%;'> </label></td>"+
                          "</tr>";

		    	$('#electro_hemo_'+(nbLigne-1)).after(html);
		    	
		    	if(nbLigne == 9){
		    		$('#electro_hemo_plus').toggle(false);
		    	}
	    	}

	    });
	    
	    $('#electro_hemo_moins').click(function(){ 
	    	var nbLigne = $("#electro_hemo tr").length;
	    	
	    	if(nbLigne > 2){
		    	$('#electro_hemo_'+(nbLigne-1)).remove();
		    	if(nbLigne == 3){ 
		    		$('#electro_hemo_moins').toggle(false);
		    	}
		    	
		    	if(nbLigne == 10){
		    		$('#electro_hemo_plus').toggle(true);
		    	}
	    	}

	    });
    }
    
    //Resultats d'une seule analyse
    //Resultats d'une seule analyse
    //Resultats d'une seule analyse
    function resultatsAnalyses(idanalyse, iddemande){
    	var tab = [];
    	$( "#resultatsAnalyses" ).dialog({
    		resizable: false,
    		height:670,
    		width:730,
    		autoOpen: false,
    		modal: true,
    		buttons: {
    			"Enregistrer": function() { 
    				     if(idanalyse ==  1) { tab = getChampsNfs();}
    				else if(idanalyse ==  2) { tab[1] = $('#groupe').val(); tab[2] =  $('#rhesus').val(); }
    				else if(idanalyse ==  3) { tab[1] = $('#antigene_d_faible').val(); }
    				else if(idanalyse ==  4) { tab[1] = $('#test_combs_direct').val(); }
    				else if(idanalyse ==  5) { tab[1] = $('#test_combs_indirect').val(); }
       				else if(idanalyse ==  6) { tab[1] = $('#test_compatibilite').val(); }
       				else if(idanalyse ==  7) { tab[1] = $('#vitesse_sedimentation').val(); }
    				else if(idanalyse ==  8) { tab[1] = $('#test_demmel').val(); }
    				else if(idanalyse ==  9) { tab[1] = $('#taux_reticulocyte').val(); } 
    				else if(idanalyse == 10) { tab[1] = $('#goutte_epaisse').val(); tab[2] = $('#densite_parasitaire').val(); }
    				     
    				else if(idanalyse == 14) { tab[1] = $('#temps_quick_temoin').val(); tab[2] = $('#temps_quick_patient').val(); tab[3] = $('#taux_prothrombine_patient').val(); tab[4] = $('#inr_patient').val(); }
    				else if(idanalyse == 15) { tab[1] = $('#tca_patient').val(); tab[2] = $('#temoin_patient').val(); }
    				else if(idanalyse == 16) { tab[1] = $('#fibrinemie').val();  }
    				else if(idanalyse == 17) { tab[1] = $('#temps_saignement').val();  }
    				     
    				else if(idanalyse == 21) { tab[1] = $('#glycemie_1').val(); tab[2] = $('#glycemie_2').val(); }
    				else if(idanalyse == 22) { tab[1] = $('#creatininemie').val(); }
    				else if(idanalyse == 23) { tab[1] = $('#uree_sanguine').val(); }
    				else if(idanalyse == 24) { tab[1] = $('#acide_urique').val(); }
    				else if(idanalyse == 25) { tab[1] = $('#cholesterol_total_1').val(); tab[2] = $('#cholesterol_total_2').val(); }
    				else if(idanalyse == 26) { tab[1] = $('#triglycerides_1').val(); tab[2] = $('#triglycerides_2').val(); }
    				else if(idanalyse == 27) { tab[1] = $('#cholesterol_HDL_1').val(); tab[2] = $('#cholesterol_HDL_2').val(); }
    				else if(idanalyse == 28) { tab[1] = $('#cholesterol_LDL_1').val(); tab[2] = $('#cholesterol_LDL_2').val(); }
    				else if(idanalyse == 29) { 
    					tab[1] = $('#cholesterol_total_1').val(); tab[2] = $('#cholesterol_total_2').val();
    				    tab[3] = $('#cholesterol_HDL_1').val();   tab[4] = $('#cholesterol_HDL_2').val(); 
    				    tab[5] = $('#cholesterol_LDL_1').val();   tab[6] = $('#cholesterol_LDL_2').val(); 
    				    tab[7] = $('#triglycerides_1').val();     tab[8] = $('#triglycerides_2').val(); 
    				}
    				     
    				else if(idanalyse == 31) { tab = getIonogramme(); }     
    				else if(idanalyse == 32) { tab[1] = $('#calcemie').val(); }
    				else if(idanalyse == 33) { tab[1] = $('#magnesemie').val(); }
    				else if(idanalyse == 34) { tab[1] = $('#phosphoremie').val(); }     
    				else if(idanalyse == 35) { tab[1] = $('#tgo_asat').val(); } 
    				else if(idanalyse == 36) { tab[1] = $('#tgp_alat').val(); } 
    				     
    				else if(idanalyse == 38) { tab[1] = $('#phosphatage_alcaline').val(); }   
    				else if(idanalyse == 39) { tab[1] = $('#gama_gt').val(); }   
    				else if(idanalyse == 40) { tab = getFerSerique(); }   
    				     
    				else if(idanalyse == 68) { tab = getTypageHemoglobine(); }
    				     
    				     
    				$( this ).dialog( "close" );

    				$.ajax({
    					type: 'POST',
    					url: tabUrl[0]+'public/laboratoire/enregistrer-resultat',
    					data: {'idanalyse':idanalyse, 'iddemande':iddemande, 'tab':tab},
    					success: function(data) {
    						var result = jQuery.parseJSON(data);  
    						var resultatExiste = result[1]; 
    						if(resultatExiste == 0){
    							$('.resultat_existe'+result[0]).empty();
    						}else {
    							$('.resultat_existe'+result[0]).html("<img  src='../images_icons/tick_16.png' />");
    						}
    					}
    				});
    			},
    			"Annuler": function() {
    				$(this).dialog( "close" );
    			}
    		}
    	});
    }
    
    function resultatAnalyse(iddemande){
    	var chemin = tabUrl[0]+'public/laboratoire/recuperer-analyse';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'iddemande='+iddemande,
            success: function(data) {
            	     var result = jQuery.parseJSON(data); 
            	     var idanalyse = result[0];
            	     resultatsAnalyses(idanalyse, iddemande);
            	     
            	     $('#contenuResultatsAnalysesParType div').empty();
            	     $('#contenuResultatsAnalysesDuneDemande div').empty();
            	     $('#contenuResultatsAnalyses div').empty();
            	     $('#contenuResultatsAnalyses div').html(result[1]);
            	     gestionFormuleLeucocytaire();
            	     rapportCHOL_HDL();
            	     getCreatininemie_umol();
            	     getTcaRatio();
            	     getCholesterolHDL();
            	     getCholesterolLDL();
            	     getCholesterolTotal();
            	     getTriglycerides();
            	     getGlycemieFormule();
            	     getElectrophoreseProteinesFormule();
            	     getElectroHemo();
            	     $("#resultatsAnalyses").dialog('open');
            }
        });
    	
    }
    
    function getChampsGroupeRhesus(){
    	var tab = [];
    	tab[1] = $('#groupe').val();
    	tab[2] = $('#rhesus').val();
    	
    	return tab;
    }
    
    function getGoutteEpaisse(){
    	var tab = [];
    	tab[1] = $('#goutte_epaisse').val();
    	tab[2] = $('#densite_parasitaire').val();
    	
    	return tab;
    }
    
    function getTpInr(){
    	var tab = [];
    	tab[1] = $('#temps_quick_temoin').val(); 
    	tab[2] = $('#temps_quick_patient').val(); 
    	tab[3] = $('#taux_prothrombine_patient').val(); 
    	tab[4] = $('#inr_patient').val();
    	
    	return tab;
    }
    
    function getTca(){
    	var tab = [];
    	tab[1] = $('#tca_patient').val(); 
    	tab[2] = $('#temoin_patient').val();
    	
    	return tab;
    }
    
    function getGlycemie(){
    	var tab = [];
    	tab[1] = $('#glycemie_1').val(); 
    	tab[2] = $('#glycemie_2').val();
    	
    	return tab;
    }
    
    function getCholesterolTotal(){
    	var tab = [];
    	tab[1] = $('#cholesterol_total_1').val(); 
    	tab[2] = $('#cholesterol_total_2').val();
    	
    	return tab;
    }
    
    function getTriglycerides(){
    	var tab = [];
    	tab[1] = $('#triglycerides_1').val(); 
    	tab[2] = $('#triglycerides_2').val();
    	
    	return tab;
    }
    
    function getCholesterolHDL(){
    	var tab = [];
    	tab[1] = $('#cholesterol_HDL_1').val(); 
    	tab[2] = $('#cholesterol_HDL_2').val();
    	
    	return tab;
    }
    
    function getCholesterolLDL(){
    	var tab = [];
    	tab[1] = $('#cholesterol_LDL_1').val(); 
    	tab[2] = $('#cholesterol_LDL_2').val();
    	
    	return tab;
    }
    
    function getIonogramme(){
    	var tab = [];
    	tab[1] = $('#sodium_sanguin').val(); 
		tab[2] = $('#potassium_sanguin').val();
		tab[3] = $('#chlore_sanguin').val();
    	
    	return tab;
    }
    
    function getChol_Total_HDL_LDL_Trigly(){
    	var tab = [];
    	tab[1] = $('#cholesterol_total_1').val(); tab[2] = $('#cholesterol_total_2').val();
	    tab[3] = $('#cholesterol_HDL_1').val();   tab[4] = $('#cholesterol_HDL_2').val(); 
	    tab[5] = $('#cholesterol_LDL_1').val();   tab[6] = $('#cholesterol_LDL_2').val(); 
	    tab[7] = $('#triglycerides_1').val();     tab[8] = $('#triglycerides_2').val(); 
	    
	    return tab;
    }
    
    function getFerSerique(){
    	var tab = [];
    	tab[1] = $('#fer_serique_ug').val();
    	tab[2] = $('#fer_serique_umol').val();
    	
    	return tab;
    }
    
    function getElectrophoreseProteines(){
    	var tab = [];
    	tab[1] = $('#albumine').val();
    	tab[2] = $('#alpha_1').val();
    	tab[3] = $('#alpha_2').val();
    	tab[4] = $('#beta_1').val();
    	tab[5] = $('#beta_2').val();
    	tab[6] = $('#gamma').val();
    	
    	return tab;
    }
    
    //Automatisation des champs calculables  -----  Automatisation des champs calculables
    //Automatisation des champs calculables  -----  Automatisation des champs calculables
    //Automatisation des champs calculables  -----  Automatisation des champs calculables
    function rapportCHOL_HDL(){
    	var cholesterol_total_1 = $('#cholesterol_total_1').val();
    	var cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
    	var rapport = null;
    	
    	if(cholesterol_total_1 && cholesterol_HDL_1){
    		rapport = cholesterol_total_1/cholesterol_HDL_1;
    		
    		$('.rapport_chol_hdl').toggle(true);
    		$('#rapport_chol_hdl').val(rapport.toFixed(2));
    	}
    	
    	$("#cholesterol_total_1, #cholesterol_HDL_1").keyup( function () {
    		var cholesterol_total_1 = $("#cholesterol_total_1").val();
    		var cholesterol_HDL_1 = $("#cholesterol_HDL_1").val();
    		if( cholesterol_total_1 && cholesterol_HDL_1 ){
    			var rapport = cholesterol_total_1/cholesterol_HDL_1;
    			$('.rapport_chol_hdl').toggle(true);
    			$("#rapport_chol_hdl").val(rapport.toFixed(2));
    		}
    		else { $("#rapport_chol_hdl").val(null); }
    		
    	} ).change( function () {
    		var cholesterol_total_1 = $("#cholesterol_total_1").val();
    		var cholesterol_HDL_1 = $("#cholesterol_HDL_1").val();
    		if( cholesterol_total_1 && cholesterol_HDL_1 ){
    			var rapport = cholesterol_total_1/cholesterol_HDL_1;
    			$('.rapport_chol_hdl').toggle(true);
    			$("#rapport_chol_hdl").val(rapport.toFixed(2));
    		}
    		else { $("#rapport_chol_hdl").val(null); }
    		
    	} );
    }
    
    function getCreatininemie_umol(){
    	var creatininemie = $('#creatininemie').val();
    	var valeur_umol = null;
    	if(creatininemie){
    		valeur_umol = creatininemie * 8.84;
    		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
    	}else{
    		$('#creatininemie_umol').val(null);
    	}
    	
    	$('#creatininemie').keyup( function () {
    		creatininemie = $('#creatininemie').val();
    		if(creatininemie){
        		valeur_umol = creatininemie * 8.84;
        		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
        	}else{
        		$('#creatininemie_umol').val(null);
        	}
    	}).change( function(){
    		creatininemie = $('#creatininemie').val();
    		if(creatininemie){
        		valeur_umol = creatininemie * 8.84;
        		$('#creatininemie_umol').val(valeur_umol.toFixed(2));
        	}else{
        		$('#creatininemie_umol').val(null);
        	}
    	});
    	
    }
    
    function getTcaRatio(){
    	var tca_patient = $('#tca_patient').val();
    	var temoin_patient = $('#temoin_patient').val();
    	
    	if(tca_patient && temoin_patient){
    		var tca_ratio = tca_patient/temoin_patient;
    		$('#tca_ratio').val(tca_ratio.toFixed(2));
    	}else{
    		$('#tca_ratio').val(null);
    	}
    	
    	$('#tca_patient, #temoin_patient').keyup( function () {
    		var tca_patient = $('#tca_patient').val();
        	var temoin_patient = $('#temoin_patient').val();
        	
    		if(tca_patient && temoin_patient){
        		var tca_ratio = tca_patient/temoin_patient;
        		$('#tca_ratio').val(tca_ratio.toFixed(2));
        	}else{
        		$('#tca_ratio').val(null);
        	}
    		
    	}).change( function(){
    		var tca_patient = $('#tca_patient').val();
        	var temoin_patient = $('#temoin_patient').val();
        	
    		if(tca_patient && temoin_patient){
        		var tca_ratio = tca_patient/temoin_patient;
        		$('#tca_ratio').val(tca_ratio.toFixed(2));
        	}else{
        		$('#tca_ratio').val(null);
        	}
    	});
    	
    }
    
    function getCholesterolTotal(){
    	var cholesterol_total_1 = $('#cholesterol_total_1').val();
    	var valeur_mmol = null;
    	
    	$('#cholesterol_total_1').keyup( function () {
    		cholesterol_total_1 = $('#cholesterol_total_1').val();
    		if(cholesterol_total_1){
        		valeur_mmol = cholesterol_total_1 * 2.587;
        		$('#cholesterol_total_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_total_2').val(null);
        	}
    	}).change( function(){
    		cholesterol_total_1 = $('#cholesterol_total_1').val();
    		if(cholesterol_total_1){
        		valeur_mmol = cholesterol_total_1 * 2.587;
        		$('#cholesterol_total_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_total_2').val(null);
        	}
    	});
    	
    }
    
    function getCholesterolHDL(){
    	var cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
    	var valeur_mmol = null;
    	
    	$('#cholesterol_HDL_1').keyup( function () {
    		cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
    		if(cholesterol_HDL_1){
        		valeur_mmol = cholesterol_HDL_1 * 2.587;
        		$('#cholesterol_HDL_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_HDL_2').val(null);
        	}
    	}).change( function(){
    		cholesterol_HDL_1 = $('#cholesterol_HDL_1').val();
    		if(cholesterol_HDL_1){
        		valeur_mmol = cholesterol_HDL_1 * 2.587;
        		$('#cholesterol_HDL_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_HDL_2').val(null);
        	}
    	});
    	
    }
    
    function getCholesterolLDL(){
    	var cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
    	var valeur_mmol = null;
    	
    	$('#cholesterol_LDL_1').keyup( function () {
    		cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
    		if(cholesterol_LDL_1){
        		valeur_mmol = cholesterol_LDL_1 * 2.587;
        		$('#cholesterol_LDL_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_LDL_2').val(null);
        	}
    	}).change( function(){
    		cholesterol_LDL_1 = $('#cholesterol_LDL_1').val();
    		if(cholesterol_LDL_1){
        		valeur_mmol = cholesterol_LDL_1 * 2.587;
        		$('#cholesterol_LDL_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#cholesterol_LDL_2').val(null);
        	}
    	});
    	
    }
    

    function getTriglycerides(){
    	var triglycerides_1 = $('#triglycerides_1').val();
    	var valeur_mmol = null;
    	
    	$('#triglycerides_1').keyup( function () {
    		triglycerides_1 = $('#triglycerides_1').val();
    		if(triglycerides_1){
        		valeur_mmol = triglycerides_1 * 1.143;
        		$('#triglycerides_2').val(valeur_mmol.toFixed(3));
        	}else{
        		$('#triglycerides_2').val(null);
        	}
    	}).change( function(){
    		triglycerides_1 = $('#triglycerides_1').val();
    		if(triglycerides_1){
        		valeur_mmol = triglycerides_1 * 1.143;
        		$('#triglycerides_2').val(valeur_mmol.toFixed(3));
        	}else{
        		$('#triglycerides_2').val(null);
        	}
    	});
    	
    }
    
    
    function getGlycemieFormule(){
    	var glycemie_1 = $('#glycemie_1').val();
    	var valeur_mmol = null;
    	
    	$('#glycemie_1').keyup( function () {
    		glycemie_1 = $('#glycemie_1').val();
    		if(glycemie_1){
        		valeur_mmol = glycemie_1 * 5.55;
        		$('#glycemie_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#glycemie_2').val(null);
        	}
    	}).change( function(){
    		glycemie_1 = $('#glycemie_1').val();
    		if(glycemie_1){
        		valeur_mmol = glycemie_1 * 5.55;
        		$('#glycemie_2').val(valeur_mmol.toFixed(2));
        	}else{
        		$('#glycemie_2').val(null);
        	}
    	});
    	
    }
    
    
    function getElectrophoreseProteinesFormule(){
    	var albumine = $('#albumine').val();
    	var alpha_1  = $('#alpha_1').val();
    	var alpha_2  = $('#alpha_2').val();
    	var beta_1   = $('#beta_1').val();
    	var beta_2   = $('#beta_2').val();
    	var gamma    = $('#gamma').val();
    	var proteine_totale    = $('#proteine_totale').val();
    	
    	$('#albumine, #proteine_totale').keyup( function () {
    		albumine = $('#albumine').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(albumine && proteine_totale){ 
        		var albumine_conc = (albumine * proteine_totale)/100;
        		$('#albumine_conc').val(albumine_conc.toFixed(1));
        	}else{
        		$('#albumine_conc').val(null);
        	}
    	}).change( function(){
    		albumine = $('#albumine').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(albumine && proteine_totale){ 
        		var albumine_conc = (albumine * proteine_totale)/100;
        		$('#albumine_conc').val(albumine_conc.toFixed(1));
        	}else{
        		$('#albumine_conc').val(null);
        	}
    	});
    	
    	$('#alpha_1, #proteine_totale').keyup( function () {
    		alpha_1 = $('#alpha_1').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(alpha_1 && proteine_totale){ 
        		var alpha_1_conc = (alpha_1 * proteine_totale)/100;
        		$('#alpha_1_conc').val(alpha_1_conc.toFixed(1));
        	}else{
        		$('#alpha_1_conc').val(null);
        	}
    	}).change( function(){
    		alpha_1 = $('#alpha_1').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(alpha_1 && proteine_totale){ 
        		var alpha_1_conc = (alpha_1 * proteine_totale)/100;
        		$('#alpha_1_conc').val(alpha_1_conc.toFixed(1));
        	}else{
        		$('#alpha_1_conc').val(null);
        	}
    	});
    	
    	$('#alpha_2, #proteine_totale').keyup( function () {
    		alpha_2 = $('#alpha_2').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(alpha_2 && proteine_totale){ 
        		var alpha_2_conc = (alpha_2 * proteine_totale)/100;
        		$('#alpha_2_conc').val(alpha_2_conc.toFixed(1));
        	}else{
        		$('#alpha_2_conc').val(null);
        	}
    	}).change( function(){
    		alpha_2 = $('#alpha_2').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(alpha_2 && proteine_totale){ 
        		var alpha_2_conc = (alpha_2 * proteine_totale)/100;
        		$('#alpha_2_conc').val(alpha_2_conc.toFixed(1));
        	}else{
        		$('#alpha_2_conc').val(null);
        	}
    	});
    	
    	$('#beta_1, #proteine_totale').keyup( function () {
    		beta_1 = $('#beta_1').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(beta_1 && proteine_totale){ 
        		var beta_1_conc = (beta_1 * proteine_totale)/100;
        		$('#beta_1_conc').val(beta_1_conc.toFixed(1));
        	}else{
        		$('#beta_1_conc').val(null);
        	}
    	}).change( function(){
    		beta_1 = $('#beta_1').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(beta_1 && proteine_totale){ 
        		var beta_1_conc = (beta_1 * proteine_totale)/100;
        		$('#beta_1_conc').val(beta_1_conc.toFixed(1));
        	}else{
        		$('#beta_1_conc').val(null);
        	}
    	});
    	
    	$('#beta_2, #proteine_totale').keyup( function () {
    		beta_2 = $('#beta_2').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(beta_2 && proteine_totale){ 
        		var beta_2_conc = (beta_2 * proteine_totale)/100;
        		$('#beta_2_conc').val(beta_2_conc.toFixed(1));
        	}else{
        		$('#beta_2_conc').val(null);
        	}
    	}).change( function(){
    		beta_2 = $('#beta_2').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(beta_2 && proteine_totale){ 
        		var beta_2_conc = (beta_2 * proteine_totale)/100;
        		$('#beta_2_conc').val(beta_2_conc.toFixed(1));
        	}else{
        		$('#beta_2_conc').val(null);
        	}
    	});
    	
    	$('#gamma, #proteine_totale').keyup( function () {
    		gamma = $('#gamma').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(gamma && proteine_totale){ 
        		var gamma_conc = (gamma * proteine_totale)/100;
        		$('#gamma_conc').val(gamma_conc.toFixed(1));
        	}else{
        		$('#gamma_conc').val(null);
        	}
    	}).change( function(){
    		gamma = $('#gamma').val();
    		proteine_totale    = $('#proteine_totale').val();
    		if(gamma && proteine_totale){ 
        		var gamma_conc = (gamma * proteine_totale)/100;
        		$('#gamma_conc').val(gamma_conc.toFixed(1));
        	}else{
        		$('#gamma_conc').val(null);
        	}
    	});
    }
    
    /**
     * ************************************************
     * ------------------------------------------------
     * ************************************************
     * ------------------------------------------------
     */
    
    //Resultats des analyses d'une seule demande
    //Resultats des analyses d'une seule demande
    //Resultats des analyses d'une seule demande
    function resultatsDesAnalysesDeLaDemande(iddemande, tabAnalyses, tabDemandes){
  	    $( "#resultatsAnalysesDuneDemande" ).dialog({
  	    resizable: false,
  	    height:670,
  	    width:720,
  	    autoOpen: false,
  	    modal: true,
  	    buttons: {
  	    	"Enregistrer": function() {
  	          
  	    		var tab = []; 
  	    		for(var i = 0 ;  i<tabAnalyses.length ; i++){
  	    			var idanalyse = tabAnalyses[i];
  	    			
  	    			     if(idanalyse ==  1) { tab  [1] = getChampsNfs(); }
    				else if(idanalyse ==  2) { tab  [2] = getChampsGroupeRhesus(); }
    				else if(idanalyse ==  3) { tab  [3] = new Array("", $('#antigene_d_faible').val()); }
    				else if(idanalyse ==  4) { tab  [4] = new Array("", $('#test_combs_direct').val()); }
    				else if(idanalyse ==  5) { tab  [5] = new Array("", $('#test_combs_indirect').val()); }
       				else if(idanalyse ==  6) { tab  [6] = new Array("", $('#test_compatibilite').val()); }
       				else if(idanalyse ==  7) { tab  [7] = new Array("", $('#vitesse_sedimentation').val()); }
    				else if(idanalyse ==  8) { tab  [8] = new Array("", $('#test_demmel').val()); }
    				else if(idanalyse ==  9) { tab  [9] = new Array("", $('#taux_reticulocyte').val()); }
    				else if(idanalyse == 10) { tab [10] = getGoutteEpaisse(); }
  	    			     
    				else if(idanalyse == 14) { tab [14] = getTpInr(); }
    				else if(idanalyse == 15) { tab [15] = getTca();  }
    				else if(idanalyse == 16) { tab [16] = new Array("", $('#fibrinemie').val());  }
    				else if(idanalyse == 17) { tab [17] = new Array("", $('#temps_saignement').val());  }
  	    			    
    				else if(idanalyse == 21) { tab [21] = getGlycemie(); }
    				else if(idanalyse == 22) { tab [22] = new Array("", $('#creatininemie').val()); }
    				else if(idanalyse == 23) { tab [23] = new Array("", $('#uree_sanguine').val()); }
    				else if(idanalyse == 24) { tab [24] = new Array("", $('#acide_urique').val()); }
    				else if(idanalyse == 25) { tab [25] = getCholesterolTotal(); }
    				else if(idanalyse == 26) { tab [26] = getTriglycerides(); }
    				else if(idanalyse == 27) { tab [27] = getCholesterolHDL(); }
    				else if(idanalyse == 28) { tab [28] = getCholesterolLDL(); }
    				else if(idanalyse == 29) { tab [29] = getChol_Total_HDL_LDL_Trigly(); }
  	    			     
    			    else if(idanalyse == 31) { tab [31] = getIonogramme(); }
    				else if(idanalyse == 32) { tab [32] = new Array("", $('#calcemie').val()); } 
    				else if(idanalyse == 33) { tab [33] = new Array("", $('#magnesemie').val()); }
    				else if(idanalyse == 34) { tab [34] = new Array("", $('#phosphoremie').val()); }
    				else if(idanalyse == 35) { tab [35] = new Array("", $('#tgo_asat').val()); }
    				else if(idanalyse == 36) { tab [36] = new Array("", $('#tgp_alat').val()); }
  	    			   
    				else if(idanalyse == 38) { tab [38] = new Array("", $('#phosphatage_alcaline').val()); }
    				else if(idanalyse == 39) { tab [39] = new Array("", $('#gama_gt').val()); }
    				else if(idanalyse == 40) { tab [40] = getFerSerique(); }   
  	    		
  	    			     
    				else if(idanalyse == 68) { tab [68] = getTypageHemoglobine(); }
  	    		}
  	    		
  	        	$( this ).dialog( "close" );
  	            
  	            $.ajax({
  	                type: 'POST',
  	                url: tabUrl[0]+'public/laboratoire/enregistrer-resultats-demande',
  	                data:{'tabAnalyses':tabAnalyses, 'tabDemandes':tabDemandes, 'tab':tab},
  	                success: function(data) {
  	                	     var iddemande = jQuery.parseJSON(data);
  	                	     $('.visualiser'+iddemande+' img').trigger('click');
  	                }
  	            });
  	        	
  	        },
  	        
  	        "Annuler": function() {
  	        	$(this).dialog( "close" );
  	        }
  	   }
  	  });
    }
    
    
    function resultatsDesAnalyses(iddemande){
        var chemin = tabUrl[0]+'public/laboratoire/recuperer-les-analyses-de-la-demande';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'iddemande='+iddemande,
            success: function(data) {
            	     var result = jQuery.parseJSON(data); 

            	     var html = result[0];
            	     var tabAnalyses = result[1];
            	     var tabDemandes = result[2];
            	     resultatsDesAnalysesDeLaDemande(iddemande, tabAnalyses, tabDemandes);
            	     
            	     $('#contenuResultatsAnalysesParType div').empty();
            	     $('#contenuResultatsAnalyses div').empty();
               	     $('#contenuResultatsAnalysesDuneDemande div').empty();
            	     $('#contenuResultatsAnalysesDuneDemande div').html(html);
            	     gestionFormuleLeucocytaire();
            	     rapportCHOL_HDL();
            	     getCreatininemie_umol();
            	     getTcaRatio();
            	     getCholesterolHDL();
            	     getCholesterolLDL();
            	     getCholesterolTotal();
            	     getTriglycerides();
            	     getGlycemieFormule();
            	     getElectrophoreseProteinesFormule();
            	     $("#resultatsAnalysesDuneDemande").dialog('open');
            }
        });
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * RECUPERER LES DONNEES POUR L'ENREGISTREMENT PAR TYPE ET PAR ANALYSE ET PAR DATE --TAD
     * id = idpatient
     */
    function getChampsNfs_TAD(id){
    	var tab = new Array();
    	var i;
    	for(i = 1 ; i <= 23 ; i++){
    		if($('.entrezResult_'+id+' #champ'+i).val()){ tab[i] = $('.entrezResult_'+id+' #champ'+i).val(); }
    		else { tab[i] = null; }
    	}
    	tab[i] = $('.entrezResult_'+id+' #type_materiel_nfs').val();
    	return tab;
    }
    

    
    
    //Resultats des analyses demandées pour un type et/ou une date donnée
    //Resultats des analyses demandées pour un type et/ou une date donnée
    //Resultats des analyses demandées pour un type et/ou une date donnée
    function resultatsAnalysesTypes(){
    	  $('#listeAnalyseParType').html('');
    	  $('#infosNbPatientParAnalyse img').attr('title', '').css({'opacity' : '0'});
    	  $( "#resultatsAnalysesParType" ).dialog({
    	    resizable: false,
    	    height:670,
    	    width:720,
    	    autoOpen: false,
    	    modal: true,
    	    buttons: {
    	        "Enregistrer": function() {
    	            //$( this ).dialog( "close" );
    	            
    	        	//alert(getChampsNfs_TAD(listeDesPatientsSelect[1]));
    	            
    	            
    	            return false;
    	            
    	            var chemin = ""; //tabUrl[0]+'public/facturation/supprimer';
    	            $.ajax({
    	                type: 'POST',
    	                url: chemin ,
    	                data: $(this).serialize(),  
    	                data:'id=',
    	                success: function(data) {
    	                	     var result = jQuery.parseJSON(data);  
    	                	     nb = result;
    	                	     $("#"+cle).parent().parent().fadeOut(function(){
    		                	 	 $("#"+cle).empty();
    		                	 });
    	                }
    	            });
    	            
    	        },
    	        "Annuler": function() {
                    $(this).dialog( "close" );
                }
    	   }
    	  });
        
    }
    
    //Recupérer la liste des analyses à l'entrée pour initialisation
    //Recupérer la liste des analyses à l'entrée pour initialisation
    function resultatsDesAnalysesParType(){
    	$('#typeAnalyseDesign').val(1);
    	$('#contenuResultatsAnalyses div').empty();
    	$('#contenuResultatsAnalysesDuneDemande div').empty();
    	$('#contenuResultatsAnalysesParType div').empty();
    	$('#contenuResultatsAnalysesParType div').html('<table> <tr> <td style="margin-top: 20px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
    	resultatsAnalysesTypes();
    	$("#resultatsAnalysesParType").dialog('open');
    	
        $.ajax({
            type: 'POST',
            url: tabUrl[0]+'public/laboratoire/recuperer-les-analyses-demandees-par-type',
            data:{ 'idtype': 1 },
            success: function(data) {
            	var result = jQuery.parseJSON(data);  
            	$('#contenuResultatsAnalysesParType div').html(result);
            	
            	gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	rapportCHOL_HDL();
            	getCreatininemie_umol();
            	getTcaRatio();
            	getCholesterolHDL();
            	getCholesterolLDL();
            	getCholesterolTotal();
            	getTriglycerides();
            	getGlycemieFormule();
            }
        });
    	
    }
    
    //Récupérer la liste des analyses en sélectionnant un type
    //Récupérer la liste des analyses en sélectionnant un type
    function getListeAnalysesDemandees(idtype){ 
    	$('#listeAnalyseParType').html('');
    	$('#listeAnalyseParTypeParDate').html('');
    	$('#listeCodesDesPatients').html('');
    	$('#contenuResultatsAnalysesParType div').empty();
    	$('#contenuResultatsAnalysesParType div').html('<table> <tr> <td style="margin-top: 20px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
    	$('#infosNbPatientParAnalyse img').attr('title', '').css({'opacity' : '0'});
    	$('#infosNbPatientParAnalyseParDate img').attr('title', '').css({'opacity' : '0'});
    	$.ajax({
             type: 'POST',
             url: tabUrl[0]+'public/laboratoire/recuperer-les-analyses-demandees-par-type',
             data:{ 'idtype': idtype },
             success: function(data) {
             	var result = jQuery.parseJSON(data);  
            	$('#contenuResultatsAnalysesParType div').html(result);
            	
            	gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	rapportCHOL_HDL();
            	getCreatininemie_umol();
            	getTcaRatio();
            	getCholesterolHDL();
            	getCholesterolLDL();
            	getCholesterolTotal();
            	getTriglycerides();
            	getGlycemieFormule();
             }
    	});
    }
    
    //Recupérer la liste des patients pour un type et une analyse donnée
    //Recupérer la liste des patients pour un type et une analyse donnée
    function getListePatientsParAnalyse(idanalyse){
    	var idtype = $('#typeAnalyseDesign').val();
    	
    	$('#listeAnalyseParTypeParDate').html('');
    	$('#listeCodesDesPatients').html('');
    	$('#contenuResultatsAnalysesParType div').empty();
    	$('#contenuResultatsAnalysesParType div').html('<table> <tr> <td style="margin-top: 20px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
    	$('#infosNbPatientParAnalyseParDate img').attr('title', '').css({'opacity' : '0'});
    	$.ajax({
             type: 'POST',
             url: tabUrl[0]+'public/laboratoire/recuperer-les-analyses-demandees-par-type-et-analyse',
             data:{ 'idtype': idtype, 'idanalyse': idanalyse },
             success: function(data) {
             	var result = jQuery.parseJSON(data);
            	$('#contenuResultatsAnalysesParType div').html(result);
            	
            	gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	rapportCHOL_HDL();
            	getCreatininemie_umol();
            	getTcaRatio();
            	getCholesterolHDL();
            	getCholesterolLDL();
            	getCholesterolTotal();
            	getTriglycerides();
            	getGlycemieFormule();
            	
             }
    	});
    	
    	
    }
    
    
    
    //Récupérer la liste des patients pour un type, une analyse et une date donnée
    //Récupérer la liste des patients pour un type, une analyse et une date donnée
    function getListePatientsParAnalyseParDate(date){
    	var idtype = $('#typeAnalyseDesign').val();
    	var idanalyse = $('#listeAnalyseParType').val();
    	
    	$('#listeCodesDesPatients').html('');
    	$('#contenuResultatsAnalysesParType div').empty();
    	$('#contenuResultatsAnalysesParType div').html('<table> <tr> <td style="margin-top: 20px;"> Chargement </td> </tr>  <tr> <td align="center"> <img style="margin-top: 20px; width: 50px; height: 50px;" src="../images/loading/Chargement_1.gif" /> </td> </tr> </table>');
    	$.ajax({
             type: 'POST',
             url: tabUrl[0]+'public/laboratoire/recuperer-les-analyses-demandees-par-type-et-analyse-et-date',
             data:{ 'idtype': idtype, 'idanalyse': idanalyse, 'date': date },
             success: function(data) {
             	var result = jQuery.parseJSON(data);
            	$('#contenuResultatsAnalysesParType div').html(result);
            	
            	gestionFormuleLeucocytaire_TAD(listeDesDemandesSelect);
            	rapportCHOL_HDL();
            	getCreatininemie_umol();
            	getTcaRatio();
            	getCholesterolHDL();
            	getCholesterolLDL();
            	getCholesterolTotal();
            	getTriglycerides();
            	getGlycemieFormule();
             }
    	});
    }
    
    
    //Filtrer par patient
    //Filtrer par patient
    function getListeCodesDesPatients(id){
    	
    	if(id){
        	$('.patientListeAnalyse').toggle(false);
        	$('.pat_'+id).toggle(true);
    	}else{
        	$('.patientListeAnalyse').toggle(true);
    	}

    }
    
    
    
    /***
     * ==============================================================================
     * ==============================================================================
     * ==============================================================================
     * ==============================================================================
     */
    
    /**
     * SCRIPT PERMETTANT D'APPLIQUER LA FORMULE POUR L'ENREGISTREMENT DES DONNEES NFS
     * @param demande : liste des demandes
     */
    function gestionFormuleLeucocytaire_TAD(demande){
    	
    	var scriptFormule = "<script>";
    	for(var i=0 ; i<demande.length ; i++){
        	var iddemande = demande[i];
        	
        	//Polynucléaires neutrophiles
        	//Polynucléaires neutrophiles
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ7').keyup( function () {"+
        	                   
        	                    "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
        	                    "var champ7 = $('.ER_"+iddemande+" #champ7').val();"+
        	                    "var champ2 = $('.ER_"+iddemande+" #champ2');"+
        	                    "if( champ1 && champ7 ){"+
        	         			    "var resultatChamp2 = (champ1*champ7)/100;"+
        	         			    "champ2.val(resultatChamp2);"+
        	         		    "}else{ champ2.val(null); }"+
        	                 
        	                 "}).change( function () {"+
        	                 
        	                    "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
     	                        "var champ7 = $('.ER_"+iddemande+" #champ7').val();"+
     	                        "var champ2 = $('.ER_"+iddemande+" #champ2');"+
     	                        "if( champ1 && champ7 ){"+
     	         			        "var resultatChamp2 = (champ1*champ7)/100;"+
     	         			        "champ2.val(resultatChamp2);"+
     	         		        "}else{ champ2.val(null); }"+
        	                 
        	                 "});";
        		
        	
        	//Polynucléaires eosinophiles
        	//Polynucléaires eosinophiles
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ8').keyup( function () {"+
            
                                "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
                                "var champ8 = $('.ER_"+iddemande+" #champ8').val();"+
                                "var champ3 = $('.ER_"+iddemande+" #champ3');"+
                                "if( champ1 && champ8 ){"+
 			                        "var resultatChamp3 = (champ1*champ8)/100;"+
 			                        "champ3.val(resultatChamp3);"+
 		                        "}else{ champ3.val(null); }"+
         
                                "}).change( function () {"+
         
                                   "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
                                   "var champ8 = $('.ER_"+iddemande+" #champ8').val();"+
                                   "var champ3 = $('.ER_"+iddemande+" #champ3');"+
                                   "if( champ1 && champ8 ){"+
			                           "var resultatChamp3 = (champ1*champ8)/100;"+
			                           "champ3.val(resultatChamp3);"+
		                           "}else{ champ3.val(null); }"+
         
                                "});";
        	
        	
        	//Polynucléaires basophiles
        	//Polynucléaires basophiles
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ9').keyup( function () {"+
            
                                "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
                                "var champ9 = $('.ER_"+iddemande+" #champ9').val();"+
                                "var champ4 = $('.ER_"+iddemande+" #champ4');"+
                                "if( champ1 && champ9 ){"+
                                    "var resultatChamp4 = (champ1*champ9)/100;"+
                                    "champ4.val(resultatChamp4);"+
                                "}else{ champ4.val(null); }"+

                                "}).change( function () {"+

                                   "var champ1 = $('.ER_"+iddemande+" #champ1').val();"+
                                   "var champ9 = $('.ER_"+iddemande+" #champ9').val();"+
                                   "var champ4 = $('.ER_"+iddemande+" #champ4');"+
                                   "if( champ1 && champ9 ){"+
                                       "var resultatChamp4 = (champ1*champ9)/100;"+
                                       "champ4.val(resultatChamp4);"+
                                   "}else{ champ4.val(null); }"+

                                "});";
        	
        	
        	//Lymphocytes Lymphocytes
        	//Lymphocytes Lymphocytes
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ10').keyup( function () {"+
            
                                "var champ1  = $('.ER_"+iddemande+" #champ1').val();"+
                                "var champ10 = $('.ER_"+iddemande+" #champ10').val();"+
                                "var champ5  = $('.ER_"+iddemande+" #champ5');"+
                                "if( champ1 && champ10 ){"+
                                    "var resultatChamp5 = (champ1*champ10)/100;"+
                                    "champ5.val(resultatChamp5);"+
                                "}else{ champ5.val(null); }"+

                                "}).change( function () {"+

                                   "var champ1  = $('.ER_"+iddemande+" #champ1').val();"+
                                   "var champ10 = $('.ER_"+iddemande+" #champ10').val();"+
                                   "var champ5  = $('.ER_"+iddemande+" #champ5');"+
                                   "if( champ1 && champ10 ){"+
                                       "var resultatChamp5 = (champ1*champ10)/100;"+
                                       "champ5.val(resultatChamp5);"+
                                   "}else{ champ5.val(null); }"+

                                "});";
        	
        	
        	//Monocytes Monocytes
        	//Monocytes Monocytes
        	scriptFormule += "$('.ER_"+iddemande+" #champ1, .ER_"+iddemande+" #champ11').keyup( function () {"+
            
                                "var champ1  = $('.ER_"+iddemande+" #champ1').val();"+
                                "var champ11 = $('.ER_"+iddemande+" #champ11').val();"+
                                "var champ6  = $('.ER_"+iddemande+" #champ6');"+
                                "if( champ1 && champ11 ){"+
                                    "var resultatChamp6 = (champ1*champ11)/100;"+
                                    "champ6.val(resultatChamp6);"+
                                "}else{ champ6.val(null); }"+

                                "}).change( function () {"+

                                   "var champ1  = $('.ER_"+iddemande+" #champ1').val();"+
                                   "var champ11 = $('.ER_"+iddemande+" #champ11').val();"+
                                   "var champ6  = $('.ER_"+iddemande+" #champ6');"+
                                   "if( champ1 && champ11 ){"+
                                       "var resultatChamp6 = (champ1*champ11)/100;"+
                                       "champ6.val(resultatChamp6);"+
                                   "}else{ champ6.val(null); }"+

                                "});";
    	}
    	
    	scriptFormule += "</script>";
    	$('#scriptFormules').html(scriptFormule);
    
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //Impression des r�sultats des analyses demand�es
    //Impression des r�sultats des analyses demand�es
    //Impression des r�sultats des analyses demand�es
    function imprimerResultatsAnalysesDemandees(iddemande){
    	
    	if(iddemande){
    		var vart = tabUrl[0]+'public/laboratoire/impression-resultats-analyses-demandees';
    		var FormulaireImprimerAnalysesDemandees = document.getElementById("FormulaireImprimerDemandesAnalyses");
    		FormulaireImprimerAnalysesDemandees.setAttribute("action", vart);
    		FormulaireImprimerAnalysesDemandees.setAttribute("method", "POST");
    		FormulaireImprimerAnalysesDemandees.setAttribute("target", "_blank");
    		
    		//Ajout dynamique de champs dans le formulaire
    		var champ = document.createElement("input");
    		champ.setAttribute("type", "hidden");
    		champ.setAttribute("name", 'iddemande');
    		champ.setAttribute("value", iddemande);
    		FormulaireImprimerAnalysesDemandees.appendChild(champ);
    		$("#ImprimerDemandesAnalyses").trigger('click');
    	}
    	
    }
    
    
    
    
    //Recupère la liste des analyses par type
    function getListeAnalysesParType(id){
    	
    	$.ajax({
    		type: 'POST',
    		url: tabUrl[0]+'public/laboratoire/get-liste-analyses',
    		data:{'id':id},
    		success: function(data) {    
    			var result = jQuery.parseJSON(data);  
    			$("#listeAnalyseParType").html(result);
    		},
        
    		error:function(e){ console.log(e); alert("Une erreur interne est survenue! voir -- laboratoire --- getListeAnalysesAction() "); },
    		dataType: "html"
    	});

    }
    

    function popupFermer() {
    	$(null).w2overlay(null);
    }
    
    
    function diagnostic(id){
    	
    	$('#diagnostic_'+id).w2overlay({ html: "" +
    		"" +
    		"<div style='border-bottom:1px solid green; height: 30px; background: #f9f9f9; width: 300px; text-align:center; padding-top: 10px; font-size: 13px; color: green; font-weight: bold;'>Diagnostic</div>" +
    		"<div style='height: 195px; width: 300px; padding-top:10px; text-align:center;'>" +
    		"<textarea style='height: 90%; width: 95%; max-height: 90%; max-width: 95%;' id='diagnostic_demande' > </textarea>" +
    		"</div>" +
    		"<script> $('#diagnostic_demande').val($('#diagnostic_demande_text').val()).attr({'readonly': true}).css({'background':'#fefefe'}); </script>" 
    	});
    	
    }