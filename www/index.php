<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">
    <title></title>
  </head>
  <style type="text/css">
    table, td, tr {
      border: 1px solid black;
    }
  </style>
  <body>
    <h1>Test</h1>
  
    <?php
      //phpinfo();
      $dbpath='sqlite:../build/finance.db';
      $db = new PDO($dbpath);
      $result = $db->query('SELECT * FROM accountings');
    ?>
  
    <table>
      <thead>
        <tr>
          <th>Jahr</th>
          <th>Departement</th>
          <th>Kostenstelle</th>
          <th>Konto</th>
          <th>Betrag</th>
        </tr>
      </thead>
      <tbody>
    
      <?php
        foreach ($result as $row) {
          $depNum = $db->query
            ('SELECT department FROM cost_units WHERE cost_unit="'
             .$row['cost_unit'].'"')->fetchColumn();
          if ($depNum=='') {
            $dep = '**** No Department for Cost Unit '.$row['cost_unit'];
          } else {
            $dep = $db->query
              ('SELECT name FROM departments WHERE cost_unit="'.$depNum.'"')
               ->fetchColumn();
          }
          if ($dep=='') $dep = '**** Unknown Department '.$depNum;
          $cu = $db->query
            ('SELECT name FROM cost_units WHERE cost_unit="'.$row['cost_unit'].'"')
             ->fetchColumn();
          if ($cu=='') $cu = '**** Unknown Cost Unit '.$row['cost_unit'];
          $acc = $db->query
            ('SELECT name FROM accounts WHERE account="'.$row['account'].'"')
             ->fetchColumn();
          if ($acc=='') $acc='**** Unknown Account '.$row['account'];
          if ($row['boa']==0)
            $boa = "Budget";
          else
            $boa = "Rechnung";
          echo "<tr>";
          echo "<td>".$boa.' '.$row['year']."</td>";
          echo "<td>".$dep."</td>";
          echo "<td>".$cu."</td>";
          echo "<td>".$acc."</td>";
          echo "<td>".$row['amount']."</td>";
          echo "</tr>";
        }
      ?>
  
    </table>
  
  </body>
</html>
