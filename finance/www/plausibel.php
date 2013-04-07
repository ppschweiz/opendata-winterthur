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
      $dbpath='sqlite:finance.db';
      $db = new PDO($dbpath);
    ?>

    <h2>Fehler bei der Summenprüfung</h2>
    
    <h3>Summen der Konti</h3>
    
    <p>Jedes einzelne Konto müsste theoretisch in der gesamtstätischen Übersicht denselben Betrag enthalten, wie die Summe des Kontos in allen Produktegruppen ergibt.</p>
    
    <table>
      <thead>
        <tr>
          <th>Jahr</th><th>Departement</th><th>Kostenstelle</th><th>Bezeichnung</th><th>Konto</th><th>Bezeichnung</th><th>Berechnet</th><th>Ausgewiesen</th><th>Differenz</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $years = $db->query('SELECT * FROM years ORDER BY year ASC, boa');
          foreach ($years as $year) {
            $accounts = $db->query('select * from accounts where account>999');
            foreach ($accounts as $account) {
              $s1 = $db->query('select sum(amount) from accountings where year='.$year['year'].' and boa='.$year['boa'].' and cost_unit!=0 and not cost_unit in (select cost_unit from departments) and account='.$account['account'])->fetchColumn();
              $s2 = $db->query('select amount from accountings where year='.$year['year'].' and boa='.$year['boa'].' and cost_unit=0 and account='.$account['account'])->fetchColumn();
              if ($s1!=$s2) {
                $l = $db->query('select * from accountings where year='.$year['year'].' and boa='.$year['boa'].' and cost_unit=0 and account='.$account['account'])->fetch();
                echo '<tr>';
                echo '<td>'.$l['year'].($l['boa']==0?' Budget':' Rechnung').'</td>';
                echo '<td>'.$db->query('select name from departments where cost_unit in (select department from cost_units where cost_unit='.$l['cost_unit'].')')->fetchColumn().'</td>';
                echo '<td>'.$l['cost_unit'].'</td>';
                echo '<td>'.$db->query('select name from cost_units where cost_unit='.$l['cost_unit'])->fetchColumn().'</td>';
                echo '<td>'.$l['account'].'</td>';
                echo '<td>'.$db->query('select name from accounts where account='.$l['account'])->fetchColumn().'</td>';
                echo '<td>'.$s1.'</td>';
                echo '<td>'.$s2.'</td>';
                echo '<td>'.abs($s1-$s2).'</td>';
                echo '</tr>';
              }
            }
          }
        ?>
      </tbody>
    </table>
    
    <h3>Gesamtaufwand und Gesamtertrag</h3>
    
    <p>Ausgewiesene Summen in der Tabelle im Vergleich zu deren Berechnung aus den Werten darunter.</p>

    <table>
      <thead>
        <tr>
          <th>Jahr</th><th>Departement</th><th>Kostenstelle</th><th>Bezeichnung</th><th>Konto</th><th>Bezeichnung</th><th>Berechnet</th><th>Ausgewiesen</th><th>Differenz</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $years = $db->query('SELECT * FROM years ORDER BY year ASC, boa');
          foreach ($years as $year) {
            $departments = $db->query('select cost_unit from cost_units');
            foreach ($departments as $department) {
              for ($eoe=-1; $eoe<=1; $eoe+=2) {
                $s1 = $db->query('select sum(amount) from accountings where year='.$year['year'].' and boa='.$year['boa'].' and cost_unit='.$department['cost_unit'].' and account>999 and account in (select account from accounts where eoe='.$eoe.')')->fetchColumn();
                $s2 = $db->query('select amount from accountings where year='.$year['year'].' and boa='.$year['boa'].' and cost_unit='.$department['cost_unit'].' and account<1000 and account in (select account from accounts where eoe='.$eoe.')')->fetchColumn();
                if ($s1!=$s2) {
                  $l = $db->query('select * from accountings where year='.$year['year'].' and boa='.$year['boa'].' and cost_unit='.$department['cost_unit'].' and account<1000 and account in (select account from accounts where eoe='.$eoe.')')->fetch();
                  echo '<tr>';
                  echo '<td>'.$l['year'].($l['boa']==0?' Budget':' Rechnung').'</td>';
                  echo '<td>'.$db->query('select name from departments where cost_unit in (select department from cost_units where cost_unit='.$l['cost_unit'].')')->fetchColumn().'</td>';
                  echo '<td>'.$l['cost_unit'].'</td>';
                  echo '<td>'.$db->query('select name from cost_units where cost_unit='.$l['cost_unit'])->fetchColumn().'</td>';
                  echo '<td>'.$l['account'].'</td>';
                  echo '<td>'.$db->query('select name from accounts where account='.$l['account'])->fetchColumn().'</td>';
                  echo '<td>'.$s1.'</td>';
                  echo '<td>'.$s2.'</td>';
                  echo '<td>'.abs($s1-$s2).'</td>';
                  echo '</tr>';
                }
              }
            }
          }
        ?>
      </tbody>
    </table>

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

        <?php
          $accounts = $db->query('select * from accounts where account>999');
          foreach ($accounts as $account) {
            $years = $db->query('SELECT * FROM years ORDER BY year ASC, boa');
            foreach ($years as $year) {
              $boa = $year['boa']==0 ? 'Budget ' : 'Rechnung ';
            }
          }
        ?>
    
  </body>
</html>