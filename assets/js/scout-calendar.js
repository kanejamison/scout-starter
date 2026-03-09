/**
 * FullCalendar initialisation for Scout Starter.
 *
 * Month grid view: uses @fullcalendar/icalendar plugin (URL-based source).
 * Agenda/list view: fetches the iCal proxy directly and uses ICAL.js to
 * parse, expand recurring events, and limit to the next 12 events within
 * a 12-month window — giving exact control the URL-based source can't.
 */
( function () {
	'use strict';

	/**
	 * Parse an iCal string and return the next `limit` upcoming events,
	 * expanding recurring events within a 12-month window from today.
	 *
	 * @param {string} icalText  Raw iCal feed text.
	 * @param {number} limit     Maximum number of events to return.
	 * @return {Array}           FullCalendar event objects, sorted by start date.
	 */
	function parseUpcomingEvents( icalText, limit ) {
		var jcal   = ICAL.parse( icalText );
		var comp   = new ICAL.Component( jcal );
		var today  = new Date();
		var cutoff = new Date( today );
		cutoff.setFullYear( cutoff.getFullYear() + 1 );

		var events = [];

		comp.getAllSubcomponents( 'vevent' ).forEach( function ( vevent ) {
			var icalEvent = new ICAL.Event( vevent );

			if ( icalEvent.isRecurring() ) {
				var expand = new ICAL.RecurExpansion( {
					component: vevent,
					dtstart:   icalEvent.startDate,
				} );

				var next;
				var guard = 0;
				while ( ( next = expand.next() ) && guard++ < 200 ) {
					var start = next.toJSDate();
					if ( start > cutoff ) { break; }
					if ( start < today )  { continue; }

					var durMs = icalEvent.duration.toSeconds() * 1000;
					events.push( {
						title:  icalEvent.summary,
						start:  start,
						end:    new Date( start.getTime() + durMs ),
						allDay: next.isDate,
					} );
				}
			} else {
				var start = icalEvent.startDate.toJSDate();
				if ( start >= today && start <= cutoff ) {
					events.push( {
						title:  icalEvent.summary,
						start:  start,
						end:    icalEvent.endDate ? icalEvent.endDate.toJSDate() : null,
						allDay: icalEvent.startDate.isDate,
					} );
				}
			}
		} );

		events.sort( function ( a, b ) { return a.start - b.start; } );
		return events.slice( 0, limit );
	}

	function initCalendar( el ) {
		var view    = el.dataset.view || 'dayGridMonth';
		var feedUrl = el.dataset.feed;
		var limit   = parseInt( el.dataset.limit, 10 ) || 12;
		var isList  = view.indexOf( 'list' ) === 0;
		var today   = new Date();

		var oneYearAgo = new Date( today );
		oneYearAgo.setFullYear( oneYearAgo.getFullYear() - 1 );

		// Agenda: fetch + parse with ICAL.js to get exactly the next 12 events.
		// Month grid: use the icalendar plugin URL source (handles all edge cases).
		var eventSource = isList
			? function ( fetchInfo, successCallback, failureCallback ) {
				fetch( feedUrl )
					.then( function ( r ) { return r.text(); } )
					.then( function ( text ) { successCallback( parseUpcomingEvents( text, limit ) ); } )
					.catch( failureCallback );
			}
			: { url: feedUrl, format: 'ics' };

		var calendar = new FullCalendar.Calendar( el, {
			initialView:    view,
			initialDate:    today,
			height:         'auto',
			fixedWeekCount: false,
			noEventsText:   'No upcoming events.',

			// Never show events older than one year (month view navigation guard).
			validRange: { start: oneYearAgo },

			buttonText: {
				today: 'Today',
			},

			headerToolbar: {
				left:   'prev,next today',
				center: 'title',
				right:  '',
			},

			events: eventSource,

			eventDidMount: function ( info ) {
				if ( info.el.tagName === 'A' ) {
					info.el.setAttribute( 'target', '_blank' );
					info.el.setAttribute( 'rel', 'noopener noreferrer' );
				}
			},

			eventSourceFailure: function () {
				var msg       = document.createElement( 'p' );
				msg.className = 'scout-calendar__error';
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
