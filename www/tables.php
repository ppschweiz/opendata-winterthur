<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8" />
    <script language="javascript" type="text/javascript"
            src="/javascript/jquery/jquery.js"></script>
  </head>
  <body>
  
    <script>
      annularSector = (centerX,centerY,startAngle,endAngle,innerRadius,outerRadius) ->               
    startAngle  = degreesToRadians startAngle+180
    endAngle    = degreesToRadians endAngle+180
    p           = [ 
        [ centerX+innerRadius*Math.cos(startAngle),     centerY+innerRadius*Math.sin(startAngle) ]
        [ centerX+outerRadius*Math.cos(startAngle),     centerY+outerRadius*Math.sin(startAngle) ]
        [ centerX+outerRadius*Math.cos(endAngle),       centerY+outerRadius*Math.sin(endAngle) ]
        [ centerX+innerRadius*Math.cos(endAngle),       centerY+innerRadius*Math.sin(endAngle) ] 
    ]
    angleDiff   = endAngle - startAngle
    largeArc    = (if (angleDiff % (Math.PI * 2)) > Math.PI then 1 else 0)
    commands    = []

    commands.push "M" + p[0].join()
    commands.push "L" + p[1].join()
    commands.push "A" + [ outerRadius, outerRadius ].join() + " 0 " + largeArc + " 1 " + p[2].join()
    commands.push "L" + p[3].join()
    commands.push "A" + [ innerRadius, innerRadius ].join() + " 0 " + largeArc + " 0 " + p[0].join()
    commands.push "z"

    return commands.join(" ")   
    </script>
  
    <svg width="500" height="500">
    <path d="M200,200 L200,20 A180,180 0 0,1 377,231 z"
        style="fill:#ff0000;
                fill-opacity: 1;
                stroke:black;
                stroke-width: 1"/>
      <path d="M100,500 a100,100 0 0,1 300,0" fill="green" stroke="blue" stroke-width="5" />
      <path d="M200,500 a50,50 0 1,1 100,0" fill="red" stroke="blue" stroke-width="5" />
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

    <p id="1">Dies ist ein Test</p>

    <script type="text/javascript">
      $.getJSON("finance.json", function(data) {
        var items = [];
        $.each(data, function(key, val) {
          items.push('<li id="' + key + '">' + val.name + '</li>');
        });
        $('<ul/>', {
          class: 'my-new-list',
          html: items.join('')
        }).appendTo('body');
      });
    </script>

  </body>
</html> 