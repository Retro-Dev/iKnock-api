/*pie chart*/
function highchart(container,title , value,total_commission,svg)
{
    var object = [];
    var cal_sign = $('[name="type"]').val() == 'amount' ? '' : '%';
    for(var i = 0;i<title.length;i++){
        var commission = {};
        commission.name = title[i];
        commission.y = value[i];
        commission.color = svg[i];
        object.push(commission);
        
    }

    Highcharts.chart(container, {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        legend: {
            align: 'right',
            verticalAlign: 'middle',
            layout: 'vertical',
            itemMarginBottom: 40,
            symbolHeight:14,
            symbolWidth:14,

        },
        title: {
            text: 'Performance Report'
        },
        subtitle: {
            text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
        },
        plotOptions: {
            pie: {


                showInLegend: true
            },

            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y:.1f}'+cal_sign

                }
            }

        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}'+cal_sign+'</b> of total<br/>'
        },

        "series": [
            {
                "name": "Commission Report",
                "colorByPoint": true,
                "data": object
            }
        ]

    });

}

function piechart(container,title,value,colour)
{

    var object = [];
    var cal_sign = $('[name="type"]').val() == 'amount' ? '' : '%';
    for(var i = 0;i<title.length;i++){
        var lead = {};
        lead.name = title[i];
        lead.y = value[i] ;
        lead.color = colour[i];
        object.push(lead);
    }

    Highcharts.chart(container, {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        legend: {
            align: 'right',
            verticalAlign: 'middle',
            layout: 'vertical',
            itemMarginBottom: 40,
            symbolHeight:14,
            symbolWidth:14,

        },
        title: {
            text: 'Performance Report'
        },
        subtitle: {
            text: 'Click the slices to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
        },
        plotOptions: {
            pie: {


                showInLegend: true
            },

            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y:.1f}'+cal_sign

                }
            }


        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}'+cal_sign+'</b> of total<br/>'
        },

        "series": [
            {
                "name": "Report",
                "colorByPoint": true,
                "data": object
            }
        ]

    });

}
/*end pie chart*/


function barchart(container, label, color, long_label, value){

    var object = [];
    
    for(var i =0; i < long_label.length; i++)
    {
        var abc   = {};
        abc.name  = label[i];
        abc.y = value[i];
        abc.drilldown = long_label[i]
        abc.color = color[i];
        abc.pointWidth =  15;

        object.push(abc);
    }

    // Highcharts.chart(container, {

    //     chart: {
    //         type: 'column',


    //     },

    //     title: {
    //         text: 'Stacked column chart'
    //     },

    //     xAxis: {
    //         categories: label
    //     },
    //     yAxis: {
    //         min: 0,max:600,
    //         title: {
    //             text: ''

    //         }
    //     },
    //     tooltip: {
    //         pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
    //         shared: true
    //     },
    //     plotOptions: {
    //         column: {
    //             stacking: 'normal'

    //         }



    //     },
    //     series: object

    // });


    // Create the chart
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Browser market shares. January, 2018'
    },
    subtitle: {
        text: 'Click the columns to view versions. Source: <a href="http://statcounter.com" target="_blank">statcounter.com</a>'
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: ''
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f}%'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}'+cal_sign+'</b> of total<br/>'
    },

    "series": [
        {
            "name": "Team Report",
            "colorByPoint": true,
            "data": object
        }
    ]
});

}
