(function ($) {

    /**
     *  This function will initialize the $field.
     */
    function initialize_field($field) {

        // Get var from this file
        let field_key = $field[0].dataset.key;
        let fieldElt = $('#acf-' + field_key).get(0);
        let data = {
            lat: fieldElt.getAttribute('data-lat'),
            lng: fieldElt.getAttribute('data-lng'),
            zoom: fieldElt.getAttribute('data-zoom'),
            mapID: 'acf-' + field_key + '-map',
            searchID: '#acf-' + field_key + '-search'
        };

        // Init algolia search
        const placesAutocomplete = places({
            container: $(data.searchID).get(0)
        });

        // Init leaflet map
        const map = L.map(data.mapID).setView([data.lat, data.lng], data.zoom);

        // Set default backend providers (theme)
        L.tileLayer.provider('Wikimedia').addTo(map);

        // Add the default/choised marker
        L.marker({'lat': data.lat, 'lng': data.lng}, {}).addTo(map);

        // Prepare markers for search functions
        let markers = [];

        // Update ACF input val from Algolia search change
        placesAutocomplete
            .on('suggestions', (e) => {
                handleOnSuggestions(e)
            })
            .on('cursorchanged', (e) => {
                handleOnCursorchanged(e)
            })
            .on('change', (e) => {
                updateHTML(e);
                handleOnChange(e)
            })
            .on('clear', () => {
                handleOnClear()
            });

        function updateHTML(e) {
            $('#acf-' + field_key + ' .acf-hidden input.input-lat').attr('value', e.suggestion.latlng.lat);
            $('#acf-' + field_key + ' .acf-hidden input.input-lng').attr('value', e.suggestion.latlng.lng);
            $('#acf-' + field_key + ' .acf-hidden input.input-address').attr('value', e.suggestion.value);
        }

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
            map.fitBounds(featureGroup.getBounds().pad(0.5), {animate: false});
        }
    }

    if (typeof acf.add_action !== 'undefined') {
        /**
         *  ready & append (ACF5)
         *
         *  These two events are called when a field element is ready for initizliation.
         *  - ready: on page load similar to $(document).ready()
         *  - append: on new DOM elements appended via repeater field or other AJAX calls
         */
        acf.add_action('ready_field/type=leaflet_map_field', initialize_field);
        acf.add_action('append_field/type=leaflet_map_field', initialize_field);
    }

})(jQuery);