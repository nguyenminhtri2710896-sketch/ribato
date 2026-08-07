var report = {
    daskboard: function () {
        inDay = this.getDate();
        console.log(inDay);
        this.getTotalTransactionAmount(".total-amount-in-day", { "query": { "created_at_from": inDay, "created_at_to": inDay }, "query_in_list": { "status_id": [2, 6] } });
        this.getTotalTransactionAmount(".total-amount-pending", { "query": { "status_id": 6 } });
        this.getTotalTransactionAmount(".total-amount", { "query_in_list": { "status_id": [2, 6] } });
        if ($(".total-user-balance").length > 0) {
            this.getSystemBalance();
        }
    },
    index: function () {
        // this.revenuesByDay();
        this.profitChart();
        this.revenuesByMonth();
    },
    user: function () {
        this.getChartTop10User();
    },
    profitChart: function () {
        var chartElement = document.querySelector('#profit-chart');
        if (!chartElement) {
            return;
        }

        const positiveColor = '#34c38f';
        const negativeColor = '#f46a6a';
        const chart = new ApexCharts(chartElement, {
            chart: { type: 'bar', height: 360, toolbar: { show: true } },
            plotOptions: { bar: { horizontal: false, columnWidth: '45%', dataLabels: { position: 'top' } } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            colors: ['#556ee6', function ({ value, seriesIndex }) {
                if (seriesIndex === 1) {
                    return value < 0 ? negativeColor : positiveColor;
                }
                return '#556ee6';
            }],
            series: [
                { name: 'Tổng giao dịch (Collect)', data: [] },
                { name: 'Lợi nhuận (Collect + Payout)', data: [] },
            ],
            xaxis: { categories: [] },
            yaxis: [
                { title: { text: 'VNĐ' }, labels: { formatter: function (val) { return $.number(val); } } },
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val) {
                        return $.number(val) + ' ₫';
                    }
                }
            },
            legend: { position: 'top' },
            fill: { opacity: 0.9 }
        });
        chart.render();

        var updateChart = function (filters) {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: urlAjaxReportProfitChart,
                data: filters || {},
                success: function (result) {
                    if (result.error_code != 0) {
                        toastr["error"](result.message);
                        return false;
                    }
                    chart.updateOptions({ xaxis: { categories: result.data.categories } });
                    chart.updateSeries([
                        { name: 'Tổng giao dịch (Collect)', data: result.data.series.collection },
                        { name: 'Lợi nhuận (Collect + Payout)', data: result.data.series.profit }
                    ]);

                    var overallProfit = result.data.overall_total_profit || 0;
                    var filteredProfit = result.data.total_profit || 0;
                    var $overallEl = $('.profit-chart-total');
                    var $filteredEl = $('.profit-chart');
                    $overallEl
                        .html($.number(overallProfit) + "<sup>đ</sup>")
                        .toggleClass('text-danger', overallProfit < 0)
                        .toggleClass('text-success', overallProfit >= 0);
                    $filteredEl
                        .html($.number(filteredProfit) + "<sup>đ</sup>")
                        .toggleClass('text-danger', filteredProfit < 0)
                        .toggleClass('text-success', filteredProfit >= 0);

                    if (result.data.filters) {
                        $('.profit-chart-range').text((result.data.filters.from_date || '') + ' - ' + (result.data.filters.to_date || ''));
                    }

                    if (result.data.filters) {
                        var form = $('#profit-chart-filter');
                        form.find('[name="from_date"]').val(result.data.filters.from_date || '');
                        form.find('[name="to_date"]').val(result.data.filters.to_date || '');
                        var userSelect = form.find('[name="user_id[]"]');
                        if (userSelect.length) {
                            if (result.data.filters.user_id) {
                                var userId = result.data.filters.user_id;
                                var userLabel = result.data.filters.user_label || ('User #' + userId);
                                if (userSelect.find("option[value='" + userId + "']").length === 0) {
                                    var option = new Option(userLabel, userId, true, true);
                                    userSelect.append(option);
                                }
                                userSelect.val(userId).trigger('change');
                            } else {
                                userSelect.val(null).trigger('change');
                            }
                        }
                    }
                }
            });
        };

        updateChart({});

        $('#profit-chart-filter').on('submit', function (event) {
            event.preventDefault();
            var params = {
                from_date: $(this).find('[name="from_date"]').val(),
                to_date: $(this).find('[name="to_date"]').val(),
                user_id: $(this).find('[name="user_id[]"]').val()
            };
            updateChart(params);
        });
    },
    revenuesByDay: function () {
        const chart = new ApexCharts(document.querySelector('#revenues-by-day'), {
            chart: { type: 'bar', height: 320, toolbar: { show: true } },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', dataLabels: { position: 'top' } } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            series: [
                { name: 'Tổng giao dịch', data: [] },   // total_in
                { name: 'Tổng chờ duyệt', data: [] },   // total_in
            ],
            xaxis: { categories: [] },
            yaxis: [
                { title: { text: 'VNĐ' } },
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val, opts) {
                        const sIndex = opts.seriesIndex;
                        // 0: Tổng tiền, 1: Tổng chi, 2: Giao dịch
                        if (sIndex === 2) return val.toLocaleString('vi-VN') + ' giao dịch';
                        return val.toLocaleString('vi-VN') + ' ₫';
                    }
                }
            },
            legend: { position: 'top' },
            fill: { opacity: 1 }
        });
        chart.render();

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: urlAjaxReportRevenueByDay,
            data: {},
            beforeSend: function () {

            },
            success: function (result) {
                if (result.error_code != 0) {
                    toastr["error"](result.message)
                    return false
                }
                chart.updateOptions({ xaxis: { categories: result.data.reports.day } });
                chart.updateSeries([
                    { name: 'Tổng giao dịch', data: result.data.reports.amount_collection },
                    { name: 'Tổng chờ duyệt', data: result.data.reports.amount_collection_pending }]);

            }, complete: function () {
            }
        });

    },
    revenuesByMonth: function () {
        const chart = new ApexCharts(document.querySelector('#revenues-by-month'), {
            chart: { type: 'bar', height: 320, toolbar: { show: true } },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', dataLabels: { position: 'top' } } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            series: [
                { name: 'Tổng giao dịch', data: [] },   // total_in
                { name: 'Tổng chờ duyệt', data: [] },   // total_in
            ],
            xaxis: { categories: [] },
            yaxis: [
                { title: { text: 'VNĐ' } },
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val, opts) {
                        const sIndex = opts.seriesIndex;
                        // 0: Tổng tiền, 1: Tổng chi, 2: Giao dịch
                        if (sIndex === 2) return val.toLocaleString('vi-VN') + ' giao dịch';
                        return val.toLocaleString('vi-VN') + ' ₫';
                    }
                }
            },
            legend: { position: 'top' },
            fill: { opacity: 1 }
        });
        chart.render();

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: urlAjaxReportRevenueByMonth,
            data: {},
            beforeSend: function () {

            },
            success: function (result) {
                if (result.error_code != 0) {
                    toastr["error"](result.message)
                    return false
                }
                chart.updateOptions({ xaxis: { categories: result.data.reports.month } });
                chart.updateSeries([
                    { name: 'Tổng giao dịch', data: result.data.reports.amount_collection },
                    { name: 'Tổng chờ duyệt', data: result.data.reports.amount_collection_pending }
                ]);

            }, complete: function () {
            }
        });

    },
    getChartTop10User: function () {
        const chart = new ApexCharts(document.querySelector('#top-10-user'), {
            chart: { type: 'bar', height: 420, toolbar: { show: true } },
            plotOptions: { bar: { horizontal: false, columnWidth: '55%', dataLabels: { position: 'top' } } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            series: [
                { name: 'Tổng số dư', data: [] },   // total_in
                { name: 'Tổng Rút trong ngày', data: [] },   // total_out
                { name: 'Tổng giao dịch trong ngày', data: [] },    // txn_count
                { name: 'Tổng chờ duyệt', data: [] }    // txn_count
            ],
            xaxis: { categories: [] },
            yaxis: [
                { title: { text: 'VNĐ' } },
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val, opts) {
                        const sIndex = opts.seriesIndex;
                        // 0: Tổng tiền, 1: Tổng chi, 2: Giao dịch
                        if (sIndex === 2) return val.toLocaleString('vi-VN') + ' giao dịch';
                        return val.toLocaleString('vi-VN') + ' ₫';
                    }
                }
            },
            legend: { position: 'top' },
            fill: { opacity: 1 }
        });
        chart.render();

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: urlAjaxReportTop10User,
            data: {},
            beforeSend: function () {

            },
            success: function (result) {
                if (result.error_code != 0) {
                    toastr["error"](result.message)
                    return false
                }
                // result.data.reports.map(r => console.log(r));

                // console.log(result.data.reports);
                // var rows = result.data.reports;
                // const cats = rows.map(r => r.email);
                // const total = rows.map(r => Number(r.balance) || 0);
                // const totalWidthdraw = rows.map(r => Number(r.amount_withdraw) || 0);
                // const totalCollection = rows.map(r => Number(r.amount_collection) || 0);
                // const totalCollectionPending = rows.map(r => Number(r.amount_collection_pending) || 0);

                chart.updateOptions({ xaxis: { categories: result.data.reports.email } });
                chart.updateSeries([
                    { name: 'Tổng số dư', data: result.data.reports.balance },
                    { name: 'Tổng rút trong ngày', data: result.data.reports.amount_withdraw },
                    { name: 'Tổng giao dịch trong ngày', data: result.data.reports.amount_collection },
                    { name: 'Tổng chờ duyệt', data: result.data.reports.amount_collection_pending }
                ]);

            }, complete: function () {
            }
        });

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
    },
    getSystemBalance: function () {
        if ($(".total-user-balance").length == 0) {
            return;
        }
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: urlAjaxReportGetSystemBalance,
            data: {},
            success: function (result) {
                $(".total-user-balance").html($.number(result.data.total_user_balance) + "đ");
                $(".total-gateway-balance").html($.number(result.data.total_gateway_balance) + "đ");
                $(".total-gateway-pending-balance").html($.number(result.data.total_gateway_pending_balance) + "đ");
            }, complete: function (result) {
                setTimeout(() => {
                    report.getSystemBalance();
                }, 5000);
            }
        });
    },
    getDate() {
        var d = new Date();
        var month = d.getMonth() + 1;
        var day = d.getDate();

        return (day < 10 ? '0' : '') + day + '/' +
            (month < 10 ? '0' : '') + month + '/' +
            d.getFullYear();
    }
};