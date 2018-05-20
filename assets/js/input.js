(function($){

	/**
	*  initialize_field
	*
	*  This function will initialize the $field.
	*
	*  @date	30/11/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	n/a
	*/
	function initialize_field( $field ) {
		
		//$field.doStuff();
		
	}
	
	
	if( typeof acf.add_action !== 'undefined' ) {
	
		/*
		*  ready & append (ACF5)
		*
		*  These two events are called when a field element is ready for initizliation.
		*  - ready: on page load similar to $(document).ready()
		*  - append: on new DOM elements appended via repeater field or other AJAX calls
		*
		*  @param	n/a
		*  @return	n/a
		*/
		acf.add_action('ready_field/type=Leaflet', initialize_field);
		acf.add_action('append_field/type=Leaflet', initialize_field);
		
		
	} else {
		
		/*
		*  acf/setup_fields (ACF4)
		*
		*  These single event is called when a field element is ready for initizliation.
		*
		*  @param	event		an event object. This can be ignored
		*  @param	element		An element which contains the new HTML
		*  @return	n/a
		*/
		$(document).on('acf/setup_fields', function(e, postbox){
			
			// find all relevant fields
			$(postbox).find('.field[data-field_type="Leaflet"]').each(function(){
				
				// initialize
				initialize_field( $(this) );
				
			});
		
		});
	
	}

})(jQuery);





/**
 * Start algolia search
 */
    // TODO : Set field unique ID because else this below code don't work


let placesAutocomplete = places({
        container: document.querySelector('#leaflet-map-input')
    });

/**
 * Vars
 */
let fields = document.getElementsByClassName('acf-field-leaflet-map-field');
let field_key = fields[0].getAttribute('data-key');
let fieldElt = document.getElementById('acf-' + field_key);

let lat = fieldElt.getAttribute('data-lat');
let lng = fieldElt.getAttribute('data-lng');
let zoom = fieldElt.getAttribute('data-zoom');

/**
 * Create the admin map
 */
let map = L.map('leaflet-map').setView([lat, lng], zoom);

// Set Providers & List
L.tileLayer.provider('Wikimedia').addTo(map);

let markers = [];
let marker = L.marker({'lat': lat, 'lng': lng}, {}).addTo(map);


jQuery(function ($) {

    /**
     * Update ACF input val from Algolia search change
     */
    placesAutocomplete.on('change', (e) => {
        // console.log(e.suggestion);
        $('#acf-leaflet-fields-hidden input').each(function () {
            if ($(this).hasClass('input-lat'))
                $(this).attr('value', e.suggestion.latlng.lat);

            if ($(this).hasClass('input-lng'))
                $(this).attr('value', e.suggestion.latlng.lng);

            if ($(this).hasClass('input-address'))
                $(this).attr('value', e.suggestion.value);
        });
    });

    placesAutocomplete.on('suggestions', handleOnSuggestions);
    placesAutocomplete.on('cursorchanged', handleOnCursorchanged);
    placesAutocomplete.on('change', handleOnChange); // Here acf.save_post
    placesAutocomplete.on('clear', handleOnClear);
});


function handleOnSuggestions(e) {
    markers.forEach(removeMarker);
    markers = [];

    if (e.suggestions.length === 0) {
        map.setView(new L.LatLng(0, 0), 1);
        return;
    }

    e.suggestions.forEach(addMarker);
    findBestZoom();
}

function handleOnChange(e) {
    markers
        .forEach(function (marker, markerIndex) {
            if (markerIndex === e.suggestionIndex) {
                markers = [marker];
                marker.setOpacity(1);
                findBestZoom();
            } else {
                removeMarker(marker);
            }
        });
    if (markers.length === 1) map.setZoom(14);
}

function handleOnClear() {
    map.setView(new L.LatLng(0, 0), 1);
    markers.forEach(removeMarker);
}

function handleOnCursorchanged(e) {
    markers
        .forEach(function (marker, markerIndex) {
            if (markerIndex === e.suggestionIndex) {
                marker.setOpacity(1);
                marker.setZIndexOffset(1000);
            } else {
                marker.setZIndexOffset(0);
                marker.setOpacity(0.5);
            }
        });
}

function addMarker(suggestion) {
    let marker = L.marker(suggestion.latlng, {opacity: .4});
    marker.addTo(map);
    markers.push(marker);
}

function removeMarker(marker) {
    map.removeLayer(marker);
}

function findBestZoom() {
    let featureGroup = L.featureGroup(markers);
    let newzoom = map.fitBounds(featureGroup.getBounds().pad(0.5), {animate: false});
    // console.log(newzoom.zoom);
}
