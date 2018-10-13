<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EMS</title>
	
	    <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600"
              rel="stylesheet" type="text/css" />
        
        <script src="https://js.stripe.com/v3/"></script>
        
        <link href="{{asset("css/app.css")}}" rel="stylesheet"
              type="text/css" />
        
        <style>
            .invalid-feedback {
	            display: block;
            }
        </style>

    </head>
    <body>
        <div class="container">
            
            @include("_partials.alert")
            
	        @include("_components.public_registration_form",['delegate'])
        </div>
    
    <script>
        // Create a Stripe client.
        var stripe = Stripe('pk_test_3gflQJsHLK3hUItQx14JLbnk');

        // Create an instance of Elements.
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
          base   : {
            color          : '#32325d',
            lineHeight     : '18px',
            fontFamily     : '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing  : 'antialiased',
            fontSize       : '16px',
            '::placeholder': {
              color: '#aab7c4'
            }
          },
          invalid: {
            color    : '#fa755a',
            iconColor: '#fa755a'
          }
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function (event) {
          var displayError = document.getElementById('card-errors');
          if (event.error) {
            displayError.textContent = event.error.message;
          } else {
            displayError.textContent = '';
          }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
          event.preventDefault();

          stripe.createToken(card).then(function (result) {
            if (result.error) {
              // Inform the user if there was an error.
              var errorElement = document.getElementById('card-errors');
              errorElement.textContent = result.error.message;
            } else {
              // Send the token to your server.
              console.log(result)
              stripeTokenHandler(result.token);
            }
          });
        });

        function stripeTokenHandler(token) {
          var el   = document.getElementById("token"),

              form = document.getElementById("payment-form")

          console.log(token)
          el.value = token.id;
          form.submit()
        }
    </script>
    </body>
</html>
