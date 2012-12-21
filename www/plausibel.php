<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Datenplasibilisierung Finanzen Winterthur</title>
  </head>
  <style type="text/css">
    table, td, th, tr, tbody, thead {
      border: 1px solid black;
    }
  </style>
  <body>
    <h1>Datenplausibilisierung Finanzen Winterthur</h1>
    
    <?php
      $dbpath='sqlite:../build/finance.db';
      $db = new PDO($dbpath);
    ?>

    <h2>Datensätze mit falschem Vorzeichen</h2>
    
    <table>
      <thead>
        <tr>
          <th>Jahr</th><th>Departement</th><th>Kostenstelle</th><th>Bezeichnung</th><th>Konto</th><th>Bezeichnung</th><th>Betrag</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $res = $db->query('select * from accountings where amount<0 and account!=0');
          foreach ($res as $l) {
            echo '<tr>';
            echo '<td>'.$l['year'].($l['boa']==0?' Budget':' Rechnung').'</td>';
            echo '<td>'.$db->query('select name from departments where cost_unit in (select department from cost_units where cost_unit='.$l['cost_unit'].')')->fetchColumn().'</td>';
            echo '<td>'.$l['cost_unit'].'</td>';
            echo '<td>'.$db->query('select name from cost_units where cost_unit='.$l['cost_unit'])->fetchColumn().'</td>';
            echo '<td>'.$l['account'].'</td>';
            echo '<td>'.$db->query('select name from accounts where account='.$l['account'])->fetchColumn().'</td>';
            echo '<td>'.$l['amount'].'</td>';
            echo '</tr>';
          }
        ?>
      </tbody>
    </table>

    <table>
      <thead>
        <tr>
          <th>Konto</th><th>Bezeichnung</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $accounts = $db->query('select * from accounts where account>999');
          foreach ($accounts as $account) {
            $years = $db->query('SELECT * FROM years ORDER BY year ASC, boa');
            foreach ($years as $year) {
              $boa = $year['boa']==0 ? 'Budget ' : 'Rechnung ';
            }
          }
        ?>
      </tbody>
    </table>
    
  </body>
</html>