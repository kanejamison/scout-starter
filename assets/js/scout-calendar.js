/**
 * FullCalendar initialisation for Scout Starter.
 *
 * Finds every .scout-calendar element on the page and initialises a
 * FullCalendar instance using the data-view and data-feed attributes
 * set by the PHP shortcode renderer.
 */
( function () {
	'use strict';

	function initCalendar( el ) {
		var view    = el.dataset.view || 'dayGridMonth';
		var feedUrl = el.dataset.feed;

		var calendar = new FullCalendar.Calendar( el, {
			initialView:    view,
			initialDate:    new Date(),
			height:         'auto',
			fixedWeekCount: false,
			noEventsText:   'No upcoming events.',

			headerToolbar: {
				left:   'prev,next today',
				center: 'title',
				right:  '',
			},

			events: {
				url:    feedUrl,
				format: 'ics',
			},

			eventSourceFailure: function () {
				var msg = document.createElement( 'p' );
				msg.className   = 'scout-calendar__error';
				msg.textContent = 'Unable to load calendar events. Please try again later.';
				el.appendChild( msg );
			},
		} );

		calendar.render();
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.scout-calendar' ).forEach( initCalendar );
	} );
} )();
