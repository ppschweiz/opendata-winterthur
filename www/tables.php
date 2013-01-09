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
      arc = function(centerX, centerY, startAngle1, endAngle1, radius) {
        startAngle = Math.PI*(startAngle1+180)/180;
        endAngle = Math.PI*(endAngle1+180)/180;
        angleDiff = endAngle - startAngle
        largeArc = angleDiff % (Math.PI * 2) > Math.PI ? 1 : 0;
        commands  = " M "+(centerX+radius*Math.cos(startAngle))+","+(centerY+radius*Math.sin(startAngle));
        commands += " A "+radius+","+radius+" 0 "+largeArc+",1 "+(centerX+radius*Math.cos(endAngle))+","+(centerY+radius*Math.sin(endAngle));
        return svg('path').attr({"d": commands});
      }
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
      href = function(v, url) {
        v.get(0).setAttributeNS("http://www.w3.org/1999/xlink", "xlink:href", url);
        return v;
      }
      link = function(url) {
        return href(svg("a"), url);
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
      link("http://marc.waeckerlin.org").append(annular(250, 500, 0, 180, 0, 100).attr({title: "Budget", id: "budget"})).appendTo('#canvas');
      svg('defs').append(arc(250, 500, 0, 180, 100).attr('id', 'bow')).appendTo('#canvas');
      svg('text').attr({}).append(href(svg('textPath'), '#bow').append(svg('tspan').attr({"textLength": "400", x: "0", dy: "1em"}).text('Hallo Welt, wie geht\'s Dir heute morgen?')).append(svg('tspan').attr({x: "0", dy: "1em"}).text('dies ist ein')).append(svg('tspan').attr({x: "0", dy: "1em"}).text('langer Text ...'))).appendTo('#canvas');
//       svg('text').attr({x: "100", y: "100", style: "stroke: #000000;"}).append(href(svg('textpath'), "#budget").text("Hallo Welt")).appendTo('#canvas');
//       //.text("Budget")).appendTo('#canvas');
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