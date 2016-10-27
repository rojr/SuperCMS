var bridge = function (leafPath) {
	window.rhubarb.viewBridgeClasses.ViewBridge.apply(this, arguments);
};

bridge.prototype = new window.rhubarb.viewBridgeClasses.ViewBridge();
bridge.prototype.constructor = bridge;

bridge.prototype.attachEvents = function () {
	var self = this;
	$('.product-image-view').magnificPopup({
		type: 'image'
	});


	$('.variation-container').click(function (event){
		self.changeVariation($(this));
		event.preventDefault();
		return false;
	});
};

bridge.prototype.changeVariation = function(selectedObject) {
	var id = selectedObject.data('id');

	$(this.viewNode).find('.variation-container').removeClass('selected');
	selectedObject.addClass('selected');

	this.model.selectedVariationId = id;

	this.raiseServerEvent('changeSelectedVariation', id, function(values) {
		var image = $('.c-main-product-image');
		var imageLink = image.closest('a');
		var name = $('.c-product-variation-title');

		image.attr('src', values.MainImage);
		imageLink.attr('href', values.LargeImage);
		name.html(values.Name);
	});
};

window.rhubarb.viewBridgeClasses.ProductItemViewBridge = bridge;