<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8" />
    <script language="javascript" type="text/javascript"
            src="/javascript/jquery/jquery.js"></script>
    <style>
      path {
        fill: yellow;
        stroke: black;
      }
    </style>
  </head>
  <body>
  
    <script>
      annular = function(centerX, centerY, startAngle1, endAngle1, innerRadius, outerRadius) {
        startAngle = Math.PI*(startAngle1+180)/180;
        endAngle = Math.PI*(endAngle1+180)/180;
        angleDiff = endAngle - startAngle
        largeArc = angleDiff % (Math.PI * 2) > Math.PI ? 1 : 0;
        commands  = " M "+(centerX+innerRadius*Math.cos(startAngle))+","+(centerY+innerRadius*Math.sin(startAngle));
        commands += " L "+(centerX+outerRadius*Math.cos(startAngle))+","+(centerY+outerRadius*Math.sin(startAngle));
        commands += " A "+outerRadius+","+outerRadius+" 0 "+largeArc+",1 "+(centerX+outerRadius*Math.cos(endAngle))+","+(centerY+outerRadius*Math.sin(endAngle));
        commands += " L "+(centerX+innerRadius*Math.cos(endAngle))+","+(centerY+innerRadius*Math.sin(endAngle));
        commands += " A "+innerRadius+","+innerRadius+" 0 "+largeArc+",0 "+(centerX+innerRadius*Math.cos(startAngle))+","+(centerY+innerRadius*Math.sin(startAngle));
        commands += " z ";
        return svg('path').attr({"d": commands});
      }
      svg = function(name) {
        return $(document.createElementNS("http://www.w3.org/2000/svg", name));
      }
      link = function(href) {
        var a = svg("a");
        a.get(0).setAttributeNS("http://www.w3.org/1999/xlink", "href", href);
        return a;
      }
    </script>
  
    <svg id="canvas" viewBox="0 0 500 500" width="100%">
      <line x1="0" y1="0" x2="500" y2="0" stroke="black" />
      <line x1="0" y1="50" x2="500" y2="50" stroke="black" />
      <line x1="0" y1="100" x2="500" y2="100" stroke="black" />
      <line x1="0" y1="150" x2="500" y2="150" stroke="black" />
      <line x1="0" y1="200" x2="500" y2="200" stroke="black" />
      <line x1="0" y1="250" x2="500" y2="250" stroke="black" stroke-width="2" />
      <line x1="0" y1="300" x2="500" y2="300" stroke="black" />
      <line x1="0" y1="350" x2="500" y2="350" stroke="black" />
      <line x1="0" y1="400" x2="500" y2="400" stroke="black" />
      <line x1="0" y1="450" x2="500" y2="450" stroke="black" />
      <line x1="0" y1="500" x2="500" y2="500" stroke="black" />
      <line y1="0" x1="0" y2="500" x2="0" stroke="black" />
      <line y1="0" x1="50" y2="500" x2="50" stroke="black" />
      <line y1="0" x1="100" y2="500" x2="100" stroke="black" />
      <line y1="0" x1="150" y2="500" x2="150" stroke="black" />
      <line y1="0" x1="200" y2="500" x2="200" stroke="black" />
      <line y1="0" x1="250" y2="500" x2="250" stroke="black" stroke-width="2" />
      <line y1="0" x1="300" y2="500" x2="300" stroke="black" />
      <line y1="0" x1="350" y2="500" x2="350" stroke="black" />
      <line y1="0" x1="400" y2="500" x2="400" stroke="black" />
      <line y1="0" x1="450" y2="500" x2="450" stroke="black" />
      <line y1="0" x1="500" y2="500" x2="500" stroke="black" />
    </svg>

    <script type="text/javascript">
//       link("https://marc.waeckerlin.org").attr({title: "Marc Wäckerlin's Homepage"})
//         .append(svg("circle").attr({cx: "400", cy: "400", r: "100", fill: "blue"}))
//         .appendTo('#canvas');
//       svg('path').attr({
//         "d": annular(250, 250, 0, 350, 50, 200),
//       }).prependTo('#canvas');
      annular(250, 500, 0, 180, 0, 100).attr({title: "Budget"}).appendTo('#canvas');
      $.getJSON("finance.json", function(data) {
        var items = [];
        $.each(data, function(key, val) {
          items.push('<li id="' + key + '">' + val.name + '</li>');
        });
        $('<ul/>', {
          "class": 'my-new-list',
          "html": items.join('')
        }).appendTo('body');
      });
    </script>

  </body>
</html> 