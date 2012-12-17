<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">
    <title></title>
  </head>
  <style type="text/css">
    table, td, th, tr, tbody, thead {
      border: 1px solid black;
    }
  </style>
  <body>
    <h1>Finanzen der Stadt Winterthur</h1>
    
    <p>Diese Daten sind von der <a href="http://winterthur.zh.piratenpartei.ch">Piratenpartei Winterthur</a> bearbeitet und werden ohne Gewähr zur Verfügung gestellt.</p>
  
    <?php
      //phpinfo();
      $dbpath='sqlite:../build/finance.db';
      $db = new PDO($dbpath);
    ?>
  
    <table>
      <thead>
        <tr>
          <th>Departement</th>
          <th>Kostenstelle</th>
          <th>Konto</th>
          <?php
            $years = $db->query('SELECT * FROM years ORDER BY year ASC, boa');
            foreach ($years as $year) {
              $boa = $year['boa']==0 ? 'Budget ' : 'Rechnung ';
              ?>
              <th><?php echo $boa.$year['year'] ?></th>
              <?php
            }
          ?>
        </tr>
      </thead>
      <tbody>
    
      <?php
        $query = 'SELECT * FROM accountings WHERE year='.$year['year'].' AND boa='.$year['boa'];
//         echo "QUERY=$query";
        $result = $db->query($query);
        foreach ($result as $row) {
          $depNum = $db->query('SELECT department FROM cost_units WHERE cost_unit="'.$row['cost_unit'].'"')->fetchColumn();
          if ($depNum=='')
            $dep = '**** No Department for Cost Unit '.$row['cost_unit'];
          else
            $dep = $db->query('SELECT name FROM departments WHERE cost_unit="'.$depNum.'"')->fetchColumn();
          if ($dep=='') $dep = '**** Unknown Department '.$depNum;
          $cu = $db->query('SELECT name FROM cost_units WHERE cost_unit="'.$row['cost_unit'].'"')->fetchColumn();
          if ($cu=='') $cu = '**** Unknown Cost Unit '.$row['cost_unit'];
          $acc = $db->query('SELECT name FROM accounts WHERE account="'.$row['account'].'"')->fetchColumn();
          if ($acc=='') $acc='**** Unknown Account '.$row['account'];
          echo '<tr>';
          echo '<td>'.$dep.'</td>';
          echo '<td>'.$cu.'</td>';
          echo '<td>'.$acc.'</td>';
          $years = $db->query('SELECT * FROM years ORDER BY year ASC, boa');
          unset($last);
          foreach ($years as $year) {
            $amount = $db->query('SELECT amount FROM accountings WHERE cost_unit='.$row['cost_unit'].' AND account='.$row['account'].' AND year='.$year['year'].' AND boa='.$year['boa'])->fetchColumn();
            if (isset($last))
                echo '<td>'.$amount.'</br>('.($amount==$last?'±':($amount<$last?'-':'+')).($last==0?($amount==0?'0':'∞'):round(abs(($amount-$last)*100/$last))).'%)</td>';
              else
                echo '<td>'.$amount.'</td>';
            $last = $amount;
          }
          echo '</tr>';
        }
      ?>
  
    </table>
    
    <h2>Projektinformationen und Quellen</h2>
    
    <dl>
      <dt>Test-URL der Daten</dt>
      <dd><a href="https://opendata-winterthur.waeckerlin.org">https://opendata-winterthur.waeckerlin.org</a></dd>
      <dt>Test-Hoster</dt>
      <dd><a href="https://marc.waeckerlin.org">Pirat Marc Wäckerlin</a></dd>
      <dt>Projekt-URL</dt>
      <dd><a href="http://projects.piratenpartei.ch/projects/opendata-winterthur">http://projects.piratenpartei.ch/projects/opendata-winterthur</a></dd>
      <dt>Piratenpartei Winterthur</dt>
      <dd><a href="http://winterthur.zh.piratenpartei.ch">http://winterthur.zh.piratenpartei.ch</a></dd>
      <dt>Inspirationsquelle und Unterstützung</dt>
      <dd><a href="http://bern.budget.opendata.ch/">http://bern.budget.opendata.ch/</a></dd>
    </dl>
  
  </body>
</html>
