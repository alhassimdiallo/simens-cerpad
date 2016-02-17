
function AjouterMedicament(){
	
$("#terminerMedicament").click(function(){
	$("#terminer").attr( 'disabled', true );
	$("#annuler").attr( 'disabled', true );

	$indication_therapeutique = $("#indication_therapeutique").val();
	           $mise_en_garde = $("#mise_en_garde").val();
      	   $adresse_fabricant = $("#adresse_fabricant").val();
	             $composition = $("#composition").val();
	       $excipient_notoire = $("#excipient_notoire").val();
	     $voie_administration = $("#voie_administration").val();
	                $intitule = $("#intitule").val();
	             $description = $("#description").val();
	             
	             //R�cup�rer l'image
	             //R�cup�rer l'image
	             $fichier_tmp = $("#fichier_tmp").val();
	
	             $.ajax({
	 	            type: 'POST',
	 	            url: '/simens/public/pharmacie/ajouter',  
	 	            data: $(this).serialize(),
	 	            data: ({'indication_therapeutique':$indication_therapeutique, 'mise_en_garde':$mise_en_garde,
 	            	        'adresse_fabricant':$adresse_fabricant, 'composition':$composition,
 	            	        'excipient_notoire':$excipient_notoire, 'voie_administration':$voie_administration,
 	            	        'intitule':$intitule, 'description':$description,
 	            	        'fichier_tmp':$fichier_tmp}),
	 	           
	 	    	    success: function() {    	 	    	    	
	 	    	    	
	 	    	    	vart ='/simens/public/pharmacie/liste-medicaments';
	 	    	        $(location).attr("href",vart);
	 	                
	 	            },
	 	            error:function(e){console.log(e);alert("Une erreur interne est survenue!");},
	 	            dataType: "html"
	 	    	});
});

$("#annulerMedicament").click(function(){
	$("#terminer").attr( 'disabled', true );
	$("#annuler").attr( 'disabled', true );
	
	vart='/simens/public/pharmacie/liste-medicaments';
    $(location).attr("href",vart);
	return false;
});


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
    	    //return false;
    	     
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

Recupererimage();
}