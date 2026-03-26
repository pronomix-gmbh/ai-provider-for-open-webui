( function () {
	'use strict';

	var apiFetch = window.wp && window.wp.apiFetch ? window.wp.apiFetch : null;
	var i18n = window.wp && window.wp.i18n ? window.wp.i18n : null;

	var __ = i18n && i18n.__ ? i18n.__ : function ( text ) {
		return text;
	};
	var _n = i18n && i18n._n ? i18n._n : function ( single, plural, number ) {
		return number === 1 ? single : plural;
	};
	var sprintf = i18n && i18n.sprintf ? i18n.sprintf : function ( text, number ) {
		return text.replace( '%d', String( number ) );
	};

	var ERROR_COLOR = '#d63638';

	document.addEventListener( 'DOMContentLoaded', function () {
		var settings = window.aiProviderForOpenWebUISettings;
		var container = document.getElementById( 'openwebui-models-container' );
		var status = document.getElementById( 'openwebui-model-status' );

		if ( ! settings || ! apiFetch || ! container || ! status ) {
			return;
		}

		status.textContent = __( 'Loading models...', 'ai-provider-for-openwebui' );

		apiFetch( { url: settings.ajaxUrl } )
			.then( function ( response ) {
				if ( ! response || ! response.success || ! Array.isArray( response.data ) ) {
					status.textContent = ( response && typeof response.data === 'string' )
						? response.data
						: __( 'Failed to load models.', 'ai-provider-for-openwebui' );
					status.style.color = ERROR_COLOR;
					return;
				}

				var models = response.data;
				container.innerHTML = '';

				if ( models.length === 0 ) {
					var empty = document.createElement( 'p' );
					empty.textContent = __( 'No models found in Open WebUI.', 'ai-provider-for-openwebui' );
					container.appendChild( empty );
					return;
				}

				var headline = document.createElement( 'p' );
				headline.textContent = sprintf(
					_n(
						'%d model available:',
						'%d models available:',
						models.length,
						'ai-provider-for-openwebui'
					),
					models.length
				);
				container.appendChild( headline );

				var list = document.createElement( 'ul' );

				models.forEach( function ( model ) {
					if ( ! model || typeof model.id !== 'string' || model.id.length === 0 ) {
						return;
					}

					var item = document.createElement( 'li' );
					var code = document.createElement( 'code' );

					code.textContent = model.id;
					item.appendChild( code );
					list.appendChild( item );
				} );

				container.appendChild( list );
			} )
			.catch( function ( error ) {
				var fallback = __( 'Could not connect to load models.', 'ai-provider-for-openwebui' );
				status.textContent = error && typeof error.message === 'string' ? error.message : fallback;
				status.style.color = ERROR_COLOR;
			} );
	} );
}() );
