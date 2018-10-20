<fieldset>
    <legend>Payment</legend>
    <div class="form-group row">
        <label class="fax col-sm-3 col-form-label">Credit card</label>
        <div class="col-sm-9">
            <div id="card-element">
                <!-- A Stripe Element will be inserted here. -->
            </div>
            <div id="card-errors" role="alert"></div>
        </div>
    </div>
</fieldset>

@push("scripts")
	<script src="https://js.stripe.com/v3/"></script>
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
@endpush