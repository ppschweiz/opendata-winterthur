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
  $dep = $db->query('SELECT department FROM cost_units WHERE cost_unit="'.$row['cost_unit'].'"')->fetchColumn();
  echo "<tr>";
  echo "<td>".$row['year']."</td>";
  echo "<td>".$db->query('SELECT name FROM departments WHERE cost_unit="'.$dep.'"')->fetchColumn()."</td>";
  echo "<td>".$db->query('SELECT name FROM cost_units WHERE cost_unit="'.$row['cost_unit'].'"')->fetchColumn()."</td>";
  echo "<td>".$db->query('SELECT name FROM accounts WHERE account="'.$row['account'].'"')->fetchColumn()."</td>";
  echo "<td>".$row['amount']."</td>";
  echo "</tr>";
}
echo "</table>";
?>

</body>
</html>
