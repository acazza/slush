jQuery( document ).ready( function($){


  /*
   * === === === === ===
   * TREE STRUCTURE ACCORDION MENU
   * === === === === ===
   */
  var accordion = (function(){
    var $accordion = $('.js-accordion');
    var $accordion_header = $accordion.find('.js-accordion-header');
    var $accordion_item = $('.js-accordion-item');

    //default settings
    var settings = {
       speed: 400,     //animation speed
       oneOpen: false  //close all other accordion items if true
    };

    return {
       //pass configurable object literal
      init: function($settings) {

          $accordion_header.on('click', function() {
              accordion.toggle($(this));
          });

          $.extend(settings, $settings);

          //ensure only one accordion is active if oneOpen is true
          if(settings.oneOpen && $('.js-accordion-item.active').length > 1) {
            $('.js-accordion-item.active:not(:first)').removeClass('active');
          }

          //ensures that current post accordion body is open when page loads
          $('.current-post').parents('.js-accordion-item').toggleClass('active');

          //reveal the active accordion bodies
          $('.js-accordion-item.active').find('> .js-accordion-body').show();

      },

      toggle: function($this) {

          if(settings.oneOpen && $this[0] != $this.closest('.js-accordion').find('> .js-accordion-item.active > .js-accordion-header')[0]) {
              $this.closest('.js-accordion')
                .find('> .js-accordion-item')
                .removeClass('active')
                .find('.js-accordion-body')
                .slideUp()
          }

         //show/hide the clicked accordion item
         $this.closest('.js-accordion-item').toggleClass('active');
         $this.next().stop().slideToggle(settings.speed);
      }
    }
  })();

  $(document).ready(function(){
      accordion.init({ speed: 300, oneOpen: false });
  });




  /*
   * === === === === ===
   * WALKER MEGAMENU
   * === === === === ===
   */

  /* When the user clicks on the button,
  toggle between hiding and showing the dropdown content */

  $( '.dropdown-toggle' ).on( 'click', function( e ){
    $( this ).siblings( '.dropdown-menu' ).toggleClass( 'show' );
  });

  /* Close the dropdown menu if the user clicks outside of it */

  window.onclick = function(event) {
    if( !event.target.matches( '.dropdown-toggle' )) {
      $( '.dropdown-menu' ).removeClass( 'show' );
    }
  }




  /*
   * === === === === ===
   * GENERATE QUIZ MARKUP (REST API)
   * === === === === ===
   */

  $( '#soundlush_generate_quiz' ).on( 'click', function( e ){

    var pool     = $(this).data('pool'),
        qty      = $(this).data('qty'),
        minutes  = $(this).data('time'),
        user     = $(this).data('user'),
        id       = $(this).data('id');

    $( '.quiz-page' ).addClass( 'js-remove-quiz-result' );

    //retrive questions REST API
    $.get( "http://localhost:8888/nysquist-dev/engine/wp-json/soundlush/v2/question/"+ parseInt( pool ) +'/'+ parseInt( qty ), function( posts ){

        html = "<form id='quiz_questions' class='fade-in-2s' method='post' data-id='"+ pool +"'>";

        //for each question
        $.each( posts, function( index, post ){

            switch( post.level ){
              case "1":
                level = "Easy"
                break;
              case "2":
                level = "Normal"
                break;
              case "3":
                level = "Hard"
                break;
              default:
                break;
            }

            //display statement markup
            html += "<span>" + ( index + 1 ) + ". " + post.post_content + "</span>" +   "<p>Level: " + level + "</p>"+ "<ul>";

            //display multiple choices markup
            for(var i = 0; i < post.options.length; i++) {

                var field = post.options[i];
                var value = field.soundlush_answer_option;
                var chr='abcdefghijklmnopqrstuvwxyz'.charAt( value - 1 );

                html += "<li><label><input type='radio' name='" + post.ID + "' value='"+value+"'/>"+ chr +") "+ field.soundlush_answer_statement +"</label></li>";

            }

            html += "</ul>";
        });

        //display submit button markup
        html += "<input type='button' id='soundlush_submit_quiz' class='btn btn-primary' value='Submit Quiz'></form>";

        //append all html
        $( "#the-quiz" ).append( html );

        //start coutdown timer
        if( minutes > 0 ) {
          var duration = 60 * minutes,
              timer = new CountDownTimer( duration ),
              display  = document.querySelector( '#time' );
              timer.onTick( format( display ) ).start();
        }
    });
  });




  /*
   * === === === === ===
   * COUNTDOWN TIMER (Vanilla JS)
   * === === === === ===
   */

  function format( display ) {
      return function( minutes, seconds ) {
          minutes = minutes < 10 ? "0" + minutes : minutes;
          seconds = seconds < 10 ? "0" + seconds : seconds;
          display.textContent = minutes + ':' + seconds;
      };
  }

  function CountDownTimer( duration, granularity ) {
      this.duration = duration;
      this.granularity = granularity || 1000;
      this.tickFtns = [];
      this.running = false;
  }

  CountDownTimer.prototype.start = function() {
      if( this.running ){
          return;
      }
      this.running = true;
      var start = Date.now(),
          that = this,
          diff, obj;

      (function timer() {
          diff = that.duration - ( ( ( Date.now() - start ) / 1000 ) | 0 );

          if( diff > 0 )
          {
              setTimeout( timer, that.granularity );
          }
          else
          {
              diff = 0;
              that.running = false;
              submitQuiz()
          }

          obj = CountDownTimer.parse( diff );
          that.tickFtns.forEach( function( ftn ){
              ftn.call( this, obj.minutes, obj.seconds );
          }, that );
     }());
  };

  CountDownTimer.prototype.onTick = function(ftn){
      if( typeof ftn === 'function' )
      {
          this.tickFtns.push( ftn );
      }
      return this;
  };

  CountDownTimer.prototype.expired = function(){
      return !this.running;
  };

  CountDownTimer.parse = function( seconds ){
      return {
          'minutes': ( seconds / 60 ) | 0,
          'seconds': ( seconds % 60 ) | 0
      };
  };



  /*
   * === === === === ===
   * SUBMIT QUIZ ANSWER (AJAX)
   * === === === === ===
   */

    //add checked attribute to html markup
    //$( '#the-quiz' ).on( 'change','input[type=radio]', function(){
    //    var name = $( this ).attr( 'name' );
    //    $( 'input[name =' + name + ']' ).removeAttr("checked");
    //    $( this ).attr('checked', true );
    //});

    //get all checked radio inputs dynamically into SERIALIZED OBJECT
    //var answers1 = $.map($("input:radio:checked"), function(elem, idx) {
    //   return $(elem).attr("name") + "=" + $(elem).val();
    //}).join('&');


    $( '#the-quiz' ).on( 'click', '#soundlush_submit_quiz', function( e )
    {
        //stop default submit behavior
        e.preventDefault();

        submitQuiz();
    });



    function submitQuiz()
    {

      //TODO disable button and input fields

      //dynamically store all radio groups name attributes into js OBJECT
      var questions = [];
      $( 'input:radio' ).each ( function(){
          var question_id = $( this ).attr( "name" );
          if( $.inArray( question_id, questions ) < 0 ){
             questions.push( question_id );
          }
      });

      //dynamically store all checked radio input name attributes into js OBJECT
      var answers = {};
      $( "input:radio:checked" ).each( function(){
          var key = $( this ).attr("name");
          var value = $( this ).val();
          answers[key] = value;
      });



      $.ajax({
		      url: ajax_slush.ajax_url,
          type: 'POST',
          data: {
               user_id : '1',
               post_id : '237',
               questions: JSON.stringify( questions ),
               answers: JSON.stringify( answers ),
               nonce: ajax_slush.ajax_nonce,
               action: 'save_user_quiz',
          },
          error : function( jqXHR, textStatus, errorThrown ){
              console.log( textStatus );
              console.log( errorThrown );
          },
          success : function( data, textStatus, jqXHR ){
              if( data.success = 1 )
              {
                  console.log( textStatus );
                  setTimeout( function(){
                      //reload page and scroll to top
                      location.reload();
                      $( document ).scrollTop( 0 )
                  }, 1000 );
              }
              else
              {
                  console.log( 'Oops... something went wrong' );
              }
          }
      });
    }



  /*
   * === === === === ===
   * USER MARK POST AS COMPLETE (AJAX)
   * === === === === ===
   */

  $( '#soundlush_markcomplete_btn' ).on( 'click', function( e ){

      var user = $( this ).data( 'user' ),
          post = $( this ).data( 'id' );

      $.ajax({
        url: ajax_slush.ajax_url,
        type : 'post',
        data: {
          user: user,
          post: post,
          nonce: ajax_slush.ajax_nonce,
          action: 'mark_as_completed'
        },
        error : function( jqXHR, textStatus, errorThrown ){
         console.log(textStatus);
        },
        success : function( data, textStatus, jqXHR ){
         //Go to the next lesson
         console.log(textStatus);
        }
      });


  });


  /*
   * === === === === ===
   * FRONTEND USER SUBMISSION FORM (AJAX)
   * === === === === ===
   */

  $('#soundlush_exercise').on('submit', function(e)
  {
      //stop default submit behavior
      e.preventDefault();

      //remove all previous error/feedback messages
      $('.has-error').removeClass('has-error');
      $('.js-show-form-feedback').removeClass('js-show-form-feedback');

      //get form data
      var form     = $(this),
          file     = form.find('#soundlush_exercise_submitted_file');
          comments = form.find('#soundlush_exercise_submitted_comments').val(),
          user     = form.data('user'),
          exercise = form.data('id'),
          nonce    = form.find("#_frontend_submission_nonce").val();

          ajaxurl  = form.data('url'),
          form_data = new FormData();


      //check if required fields are filled in
      //TODO check if file is from the accepted type
      if( file.val() == '' ){
        file.parents('.form-control').addClass('has-error');
        console.log('Required inputs are empty');
        return;
      }


      //append all submitted values to formdata variable
      form_data.append("user", user);
      form_data.append("exercise", exercise);
      form_data.append("file", file[0].files[0]);
      form_data.append("comments", comments);
      form_data.append("nonce", nonce);
      form_data.append("action", "save_frontend_submission" );


      //disable submit button during ajax call to avoid multiple submissions
      form.find('input, button, textarea').attr('disabled', 'disabled');
      $('.js-form-submission').addClass('js-show-form-feedback');


      //submit data
      $.ajax({
        url: ajaxurl,
        type : 'post',
        data: form_data,
        cache: false,
        dataType: 'json',
        processData: false,   //Don't process the files
        contentType: false,   //Set content type to false as jQuery will tell the server its a query string request
        error : function( response ){

          $('.js-form-submission').removeClass('js-show-form-feedback');
          $('.js-form-error').addClass('js-show-form-feedback');
          form.find('input, button, textarea').removeAttr('disabled');

        },
  		  success : function( response ){

            //check how to deal with response array
            if( response.response == "ERROR" ){

              setTimeout(function(){
                $('.js-form-submission').removeClass('js-show-form-feedback');
                $('.js-form-error-detail').html( response.error );
                $('.js-form-error').addClass('js-show-form-feedback');
                form.find('input, button, textarea').removeAttr('disabled');
              }, 2000);

            } else {

              setTimeout(function(){
                $('.js-form-submission').removeClass('js-show-form-feedback');
                $('.js-form-success').addClass('js-show-form-feedback');
                form.find('input, button, textarea').removeAttr('disabled').val('');
              }, 2000);

            }
  		  }
      })

  })


});
