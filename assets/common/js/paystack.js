function payWithPaystack(verify_url, data, success_callback, onclose = null) {
    var ref = ''+Math.floor((Math.random() * 1000000000) + 1);
    var handler = PaystackPop.setup({
        key: 'pk_test_c500f9d42c0d994e4a92578384cd10e11801f9d0',
        email: data.email,
        amount: data.amount * 100,
        firstname: data.first_name,
        lastname: data.last_name,
        currency: data.currency_key ?? 'NGN',
        ref: ref,
        channels: ['card', 'bank'],
        // label: "Optional string that replaces customer email",
        /*metadata: {
            "custom_fields": [
                {
                    "display_name": "Invoice ID",
                    "variable_name": "Invoice ID",
                    "value": 209
                },
            ]
        },*/
        onClose: function(){
            if (typeof onclose === "function") {
                onclose();
            }
        },
        callback: function(response) {
            var payload = {
                reference: response.reference,
                amount: data.amount,
                email: data.email,
                first_name: data.first_name,
                last_name: data.last_name,
                provider: 'PayStack'
            };
            post_data_ajax(base_url+verify_url, payload, false, success_callback);
        }
    });
    //open iframe
    handler.openIframe();
}