var report = {
    daskboard: function () {
        inDay = this.getDate();
        console.log(inDay);
        this.getTotalTransactionAmount(".total-amount-in-day", { "query": { "created_at_from": inDay, "created_at_to": inDay }, "query_in_list": { "status_id": [2, 6] } });
        this.getTotalTransactionAmount(".total-amount-pending", { "query": { "status_id": 6 } });
        this.getTotalTransactionAmount(".total-amount", { "query_in_list": { "status_id": [2, 6] } });
    },
    getTotalTransactionAmount: function (tag, params) {
        $.ajax({
            type: "POST",
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
            url: urlAjaxReportGetTotalTransactionAmount,
            data: JSON.stringify(params),
            beforeSend: function () {
            },
            success: function (result) {
                $(tag).html($.number(result.data.total_amount) + "đ");
            }, complete: function (result) {
                setTimeout(() => {
                    report.getTotalTransactionAmount(tag, params);
                }, 5000);
            }
        });
    }, getDate() {
        var d = new Date();
        var month = d.getMonth() + 1;
        var day = d.getDate();

        return (day < 10 ? '0' : '') + day + '/' +
            (month < 10 ? '0' : '') + month + '/' +
            d.getFullYear();
    }
};