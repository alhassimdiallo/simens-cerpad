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

    	"sAjaxSource":  tabUrl[0]+"public/secretariat/liste-patients-ajax",
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

	
	//GESTION DES TYPES DE PATIENTS
	//GESTION DES TYPES DE PATIENTS
	//GESTION DES TYPES DE PATIENTS

	$('#patientTous').click(function(){ 
		oTable.fnFilter( '' , 6 );
		$('#patientsDepistes, #patientsExternes').css({ 'font-weight':'normal', 'font-size':'12px' }); 
		$(this).css({ 'font-weight':'bold', 'font-size':'19px' });
		
		$('#t_neg_pos').fadeOut(0);
	});

	$('#patientsDepistes').click(function(){
		oTable.fnFilter( 'patient_depister' , 6 );
		$('#patientTous, #patientsExternes').css({ 'font-weight':'normal', 'font-size':'12px' }); 
		$(this).css({ 'font-weight':'bold', 'font-size':'19px' });
		
		$('#t_negatif, #t_positif').css({'border': '1px solid white'});
		$('#t_neg_pos').fadeIn(1000);
	});

	$('#patientsExternes').click(function(){
		oTable.fnFilter( 'patient_externe' , 6 );
		$('#patientTous, #patientsDepistes').css({ 'font-weight':'normal', 'font-size':'12px' }); 
		$(this).css({ 'font-weight':'bold', 'font-size':'19px' });
		
		$('#t_neg_pos').fadeOut(0);
	});
	
	
	/**
	 ** Gestion des tests positifs et negatifs
	 **/
	$('#t_negatif').click(function(){
		oTable.fnFilter( 'patient_depister patient_externe' , 6 );
		
		$('#t_positif').css({'border': '1px solid white'});
		$('#t_negatif').css({'border': '1px solid green', 'border-radius': '50%'});
	});
	
	$('#t_positif').click(function(){
		oTable.fnFilter( 'patient_depister patient_interne' , 6 );
		
		$('#t_negatif').css({'border': '1px solid white'});
		$('#t_positif').css({'border': '1px solid green', 'border-radius': '50%'});
	});
	//-------------------------
	//-------------------------
	//-------------------------
	
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
    				if($(e.target).text() == 'Modifier' || $(e.target).is('#modifierCTX')){
    					modifierPatient(id);
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
            	    		 $('#titre span').html('LISTE DES PATIENTS'); 
                	    	 $('#contenu').fadeIn();
                	    	 $('#vue_patient').html("");
                	     });
            	     });
            }
        
        });
    	
    }
    
    function modifierPatient(id){
    	vart=tabUrl[0]+'public/secretariat/modifier-patient/id_patient/'+id;
        $(location).attr("href",vart);
    }
    
    