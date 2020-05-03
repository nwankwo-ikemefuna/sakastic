function payWithPaystack(verify_url, data, success_callback, onclose = null) {
    var ref = ''+Math.floor((Math.random() * 1000000000) + 1);
    var handler = PaystackPop.setup({
        key: 'paystack_public_key_*****', // Replace with your public key
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
            $.ajax({
                url: base_url+verify_url,
                type: 'POST',
                dataType: "json",
                data: payload
            })
            .done(function (jres) {
                if (typeof success_callback === 'function') {
                    success_callback(jres);
                }
            });
        }
    });

    handler.openIframe();
}