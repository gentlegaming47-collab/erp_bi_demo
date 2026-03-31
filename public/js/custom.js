jQuery.noConflict();





const IGST = 18;



// // Add event listener on keypress

//     document.addEventListener('keypress', (event) => {

//       var name = event.key;

//       var code = event.code;

//       if(code == "Enter"){

//           jQuery('.stdformbutton button').click();

//       }

//       return false;

//     }, false);



//Set default options for datatables

jQuery.extend(true, jQuery.fn.dataTable.defaults, {



	"fixedHeader": true,

	"pageLength": 25,

	"lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],

	"oLanguage": {

		"sEmptyTable": "No record found!",

		"sZeroRecords": "No search results found!",

		"sProcessing": "<div class='center'>"

			+ "<img src='images/loaders/loader6.gif' alt='loader'/>"

			+ "</div>"

	}

});





toastr.options.timeOut = 2000; // How long the toast will display without user interaction

toastr.options.extendedTimeOut = 5000; // How long the toast will display after a user hovers over it



function toastSuccess(msg, callNext = null) {



	if (msg.indexOf("{org-text}") != -1) {

		msg = msg.replace('{org-text}', '');

		setTimeout(() => {

			jQuery('#popup_container').addClass('org-text');

			jQuery('.alert').addClass('org-text');

		}, 100);

	}



	// jQuery.alerts.dialogClass = 'alert-success';

	jAlert(msg, 'Success', function (r) {



		if (callNext != null) {

			callNext(); //call back function

		}

		jQuery.alerts.dialogClass = null; // reset to default

	});



}











/* ------------------------ Preview Date : 02-06-2023 ---------------------------------*/



function toastPreview(msg, callNext = null, callPre = null) {



	if (msg.indexOf("{org-text}") != -1) {

		msg = msg.replace('{org-text}', '');

		setTimeout(() => {

			jQuery('#popup_container').addClass('org-text');

			jQuery('.alert').addClass('org-text');

		}, 100);

	}



	// jQuery.alerts.dialogClass = 'alert-success';

	jAlert(msg, 'Preview', function (r) {



		if (callNext != null) {

			callNext(); //call back function

		}

		jQuery.alerts.dialogClass = null; // reset to default

	}, function (r) {



		if (callPre != null) {

			callPre(); //call back function

		}

		jQuery.alerts.dialogClass = null; // reset to default

	});



}



/*------------------------------------- End Preview ---------------------------------*/













function toastError(msg, callNext = null) {



	if (msg.indexOf("{org-text}") != -1) {

		msg = msg.replace('{org-text}', '');

		setTimeout(() => {

			jQuery('#popup_container').addClass('org-text');

			jQuery('.alert').addClass('org-text');

		}, 100);

	}



	// jQuery.alerts.dialogClass = 'alert-danger';

	jAlert(msg, 'Error', function (r) {



		if (callNext != null) {

			callNext(); //call back function

		}

		jQuery.alerts.dialogClass = null; // reset to default

	});



}

// Duplicate value check then focus on this element

function toastElement(msg, element = null) {
	if (msg.indexOf("{org-text}") != -1) {
		msg = msg.replace('{org-text}', '');
		setTimeout(() => {
			jQuery('#popup_container').addClass('org-text');
			jQuery('.alert').addClass('org-text');
		}, 100);
	}
	jAlert(msg, "Alert", function (r) {
		if (element != null) {
			jQuery(document).on("click", "#popup_ok", function () {
				jQuery(element).focus();
			});
		}
		jQuery.alerts.dialogClass = null; // reset to default
	});
}
// ends

function toastWarning(msg, callNext = null) {



	if (msg.indexOf("{org-text}") != -1) {

		msg = msg.replace('{org-text}', '');

		setTimeout(() => {

			jQuery('#popup_container').addClass('org-text');

			jQuery('.alert').addClass('org-text');

		}, 100);

	}



	// jQuery.alerts.dialogClass = 'alert-warning';

	jAlert(msg, 'Warning', function (r) {



		if (callNext != null) {

			callNext(); //call back function

		}

		jQuery.alerts.dialogClass = null; // reset to default

	});



}



function toastInfo(msg, callNext = null) {



	if (msg.indexOf("{org-text}") != -1) {

		msg = msg.replace('{org-text}', '');

		setTimeout(() => {

			jQuery('#popup_container').addClass('org-text');

			jQuery('.alert').addClass('org-text');

		}, 100);

	}



	// jQuery.alerts.dialogClass = 'alert-info';

	jAlert(msg, 'Info', function (r) {



		if (callNext != null) {

			if (r) {

				callNext(); //call back function

			}

		}

		jQuery.alerts.dialogClass = null; // reset to default

	});



}



//  function toastSuccess(msg) {

// 	toastr.success(msg, 'Success', {

// 	  closeButton: true,

// 	  progressBar: true,

// 	  preventDuplicates:true

// 	});

//   }



//   function toastError(msg) {

// 	toastr.error(msg, 'Error', {

// 	  closeButton: true,

// 	  progressBar: true,

// 	  preventDuplicates:true

// 	});

//   }



//   function toastWarning(msg) {

// 	toastr.warning(msg, 'Warning', {

// 	  closeButton: true,

// 	  progressBar: true,

// 	  preventDuplicates:true

// 	});

//   }



//   function toastInfo(msg) {

// 	toastr.info(msg, 'Info', {

// 	  closeButton: true,

// 	  progressBar: true,

// 	  preventDuplicates,

// 	});

//   }



/**

 * Format Numbers

 */

function formatWeight($number) {

	if ($number != "" && $number > 0) {

		return Number($number).toFixed(3);

	} else {

		return '';

	}



}



function formatAmount($number) {

	if ($number != "" && $number > 0) {

		return Number($number).toFixed(2);

	} else {

		return '';

	}

}



function formatPoints($this, num) {

	if (jQuery($this).val() != "" && jQuery($this).val() > -1) {

		let formatVal = Number(jQuery($this).val()).toFixed(num);

		jQuery($this).val(formatVal)

	} else {

		jQuery($this).val('');

	}

}






//Set default options for validator

jQuery.extend(true, jQuery.validator.defaults, {



	ignore: ":hidden:not(.chzn-select)",

	onfocusout: false,

	// onkeyup: false,

	onkeyup: function (element, event) {

		// console.log(element,jQuery(element).val())

		if (event.which === 9 && jQuery(element).val() === "") {

			return;

		} else if (element.name in this.submitted && jQuery(element).valid() != true) {

			this.element(element);

		}



	},





	showErrors: function (errorMap, errorList) {



		if (errorList.length) {

			toastError(errorList[0]['message'].toString());





			//    function focusErrorElement(){

			// 		if(jQuery(errorList[0]['element']).hasClass('chzn-select')){

			// 			jQuery(errorList[0]['element']).trigger('liszt:activate');

			// 		}else {

			// 			jQuery(errorList[0]['element']).focus();

			// 		}

			//    }



			if (jQuery(errorList[0]['element']).closest('.ui-accordion-content')) {

				jQuery(errorList[0]['element']).closest('.ui-accordion-content').prev('.ui-accordion-header:not(.ui-state-active)').find('a').click()

			}



			if (jQuery(errorList[0]['element']).hasClass('chzn-select')) {

				jQuery(errorList[0]['element']).trigger('liszt:activate');

				jQuery("#popup_ok").click(function () {
					setTimeout(() => {
						// jQuery(errorList[0]['element']).focus();
						jQuery(errorList[0]['element']).trigger('liszt:activate');
					}, 1000);
				});

				// setTimeout(()=>{
				// 	jQuery(errorList[0]['element']).focus();
				// },1000);

			} else {

				jQuery(errorList[0]['element']).focus();

			}

			if (jQuery(errorList[0]['element']).closest('.control-group').length > 0) {

				jQuery(errorList[0]['element']).closest('.control-group').addClass('error');

				// setTimeout(()=>{
				// 	jQuery(errorList[0]['element']).trigger('liszt:activate');
				// },1000);

				jQuery("#popup_ok").click(function () {
					setTimeout(() => {
						jQuery(errorList[0]['element']).trigger('liszt:activate');
					}, 100);
				});

			} else {



				jQuery(errorList[0]['element']).parent('td').addClass('error');

				if (jQuery(errorList[0]['element']).hasClass('chzn-select')) {
					jQuery("#popup_ok").click(function () {
						setTimeout(() => {
							jQuery(errorList[0]['element']).trigger('liszt:activate');
						}, 1000);
					})

				} else {
					jQuery("#popup_ok").click(function () {
						setTimeout(() => {
							jQuery(errorList[0]['element']).focus();
						}, 100);
					});

				}

			}





			if (jQuery(errorList[0]['element']).parent('td').parent('tr').find('input[name="form_indx"]') && jQuery(errorList[0]['element']).parent('td').parent('tr').find('input[name="form_type"]').length <= 0) {

				jQuery(errorList[0]['element']).parent('td').parent('tr').find('input[name="form_indx"]').parent('td').find('a:first').trigger('click');



				let elmName = jQuery(errorList[0]['element']).attr('name');



				elmName = elmName.replace('[]', '');



				if (jQuery('#' + elmName).closest('form').parent('div').hasClass('.modal')) {



					setTimeout(() => {

						if (jQuery('#' + elmName).closest('form').find('#' + elmName).closest('.control-group').length > 0) {

							jQuery('#' + elmName).closest('form').find('#' + elmName).closest('.control-group').addClass('error');

							jQuery("#popup_ok").click(function () {
								setTimeout(() => {
									jQuery('#' + elmName).focus();
								}, 100);
							});

							// setTimeout(()=>{
							// 	jQuery('#' + elmName).focus();
							// },1000);

						} else {

							jQuery('#' + elmName).closest('form').find('#' + elmName).parent('td').addClass('error');

						}

					}, 300);



				} else {



					if (jQuery('#' + elmName).closest('.control-group').length > 0) {

						jQuery('#' + elmName).closest('.control-group').addClass('error');
						// setTimeout(()=>{
						// 	jQuery('#' + elmName).focus();
						// },1000);

						jQuery("#popup_ok").click(function () {
							setTimeout(() => {
								jQuery('#' + elmName).focus();
							}, 100);
						});

						jQuery('#' + elmName).on('change', function () {
							jQuery('#' + elmName).closest('.control-group').removeClass('error');
						});
					} else {

						// jQuery('#' + elmName).parent('td').removeClass('error');    //this is effect of first tr item select not red border

					}

				}



			}

		} else {



			if (jQuery(this.currentElements.context).closest('.control-group').length > 0) {

				jQuery(this.currentElements.context).closest('.control-group').removeClass('error');

			} else {

				jQuery(this.currentElements.context).parent('td').removeClass('error');

			}

		}

	}

});



var RouteBasePath = "";

var uploadURL = "";

var transDateFormat = {};

var futureDateFormat = {};

var futureSelDateFormat = {};

var defaultDateFormat = {};



function setSelTabIndxToReadonly($modal = null) {

	if ($modal != null) {



		jQuery($modal).find('select.chzn-select').each(function (e) {



			if (jQuery(this).is('[readonly]')) {

				jQuery(this).attr('tabindex', -1);

				jQuery(this).next('div.chzn-container').attr('tabindex', -1);

				jQuery(this).next('div.chzn-container').find('div.chzn-search').find('input').attr('tabindex', -1);

				jQuery(this).next('div.chzn-container').addClass('chzn-disabled');

				jQuery(this).next('div.chzn-container').find('a').addClass('chzn-disabled');

			} else {

				jQuery(this).removeAttr('tabindex');

				jQuery(this).next('div.chzn-container').removeAttr('tabindex');

				jQuery(this).next('div.chzn-container').find('input').removeAttr('tabindex');

				jQuery(this).next('div.chzn-container').find('a').removeClass('chzn-disabled');

			}

		});

	} else {

		jQuery('select.chzn-select').each(function (e) {

			if (jQuery(this).is('[readonly]')) {

				jQuery(this).attr('tabindex', -1);

				jQuery(this).next('div.chzn-container').attr('tabindex', -1);

				jQuery(this).next('div.chzn-container').find('div.chzn-search').find('input').attr('tabindex', -1);

				jQuery(this).next('div.chzn-container').addClass('chzn-disabled');

				jQuery(this).next('div.chzn-container').find('a').addClass('chzn-disabled');

			} else {

				jQuery(this).removeAttr('tabindex');

				jQuery(this).next('div.chzn-container').removeAttr('tabindex');

				jQuery(this).next('div.chzn-container').find('input').removeAttr('tabindex');

				jQuery(this).next('div.chzn-container').find('a').removeClass('chzn-disabled');

			}

		});

	}



}



function setInputTabIndexToReadonly($modal = null) {



	if ($modal != null) {



		jQuery($modal).find('input,textarea').each(function (e) {

			if (jQuery(this).is('[readonly]')) {

				jQuery(this).attr('tabindex', -1);

			} else {

				jQuery(this).removeAttr('tabindex');

			}

		});

	} else {

		jQuery('input,textarea').each(function (e) {

			if (jQuery(this).is('[readonly]')) {

				jQuery(this).attr('tabindex', -1);

			} else {

				jQuery(this).removeAttr('tabindex');

			}

		});

	}



}



jQuery(document).ready(function () {

	// Function to add the "Reset Filter" button for a given DataTable
	function addResetButton(table, $tableElement) {
		// Get the closest wrapper around the table where DataTables initializes its UI
		var wrapper = $tableElement.closest('.dataTables_wrapper');

		// Locate the filter (search box) container
		var filterContainer = wrapper.find('.dataTables_filter');

		// Ensure the filter container uses flex styling so the button aligns well with the search input
		filterContainer.css({
			'display': 'flex',
			'align-items': 'center',
			'gap': '10px',
			'flex-wrap': 'nowrap'
		});

		// Generate a unique ID for the reset button using the table ID (or a random fallback)
		var tableId = $tableElement.attr('id') || 'datatable-' + Math.floor(Math.random() * 10000);
		var resetBtnId = 'reset-filters-' + tableId;

		// Check if reset button already exists to avoid duplicates
		if (!filterContainer.find('#' + resetBtnId).length) {
			// Create the reset button
			var resetBtn = jQuery('<button>', {
				id: resetBtnId,
				class: 'btn btn-default',
				type: 'button',
				text: 'Reset Filter'
			});

			// Apply minimal inline styles to the button
			resetBtn.css({
				'outline': 'none',
				'box-shadow': 'none',
				'border': '',
				'background': '',
				'cursor': 'pointer'
			});

			// Remove focus styling when the button is focused
			resetBtn.on('focus', function () {
				jQuery(this).css({
					'outline': 'none',
					'box-shadow': 'none'
				});
			});

			// Remove focus when mouseup occurs (for better UX)
			resetBtn.on('mouseup', function () {
				this.blur();
			});

			// Add the reset button before the search input in the filter container
			filterContainer.prepend(resetBtn);

			// Button click behavior: reset all filters
			resetBtn.on('click', function () {
				// Clear global search
				table.search('').draw();

				// Loop through each column and clear individual column search input if present
				table.columns().every(function (index) {
					var input = jQuery(this.header())
						.closest('table')
						.find('thead tr.search-row th')
						.eq(index)
						.find('input');

					if (input.length) {
						input.val('');      // Clear input value
						this.search('');    // Clear column search
					}
				});

				// Redraw the table with all filters cleared
				table.draw();
			});
		}
	}

	// When any DataTable is initialized, hook into the event to conditionally add the reset button
	jQuery(document).on('init.dt', function (e, settings) {
		var table = new jQuery.fn.dataTable.Api(settings);         // DataTable instance
		var $tableElement = jQuery(settings.nTable);               // jQuery-wrapped <table> element

		// Only add reset button if:
		// - Table has 'dataTable' class
		// - Table does NOT have 'remove-reset-filter' class (used to exclude some tables)
		if ($tableElement.hasClass('dataTable') && !$tableElement.hasClass('remove-reset-filter')) {
			addResetButton(table, $tableElement);
		}
	});


	// setTimeout(() => {	
	// 	jQuery('.dataTable').each(function () {
	// 	  const $table = jQuery(this);
	// 	  const table = $table.DataTable(); 
	// 	  addResetButton(table, $table); 
	//   });
	// }, 1500);

	jQuery('.modal').on('show.bs.modal', function () {
		setTimeout(() => {
			jQuery(this).find('[autofocus]').focus();
		}, 500);
	});


	RouteBasePath = jQuery('#rt_base_path').val();

	uploadURL = jQuery('#upload_url').val();



	// setTimeout(()=>{

	// 	setSelTabIndxToReadonly();

	setInputTabIndexToReadonly();

	// },1000);





	/* Disable choosen selection on readonly also */



	// Select the elements with the desired class

	const elements = jQuery('select.chzn-select');



	// Create a new MutationObserver instance

	const observer = new MutationObserver((mutationsList) => {

		for (let mutation of mutationsList) {

			if (mutation.type === 'attributes' && mutation.attributeName == 'readonly') {

				if (mutation.oldValue == null) {

					jQuery(mutation.target).attr('tabindex', -1);

					jQuery(mutation.target).next('div.chzn-container').attr('tabindex', -1);

					jQuery(mutation.target).next('div.chzn-container').find('div.chzn-search').find('input').attr('tabindex', -1);

					jQuery(mutation.target).next('div.chzn-container').addClass('chzn-disabled');

					jQuery(mutation.target).next('div.chzn-container').find('a').addClass('chzn-disabled');

				} else {

					jQuery(mutation.target).removeAttr('tabindex');

					jQuery(mutation.target).next('div.chzn-container').removeAttr('tabindex');

					jQuery(mutation.target).next('div.chzn-container').find('div.chzn-search').find('input').removeAttr('tabindex');

					jQuery(mutation.target).next('div.chzn-container').removeClass('chzn-disabled');

					jQuery(mutation.target).next('div.chzn-container').find('a').removeClass('chzn-disabled');

				}

			}

		}

	});



	const observesOptions = {

		attributes: true,

		attributeOldValue: true

	};



	// Observe each element with the class 'your-class' for attribute changes

	Array.from(elements).forEach((element) => {

		observer.observe(element, observesOptions);

	});



	/* : END : */



	/* Disable elements by adding tabindex="-1" on readonly also */



	// Select the elements with the desired class

	const elements2 = jQuery('input,textarea');



	// Create a new MutationObserver instance

	const observer2 = new MutationObserver((mutationsList) => {

		for (let mutation of mutationsList) {

			if (mutation.type === 'attributes' && mutation.attributeName == 'readonly') {

				if (mutation.oldValue == null) {

					jQuery(mutation.target).attr('tabindex', -1);

				} else {

					jQuery(mutation.target).removeAttr('tabindex');

				}

			}

		}

	});



	const observesOptions2 = {

		attributes: true,

		attributeOldValue: true

	};



	// Observe each element with the class 'your-class' for attribute changes

	Array.from(elements2).forEach((element2) => {

		observer.observe(element2, observesOptions2);

	});



	/* : END : */



	/* Preventing focus if element is readonly */

	jQuery(document).on('keydown', ':focusable[readonly]', function (event) {

		if (event.key === 'Tab') {

			event.preventDefault();

		}

	});



	jQuery(':focusable[readonly]').on('mousedown', function (event) {

		event.preventDefault();

	});



	jQuery(document).on('focus', function (e) {

		if (jQuery(e).hasAttribute('readonly')) {

			event.preventDefault();

		}

	});



	jQuery(document).on('show.bs.modal', '.modal', function () {



		setTimeout(() => {

			setSelTabIndxToReadonly(jQuery(this));

			setInputTabIndexToReadonly(jQuery(this));

		}, 300);





	});







	jQuery.validator.addMethod("decimalpoints", function (value, element, points) {



		function test(pts) {

			var decm = new RegExp("^\\d+(?:\\.\\d{0," + pts + "})?$");

			if (decm.test(value) == true) {

				return true;

			} else {

				return false;

			}

		}



		return this.optional(element) || test(points);



	}, "Please Enter Valid Format");



	jQuery.validator.addMethod("gstInValidator", function (value, element) {



		function test() {

			const GSTIINregexp = /^([0][1-9]|[1-2][0-9]|[3][0-7])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/;

			if (GSTIINregexp.test(value) == true) {

				return true;

			} else {

				return false;

			}

		}

		return this.optional(element) || test();



	}, "Please Enter Valid GST No.");





	jQuery.validator.addMethod("ifscValidator", function (value, element) {



		function test() {

			const IFSCregexp = /^[A-Za-z]{4}[a-zA-Z0-9]{7}$/;

			if (IFSCregexp.test(value) == true) {

				return true;

			} else {

				return false;

			}

		}

		return this.optional(element) || test();



	}, "Please Enter Valid IFSC No.");





	jQuery.validator.addMethod("panValidator", function (value, element) {



		function test() {

			const PANregexp = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

			if (PANregexp.test(value) == true) {

				return true;

			} else {

				return false;

			}



		}

		return this.optional(element) || test();



	}, "Please Enter Valid PAN No.");



	setTimeout(() => {

		jQuery('input,textarea').attr('autocomplete', 'off');

		jQuery('select[tabindex="-1"]').attr('tabindex', 1);

	}, 500);



	jQuery(document).click(function () {

		var thisList;

		var totalFocus = 0;

		jQuery('.suggestion_list').find('li').each(function (idx) {



			thisList = jQuery(this);

			if (jQuery(this).is(':focus')) {

				totalFocus++;

			}

		});



		if (totalFocus == 0) {

			var list = jQuery(thisList).parent('ul').parent('div.suggestion_list');

			var listId = jQuery(thisList).attr('list-id');

			if (jQuery('#' + listId).length > 0) {

				jQuery('#' + listId).html('');

			} else {

				jQuery(list).html('');

			}



		}

	});



	jQuery(document).on('blur', '.suggestion_list ul li.list-group-item:last', function (e) {

		var listId = jQuery(this).attr('list-id');

		jQuery('#' + listId).html('');

	});



	jQuery(document).on('keypress', '.only-numbers', function (evt) {

		evt = (evt) ? evt : window.event;

		var charCode = (evt.which) ? evt.which : evt.keyCode;

		if (charCode > 31 && (charCode < 48 || charCode > 57)) {

			return false;

		}

		return true;

	});

	jQuery(document).on('keypress', '.isNumberKey', function (evt) {

		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode != 46 && charCode > 31
			&& (charCode < 48 || charCode > 57))
			return false;

		return true;

	});



	jQuery(document).on('click keydown', '.suggestion_list ul li.list-group-item', function (e) {



		if (e.type == "keydown") {

			if (e.which == 13) {

				var closestElm = "";



				if (jQuery(this).attr("trg-elm")) {

					let trgId = jQuery(this).attr("trg-elm");

					let trgVal = jQuery(this).attr("trg-val");



					jQuery(this).parent('ul').parent('div.suggestion_list').siblings('#' + trgId).val(trgVal).trigger('change');

				}



				if (jQuery(this).parent('ul').parent('div.suggestion_list').siblings("textarea").length > 0) {

					closestElm = jQuery(this).parent('ul').parent('div.suggestion_list').siblings('textarea');

				} else if (jQuery(this).parent('ul').parent('div.suggestion_list').siblings("input:not([type=hidden])").length > 0) {

					closestElm = jQuery(this).parent('ul').parent('div.suggestion_list').siblings("input:not([type=hidden])");

				}

				var list = jQuery(this).parent('ul').parent('div.suggestion_list');



				if (closestElm != "") {

					var thisVal = jQuery(this).text();

					jQuery(closestElm).val(thisVal);

					jQuery(list).html('');

				} else {

					var parentId = jQuery(this).attr('parent-id');

					var listId = jQuery(this).attr('list-id');

					var thisVal = jQuery(this).text();

					jQuery('#' + parentId).val(thisVal);

					jQuery('#' + listId).html('');

				}

			}

		} else {

			var closestElm = "";



			if (jQuery(this).attr("trg-elm")) {

				let trgId = jQuery(this).attr("trg-elm");

				let trgVal = jQuery(this).attr("trg-val");



				jQuery(this).parent('ul').parent('div.suggestion_list').siblings('#' + trgId).val(trgVal).trigger('change');

			}



			if (jQuery(this).parent('ul').parent('div.suggestion_list').siblings("textarea").length > 0) {

				closestElm = jQuery(this).parent('ul').parent('div.suggestion_list').siblings("textarea");

			} else if (jQuery(this).parent('ul').parent('div.suggestion_list').siblings("input:not([type=hidden])").length > 0) {

				closestElm = jQuery(this).parent('ul').parent('div.suggestion_list').siblings("input:not([type=hidden])");

			}

			var list = jQuery(this).parent('ul').parent('div.suggestion_list');



			if (closestElm != "") {

				var thisVal = jQuery(this).text();

				jQuery(closestElm).val(thisVal);

				jQuery(list).html('');

			} else {

				var parentId = jQuery(this).attr('parent-id');

				var listId = jQuery(this).attr('list-id');

				var thisVal = jQuery(this).text();

				jQuery('#' + parentId).val(thisVal);

				jQuery('#' + listId).html('');

			}

		}



	});



	jQuery(document).on('keydown', 'i', function (e) {



		if (e.type == "keydown") {

			if (e.which == 13) {

				jQuery(this).click();

			}

		}



	});



	var validChars = [

		"-",

		"+"

	];



	var specialChars = [

		"!",

		"@",

		"#",

		"$",

		"%",

		"^",

		"&",

		"*",

		"(",

		")"

	];



	jQuery(document).on('keydown', '.mobile-f', function (e) {



		if (e.which != 8 && e.which != 0 && e.which != 9 && e.which != 32 && e.which != 96 && e.which != 97 && e.which != 98 && e.which != 99 && e.which != 100 && e.which != 101 && e.which != 102 && e.which != 103 && e.which != 104 && e.which != 105 && e.which != 107 && (e.which < 48 || (e.which > 57 && validChars.includes(e.key) !== true))) {

			e.preventDefault();

			// console.log(e.which);

		} else if (specialChars.includes(e.key)) {

			// console.log("EL : " + e.which);

			e.preventDefault();

		}

	});

	jQuery(document).on('keydown', '.checkNumberFormat', function (e) {



		if (e.which != 8 && e.which != 0 && e.which != 9 && e.which != 32 && e.which != 96 && e.which != 97 && e.which != 98 && e.which != 99 && e.which != 100 && e.which != 101 && e.which != 102 && e.which != 103 && e.which != 104 && e.which != 105 && e.which != 107 && (e.which < 48 || (e.which > 57 && validChars.includes(e.key) !== true))) {

			e.preventDefault();

			// console.log(e.which);

		} else if (specialChars.includes(e.key)) {

			// console.log("EL : " + e.which);

			e.preventDefault();

		}

	});




	jQuery(document).on("keydown", '.nodecimal', function (e) {

		if (e.key == '.') {

			e.preventDefault();

		}

	})



	var invalidChars = [

		"-",

		"+",

		"e",

	];



	jQuery(document).on("keydown", 'input[type="number"]', function (e) {

		if (invalidChars.includes(e.key)) {

			e.preventDefault();

		}

	});



	jQuery(".date-picker:not(.future-sel):not([readonly])").datepicker({

		dateFormat: "dd/mm/yy",
		onClose: function () {
			this.focus()
		},

	});



	defaultDateFormat = {

		dateFormat: "dd/mm/yy",

	};



	jQuery(".date-picker.future-sel:not([readonly])").datepicker({

		dateFormat: "dd/mm/yy",

		minDate: 0

	});



	futureSelDateFormat = {

		dateFormat: "dd/mm/yy",

		minDate: 0

	};



	jQuery(".future-date-picker:not([readonly])").datepicker({

		dateFormat: "dd/mm/yy",

		minDate: '+1d'

	});



	futureDateFormat = {

		dateFormat: "dd/mm/yy",

		minDate: '+1d'

	};



	let startDate = new Date(jQuery('#def_year_startdate').val());

	let endDate = new Date(jQuery('#def_year_enddate').val());



	// 	startDate = moment(startDate).format('yy-mm-dd');

	// 	endDate = moment(endDate).format('yy-mm-dd');

	jQuery(".date-picker:not([readonly])").datepicker({

		dateFormat: "dd/mm/yy",
		onClose: function () {
			this.focus()
		},

	});
	// jQuery(".dates-picker:not([readonly])").datepicker({
	// 	dateFormat: "dd/mm/yy",
	// 	onClose: function () {
	// 		this.focus()
	// 	},
	// });



	jQuery(".trans-date-picker:not([readonly])").datepicker({

		dateFormat: "dd/mm/yy",

		minDate: startDate,

		maxDate: endDate,
		onClose: function () {
			this.focus()
		},

	});

	// Report Date Picker 
	jQuery(".report-date-picker:not([readonly])").datepicker({

		dateFormat: "dd/mm/yy",
		autoclose: true,
		changeYear: true,              // Allow year dropdown
		changeMonth: true,             // Allow month dropdown (optional)
		yearRange: "2000:4000",
		onClose: function () {
			this.focus()
		},

	});

	if (jQuery(".report-date-picker:not(.no-fill)").val() == "") {
		jQuery(".report-date-picker:not(.from-april)").datepicker("setDate", "today");
	}





	transDateFormat = {

		dateFormat: "dd/mm/yy",

		minDate: startDate,

		maxDate: endDate

	};





	jQuery('.time-picker').timepicker({

		showSeconds: false,

		showMeridian: false,

		defaultTime: false

	});







	if (jQuery('.current-time').val() == "") {



		var dt = new Date();

		var min = dt.getMinutes();

		var hr = dt.getHours();

		jQuery('.current-time').timepicker('setTime', hr + ':' + min);

	}



	jQuery(".time-picker-12").timepicker({

		maxHours: 12,

		showSeconds: false,

		showMeridian: false,

		defaultTime: false

	})



	jQuery(".time-picker-12, .time-picker").attr('placeholder', 'HH:MM');





	function from24to12(s) {

		function z(n) { return (n < 10 ? '0' : '') + n }

		var h, m, b, re = /\D/;



		// If there's a separator, split on it

		// First part is h, second is m

		if (re.test(s)) {

			b = s.split(re);

			h = b[0];

			m = z(+b[1]);



			// Otherwise, last two chars are mm, first one or two are h

		} else {

			h = s.substring(0, s.length - 2);

			m = s.substring(s.length - 2);

		}

		if (h > 0 || m > 0) {

			return (h % 12) + ':' + m;

		} else {

			return '';

		}



	}



	jQuery(".time-picker-12, .time-picker").on('change', function (e) {

		let thVal = jQuery(this).val();

		jQuery(this).val(from24to12(thVal));

	});



	// .on('changeTime.timepicker', function(e) {  

	// 	var h= e.time.hours;

	// 	var m= e.time.minutes;

	// 	let thisId = jQuery(this).attr("id");

	// 	document.getElementById(thisId).value = h+':'+m;

	// 	return false;



	// });



	jQuery(document).on('change', '.hour-selector', function () {

		let relationField = jQuery(this).attr('relt');

		if (jQuery(this).children("option:selected").val() == "24") {

			jQuery('#' + relationField).val("0");

		}



	});



	jQuery(document).on('change', '.minute-selector', function () {

		let relationField = jQuery(this).attr('relt');

		if (jQuery('#' + relationField).children("option:selected").val() == "24") {

			jQuery(this).val("0");

		}

	});



	if (jQuery(".date-picker:not(.no-fill)").val() == "") {

		jQuery(".date-picker:not(.no-fill)").datepicker("setDate", "today");

	}



	jQuery(".datetime").datetimepicker({

		format: "DD/MM/YYYY hh:mm"

	});



	jQuery(".chzn-select").chosen();



	// trigger validation on change chosen select

	jQuery(document).on('change', '.chzn-select', function (evt, params) {



		if (jQuery(this).is(":visible")) {
			jQuery(this).valid();
		}

	});





	// dropdown in leftmenu

	jQuery('.leftmenu .dropdown > a').click(function () {

		if (!jQuery(this).next().is(':visible'))

			jQuery(this).next().slideDown('fast');

		else

			jQuery(this).next().slideUp('fast');

		return false;

	});





	if (jQuery.uniform)

		jQuery('input:checkbox:not(.simple-check), input:radio:not(.simple-check), .uniform-file').uniform();



	if (jQuery('.widgettitle .close').length > 0) {

		jQuery('.widgettitle .close').click(function () {

			jQuery(this).parents('.widgetbox').fadeOut(function () {

				jQuery(this).remove();

			});

		});

	}





	jQuery('<div class="topbar"><a class="barmenu">' +

		'</a><div class="chatmenu"></a></div>').insertBefore('.mainwrapper');



	jQuery('.topbar .barmenu').click(function () {



		var lwidth = '260px';

		if (jQuery(window).width() < 340) {

			lwidth = '240px';

		}


		if (!jQuery(this).hasClass('open')) {

			jQuery('.rightpanel, .headerinner').css({ marginLeft: lwidth }, 'slow');

			jQuery('.leftpanel').css({ marginLeft: 0 }, 'slow');

			jQuery('body').css({ background: "url(../images/leftpanelbg.png) repeat-y 0 0" });

			jQuery(this).addClass('open');

		} else {

			jQuery('.rightpanel, .headerinner').css({ marginLeft: 0 }, 'slow');

			jQuery('.leftpanel').css({ marginLeft: '-' + lwidth }, 'slow');

			jQuery('body').css({ background: "none" });

			jQuery(this).removeClass('open');

		}

		// if (!jQuery(this).hasClass('open')) {

		// 	jQuery('.rightpanel, .headerinner, .topbar').css({ marginLeft: lwidth }, 'fast');

		// 	jQuery('.logo, .leftpanel').css({ marginLeft: 0 }, 'fast');

		// 	jQuery(this).addClass('open');

		// } else {

		// 	jQuery('.rightpanel, .headerinner, .topbar').css({ marginLeft: 0 }, 'fast');

		// 	jQuery('.logo, .leftpanel').css({ marginLeft: '-' + lwidth }, 'fast');

		// 	jQuery(this).removeClass('open');

		// }

	});



	jQuery('.topbar .chatmenu').click(function () {

		if (!jQuery('.onlineuserpanel').is(':visible')) {

			jQuery('.onlineuserpanel,#chatwindows').show();

			jQuery('.topbar .chatmenu').css({ right: '210px' });

		} else {

			jQuery('.onlineuserpanel, #chatwindows').hide();

			jQuery('.topbar .chatmenu').css({ right: '10px' });

		}

	});


	jQuery(window).resize(function () {

		if (!jQuery('.topbar .barmenu').hasClass('open')) {

			jQuery('body').css({ background: "none" });

		} else {

			jQuery('body').css({ background: "url(../images/leftpanelbg.png) repeat-y 0 0" });

		}



	});

	// show/hide left menu

	// jQuery(window).resize(function () {

	// 	if (!jQuery('.topbar').is(':visible')) {

	// 		jQuery('.rightpanel, .headerinner').css({ marginLeft: '260px' });

	// 		jQuery('.logo, .leftpanel').css({ marginLeft: 0 });

	// 	} else {

	// 		jQuery('.rightpanel, .headerinner').css({ marginLeft: 0 });

	// 		jQuery('.logo, .leftpanel').css({ marginLeft: '-260px' });

	// 	}

	// });



	// dropdown menu for profile image

	jQuery('.userloggedinfo img').click(function () {

		if (jQuery(window).width() < 480) {

			var dm = jQuery('.userloggedinfo .userinfo');

			if (dm.is(':visible')) {

				dm.hide();

			} else {

				dm.show();

			}

		}

	});



	// change skin color

	jQuery('.skin-color a').click(function () { return false; });

	jQuery('.skin-color a').hover(function () {

		var s = jQuery(this).attr('href');

		if (jQuery('#skinstyle').length > 0) {

			if (s != 'default') {

				jQuery('#skinstyle').attr('href', 'css/style.' + s + '.css');

				jQuery.cookie('skin-color', s, { path: '/' });

			} else {

				jQuery('#skinstyle').remove();

				jQuery.cookie("skin-color", '', { path: '/' });

			}

		} else {

			if (s != 'default') {

				jQuery('head').append('<link id="skinstyle" rel="stylesheet" href="css/style.' + s + '.css" type="text/css" />');

				jQuery.cookie("skin-color", s, { path: '/' });

			}

		}

		return false;

	});



	// load selected skin color from cookie

	if (jQuery.cookie('skin-color')) {

		var c = jQuery.cookie('skin-color');

		if (c) {

			jQuery('head').append('<link id="skinstyle" rel="stylesheet" href="css/style.' + c + '.css" type="text/css" />');

			jQuery.cookie("skin-color", c, { path: '/' });

		}

	}





	// expand/collapse boxes

	if (jQuery('.minimize').length > 0) {



		jQuery('.minimize').click(function () {

			if (!jQuery(this).hasClass('collapsed')) {

				jQuery(this).addClass('collapsed');

				jQuery(this).html("&#43;");

				jQuery(this).parents('.widgetbox')

					.css({ marginBottom: '20px' })

					.find('.widgetcontent')

					.hide();

			} else {

				jQuery(this).removeClass('collapsed');

				jQuery(this).html("&#8211;");

				jQuery(this).parents('.widgetbox')

					.css({ marginBottom: '0' })

					.find('.widgetcontent')

					.show();

			}

			return false;

		});



	}



	// fixed right panel

	var winSize = jQuery(window).height();

	if (jQuery('.rightpanel').height() < winSize) {

		jQuery('.rightpanel').height(winSize);

	}





	// if facebook like chat is enabled

	if (jQuery.cookie('enable-chat')) {



		jQuery('body').addClass('chatenabled');

		jQuery.get('ajax/chat.html', function (data) {

			jQuery('body').append(data);

		});



	} else {



		if (jQuery('.chatmenu').length > 0) {

			jQuery('.chatmenu').remove();

		}



	}



	// check date of current year


	// function checkDate(date) {

	// 	defYear = jQuery('#def_year_startdate').val();

	// 	var newdate = date.split("/").reverse().join("-");

	// 	const defDate = new Date(defYear);
	// 	const currentDate = new Date(newdate);
	// 	// console.log(defDate);
	// 	// console.log(currentDate);

	// 	if (date !== undefined && date != "") {
	// 		if (currentDate >= defDate) {
	// 			return 'yes';
	// 		} else {
	// 			return 'no';
	// 		}
	// 	} else {
	// 		return 'yes';
	// 	}


	// }
	function checkDate(date) {

		defYear = jQuery('#def_year_startdate').val();
		var defYearLastDate = jQuery('#def_year_enddate').val();
		var newdate = date.split("/").reverse().join("-");

		const defDate = new Date(defYear);
		const currentDate = new Date(newdate);
		const deflastDate = new Date(defYearLastDate);
		// console.log(defDate);
		// console.log(currentDate);

		if (date !== undefined && date != "") {
			if (currentDate >= defDate && currentDate <= deflastDate) {
				return 'yes';
			} else {
				return 'no';
			}
		} else {
			return 'yes';
		}


	}

	// check date year

	jQuery.validator.addMethod("date_check", function (value, element) {

		if (checkDate(value) == 'no') {
			return false;
		} else {
			return true;
		}
		// }, "Please enter valid date");
	}, "Enter Date within Current Financial Year Selected.");



	// check date on focus on tab

	jQuery(".trans-date-picker").one("focus", function (e) {

		if (checkDate(e.target.value) == 'no') {
			toastError("Enter Date within Current Financial Year Selected.");

			jQuery('.trans-date-picker').trigger('liszt:activate');
			// jQuery(".trans-date-picker").on("focus");
		} else {
			return true;
		}

	});
	jQuery(".trans-date-picker").on("change", function (e) {
		// console.log();
		if (checkDate(e.target.value) == 'no') {
			toastError("Enter Date within Current Financial Year Selected.");

			// jQuery('#' + id).focus();


			jQuery('.trans-date-picker').trigger('liszt:activate');
			// jQuery(".trans-date-picker").on("focus");
		} else {
			return true;
		}

	});

	// .trans-date-picker on change Validation
	jQuery(".trans-date-picker").change(function (e) {
		var idata = e.target.value;
		var check = false;
		var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
		if (re.test(idata)) {
			var adata = idata.split('/');
			var dd = parseInt(adata[0], 10);
			var mm = parseInt(adata[1], 10);
			var yyyy = parseInt(adata[2], 10);
			var xdata = new Date(yyyy, mm - 1, dd);
			if ((xdata.getFullYear() === yyyy) && (xdata.getMonth() === mm - 1) && (xdata.getDate() === dd)) {
				check = true;
			} else {
				check = false;
			}
		} else {
			// jQuery('.trans-date-picker').val('');
			check = false;
		}
		if (check == false) {
			jAlert("Please Enter A Valid Date!");
		}

		// if (checkDate(idata) == 'no') {
		//     toastError("Enter Date within Current Financial Year Selected.");

		//     // jQuery('.trans-date-picker').trigger('liszt:activate');
		//     // jQuery(".trans-date-picker").on("focus");
		// } else {
		//     return true;
		// }
	});


	jQuery(".manual-date").on('change', function (e) {
		var idata = jQuery(this).val().trim(); // user input
		var isValid = false;

		// Regular expression dd/mm/yyyy
		var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;

		if (re.test(idata)) {
			var parts = idata.split('/');
			var dd = parseInt(parts[0], 10);
			var mm = parseInt(parts[1], 10);
			var yyyy = parseInt(parts[2], 10);

			// Create JS Date object
			var dt = new Date(yyyy, mm - 1, dd);

			// Check if date is valid
			if (dt.getFullYear() === yyyy && dt.getMonth() === mm - 1 && dt.getDate() === dd) {
				isValid = true;
			}
		}

		if (!isValid) {
			jAlert("Please Enter A Valid Date!");
			jQuery(this).val(''); // Optional: clear invalid input
			jQuery(this).focus();
		}
	});

	// jQuery(".manual-date").on('keypress paste', function (e) {
	//     // Only allow numbers and slash
	//     var charCode = e.which ? e.which : e.keyCode;
	//     if (
	//         (charCode >= 48 && charCode <= 57) || // numbers 0-9
	//         charCode === 47 // slash /
	//     ) {
	//         return true;
	//     } else {
	//         e.preventDefault();
	//         return false;
	//     }
	// });

	// jQuery(".manual-date").on('blur', function () {
	//     var idata = jQuery(this).val().trim(); // user input
	//     var isValid = false;

	//     var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
	//     if (re.test(idata)) {
	//         var parts = idata.split('/');
	//         var dd = parseInt(parts[0], 10);
	//         var mm = parseInt(parts[1], 10);
	//         var yyyy = parseInt(parts[2], 10);

	//         var dt = new Date(yyyy, mm - 1, dd);
	//         if (dt.getFullYear() === yyyy && dt.getMonth() === mm - 1 && dt.getDate() === dd) {
	//             isValid = true;
	//         }
	//     }

	//     if (!isValid && idata != "") {
	//         jAlert("Please Enter A Valid Date!");
	//         jQuery(this).val('');
	//         jQuery(this).focus();
	//     }
	// });
	jQuery.validator.addMethod("greaterThan",
		function (value, element, params) {

			value = value != "" ? value.split("/") : [];
			let value2 = jQuery(params).val() != "" ? jQuery(params).val().split("/") : [];

			if (value.length && value2.length) {
				value = `${value[2]}-${value[1]}-${value[0]}`;
				value2 = `${value2[2]}-${value2[1]}-${value2[0]}`;

				return new Date(value) >= new Date(value2);
			}

			return true;

		}, 'Must Be Greater Than Equal To {0}.');

	jQuery.validator.addMethod("lessThan",
		function (value, element, params) {

			value = value != "" ? value.split("/") : [];
			let value2 = jQuery(params).val() != "" ? jQuery(params).val().split("/") : [];

			if (value.length && value2.length) {
				value = `${value[2]}-${value[1]}-${value[0]}`;
				value2 = `${value2[2]}-${value2[1]}-${value2[0]}`;

				return new Date(value) <= new Date(value2);
			}
			return true;

		}, 'Must Be Less Than Or Equal To {0}.');

	// .date-picker on change Validation
	jQuery(".date-picker").change(function (e) {
		var idata = e.target.value;
		var check = false;
		var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
		if (re.test(idata)) {
			var adata = idata.split('/');
			var dd = parseInt(adata[0], 10);
			var mm = parseInt(adata[1], 10);
			var yyyy = parseInt(adata[2], 10);
			var xdata = new Date(yyyy, mm - 1, dd);
			if ((xdata.getFullYear() === yyyy) && (xdata.getMonth() === mm - 1) && (xdata.getDate() === dd)) {
				check = true;
			} else {
				check = false;
			}
		} else {
			jQuery(e.target).val('');
			check = false;
		}
		if (check == false) {
			jAlert("Please Enter A Valid Date!");
		}
	});

	jQuery.validator.addMethod(
		"dateFormat",
		function (value, element) {
			var check = false;
			var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
			if (re.test(value)) {
				var adata = value.split('/');
				var dd = parseInt(adata[0], 10);
				var mm = parseInt(adata[1], 10);
				var yyyy = parseInt(adata[2], 10);
				var xdata = new Date(yyyy, mm - 1, dd);
				if ((xdata.getFullYear() === yyyy) && (xdata.getMonth() === mm - 1) && (xdata.getDate() === dd)) {
					check = true;
				} else {
					check = false;
				}
			} else {
				check = false;
			}
			return this.optional(element) || check;
		},

		"Please Enter A Valid Date!"
	);


	jQuery.validator.addMethod("numberFormat", function (value, element) {

		function testMobile(val) {

			var format = /[`!@#$%^&*()_,\=\[\]{};':"\\|.<>\/?~]/;

			if (format.test(value) == true) {

				return false;

			} else {

				return true;

			}

		}

		return this.optional(element) || testMobile(value);



	}, "only 0-9 and ('-','+') allowed");

	// Reinitialize date-picker for new elements
	jQuery('.date-picker').datepicker({
		dateFormat: "dd/mm/yy",
		autoclose: true,
	});

});
// new validation of duplicate item as per search in item
jQuery(document).on('change', '.item_id', function (e) {
	var selected = jQuery(this).val();
	var thisSelected = jQuery(this);

	var pageName = jQuery('#hidViewPage').val();

	if (selected && pageName != 'purchaseOrder' && pageName != 'purchaseRequisition' && pageName != 'salesReturn' && pageName != 'GRN' && pageName != 'itemReturn' && pageName != 'itemIssue' && pageName != 'CustomerReplacementEntry') {
		var duplicateFound = false;

		jQuery('.item_id').not(thisSelected).each(function () {
			if (thisSelected.val() == jQuery(this).val()) {
				duplicateFound = true;
				return false;
			}
		});

		if (duplicateFound) {
			jAlert('This Item Is Already Selected.');
			var selectTd = thisSelected.closest('td');
			selectTd.html(`<select name="item_id[]" class="chzn-select add_item item_id">${productDrpHtml}</select>`);
			jQuery(".item_id").chosen();
		}
	}
});





// // Add event listener to document to capture Enter key press
// document.addEventListener('keydown', function (event) {
// 	// Check if the pressed key is 'Enter'
// 	if (event.key === 'Enter') {
// 		const target = event.target;

// 		// Allow enter in a specific field (e.g., search bar with id='searchField')
// 		if (target.id === 'searchField') {
// 			return; // Allow default Enter behavior
// 		}

// 		// Prevent form submission on Enter for all other input fields
// 		if (target.type !== 'submit') {
// 			event.preventDefault();
// 		}
// 		// if (target.tagName === 'INPUT' && target.type !== 'submit' || target.tagName === 'SELECT') {
// 		// 	event.preventDefault();
// 		// }
// 	}
// });


document.addEventListener('keydown', function (event) {
	if (event.key === 'Enter') {
		const target = event.target;
		if (target.id === 'searchField') {
			return;
		}
		if (target.type !== 'submit') {
			event.preventDefault();
		}
	}
});





// // To Export All Data in Excel 
// function newexportaction(e, dt, button, config) {

// 	var self = this;
// 	var oldStart = dt.settings()[0]._iDisplayStart;
// 	dt.one('preXhr', function (e, s, data) {
// 		// Just this once, load all data from the server...
// 		data.start = 0;
// 		data.length = -1;
// 		dt.one('preDraw', function (e, settings) {
// 			// Call the original action function
// 			if (button[0].className.indexOf('buttons-copy') >= 0) {
// 				jQuery.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
// 			} else if (button[0].className.indexOf('buttons-excel') >= 0) {
// 				jQuery.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
// 					jQuery.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
// 					jQuery.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
// 			}
// 			dt.one('preXhr', function (e, s, data) {
// 				// DataTables thinks the first item displayed is index 0, but we're not drawing that.
// 				// Set the property to what it was before exporting.
// 				settings._iDisplayStart = oldStart;
// 				data.start = oldStart;
// 			});
// 			// Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
// 			setTimeout(dt.ajax.reload, 0);
// 			// Prevent rendering of the full data to the DOM
// 			return false;
// 		});
// 	});
// 	// Requery the server with the new one-time export settings
// 	dt.ajax.reload();
// }
// // End Export All Data In excel

// To Export All Data in Excel  
function newexportaction(e, dt, button, config) {

	var self = this;
	var oldStart = dt.settings()[0]._iDisplayStart;

	// START: Only Points-related Excel format logic added
	config.customize = config.customize || function(xlsx) {
		var sheet = xlsx.xl.worksheets['sheet1.xml'];
		var styles = xlsx.xl['styles.xml'];

		var customFormatCode = '0.000'; // format to 3 decimal places
		var numFmtId = 164;
		var numFmts = jQuery('numFmts', styles);

		if (numFmts.length === 0) {
			numFmts = jQuery('<numFmts count="0"/>');
			jQuery('cellStyleXfs', styles).before(numFmts);
		}

		var existingFormat = numFmts.find('numFmt[formatCode="' + customFormatCode + '"]');
		if (existingFormat.length === 0) {
			numFmts.attr('count', parseInt(numFmts.attr('count')) + 1);
			numFmts.append('<numFmt numFmtId="' + numFmtId + '" formatCode="' + customFormatCode + '"/>');
		} else {
			numFmtId = existingFormat.attr('numFmtId');
		}

		var cellXfs = jQuery('cellXfs', styles);
		var newXfIndex = cellXfs.children().length;

		cellXfs.attr('count', parseInt(cellXfs.attr('count')) + 1);
		cellXfs.append(
			'<xf numFmtId="' + numFmtId + '" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
		);

		jQuery('row:gt(0) c[t="n"]', sheet).attr('s', newXfIndex); // apply format to numeric cells
		jQuery('row:first c', sheet).attr('s', '2'); // apply style to header
	};
	// END: Points formatting logic only

	dt.one('preXhr', function (e, s, data) {
		// Just this once, load all data from the server...
		data.start = 0;
		data.length = -1;
		dt.one('preDraw', function (e, settings) {
			// Call the original action function
			if (button[0].className.indexOf('buttons-copy') >= 0) {
				jQuery.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
			} else if (button[0].className.indexOf('buttons-excel') >= 0) {
				jQuery.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
					jQuery.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
					jQuery.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
			}
			dt.one('preXhr', function (e, s, data) {
				// DataTables thinks the first item displayed is index 0, but we're not drawing that.
				// Set the property to what it was before exporting.
				settings._iDisplayStart = oldStart;
				data.start = oldStart;
			});
			// Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
			setTimeout(dt.ajax.reload, 0);
			// Prevent rendering of the full data to the DOM
			return false;
		});
	});
	// Requery the server with the new one-time export settings
	dt.ajax.reload();
}
// End Export All Data In Excel


// /**
//  * Enhanced DataTables Excel Export Function
//  * Features:
//  * - FIX: Excludes the first column (Action Column, Index 0) from export.
//  * - FIX: Excludes columns that are hidden (visible:false) from export universally.
//  * - FIX: Preserves trailing zeros in ALL numeric columns by forcing 0.000 format.
//  * - FIX: Maintains bold headers/labels.
//  * - Preserves original DataTables functionality.
//  */
// function newexportaction(e, dt, button, config) {
//     var self = this;
//     var oldStart = dt.settings()[0]._iDisplayStart;
    
//     // ====================================================================
//     // 🔍 લોજિક 1: Action Column અને Hidden Columns ને બાકાત રાખવા
//     // ====================================================================
    
//     // Step 0: Detect if table has 'expire-dealer-report' class
//     var isExpireDealerReport = dt.table().node().classList.contains('expire-dealer-report');

//     // Step 1: Construct exportOptions.columns if not already set
//     if (!config.exportOptions) {
//         config.exportOptions = {};
//     }

//     config.exportOptions.columns = function(idx, data, node) {
//         var isVisible = dt.column(idx).visible();

//         // IF table has class 'expire-dealer-report' THEN export all visible columns
//         // ELSE exclude first column (action) AND any hidden columns
//         if (isExpireDealerReport) {
//             return isVisible; // Export all visible columns
//         } else {
//             return idx !== 0 && isVisible; // Exclude Action column (0) and hidden columns
//         }
//     };
    
//     // ====================================================================
//     // 🔍 લોજિક 2: ટ્રેલિંગ શૂન્ય અને હેડર બોલ્ડ ફિક્સ (XML કસ્ટમાઇઝેશન)
//     // ====================================================================
//     config.customize = config.customize || function(xlsx) {
//         var sheet = xlsx.xl.worksheets['sheet1.xml'];
//         var styles = xlsx.xl['styles.xml'];
        
//         // Step 1: કસ્ટમ નંબર ફોર્મેટ (0.000) ઉમેરો. આ ટ્રેલિંગ ઝીરો જાળવવા માટે જરૂરી છે.
//         var customFormatCode = '0.000';
//         var numFmtId = 164;
//         var numFmts = jQuery('numFmts', styles);
        
//         if (numFmts.length === 0) {
//             numFmts = jQuery('<numFmts count="0"/>');
//             jQuery('cellStyleXfs', styles).before(numFmts);
//         }
        
//         // ફોર્મેટ અસ્તિત્વમાં ન હોય તો જ ઉમેરો. (સંક્ષિપ્ત કોડ માટે ચેક રાખેલ છે).
//         var existingFormat = numFmts.find('numFmt[formatCode="' + customFormatCode + '"]');
//         if (existingFormat.length === 0) {
//              numFmts.attr('count', parseInt(numFmts.attr('count')) + 1);
//              numFmts.append('<numFmt numFmtId="' + numFmtId + '" formatCode="' + customFormatCode + '"/>');
//         } else {
//              numFmtId = existingFormat.attr('numFmtId');
//         }
        
//         // Step 2: કસ્ટમ સેલ સ્ટાઇલ (xf) બનાવો (બોર્ડર વિના અને કસ્ટમ નંબર ફોર્મેટ સાથે).
//         var cellXfs = jQuery('cellXfs', styles);
//         var newXfIndex = cellXfs.children().length;
        
//         cellXfs.attr('count', parseInt(cellXfs.attr('count')) + 1);
//         cellXfs.append(
//             '<xf numFmtId="' + numFmtId + '" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
//         );
        
//         // Step 3: બધા ન્યુમેરિક ડેટા સેલ્સ પર સ્ટાઇલ લાગુ કરો.
//         // row:gt(0) (હેડર સિવાય) અને c[t="n"] (ન્યુમેરિક સેલ્સ).
//         jQuery('row:gt(0) c[t="n"]', sheet).attr('s', newXfIndex);
        
//         // Step 4: હેડર (Labels) ને બોલ્ડ જાળવવા માટે ડિફોલ્ટ બોલ્ડ સ્ટાઇલ (s='2') ફરીથી લાગુ કરો.
//         jQuery('row:first c', sheet).attr('s', '2');
//     };
    
//     // ====================================================================
//     // 💾 મૂળ DataTables Export લોજિક (પેજીનેશન બાયપાસ) - કોઈ ફેરફાર નથી
//     // ====================================================================
//     dt.one('preXhr', function (e, s, data) {
//         // Export માટે બધા ડેટા લોડ કરો (start = 0, length = -1).
//         data.start = 0;
//         data.length = -1;
        
//         dt.one('preDraw', function (e, settings) {
//             // મૂળ Export Action ચલાવો
//             if (button[0].className.indexOf('buttons-copy') >= 0) {
//                 jQuery.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
//             } else if (button[0].className.indexOf('buttons-excel') >= 0) {
//                 jQuery.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
//                     jQuery.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
//                     jQuery.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
//             }
            
//             // Export પછી મૂળ પેજીનેશન સ્થિતિ પુનઃસ્થાપિત કરો
//             dt.one('preXhr', function (e, s, data) {
//                 settings._iDisplayStart = oldStart;
//                 data.start = oldStart;
//             });
            
//             // Grid ને મૂળ સ્થિતિમાં રીલોડ કરો
//             setTimeout(dt.ajax.reload, 0);
            
//             // સંપૂર્ણ ડેટા DOM માં રેન્ડર થતો અટકાવો
//             return false;
//         });
//     });
    
//     // Export પ્રક્રિયા શરૂ કરો
//     dt.ajax.reload();
// }

function BlankTrVal($item) {
	jQuery($item).closest('tr').find('#code').val('');
	jQuery($item).closest('tr').find('#group').val('');
	jQuery($item).closest('tr').find('#stock_qty').val('');
	jQuery($item).closest('tr').find('#unit').val('');
}

function focusInput_second(input) {
	setTimeout(() => {
		jQuery(input).focus();
	}, 800);
}




function DataYearWise() {

	var startDateStr = jQuery('#def_year_startdate').val(); // e.g. "2026-04-01"
	var endDateStr = jQuery('#def_year_enddate').val();   // e.g. "2027-03-31"

	// Parse dates
	var startDate = moment(startDateStr, 'YYYY-MM-DD');
	var endDate = moment(endDateStr, 'YYYY-MM-DD');
	var currentDate = moment(); // Today

	// Format for display
	var formattedStartDate = startDate.format('DD/MM/YYYY');
	var formattedEndDate = endDate.format('DD/MM/YYYY');
	var formattedToday = currentDate.format('DD/MM/YYYY');

	// Set From Date always
	jQuery('.report-date-picker.from-april').datepicker('setDate', formattedStartDate);

	//Only set To Date as today if today is inside the financial year range
	if (currentDate.isBetween(startDate, endDate, 'day', '[]')) {
		jQuery('.report-date-picker:not(.from-april)').datepicker('setDate', formattedToday); // Today
	} else {
		jQuery('.report-date-picker:not(.from-april)').datepicker('setDate', formattedEndDate); // End of FY
	}
}

/*function DataYearWise(){

		var startDateStr = jQuery('#def_year_startdate').val(); // e.g. "2026-04-01"
		var endDateStr   = jQuery('#def_year_enddate').val();   // e.g. "2027-03-31"

		// Parse dates
		var startDate = moment(startDateStr, 'YYYY-MM-DD');
		var endDate   = moment(endDateStr, 'YYYY-MM-DD');
		var currentDate = moment(); // Today

		// Format for display
		var formattedStartDate = startDate.format('DD/MM/YYYY');
		var formattedEndDate   = endDate.format('DD/MM/YYYY');
		var formattedToday     = currentDate.format('DD/MM/YYYY');

		// Set From Date always
		jQuery('.dates-picker.from-april').datepicker('setDate', formattedStartDate);

		//Only set To Date as today if today is inside the financial year range
		if (currentDate.isBetween(startDate, endDate, 'day', '[]')) {
			jQuery('.date-picker').datepicker('setDate', formattedToday); // Today
		} else {
			jQuery('.date-picker').datepicker('setDate', formattedEndDate); // End of FY
		}
}*/

function returnCurrentDate() {
	const date = new Date();
	let currentDay = String(date.getDate()).padStart(2, '0');
	let currentMonth = String(date.getMonth() + 1).padStart(2, "0");
	let currentYear = date.getFullYear();
	var currentDate = `${currentDay}/${currentMonth}/${currentYear}`;
	return currentDate;
}


// common-datatable-search.js
/**
 * Initialize column search for a DataTable.
 * @param {string} tableSelector - jQuery selector for the table.
 * @param {Array} excludeColumns - Column indexes to exclude from search (optional).
 */

// Reusable column search function
function initColumnSearch(tableSelector, excludeColumns = []) {
	let $table = jQuery(tableSelector);
	if (!$table.length || !$table.DataTable().settings().length) return;

	let tableApi = $table.DataTable();

	// Target scrollable header thead
	let $header = $table.closest('.dataTables_scroll').find('.dataTables_scrollHeadInner table thead');

	// Remove any existing search-row to avoid duplicates
	$header.find('tr.search-row').remove();

	// Create search row
	let $searchRow = jQuery('<tr class="search-row"></tr>').appendTo($header);

	// Determine searchable columns automatically
	let totalColumns = $header.find('tr').first().find('th').length;
	// console.log(totalColumns);

	let searchableColumns = [];
	for (let i = 0; i < totalColumns; i++) {
		if (!excludeColumns.includes(i)) {
			searchableColumns.push(i);
		}
	}

	// Add input fields or empty ths
	$header.find('tr').first().find('th').each(function (i) {
		if (searchableColumns.includes(i)) {
			$searchRow.append(`<th><input type="text" style="width: 100%;" placeholder="${jQuery(this).text().trim()}" /></th>`);
		} else {
			$searchRow.append('<th></th>');
		}
	});

	// Add column search functionality
	tableApi.columns().every(function () {
		let column = this;
		let idx = this.index();

		if (searchableColumns.includes(idx)) {
			jQuery('input', $searchRow.find('th').eq(idx)).on('keyup change clear', function () {
				if (column.search() !== this.value) {
					column.search(this.value).draw();
				}
			});
		}
	});

	// Swap search-row before main-header for scrollable table
	let $scrollHead = jQuery(tableApi.table().container()).find('.dataTables_scrollHead thead');
	let $main = $scrollHead.find('tr.main-header');
	let $search = $scrollHead.find('tr.search-row');
	$search.insertBefore($main);

	// Also sync with original header
	let $origHead = jQuery(tableApi.table().header());
	let $mainOrig = $origHead.find('tr.main-header');
	let $searchOrig = $origHead.find('tr.search-row');
	$searchOrig.insertBefore($mainOrig);
}


// Function to add the reset filters button with styles and click logic
// function addResetButton(table) {
// 	var wrapper = jQuery('#dyntable_wrapper');
// 	var filterContainer = wrapper.find('.dataTables_filter');

// 	filterContainer.css({
// 		'display': 'flex',
// 		'align-items': 'center',
// 		'gap': '10px',
// 		'flex-wrap': 'nowrap'
// 	});

// 	if (!filterContainer.find('#reset-filters').length) {
// 		var resetBtn = jQuery('<button id="reset-filters" class="btn btn-default" type="button">Reset Filter</button>');

// 		resetBtn.css({
// 			'outline': 'none',
// 			'box-shadow': 'none',
// 			'border': '',
// 			'background': '',
// 			'cursor': 'pointer'
// 		});

// 		resetBtn.on('focus', function () {
// 			jQuery(this).css({
// 				'outline': 'none',
// 				'box-shadow': 'none'
// 			});
// 		});

// 		resetBtn.on('mouseup', function () {
// 			this.blur();
// 		});

// 		filterContainer.prepend(resetBtn);
// 	}

// 	jQuery('#reset-filters').off('click').on('click', function () {
// 		// Clear global search filter and redraw
// 		table.search('').draw();

// 		// Clear all column searches and inputs
// 		table.columns().every(function (index) {
// 			var input = jQuery(this.header()).closest('table').find('thead tr.search-row th').eq(index).find('input');
// 			if (input.length) {
// 				input.val('');
// 				this.search('');
// 			}
// 		});

// 		// Redraw table again to reset all filters
// 		table.draw();
// 	});
// }