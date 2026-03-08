/**
 * Scout Starter — Onboarding Wizard
 */
( function () {
  'use strict';

  var panes   = document.querySelectorAll( '[data-pane]' );
  var steps   = document.querySelectorAll( '[data-step]' );
  var btnBack   = document.getElementById( 'scout-btn-back' );
  var btnNext   = document.getElementById( 'scout-btn-next' );
  var btnSubmit = document.getElementById( 'scout-btn-submit' );

  var current = 1;
  var total   = panes.length;

  /**
   * Show the given pane and update progress indicators.
   *
   * @param {number} n Pane number (1-indexed).
   */
  function showPane( n ) {
    // Update panes.
    panes.forEach( function ( pane ) {
      pane.classList.remove( 'active' );
      if ( parseInt( pane.getAttribute( 'data-pane' ), 10 ) === n ) {
        pane.classList.add( 'active' );
      }
    } );

    // Update step indicators.
    steps.forEach( function ( step ) {
      var stepNum = parseInt( step.getAttribute( 'data-step' ), 10 );
      step.classList.remove( 'active', 'done' );
      if ( stepNum === n ) {
        step.classList.add( 'active' );
      } else if ( stepNum < n ) {
        step.classList.add( 'done' );
      }
    } );

    // Show / hide navigation buttons.
    btnBack.style.display   = n > 1 ? '' : 'none';
    btnNext.style.display   = n < total ? '' : 'none';
    btnSubmit.style.display = n === total ? '' : 'none';
  }

  /**
   * Validate all required inputs in the current pane.
   * Returns false and focuses the first empty field if validation fails.
   *
   * @return {boolean}
   */
  function validateCurrentPane() {
    var activePane = document.querySelector( '[data-pane="' + current + '"]' );
    if ( ! activePane ) {
      return true;
    }

    var required = activePane.querySelectorAll( '[required]' );
    for ( var i = 0; i < required.length; i++ ) {
      if ( '' === required[ i ].value.trim() ) {
        required[ i ].focus();
        return false;
      }
    }

    return true;
  }

  // Back button.
  btnBack.addEventListener( 'click', function () {
    if ( current > 1 ) {
      current--;
      showPane( current );
    }
  } );

  // Next button.
  btnNext.addEventListener( 'click', function () {
    if ( ! validateCurrentPane() ) {
      return;
    }
    if ( current < total ) {
      current++;
      showPane( current );
    }
  } );

  // Submit button — show loading state and let the form submit naturally.
  btnSubmit.addEventListener( 'click', function () {
    btnSubmit.textContent = 'Setting things up\u2026';
    btnSubmit.disabled = true;
  } );

  // Initialize.
  showPane( 1 );
}() );
