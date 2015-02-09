
var g_NumUnityDogs = 0;

var unityDog =
{
	Setup : function( element )
	{
		var webURL		= element.attr( "src" );
        var fullscreen = element.attr("fullscreen");
        // var preview = element.attr("preview");

		var unityelement = jQuery( '<div class="unitydog standard"><div class="missing" style="display:none;"><a href="http://unity3d.com/webplayer/" title="Unity Web Player. Install now!"><img alt="Unity Web Player. Install now!" src="http://webplayer.unity3d.com/installation/getunity.png" width="193" height="63" /></a></div></div>' );
		unityelement.css("width", element.attr("width") );
		unityelement.css("height", element.attr("height") );

		// Add the controls @todo improve fullscreen behaviour
        if(fullscreen != "no"){
		var controls = jQuery( '<div class="controls"></div>' );
		
			var ctrl = jQuery( '<a class="make_fullscreen" ><img src="'+unitydogsettings.fullscreen+'"></a>' );
				ctrl.click( function(){ unityelement.addClass( 'fullscreen' ); unityelement.removeClass( 'standard' ); jQuery( 'BODY' ).addClass( 'unitydog_fullscreen' ); } );
			controls.append( ctrl );
		
			var ctrl = jQuery( '<a class="make_normal" ><img src="'+unitydogsettings.restore+'"></a>' );
				ctrl.click( function(){ unityelement.removeClass( 'fullscreen' ); unityelement.addClass( 'standard' ); jQuery( 'BODY' ).removeClass( 'unitydog_fullscreen' ); } );
			controls.append( ctrl );
        }
		
		var playbutton = jQuery( '<div class="playbutton">&nbsp;</div>' );
		playbutton.click( function(){ unityDog.Play( unityelement, webURL ); } );
		
		unityelement.prepend( playbutton );
		unityelement.append( controls );
		
		element.replaceWith( unityelement );
	},
	
	Play : function( unityelement, webURL )
	{	
		var player = new UnityObject2( { width: "100%", height: "100%" } );
		
		// Stop all other instances
		unityDog.StopAll();
		
		// hide our play button
		unityelement.find( '.playbutton' ).hide();

		player.observeProgress(function (progress) {
            var missingScreen = unityelement.find(".missing");
            switch(progress.pluginStatus) {
                case "unsupported":
                	alert("unity3d Webplayer is not supported on your Platform");
                break;
                case "broken":
                    alert("You will need to restart your browser after installation.");
                break;
                case "missing":
                    missingScreen.find("a").click(function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        player.installPlugin();
                        return false;
                    });
                    missingScreen.show();
                break;
                case "installed":
                    missingScreen.remove();
                break;
                case "first":
                break;
            }
        });
		
		var plyelement = jQuery( '<div class="player"></div>' );
		unityelement.prepend( plyelement );

		player.initPlugin( plyelement[0], webURL );
	},
	
	StopAll : function()
	{
		jQuery( "DIV.unitydog .player" ).remove();
		jQuery( "DIV.unitydog .playbutton" ).show();
	}
}

jQuery(function()
{
	jQuery( 'unitydog' ).each( function( idx, element )
	{
		unityDog.Setup( jQuery( element ) );
	})
});