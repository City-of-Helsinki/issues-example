function PateFormInitializer() {
	this.init();
}

PateFormInitializer.prototype.init = function() {
	this.initErrors();
//	this.initForm();
	this.initButtons();
};

PateFormInitializer.prototype.initErrors = function() {
	var actionValue = this.getParameterValue(window.location, "action");
	if (actionValue != undefined && actionValue != null && actionValue === "failed") {
		$("#pate #pate-error").show();
	}
};

PateFormInitializer.prototype.initForm = function() {
	$(document).delegate("*[lengthdisplay='true']", 'keyup', function() {
		$(this).keyup(function() {
			var $charDisplay = $(this).siblings("#charsleftdisplay");
			if($charDisplay.length == 0) {
				$(this).after("<div id='charsleftdisplay' title='Merkkejääellä</div>");
				$charDisplay = $(this).siblings("#charsleftdisplay");
			}
			var max = $(this).attr("maxlength");
			var used = $(this).val().length;
			$charDisplay.html("[ " + ( max - used ) + " ]");
		});
	});
};

PateFormInitializer.prototype.initButtons = function() {
	$("#pate #pate-submit").bind("click", handleSubmit);
	$("#pate #pate-clear-selection").bind("click", clearAttachment);
	$("#pate #pate-refresh").bind("click", function() {
		window.location.reload();
	});
	
	function clearAttachment() {
		var attachmentId = $(this).attr("attachmentId");
		var control = $("#pate #files" + attachmentId);
		
		control.replaceWith(control.val('').clone(true));
	}
	
	function handleSubmit() {
		$("#pate #pate-submit").attr("disabled", "disabled");
		
		setRefererUrl();
		setClientInfo();
		
		var item = $("#pate #feedbackForm").serialize();

		$.ajax({
			type : "POST",
			url : "/open311-test/proxy/proxy_test.php",
			data : item,
			accept : "application/json",
			contentType : "application/x-www-form-urlencoded; charset=UTF-8",
			success : function(result) {
				var obj = result;	
				//var obj = jQuery.parseJSON(result);
				//alert("vastaus " + obj[0].code);
				for ( var i = 0; i < obj.length; i++) {
                        		var r = obj[i];
					if (r.service_request_id) {
						//alert(r.service_request_id);
						onSuccess(r.service_request_id);
					} else {
						onFailure(result);
					}
				}
			}
		});
		
		function setRefererUrl() {
			var refererUrl = document.referrer;
			
			var refererInput = $("#pate #refererUrl");
			refererInput.val(refererInput.attr("categoryId") + "#" + refererUrl);
		}
		
		function setClientInfo() {
			var clientInfo = navigator.platform + " - " + navigator.userAgent;
			
			var clientInput = $("#pate #clientInfo");
			clientInput.val(clientInput.attr("categoryId") + "#" + clientInfo);
		}
	};
	
	function onSuccess(result) {
		$("#pate #feedbackForm").submit();
		 alert("Palautteen lahetys onnistui. " + result);
                 return;
	}
	
	function onFailure(fieldErrors) {
		$("#pate input, #pate textarea").removeClass("pate-error");
		$("#pate #feedbackForm").submit();

		//alert("Palautteen lahetys ei onnistunut.");
		return;
		//for ( var i = 0; i < fieldErrors.length; i++) {
		//	var fieldError = fieldErrors[i];	
		//	var fieldName = fieldError.field; 
		//	var message = fieldError.message;	
		//	var field = $('#pate [name="' + fieldName + '"]');
		//	field.addClass('pate-error');
		//	field.attr('title', message);
		//}
		
		//$("#pate #pate-submit").removeAttr("disabled");
	}
};

PateFormInitializer.prototype.getParameterValue = function(url, key) {
	var urlParams = this.getUrlParams(url);
	if (urlParams == null || urlParams.length == 0) {
		return null;
	}
	
	return urlParams[key];
};

PateFormInitializer.prototype.getUrlParams = function(url) {
	var params = [], hash;
	var hashes = url.href.slice(url.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++) {
		hash = hashes[i].split('=');
		params.push(hash[0]);
		params[hash[0]] = hash[1];
	}
	
	return params;
};
