/**
 * JavaScript for the public-facing Sermon Media plugin
 */

(function( $ ) {

	'use strict';

	function smet0_TabClick(buttonId, tabId) {

		// If we are leaving the video tab, pause the video
		if (buttonId != "smet0_video_tab") {
			if ( $( "#smet0_video" ).length > 0) {
				var iframe = $( "#smet0_video > iframe" );
				if (iframe.length > 0) {
					iframe[0].contentWindow.postMessage( '{"event":"command","func":"pauseVideo","args":""}', '*' );
				}
			}
		}

		// If we are leaving the audio tab, pause the audio
		if (buttonId != "smet0_audio_tab") {
			if ( $( "#smet0_audio > div > audio" ).length > 0) {
				var playerId = $( "#smet0_audio > div > audio" )[0].id;
				if (playerId) {
					var player = $( "#" + playerId )[0]
					if (player && ! player.paused) {
							player.pause();
					}
				}
			} else {
				if ( $( "#smet0_audio" ).find( ".mejs-container" ).length > 0) {
					var playerId = $( "#smet0_audio" ).find( ".mejs-container" )[0].id
					if (playerId) {
						var player = mejs.players[playerId];
						if (player && ! player.paused) {
								player.pause();
						}
					}
				}
			}

		}

		// Get all elements with class="smet0_tabcontent" and hide them
		$( ".smet0_tabcontent" ).each(
			function(index) {
				$( this ).css( "display", "none" );
			}
		);

		// Get all elements with class="smet0_tablinks" and remove the class "active"
		$( ".smet0_tablinks" ).each(
			function(index) {
				$( this ).removeClass( "active" );
			}
		);

		// Show the current tab, and add an "active" class to the button that opened the tab
		$( "#" + tabId ).css( "display", "block" )
		$( "#" + buttonId ).addClass( "active" );

	};

	$( document ).ready(
		function() {

			$( "#smet0_video_tab" ).click(
				function() {
					smet0_TabClick( "smet0_video_tab", "smet0_video" );
				}
			);

			$( "#smet0_audio_tab" ).click(
				function() {
					smet0_TabClick( "smet0_audio_tab", "smet0_audio" );
				}
			);

			$( "#smet0_download_tab" ).click(
				function() {
					smet0_TabClick( "smet0_download_tab", "smet0_download" );
				}
			);

			if ( $( "#smet0_video" ).length > 0) {
				$( "#smet0_video_tab" ).click();
			} else {
				if ( $( "#smet0_audio" ).length > 0) {
					$( "#smet0_audio_tab" ).click();
				}
			}
		}
	);

})( jQuery );
