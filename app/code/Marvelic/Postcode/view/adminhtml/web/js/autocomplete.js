require([
    'jquery',
    'thailand',
    'uiComponent',
    'uiRegistry',
    'Marvelic_Postcode/js/Thailand_loader',
    'text!Marvelic_Postcode/js/database/db.json'
], function ($,
             Thailand,
             Component,
             uiRegistry,
             Thailand_loader){

	var countClickAddress	= 0;

    $(document).on("click","#tab_address",function() {
    	
	    if (configEnablePostcode === '1') {
	        Thailand_loader.done(function () {

	            // BEGIN 
	            // check until all field ready

	            var keepInterval = true;
	            var intervalAutocomplete = setInterval(function(){ 
	                if (!keepInterval) {
	                    clearInterval(intervalAutocomplete);
	                    return;
	                }

	                if ($('.address-item-edit').length) {

						$.each($('.address-item-edit'), function(index, child) {
							currentAttr	= $(child).attr('style');

							if (typeof(currentAttr) === 'undefined'
									&& $(child).find('div[data-index="district"]>.admin__field-control>input').length
									&& $(child).find('div[data-index="city"]>.admin__field-control>input').length
									&& $(child).find('div[data-index="region_id_input"]>.admin__field-control>input').length
									&& $(child).find('div[data-index="postcode"]>.admin__field-control>input').length
									&& $(child).find('div[data-index="country_id"]>.admin__field-control>select').length) {

								keepInterval = false;

								select 				= $(child).find('div[data-index="country_id"]>.admin__field-control>select');

								districtAddress = $(child).find('div[data-index="district"]>.admin__field-control>input').attr('id');
								cityAddress 	= $(child).find('div[data-index="city"]>.admin__field-control>input').attr('id');
								provinceAddress = $(child).find('div[data-index="region_id_input"]>.admin__field-control>input').attr('id');
								postcodeAddress = $(child).find('div[data-index="postcode"]>.admin__field-control>input').attr('id');
								

			                    dbUrl = require.toUrl('') + '/Marvelic_Postcode/js/database/db.json';

			                    $.Thailand.setup({
			                        database: dbUrl // path หรือ url ไปยัง database
			                    });

			                    if (select.val() === 'TH'){
			                        $.Thailand({
			                            $district: $('#' + districtAddress), // input ของตำบล
			                            $amphoe: $('#' + cityAddress), // input ของอำเภอ
			                            $province: $('#' + provinceAddress), // input ของจังหวัด
			                            $zipcode: $('#' + postcodeAddress), // input ของรหัสไปรษณีย์
			                            onLoad: function () {
			                                console.info('Autocomplete is ready!');
			                            }
			                        });
			                    }

				                select.on('change', function () {
				                    if($(this).val() !== '') {
				                        if ($(this).val() !== 'TH') {
				                            $('.tt-menu').remove();
				                        } else {
				                            $('.tt-menu').remove();
				                            $.Thailand({
				                                $district: $('#' + districtAddress), // input ของตำบล
				                                $amphoe: $('#' + cityAddress), // input ของอำเภอ
				                                $province: $('#' + provinceAddress), // input ของจังหวัด
				                                $zipcode: $('#' + postcodeAddress), // input ของรหัสไปรษณีย์
				                                onLoad: function () {
				                                    console.info('Autocomplete is ready!');
				                                }
				                             });
				                        }
				                    }
				                })


							}
						    
						}); 

	                } else{
	                    console.log('Not yet load field');  
	                }

	            }, 500);

	            //END

	        }).fail(function () {
	            console.error("ERROR: library failed to load");
	        });
	    }
    });

	$(document).on("click",".address-list-actions.last>button",function() {

		currentAddress 	= countClickAddress;		
		countClickAddress++;

	    if (configEnablePostcode === '1') {
	        Thailand_loader.done(function () {

	            // BEGIN 
	            // check until all field ready

	            var keepInterval = true;
	            var intervalAutocomplete = setInterval(function(){
	                if (!keepInterval) {
	                    clearInterval(intervalAutocomplete);
	                    return;
	                }

	                if (uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".country_id")
	                    && uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".city")
	                    && uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".region_id_input")
	                    && uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".postcode")) {

	                    select = $('#'+uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".country_id").uid);

                        keepInterval = false;
                        var dbUrl = require.toUrl('') + '/Marvelic_Postcode/js/database/db.json';

                        $.Thailand.setup({
                            database: dbUrl // path หรือ url ไปยัง database
                        });                        
                        if (typeof(uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".district")) !== 'undefined') {
                            var districtAddress = uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".district").uid;
                            var cityAddress 	= uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".city").uid;
                            var provinceAddress = uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".region_id_input").uid;
                            var postcodeAddress = uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".postcode").uid;
                            var addressSelect 	= uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".country_id").uid;
                        } else {
                            var districtAddress = '';
                            var cityAddress 	= uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".city").uid;
                            var provinceAddress = uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".region_id_input").uid;
                            var postcodeAddress = uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".postcode").uid;
                            var addressSelect 	= uiRegistry.get("customer_form.areas.address.address.address_collection.new_"+currentAddress+".country_id").uid;
                        }

                        if (select.val() === 'TH'){
                            $.Thailand({
                                $district: $('#' + districtAddress), // input ของตำบล
                                $amphoe: $('#' + cityAddress), // input ของอำเภอ
                                $province: $('#' + provinceAddress), // input ของจังหวัด
                                $zipcode: $('#' + postcodeAddress), // input ของรหัสไปรษณีย์
                                onLoad: function () {
                                    console.info('Autocomplete is ready!');
                                }
                            });
                        }

                        select.on('change', function () {
                            if($(this).val() !== '') {
                                if ($(this).val() !== 'TH') {
                                    $('.tt-menu').remove();
                                } else {
                                    $('.tt-menu').remove();
                                    $.Thailand({
                                        $district: $('#' + districtAddress), // input ของตำบล
                                        $amphoe: $('#' + cityAddress), // input ของอำเภอ
                                        $province: $('#' + provinceAddress), // input ของจังหวัด
                                        $zipcode: $('#' + postcodeAddress), // input ของรหัสไปรษณีย์
                                        onLoad: function () {
                                            console.info('Autocomplete is ready!');
                                        }
                                     });
                                }
                            }
                        })


	                } else{
	                    console.log('Not yet load field');  
	                }

	            }, 500);

	            //END

	        }).fail(function () {
	            console.error("ERROR: library failed to load");
	        });
	    }

	});

	$(document).on("click",".address-list-item",function() {
		if (configEnablePostcode === "1") {
			$.each($('.address-item-edit'), function(index, child) {
				currentAttr	= $(child).attr('style');
				if (currentAttr == "") {

					select 				= $(child).find('div[data-index="country_id"]>.admin__field-control>select');
					if($(child).find('div[data-index="district"]>.admin__field-control>.twitter-typeahead').length){
						districtAddress = $(child).find('div[data-index="district"]>.admin__field-control>.twitter-typeahead>.tt-input').attr('id');
						cityAddress 	= $(child).find('div[data-index="city"]>.admin__field-control>.twitter-typeahead>.tt-input').attr('id');
						provinceAddress = $(child).find('div[data-index="region_id_input"]>.admin__field-control>.twitter-typeahead>.tt-input').attr('id');
						postcodeAddress = $(child).find('div[data-index="postcode"]>.admin__field-control>.twitter-typeahead>.tt-input').attr('id');
					}else{
						districtAddress = $(child).find('div[data-index="district"]>.admin__field-control>input').attr('id');
						cityAddress 	= $(child).find('div[data-index="city"]>.admin__field-control>input').attr('id');
						provinceAddress = $(child).find('div[data-index="region_id_input"]>.admin__field-control>input').attr('id');
						postcodeAddress = $(child).find('div[data-index="postcode"]>.admin__field-control>input').attr('id');
					}


			        Thailand_loader.done(function () {

	                    dbUrl = require.toUrl('') + '/Marvelic_Postcode/js/database/db.json';

	                    $.Thailand.setup({
	                        database: dbUrl // path หรือ url ไปยัง database
	                    });

	                    if (select.val() === 'TH'){
	                        $.Thailand({
	                            $district: $('#' + districtAddress), // input ของตำบล
	                            $amphoe: $('#' + cityAddress), // input ของอำเภอ
	                            $province: $('#' + provinceAddress), // input ของจังหวัด
	                            $zipcode: $('#' + postcodeAddress), // input ของรหัสไปรษณีย์
	                            onLoad: function () {
	                                console.info('Autocomplete is ready!');
	                            }
	                        });
	                    }

		                select.on('change', function () {
		                    if($(this).val() !== '') {
		                        if ($(this).val() !== 'TH') {
		                            $('.tt-menu').remove();
		                        } else {
		                            $('.tt-menu').remove();
		                            $.Thailand({
		                                $district: $('#' + districtAddress), // input ของตำบล
		                                $amphoe: $('#' + cityAddress), // input ของอำเภอ
		                                $province: $('#' + provinceAddress), // input ของจังหวัด
		                                $zipcode: $('#' + postcodeAddress), // input ของรหัสไปรษณีย์
		                                onLoad: function () {
		                                    console.info('Autocomplete is ready!');
		                                }
		                             });
		                        }
		                    }
		                })

			        }).fail(function () {
			            console.error("ERROR: library failed to load");
			        });

				}
			    
			}); 
		}


	});

    return Component;

});