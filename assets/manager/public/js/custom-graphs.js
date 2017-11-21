// Flot Charts
var updateInterval = 60;

$("#updateInterval").val(updateInterval).change(function () {
  var v = $(this).val();
  if (v && !isNaN(+v)) {
    updateInterval = +v;
    if (updateInterval < 1)
      updateInterval = 1;
    if (updateInterval > 2000)
      updateInterval = 2000;
    $(this).val("" + updateInterval);
  }
});

var data = [], totalPoints = 200;
function getRandomData() {
  if (data.length > 0)
    data = data.slice(1);

  // do a random walk
  while (data.length < totalPoints) {
    var prev = data.length > 0 ? data[data.length - 1] : 90;
    var y = prev + Math.random() * 10 - 5;
    if (y < 0)
      y = 0;
    if (y > 100)
      y = 100;
    data.push(y);
  }

  // zip the generated y values with the x values
  var res = [];
  for (var i = 0; i < data.length; ++i)
    res.push([i, data[i]])
  return res;
}

if($("#serverLoad").length){
  var options = {
    series: { shadowSize: 1 },
    lines: { show: true, lineWidth: 3, fill: true, fillColor: { colors: [ { opacity: 0.5 }, { opacity: 0.5 } ] }},
    yaxis: { min: 0, max: 200, tickFormatter: function (v) { return v + "%"; }},
    xaxis: { show: false },
    colors: ["#3660aa"],
    grid: { tickColor: "#f2f2f2",
      borderWidth: 0, 
    },
  };
  var plot = $.plot($("#serverLoad"), [ getRandomData() ], options);
  function update() {
    plot.setData([ getRandomData() ]);
    // since the axes don't change, we don't need to call plot.setupGrid()
    plot.draw();
    setTimeout(update, updateInterval);
  }
  update();
}


//Google Visualization 
google.load("visualization", "1", {
  packages: ["corechart"]
});

$(document).ready(function () {
//   drawChart1();
//   drawChart2();
//   drawChart3();
//   drawChart4();
//   drawRegionsMap();
//   drawTable();
//   candlestick();
//   bubbleChart();
});

function drawChart1() {
  var data = google.visualization.arrayToDataTable([
    ['Year', 'Google+', 'Facebook'],
    ['2005', 90, 30],
    ['2006', 180, 260],
    ['2007', 1050, 320],
    ['2008', 1390, 650],
    ['2009', 2120, 970],
    ['2010', 3970, 1560],
    ['2011', 2650, 2390],
    ['2012', 1390, 2940]
    ]);

  var options = {
    width: 'auto',
    pointSize: 7,
    lineWidth: 1,
    height: '200',
    backgroundColor: 'transparent',
    colors: ['#3eb157', '#3660aa', '#d14836', '#dba26b', '#666666', '#f26645'],
    tooltip: {
      textStyle: {
        color: '#666666',
        fontSize: 11
      },
      showColorCode: true
    },
    legend: {
      textStyle: {
        color: 'black',
        fontSize: 12
      }
    },
    chartArea: {
      left: 40,
      top: 10,
      height: "80%"
    }
  };

  var chart = new google.visualization.AreaChart(document.getElementById('area_chart'));
  chart.draw(data, options);
}




function drawChart3() {
  var data = google.visualization.arrayToDataTable([
    ['Year', 'Visits', 'Orders', 'Income', 'Expenses'],
    ['2007', 300, 800, 900, 300],
    ['2008', 1170, 860, 1220, 564],
    ['2009', 260, 1120, 2870, 2340],
    ['2010', 1030, 540, 3430, 1200],
    ['2011', 200, 700, 1700, 770],
    ['2012', 1170, 2160, 3920, 800],
    ['2013', 2170, 1160, 2820, 500] ]);

  var options = {
    width: 'auto',
    height: '160',
    backgroundColor: 'transparent',
    colors: ['#3eb157', '#3660aa', '#d14836', '#dba26b', '#666666', '#f26645'],
    tooltip: {
      textStyle: {
        color: '#666666',
        fontSize: 11
      },
      showColorCode: true
    },
    legend: {
      textStyle: {
        color: 'black',
        fontSize: 12
      }
    },
    chartArea: {
      left: 60,
      top: 10,
      height: '80%'
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('column_chart'));
  chart.draw(data, options);
}

// function drawChart4() {
//   var data = google.visualization.arrayToDataTable([
//     ['Task', 'Hours per Day'],
//     ['Eat', 2],
//     ['Work', 9],
//     ['Commute', 2],
//     ['Read', 2],
//     ['Sleep', 7],
//     ['Play', 2],
//     ]);
// 
//   var options = {
//     width: 'auto',
//     height: '265',
//     backgroundColor: 'transparent',
//     colors: ['#3eb157', '#3660aa', '#d14836', '#dba26b', '#666666', '#f26645'],
//     tooltip: {
//       textStyle: {
//         color: '#666666',
//         fontSize: 11
//       },
//       showColorCode: true
//     },
//     legend: {
//       position: 'left',
//       textStyle: {
//         color: 'black',
//         fontSize: 12
//       }
//     },
//     chartArea: {
//       left: 0,
//       top: 10,
//       width: "300%",
//       height: "100%"
//     }
//   };
// 
//   var chart = new google.visualization.PieChart(document.getElementById('pie_chart'));
//   chart.draw(data, options);
// }

//Geo Charts
google.load('visualization', '1', {'packages': ['geochart']});
google.setOnLoadCallback(drawRegionsMap);

function drawRegionsMap() {
  var data = google.visualization.arrayToDataTable([
    ['Country', 'Popularity'],
    ['Germany', 200],
    ['IN', 900],
    ['United States', 300],
    ['Brazil', 400],
    ['Canada', 500],
    ['France', 600],
    ['RU', 700]
    ]);

  var options = {
    width: 'auto',
    height: '280',
    backgroundColor: 'transparent',
    colors: ['#3eb157', '#3660aa', '#d14836', '#dba26b', '#666666', '#f26645'],
  };

  var chart = new google.visualization.GeoChart(document.getElementById('geo_chart'));
  chart.draw(data, options);
};



//Resize charts and graphs on window resize
$(document).ready(function () {
  $(window).resize(function(){
//     drawChart1();
//     drawChart2();
//     drawChart3();
//     drawChart4();
//     drawTable();
//     bubbleChart();
//     drawRegionsMap();
//     candlestick()
  });
});



//NVD3 Charts

//lineWithFocusChart
nv.addGraph(function() {
  var chart = nv.models.lineWithFocusChart();

  chart.xAxis
      .tickFormat(d3.format(',f'));
  chart.x2Axis
      .tickFormat(d3.format(',f'));

  chart.yAxis
      .tickFormat(d3.format(',.2f'));
  chart.y2Axis
      .tickFormat(d3.format(',.2f'));

  d3.select('#lineWithChart svg')
      .datum(testData())
    .transition().duration(500)
      .call(chart);

  nv.utils.windowResize(chart.update);

  return chart;
});



function testData() {
  return stream_layers(3, 128, .1).map(function(data, i) {
    return { 
      key: 'Data - '+ i,
      values: data
    };
  });
}