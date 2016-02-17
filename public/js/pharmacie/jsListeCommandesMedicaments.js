function initialisation(){	
    	
     var asInitVals = new Array();
	 var  oTable = $('#listeMedicaments').dataTable
	  ( {    
					"aaSorting": "", //pour trier la liste affich�e
					"oLanguage": { 
						"sProcessing":   "Traitement en cours...",
						"sZeroRecords":  "Aucun &eacute;l&eacute;ment &agrave; afficher",
						//"sInfo": "_TOTAL_ M&eacute;dicament(s)",
						"sInfoEmpty": "0 &eacute;l&eacute;ment &agrave; afficher",
						"sInfoFiltered": "",
						"sInfoPostFix":  "",
						"sSearch": "",
						"sUrl": "",
						"sWidth": "30px",
						"oPaginate": {
							"sFirst":    "|<",
							"sPrevious": "",
							"sNext":     "",
							"sLast":     ">|",
						}
					   },
					   "iDisplayLength": "5",
					   "aLengthMenu": [5,8,15],
					   				
	  } );

	//le filtre du select
	$('#filter_statut').change(function() 
	{					
		oTable.fnFilter( this.value );
	});
	
	//le filtre du select du type personnel
	/*$('#type_personnel').change(function() 
	{					
		oTable.fnFilter( this.value );
	});*/
	
	$("tfoot input").keyup( function () { //Permet de rechercher par l'element saisi
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
	
	$("tfoot input").focus( function () { //reinitialise
		if ( this.className == "search_init" )
		{
			this.className = "";
			this.value = "";
		}
	} );
	
	$("tfoot input").blur( function (i) { //ne reinitialise pas
		if ( this.value == "" )
		{
			this.className = "search_init";
			this.value = asInitVals[$("tfoot input").index(this)];
		}
	} );
}
/*********************************************************************************************/
/*********************************************************************************************/
/*********************************************************************************************/
function affichercommandes(id){
	
    var chemin = '/simens/public/pharmacie/vue-commande';
    $.ajax({
        type: 'POST',
        url: chemin ,
        data: $(this).serialize(),  
        data:'id='+id,
        success: function(data) {
   	    
        	     $("#titre").replaceWith("<div id='titre' style='font-family: police2;  color: green; font-size: 20px; font-weight: bold;'><iS style='font-size: 25px;'>&curren;</iS> D&Eacute;TAILS SUR LA COMMANDE</div>");
        	     var result = jQuery.parseJSON(data);
        	     $("#contenu").fadeOut(function(){$("#contenu_vue_medicament").html(result).fadeIn("fast"); }); 
    
        },
        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        dataType: "html"
    });
   // return false;
}

function utiliserdansvuemedicament(){
	$("#terminervue").click(function(){
		$("#titre").replaceWith("<div id='titre' style='font-family: police2;  color: green; font-size: 20px; font-weight: bold;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES COMMANDES</div>");
		$("#contenu_vue_medicament").fadeOut(function(){$("#contenu").fadeIn("fast"); });
	});
}

/*********************************************************************************************/
/*********************************************************************************************/
/*********************************************************************************************/
function modifiermedicament(id){

	  var chemin = '/simens/public/pharmacie/nouvelle-commande';
	    $.ajax({
	        type: 'GET',
	        url: chemin ,  
	        data:'id='+id,
	        success: function(data) {
	   	    
	        	     $("#titre").replaceWith("<div id='titre' style='font-family: police2;  color: green; font-size: 20px; font-weight: bold;'><iS style='font-size: 25px;'>&curren;</iS> LANCER UNE NOUVELLE COMMANDE</div>");
	        	     var result = jQuery.parseJSON(data);
	        	     $("#contenu").fadeOut(function(){$("#contenu_modification_medicament").html(result).fadeIn("fast"); }); 
	    
	        },
	        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	        dataType: "html"
	  });
	 // return false;
}

function vendremedicament(id){

	  var chemin = '/simens/public/pharmacie/vente';
	    $.ajax({
	        type: 'GET',
	        url: chemin ,  
	        data:'id='+id,
	        success: function(data) {
	   	    
	        	     $("#titre").replaceWith("<div id='titre' style='font-family: police2;  color: green; font-size: 20px; font-weight: bold;'><iS style='font-size: 25px;'>&curren;</iS> EFFECTUER UNE VENTE</div>");
	        	     var result = jQuery.parseJSON(data);
	        	     $("#contenu").fadeOut(function(){$("#contenu_modification_medicament").html(result).fadeIn("fast"); }); 
	    
	        },
	        error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	        dataType: "html"
	  });
	 // return false;
}

function utiliserdansmodification(){
	//POUR LE BOUTON 'ANNULER'
	$("#annulerNouvelleCommande").click(function(){
		$("#terminerNouvelleCommande").attr( 'disabled', true );
		$("#terminerNouvelleCommande").attr( 'disabled', true );
		
		$("#titre").replaceWith("<div id='titre' style='font-family: police2;  color: green; font-size: 20px; font-weight: bold;'><iS style='font-size: 25px;'>&curren;</iS> LISTE DES M&Eacute;DICAMENTS</div>");
		$("#contenu_modification_medicament").fadeOut(function(){$("#contenu").fadeIn("fast"); });
	});
	
	//POUR LE BOUTON 'TERMINER'
	$("#terminerNouvelleCommande").click(function(){
		$("#terminerNouvelleCommande").attr( 'disabled', true );
		$("#terminerNouvelleCommande").attr( 'disabled', true );

		            $idMedicament = $("#id_medicament").val();
		
		$indication_therapeutique = $("#indication_therapeutique").val();
		           $mise_en_garde = $("#mise_en_garde").val();
	      	   $adresse_fabricant = $("#adresse_fabricant").val();
		             $composition = $("#composition").val();
		       $excipient_notoire = $("#excipient_notoire").val();
		     $voie_administration = $("#voie_administration").val();
		                $intitule = $("#intitule").val();
		             $description = $("#description").val();
		
		             //Recup�ration du nom de l'image
		             $fichier_tmp = $("#fichier_tmp").val();
		             
		             $.ajax({
		 	            type: 'POST',
		 	            url: '/simens/public/pharmacie/modification',  
		 	            data: ({'indication_therapeutique':$indication_therapeutique, 'mise_en_garde':$mise_en_garde,
	 	            	        'adresse_fabricant':$adresse_fabricant, 'composition':$composition,
	 	            	        'excipient_notoire':$excipient_notoire, 'voie_administration':$voie_administration,
	 	            	         'intitule':$intitule, 'description':$description,
	 	            	         'id':$idMedicament,
	 	            	         'fichier_tmp':$fichier_tmp
		 	                     }),
		 	           
		 	    	    success: function(data) {    	 	    	    	
		 	    	    	var result = jQuery.parseJSON(data);
		 	    	    	vart ='/simens/public/pharmacie/liste-medicaments';
		 	    	        $(location).attr("href",vart);
		 	                
		 	            },
		 	            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
		 	            dataType: "html"
		 	    	});
		
	});
	
	
	/*=================================================================================*/		
	/*************************** GESTION DE L'IMAGE MEDICAMENT *************************/
	/*************************** GESTION DE L'IMAGE MEDICAMENT *************************/
	/*************************** GESTION DE L'IMAGE MEDICAMENT *************************/
	/*=================================================================================*/	
	//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
	function confirmation(){
	  $( "#confirmation" ).dialog({
	    resizable: false,
	    height:170,
	    width:375,
	    autoOpen: false,
	    modal: true,
	    buttons: {
	        "Oui": function() {
	            $( this ).dialog( "close" );

	            $('#image').children().remove(); 
	            $('<input type="file" />').appendTo($('#image')); 
	         	$(".supprimer_photo").toggle(false);
	         	Recupererimage();          	       
	    	     
	        },
	        "Annuler": function() {
	            $( this ).dialog( "close" );
	        }
	   }
	  });
	}

	//FONCTION QUI RECUPERE LA PHOTO ET LA PLACE SUR L'EMPLACEMENT SOUHAITE
	function Recupererimage(){
		$('#image input[type="file"]').change(function() {
			
		   var file = $(this);
			   var reader = new FileReader;
			   
	       reader.onload = function(event) {
	    		var img = new Image();
	             
	    		img.onload = function() {
				   var width  = 220;
				   var height = 283;
				    
				   //On supprimer l'ancienne photo
				   $("#MonImage").remove();
				   $("#MonImage").remove();
				   
				   var canvas = $('<canvas></canvas>').attr({ width: width, height: height });
				   file.replaceWith(canvas);
				   var context = canvas[0].getContext('2d');
	        	    	context.drawImage(img, 0, 0, width, height);
			    };
			    document.getElementById('fichier_tmp').value = img.src = event.target.result;
			   
		};

		reader.readAsDataURL(file[0].files[0]);
		
		//Cr�ation de l'onglet de suppression de la photo
		$(".supprimer_photo").toggle(true);
		$("#div_supprimer_photo").children().remove();
		$('<input title="Supprimer la photo" name="supprimer_photo" id="supprimer_photo">').appendTo($("#div_supprimer_photo"));
	  
		//SUPPRESSION DE LA PHOTO
	    //SUPPRESSION DE LA PHOTO
	      $("#supprimer_photo").click(function(e){
	    	 e.preventDefault();
	    	 confirmation();
	         $("#confirmation").dialog('open');
	      });
	  });
	}
	
	$(".supprimer_photo").toggle(false);
	
	Recupererimage();
}

/*********************************************************************************************/
/*********************************************************************************************/
/*********************************************************************************************/
//BOITE DE DIALOG POUR LA CONFIRMATION DE SUPPRESSION
function confirmation(id){
  $( "#confirmationSuppression" ).dialog({
    resizable: false,
    height:170,
    width:405,
    autoOpen: false,
    modal: true,
    buttons: {
        "Oui": function() {
            $( this ).dialog( "close" );
            
            var cle = id;
            var chemin = '/simens/public/pharmacie/supprimer';
            $.ajax({
                type: 'POST',
                url: chemin , 
                data:'id='+cle,
                success: function(data) { 
                	 var result = jQuery.parseJSON(data);
                	     $("#"+cle).fadeOut(function(){$("#"+cle).empty();});
                	     $("#compteur").html(result);
                	     
                },
                error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
                dataType: "html"
            });
    	     return false;
    	     
        },
        "Annuler": function() {
            $( this ).dialog( "close" );
        }
   }
  });
}

function supprimermedicament(id){
	confirmation(id);
    $("#confirmationSuppression").dialog('open');
}

var globresult = 0;

function getMedicament(cle)
{
     $.ajax({
        type: 'POST',
        url: '/simens/public/pharmacie/tarif',
        data: 'id='+cle,
        success: function(data) {
            var result = jQuery.parseJSON(data);
            globresult = result;
            
            if(cle == 2){
            	$("#prix_unitaire2").html(result);
            }else
            	if(cle == 3){
            		$("#prix_unitaire3").html(result);
            	}else
            		if(cle == 1){
            			$("#prix_unitaire1").html(result);
            		}
            //alert(result);
      },
      error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
        dataType: "html"
    });
 
    return false;
 }

function calcule ()
{
	$("#champ1 input[type='text']").keyup( function () { //Permet de rechercher par l'element saisi
		/* Filter on the column (the index) of this element */
		//oTable.fnFilter( this.value, $("tfoot input").index(this) );
		
		$("#prix_total1").html(this.value * globresult);
	} ); 
	
	$("#champ2 input[type='text']").keyup( function () { //Permet de rechercher par l'element saisi
		/* Filter on the column (the index) of this element */
		//oTable.fnFilter( this.value, $("tfoot input").index(this) );
		
		$("#prix_total2").html(this.value * globresult);
	} ); 
	
	$("#champ3 input[type='text']").keyup( function () { //Permet de rechercher par l'element saisi
		/* Filter on the column (the index) of this element */
		//oTable.fnFilter( this.value, $("tfoot input").index(this) );
		
		$("#prix_total3").html(this.value * globresult);
	} ); 
}
