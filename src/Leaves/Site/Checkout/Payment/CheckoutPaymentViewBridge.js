var bridge = function (leafPath) {
	window.rhubarb.viewBridgeClasses.ViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.ViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function () {
	var self = this;
    var submitButton = this.findChildViewBridge('Next');

    var handler = StripeCheckout.configure({
        key:this.model.stripePubKey,
        locale:'auto',
        image: '/static/favicon/favicon-128.png',
        token: function(token) {
            $('body').addClass('ajax-progress');
            self.raiseServerEvent('paymentMade', token, function(res) {
                if (res.success) {
                    window.location = res.url;
                } else {

                }
                $('body').removeClass('ajax-progress');
            });
        }
    });

    submitButton.viewNode.addEventListener('click', function(e) {
        // Open Checkout with further options:
        handler.open({
            name: '',
            description: '',
            currency:'gbp',
            amount: self.model.basketAmount,
            email: self.model.email
        });
        e.preventDefault();
    });
};

window.rhubarb.viewBridgeClasses.CheckoutPaymentViewBridge = bridge;