/**
 * simpleTabs ( http://supercanard.phpnet.org/jquery-test/simpleTabs/ )
 * plugin jQuery pour afficher des bôites d'onglet.
 * 
 * Version 1.0
 *
 * Auteur : Jonathan Coulet ( j.coulet@gmail.com )
 * 
 **/
(function($){
	$.fn.simpleTabs = function(option){
		// Param plugin
		var param = jQuery.extend({
			fadeSpeed: "medium", // @param : low, medium, fast
			defautContent: 1, // @param : number ( simpleTabs-nav-number)
			autoNav: "false", // @param : true or false
			closeTabs : "true" // @param : true or false;
		}, option);
		$(this).each(function() {
			// Initialisation
			var $this = this;
			var $thisId = "#"+this.id;
			var nbTab = $($thisId+" > div").size();
			autoNav();
			showCloseTabs();
			hideAll();
			changeContent(param.defautContent);
			// Fonctions
			function hideAll(){
				// Masque tous les content
				$($thisId+" .simpleTabs-content").hide();
			}
			function changeContent(indice){
				// Masque tous les content - Supprime la classe actif de tous les onglets 
				// Ajoute la classe actif à l'onglet cliqué - Affiche le content ciblé - Execute showCloseTabs
				hideAll();
				$($thisId+" .simpleTabs-nav li").removeClass("actif");
				$($thisId+" #simpleTabs-nav-"+indice).addClass("actif");
				$($thisId+" #simpleTabs-content-"+indice).fadeIn(param.fadeSpeed);
				showCloseTabs();
			}
			function autoNav(){
				// Génère les onglets automatiquement
				if(param.autoNav == "true"){
					var listeNav = '';
					for(i=1; i!=nbTab; i++){
						listeNav = listeNav+'<li id="simpleTabs-nav-'+i+'">'+i+'</li>';
					}
					$($thisId+" .simpleTabs-nav").append('<ul>'+listeNav+'</ul>');
				}
			}
			function showCloseTabs(){
				// Génére un bouton de fermeture générale des content
				if(param.closeTabs == "true"){
					if($($thisId+" .simpleTabs-nav li.close").size() == 0){
						$($thisId+" .simpleTabs-nav ul").append("<li title=\"Fermer tous les onglets\" class=\"close\">x</li>");
					}
				}
			}
			// Exec
			$($thisId+" .simpleTabs-nav li").click(function(){
				var numContent = this.id.substr(this.id.length-1,this.id.length);
				changeContent(numContent);
			});
			// test function closeTabs
			$($thisId+" .simpleTabs-nav li.close").click(function(){
				hideAll();
				$($thisId+" .simpleTabs-nav li").removeClass("actif");
				$($thisId+" .simpleTabs-nav li.close").remove();
				//alert($($thisId+" .simpleTabs-nav li.close").size());
			});
		});
	}
})(jQuery);
