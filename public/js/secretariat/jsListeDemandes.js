    var nb="_TOTAL_";
    var asInitVals = new Array();
    var base_url = window.location.toString();
	var tabUrl = base_url.split("public");
	//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
    function confirmation(id){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:485,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );
	            
	            var cle = id;
	            var chemin = tabUrl[0]+'public/facturation/supprimer';
	            $.ajax({
	                type: 'POST',
	                url: chemin ,
	                data: $(this).serialize(),  
	                data:'id='+cle,
	                success: function(data) {
	                	     var result = jQuery.parseJSON(data);  
	                	     nb = result;
	                	     $("#"+cle).parent().parent().fadeOut(function(){
		                	 	 $("#"+cle).empty();
		                	 });
	                },
	                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	                dataType: "html"
	            });
	        },
	        "Annuler": function() {
                $(this).dialog( "close" );
            }
	   }
	  });
    }
    
    function envoyer(id){
   	 confirmation(id);
     $("#confirmation").dialog('open');
   	}
    
   
/**********************************************************************************/
  
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

    	"sAjaxSource":  tabUrl[0]+"public/secretariat/liste-demandes-ajax",
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
 
    			if($(e.target).text() == 'Détails' || $(e.target).is('#visualiserCTX')){
    				visualiser(id);
    			} else 
    				if($(e.target).text() == 'Liste demandes' || $(e.target).is('#modifierCTX')){
    					listeDemandes(id);
    				}
    			
    		}
    	
    	}).bind('mousedown', function (e) {
    			var aData = oTable.fnGetData( this );
    		    id = aData[7];
    	});
    	
    }

    
    function visualiser(id){
    	var cle = id;
        var chemin = tabUrl[0]+'public/secretariat/infos-patient';
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
            	    		 $('#titre span').html('LISTE DES DEMANDES PAR PATIENT'); 
                	    	 $('#contenu').fadeIn();
                	    	 $('#vue_patient').html("");
                	     });
            	     });
            }
        
        });
    	
    }
    
    function listeDemandes(id){
    	
    	var cle = id;
        var chemin = tabUrl[0]+'public/secretariat/get-informations-patient';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'id='+cle,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);
            	     $('#info_patient').html(result[0]);
            	     $('#listeAnalysesParDemande').html(result[1]);
            	     $('.visualiser'+result[2]).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");
            	     
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
    	var chemin = tabUrl[0]+'public/secretariat/get-liste-analyses-demandees';
        $.ajax({
            type: 'POST',
            url: chemin ,
            data:'iddemande='+iddemande,
            success: function(data) {
            	     var result = jQuery.parseJSON(data);
            	     //Ici on modifie les icones 
            	     $('.iconeListeAffichee').html("<img style='padding-left: 3px; cursor: pointer;' src='../images_icons/transfert_droite2.png' />");
            	     $('.visualiser'+iddemande).html("<img style='padding-left: 3px; ' src='../images_icons/transfert_droite.png' />");

            	     $('#liste_analyses_demandes').html(result);
            	     listeAnalysesDemandes();
            }
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
    		"<textarea style='height: 77%; width: 95%; max-height: 77%; max-width: 95%;' id='diagnostic_demande' > </textarea>" +
    		"<button class='btn' style='cursor:pointer; margin-top: 7px;' onclick='popupFermer(); return false;'>Fermer</button>" +
    		"</div>" +
    		"<script> $('#diagnostic_demande').val($('#diagnostic_demande_text').val()).attr({'readonly': true}).css({'background':'#fefefe'}); </script>" 
    	});
    	
    }
    
    