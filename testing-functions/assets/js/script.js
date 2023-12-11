jQuery(document).ready(function($){

	document.querySelector('#doaction').addEventListener('click', function(e) {
		e.preventDefault();

		$("table > tbody > tr").each(function () {

			var $tr = $(this);

			// condition 1 = Min 1 checkbox is checked
			if ( $tr.find('input[name="post[]"]').is(":checked") ) {

				var selected_option_value = $('#bulk-action-selector-top').val();

				// Start - Checked checkbox return
		       	var selected = new Array();

		       	// Reference the CheckBoxes and insert the checked CheckBox value in Array.
	        	$("input[name='post[]']:checked").each(function () {
		        	selected.push(this.value);
		        });

		        // Display the selected CheckBox values.
		        if (selected.length > 0) {
		           var products_ids = selected.join(",");
		        }

		        // Data in [ products_ids ]
		        // End - Checked checkbox return

		        // condition 2 = selected option is [add_to_services]
				if( selected_option_value == "add_to_services" ){

					// swal start
					swal.fire({

						title: 'Are you sure?',
						text: "Want to add selected products in collection!",
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: 'Yes',
						cancelButtonText: 'No',

					}).then(( result ) => {

						// condition 3 = select collection [In which you want to add this selected products]
						if (result.isConfirmed) {
 
							$.ajax({
							    url:   BLOCKSY_AJAX_OBJ.ajax_url,
							    type: 'POST',
							    dataType: "json",
							    data: { 
							        action : 'Blocksy_Action_Get_CPT_List_in_ajax'
							    },
							    success: function(result ) {
									$.map(result, function (o) {
									    $('.swal2-select').append($('<option>').val(o.post_id).text(o.post_title));
									});
							    }
							}); // ajax

							swal.fire({

								title: 'In Which collection Post ?',
								text: 'Plz select collection post to add products !',
								icon: 'warning',
								input: 'select',
								inputPlaceholder: 'Select collection',
								showCancelButton: true,
								confirmButtonText: 'Yes',
								cancelButtonText: 'No',

					            inputValidator: (value) => {

					            	return new Promise(function (resolve, reject) {

										if (value !== '') {
											resolve();
										} else {
											resolve('Select a collection');
										}

					        		});
					    		}
					          
							}).then(function (result) {

								if ( result.isConfirmed ) {

									swal.fire({
										title: 'Done',
										text : 'Products added in collection successfully !',
										icon: 'success',
										timer: 5000
									}); // swal

	                                $.ajax({
	                                    type: 'POST',
	                                    url:   BLOCKSY_AJAX_OBJ.ajax_url,
	                                    data: { 
	                                        action : 'Blocksy_Action_Add_To_Product_in_ajax',
	                                        cpt_id: result.value,
	                                        products_ids: products_ids,
	                                    }
	                                }); // ajax

						        } else if (result.dismiss === "cancel") {

									swal.fire({
										title: 'Cancelled',
									  	text : 'Action is cancelled ! !',
									  	icon: 'error',
									  	timer: 5000
									}); // swal

						        }

							}) // swal

				        } else if (result.dismiss === "cancel") {

							swal.fire({
								title: 'Cancelled',
								text : 'Action is cancelled ! !',
								icon: 'error',
								timer: 5000
							}); // swal

				        } // condition 3rd
					}); //swal end
				} // condition 2nd
			} // condition 1st
		});
	});
});