<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8" />
    <script language="javascript" type="text/javascript"
            src="/javascript/jquery/jquery.js"></script>
  </head>
  <body>
  
    <svg width="100" height="100">
      <a xlink:href="https://marc.waeckerlin.org">
        <circle cx="50" cy="50" r="40"
                fill="red" stroke="blue" stroke-width="5" />
      </a>
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