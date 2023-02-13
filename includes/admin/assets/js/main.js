jQuery(function(){
    jQuery('#btn-send-api-nasdaq').click(function(e){
        e.preventDefault();
        jQuery.ajax({
            type: 'get',
            url: rc_api_nasdaq_obj.ajax_url,
            async: true,
            data: {
                action: 'rc_api_nasdaq_data',
                api_key: jQuery('#api_key').val(),
                database_code: jQuery('#database_code').val(),
                dataset_code: jQuery('#dataset_code').val(),
                start_date: jQuery('#start_date').val(),
                end_date: jQuery('#end_date').val()
            },
            beforeSend: function(){
                jQuery('#btn-send-api-nasdaq').attr('disabled', true);
                jQuery('#btn-send-api-nasdaq').html('Loading...');
                jQuery('#resultAPI').html(`
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `);
            },
            success: function(data){

                if(data.data.quandl_error) {
                    jQuery('#resultAPI').html(`
                        <div class="alert alert-danger" role="alert">
                            ${data.data.quandl_error.message}
                        </div>
                    `);
                    jQuery('#btn-send-api-nasdaq').attr('disabled', false);
                    jQuery('#btn-send-api-nasdaq').html('Enviar');
                }

                let res = data.data.dataset;

                let openValue = 0.00;
                let closeValue = 0.00;
                let columnNameDate = '';
                let dayAndYear = '';
                let percentageResult = 0.00;
                let percentageResultClass = '';

                if(res.data.length > 0) {
                    openValue = (res.data[0][1]) ? res.data[0][1].toFixed(2) : 0.00;
                    closeValue = (res.data[0][4]) ? res.data[0][4].toFixed(2) : 0.00;
                    columnNameDate = res.data[0][0];
                }

                let currentDate = new Date();

                let onlyMonth = currentDate.toLocaleString("en-US", { month: "short" }).toLocaleUpperCase();

                function addZero(i) {
                    if (i < 10) {i = "0" + i}
                    return i;
                }
                let time = addZero(currentDate.getHours()) + ':' + addZero(currentDate.getMinutes()) + ':' + addZero(currentDate.getSeconds());
                let timeCompareInitial = '09:00:00';
                let timeCompareFinal = '16:00:00';

                let msgMarket = (time >= timeCompareInitial && time <= timeCompareFinal) ? `CLOSED AT 4:00 PM ET ON ${onlyMonth}` : `MARKET OPEN` ;

                if(columnNameDate !== '') {
                    let dateTransform = new Date(columnNameDate);
                    dayAndYear = dateTransform.getDate() + ', ' + dateTransform.getFullYear();
                    percentageResult = (((closeValue - openValue)/openValue) * 100).toFixed(2);                
                }
                
                if(percentageResult !== '') {
                    if(percentageResult == 0) {
                        percentageResultClass = 'text-light';
                    } else if(percentageResult > 0) {
                        percentageResultClass = 'text-success';
                    } else {
                        percentageResultClass = 'text-danger';
                    }
                }

                jQuery('#resultAPI').html(`
                    <div class="card-rc-api-nasdaq mt-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="box-title">
                                    <h4 class="card-title">${res.name}</h4>
                                    <h4 class="symbol">(${res.dataset_code})</h4>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6 col-one">
                                <div class="c1">
                                    <span class="sub-title">
                                        <strong>Nasdaq</strong> <span>Listed</span>
                                    </span>
                                    <span class="sub-title">
                                        <strong>Nasdaq</strong> <span>100</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6 col-two">
                                <p class="lastSalePrice">$${openValue} <span class="${percentageResultClass}">(${percentageResult}%)</span></p>
                                <p class="marketStatus">${msgMarket}</p>
                                <p class="lastTradeTimestamp">${dayAndYear}</p>
                            </div>
                        </div>
                    </div>
                `);
                jQuery('#btn-send-api-nasdaq').attr('disabled', false);
                jQuery('#btn-send-api-nasdaq').html('Enviar');
            },
            error: function(error){
                console.log(error)
            }
        });
    });
});
