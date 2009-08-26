<?php

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" ); 

$admins = array();
$admins['AkshayJoshi'] = true;
$admins['admin'] = true;

$teachers = array();
$teachers['teacher'] = TRUE;
$teachers['CarolynClark'] = TRUE;$teachers['LaneGraciano'] = TRUE;$teachers['PaulSelf'] = TRUE;$teachers['MarkSommers'] = TRUE;$teachers['JocelynWiley'] = TRUE;$teachers['RobertAllan'] = TRUE;$teachers['LauraBerntson'] = TRUE;$teachers['OckiFernandes'] = TRUE;$teachers['DanielNicolson'] = TRUE;$teachers['JoannaRoberts'] = TRUE;$teachers['SamWerlinich'] = TRUE;$teachers['DrewDavis'] = TRUE;$teachers['SharonKerr'] = TRUE;$teachers['AmitKhanna'] = TRUE;$teachers['ShawnKrause'] = TRUE;$teachers['LindseyShoemaker'] = TRUE;$teachers['DavidBerntson'] = TRUE;$teachers['PaulBrocklehurst'] = TRUE;$teachers['GlendaFrank'] = TRUE;$teachers['DebJones'] = TRUE;$teachers['HanaBayyari'] = TRUE;$teachers['RajaBayyari'] = TRUE;$teachers['HanyaMikati'] = TRUE;$teachers['AliMirzo'] = TRUE;$teachers['RimaSarakbi'] = TRUE;$teachers['LaylaBlock'] = TRUE;$teachers['ValdirChagas'] = TRUE;$teachers['ManalYoussef'] = TRUE;$teachers['ClaudiaGonzalez'] = TRUE;$teachers['ValiaJimenez'] = TRUE;$teachers['MercedesdeRamos'] = TRUE;$teachers['LorettaMazzuchin'] = TRUE;$teachers['LyndaHalabi'] = TRUE;$teachers['MarthaJensen'] = TRUE;$teachers['JohnSalminen'] = TRUE;$teachers['NicolasPavlos'] = TRUE;$teachers['DonnaAllen'] = TRUE;$teachers['JasmineBrawn'] = TRUE;$teachers['HajeHalabi'] = TRUE;$teachers['BradleyNewell'] = TRUE;$teachers['AnneRussell'] = TRUE;$teachers['MarkHopkin'] = TRUE;$teachers['AbhayaNivarthi'] = TRUE;$teachers['JesseRemington'] = TRUE;

$base_path = dirname($_SERVER['SCRIPT_FILENAME']);

$date_boundaries = array();
$date_boundaries['start'] = '2009-03-19';
$date_boundaries['end'] = '2009-03-19';

$time_boundaries = array();
$time_boundaries['start'] = (8*60*60);
$time_boundaries['end'] = (16*60*60) + (30*60);
$time_increments = (10*60);


// Only use one authentication method. Comment out the unused ones.
$auth = array();
/*$auth['ldap'] = array('host' => 'home.acs.sch.ae',
					  'port' => NULL,
					  'basedn' => 'dc=home,dc=acs,dc=sch,dc=ae',
					 );*/
$auth['test'] = array();

$db_url = 'sqlite:db.sqlite';

$debug = TRUE;
if ($debug) {
  error_reporting(E_ALL);
  if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
  }
}
