<?php
/*
Plugin Name: OpenData Community Finances
Plugin URI: http://winterthur.zh.piratenpartei.ch/
Description: Access to public financial community data
Version: 0.1.0
Author: Marc Wäckerlin
Author URI: https://marc.waeckerlin.org/politik/index
License: GPL3
*/
if (!class_exists('OpenDataCommunityFinances')) {

  include("class/pData.class.php");
  include("class/pDraw.class.php");
  include("class/pImage.class.php");
  
  class OpenDataCommunityFinances {
    var $settings;
    var $dbpath;
    var $db;
    var $error;
    var $name;
    var $usr;
    
    // Initialization
    function OpenDataCommunityFinances() {
      $this->name = "OpenDataCommunityFinances";
      $this->error = "";
      if (!extension_loaded('gd') && !extension_loaded('gd2')) {
	$this->error .= "<li>GD-Extension is not loaded!</li>";
      }
      try {
	$this->dbpath='sqlite:'.dirname(__FILE__).'/finance.db';
	$this->db = new PDO($this->dbpath);
      } catch (Exception $e) {
	$this->error .= "<li>Opening Database \"".$this->dbpath." results in: ".$e->getMessage().'</li>';
      }
      $this->get_options();
      if (function_exists('load_plugin_textdomain'))
        load_plugin_textdomain($this->name,
                               dirname(__FILE__).'/languages',
                               dirname(__FILE__).'/languages');

      // use [OpenDataCommunityFinances] in WordPress
      add_shortcode($this->name, array(&$this, 'show'));

      // use [FinGraph] in WordPress
      add_shortcode('FinGraph', array(&$this, 'graph'));

      if (is_admin()) {
        //add_action('admin_menu', array(&$this, 'add_menupages'));
      }
    }

    function get_options() {
      $this->settings = get_option($this->name);
      //if (!array_key_exists('irgendwas',$this->settings)) {
      //  $this->settings['irgendwas'] = 'wert';
      //  update_option('TEMPLATE', $this->settings);
      //}
    }

    // calles on plugin activation
    function activate() {
      // Create Tables if needed or generate whatever on installation
    }

    // called before plugin removal
    function uninstall() {
      // Delete Tables or settings if needed be deinstallation
    }

    // admin menu
    function add_menupages() {
      // For Option Pages, see WordPress function:
      //   add_options_page()
      // For own Menu Pages, see WordPress function:
      //   add_menu_page() and add_submenu_page()
    }

    function graph() {
      $filename = 'basic.png';
      try {

	/* Create your dataset object */
	$myData = new pData();
 
	/* Add data in your dataset */
	$myData->addPoints(array(VOID,3,4,3,5));
 
	/* Create a pChart object and associate your dataset */
	$myPicture = new pImage(700,230,$myData);
 
	/* Choose a nice font */
	$myPicture->setFontProperties(array("FontName"=>dirname(__FILE__).'/fonts/Forgotte.ttf', "FontSize"=>11));
 
	/* Define the boundaries of the graph area */
	$myPicture->setGraphArea(60,40,670,190);
 
	/* Draw the scale, keep everything automatic */
	$myPicture->drawScale();
 
	/* Draw the scale, keep everything automatic */
	$myPicture->drawSplineChart();
 
	/* Build the PNG file and send it to the web browser */
	$uploads = wp_upload_dir();
	$myPicture->Render($uploads['basedir'].'/'.$this->name.'-'.$filename);
	return '<img src="'.$uploads['baseurl'].'/'.$this->name.'-'.$filename.'"/>';
      } catch (Exception $e) {
	$this->error .= "<li>Image generation failed: ".$e->getMessage()."</li>";
	return "<p>Error:<ul>".$this->error."</ul></p>";
      }
    }

    function postVar($name, $default=null, $ereg='[-_A-Za-z0-9]+') {
      if (isset($this->$name))
	return $this->$name;
      if (isset($_POST) && isset($_POST[$name]) 
	  && ereg($ereg, is_array($_POST[$name])?join($_POST[$name]):$_POST[$name])) 
	return $_POST[$name];
      return $default;
    }

    function select($name, $default=0, $options=[], $ereg='[-_A-Za-z0-9]+') {
      $this->$name = $this->postvar($name, $default, $ereg);
      foreach ($options as $option)
	if (is_array($option))
	  $options .= $this->option($name, $option[0], $option[1]);
	else
	  $options .= $this->option($name, $option);
      return
	'<select name="'.$name.'">'.$options.'</select>';
    }

    function option($name, $value, $text=null) {
      return 
	'<option value="'.$value.'"'.($value==$this->$name?' selected="selected"':'').'>'
	.($text?$text:$value).
	'</option>';
    }

    function checkbox($name, $value, $default=null, $ereg='[-_A-Za-z0-9]+') {
      $this->$name = $this->postvar($name, $default, $ereg);
      return
	'<input type="checkbox" name="'.$name.'[]" value="'.$value.'"'
	.(in_array($value, $this->$name)?' checked="checked"':'').'/>';
    }

    function radio($name, $value, $default=null, $ereg='[-_A-Za-z0-9]+') {
      $this->$name = $this->postvar($name, $default, $ereg);
      return
	'<input type="radio" name="'.$name.'" value="'.$value.'"'
	.($value==$this->$name?' checked="checked"':'').'/>';
    }

    function show($atts) {
      $page_url = ('80'!=$_SERVER["SERVER_PORT"]&&isset($_SERVER["HTTPS"])&& $_SERVER["HTTPS"]=="on" 
		   ? "https://" : "http://")
	.$_SERVER["SERVER_NAME"].':'. $_SERVER["SERVER_PORT"].strip_tags($_SERVER["REQUEST_URI"]);
      $out = null; //wp_cache_get('archive', $this->name);
      if (!$out) {
	if ($this->error) {
	  $out = "<p>Error:<ul>".$this->error."</ul></p>";
	} elseif ($this->db) {
	  $out .= '<form method="post" enctype="multipart/form-data" action="'.$page_url.'">';
	  try {
	    $query = $this->db->query('select distinct year from years order by year asc');
	    foreach ($query as $row) $years[] = $row['year'];
	    $out .= 'Vergleiche ';
	    $out .= $this->select('fromboa', 0, [[0, 'Budget'],
						 [1, 'Rechnung']]);
	    $out .= $this->select('fromyear', date('Y')-1, $years);
	    $out .= ' mit ';
	    $out .= $this->select('toboa', 1, [[0, 'Budget'],
					       [1, 'Rechnung']]);
	    $out .= $this->select('toyear', date('Y')-1, $years);
	    $out .= '<br/>';
	    $out .= $this->checkbox('filter', 'fdepartment', ['fdepartment']);
	    $out .= $this->select('department', 0, $this->db->query('select cost_unit, substr("000000"||cost_unit, -6, 6)||": "||name from departments')->fetchAll());
	    $out .= '<br/>';
	    $out .= $this->checkbox('filter', 'fcost_unit');
	    $out .= $this->select('cost_unit', 0, $this->db->query('select cost_unit, substr("000000"||cost_unit, -6 ,6)||": "||name from cost_units')->fetchAll());
	    $out .= '<br/>';
            $out .= $this->radio('eoe', 'expense', 'expense').' Ausgaben ';
	    $out .= $this->checkbox('filter', 'fexpense');
	    $out .= $this->select('expense', 0, $this->db->query('select account, substr("0000"||account, -4, 4)||": "||name from accounts where eoe=-1')->fetchAll());
	    $out .= '<br/>';
            $out .= $this->radio('eoe', 'earning').' Einnahmen ';
	    $out .= $this->checkbox('filter', 'fearning');
	    $out .= $this->select('earning', 0, $this->db->query('select account, substr("0000"||account, -4, 4)||": "||name from accounts where eoe=1')->fetchAll());
	    //wp_cache_set( 'archive', $out, $this->name, '86400');
	  } catch (Exception $e) {
	    $this->error .= "<li>Formsetup failed: ".$e->getMessage()."</li>";
	    $out = "<p>Error:<ul>".$this->error."</ul></p>";
	  }
	  $out .= '<br/><input type="submit" name="openfinance" value="selection">Auswahl</input></form>';
	  /*
select (select name from cost_units where cost_unit=a.cost_unit),(select name from departments where cost_unit=a.cost_unit),(select name from accounts where account=a.account),a.amount,a.amount-b.amount as diff,100*(a.amount-b.amount)/b.amount as percentage from accountings as b left join accountings as a where b.year=2012 and b.boa=0 and a.year=2012 and a.boa=1 and b.cost_unit=a.cost_unit and b.account=a.account order by abs(a.amount-b.amount) desc;
	   */
	  $headings = [];
	  $select = [];
	  $where = [];
	  if (!in_array('fdepartment', $this->filter)) {
	    $headings[] = 'Departement';
	    $select[] = '(select name from departments where cost_unit=a.cost_unit)';
            $where[] = 'a.department='.$this->department;
	  } else {
	    $headings[] = 'Departement';
	    $select[] = '(select name from departments where cost_unit=a.cost_unit)';
	  }
	  if (!in_array('fcost_unit', $this->filter)) {
	    $headings[] = 'Produktegruppe';
	    $select[] = '(select name from cost_units where cost_unit=a.cost_unit)';
            $where[] = 'a.cost_unit='.$this->cost_unit;
	  } else {
	    $headings[] = 'Produktegruppe';
	    $select[] = '(select name from cost_units where cost_unit=a.cost_unit)';
	  }
          if ($this->eoe=='expense') {
	    $where[] = '(select eoe from accounts where account=a.account)=-1';
	    if (!in_array('fexpense', $this->filter)) {
              $headings[] = 'Aufwandskonto';
	      $select[] = "substr('0000'||a.account, -4, 4)||': '||(select name from accounts where account=a.account)";
	      $where[] = 'a.account='.$this->expense;
	    } else {
              $headings[] = 'Aufwandskonto';
	      $select[] = '(select name from accounts where account=a.account)';
            }
          } elseif ($this->eoe=='earning') {
	    $where[] = '(select eoe from accounts where account=a.account)=1';
	    if (!in_array('fearning', $this->filter)) {
              $headings[] = 'Ertragskonto';
	      $select[] = '(select name from accounts where account=a.account)';
	      $where[] = 'a.account='.$this->expense;
	    } else {
              $headings[] = 'Ertragskonto';
	      $select[] = '(select name from accounts where account=a.account)';
            }
          }
	  $headings[] = ($this->fromboa==0?'Budget ':'Rechnung ').$this->fromyear;
	  $select[] = 'a.amount';
	  $where[] = 'a.boa='.$this->fromboa;
	  $where[] = 'a.year='.$this->fromyear;
	  $headings[] = ($this->toboa==0?'Budget ':'Rechnung ').$this->toyear;
	  $select[] = 'b.amount';
	  $where[] = 'b.boa='.$this->toboa;
	  $where[] = 'b.year='.$this->toyear;
	  $query = 'select '.join(',', $select).' from accountings as a left join accountings as b where '.join(' and ', $where).' limit 3';
	  $out .= '<p><code>'.htmlspecialchars($query).'</code></p>';
	  $out .= $this->table($headings, $query);
        } else {
          $out=__("NO DATABASE", $this->name);
        }
      }
      $out .= '<p>_POST:</p><pre>'.print_r($_POST, true).'</pre>';
      return $out;
    }

    function table($headings, $query, $sum=null) {
      try {
	$head = function($h){return '<th>'.$h.'</th>';};
	$len = count($headings);
	$out .= "<table>\n<thead>\n<tr>".join(array_map($head, $headings))."</tr>\n</thead>\n<tbody>\n";
	if ($res=$this->db->query($query)) foreach ($res as $line) {
	    $out .= "<tr>";
	    for ($i=0; $i<$len; ++$i)
	      $out .= "<td>".$line[$i]."</td>";
	    $out .= "</tr>\n";
	  }
	$out .= "</tbody>\n"
	  .($sum
	    ?"<tfoot>\n<tr>".
	    (count($headings)>count($sum)?'<th colspan="'.(count($headings)-count($sum)).'">Summe:</th>':'')
	    .
	    join(array_map($head, $sum))
	    ."</tr>\n</tfoot>\n"
	    :'')
	  ."</table>\n";
	//$out .= '<pre>'.print_r($this->db->query($query)->fetchAll(), true).'</pre>';
	return $out;
      } catch (Exception $e) {
	$this->error .= "<li>Databasequery failed: ".$e->getMessage()."</li>";
	$out = "<p>Error:<ul>".$this->error."</ul></p>";
      }
    }

  }

  add_action('init', 'init_OpenDataCommunityFinances');
  function init_OpenDataCommunityFinances() {
    global $OpenDataCommunityFinances;
    $OpenDataCommunityFinances = new OpenDataCommunityFinances();
  }

}

if (function_exists('register_activation_hook')) {
  register_activation_hook(__FILE__, array('OpenDataCommunityFinances',
                                           'activate'));
}

if (function_exists('register_uninstall_hook')) {
  register_uninstall_hook(__FILE__, array('OpenDataCommunityFinances',
                                          'uninstall'));
}

?>