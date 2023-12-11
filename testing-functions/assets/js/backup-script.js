jQuery(document).ready(function($){

	document.querySelector('#doaction').addEventListener('click', function(e) {

		e.preventDefault();

		$("table > tbody > tr").each(function () {

			var $tr = $(this);

			if ( $tr.find('input[name="post[]"]').is(":checked") ) {

				var selected_option_value = $('#bulk-action-selector-top').val();

					       var selected = new Array();

					       // Reference the CheckBoxes and insert the checked CheckBox value in Array.
					        $("input[name='post[]']:checked").each(function () {
					            selected.push(this.value);
					        });

					       // Display the selected CheckBox values.
					        if (selected.length > 0) {
					           var checked_ids = selected.join(",");
					        }


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

						if (result.isConfirmed) {

							swal.fire({

								title: 'In Which collection Post ?',
								text: 'Plz select collection post to add products !',
								icon: 'warning',
								input: 'select',
								inputOptions: {
									14: 'Graphic Designer',
								    12: 'Mobile App Development',
								    10: 'Web Development Company'
								},
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

								if (result.isConfirmed) {

									  // trying to get text value
									  // alert(result.value);
									  // alert(result.text);
									  // alert(result);

									  swal.fire({
									  	title: 'Done',
									  	text : 'Products added in collection successfully !',
									  	icon: 'success',
									  });


		                                $.ajax({
		                                    type: 'POST',
		                                    url:   BLOCKSY_AJAX_OBJ.ajax_url,
		                                    data: { 
		                                        action : 'Blocksy_Action_Add_To_Product_in_ajax',
		                                        collection: result.value,
		                                        checked_ids: checked_ids,
		                                    },
		                                    success: function( data ) {

		                                      	console.log( data );
		                                   
		                                    }
		                                });

						        } else if (result.dismiss === "cancel") {

									  swal.fire({
									  	title: 'Cancelled',
									  	text : 'Action is cancelled ! !',
									  	icon: 'error',
									  });

						        }

							})

				        } else if (result.dismiss === "cancel") {

								swal.fire({
									title: 'Cancelled',
									text : 'Action is cancelled ! !',
									icon: 'error',
								});

				        }

					});

					//swal end

				}
			}
		});
	});
});