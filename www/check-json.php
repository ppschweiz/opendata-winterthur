<html>
  <body>
    <h1>JSON-Winterthur</h1>
    <?php 
      $obj = json_decode(file_get_contents("open-budget/data/winterthur.json")); 
      if (!$obj) {
        echo "<p>PARSE ERROR</p>";
        echo "<p>".json_last_error()."</p>";
      }
    ?>
    <code>
      <?php var_dump($obj); ?>
    </code>
    <p>
  </body>
</html>
