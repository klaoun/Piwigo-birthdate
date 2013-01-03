<?php
defined('BIRTHDATE_PATH') or die('Hacking attempt!');

function birthdate_compute_age($birthdate, $date_ref=null)
{
  $birthdate_unixtime = strtotime($birthdate);

  if (!isset($date_ref))
  {
    $date_ref_unixtime = time();
  }
  else
  {
    $date_ref_unixtime = strtotime($date_ref);
  }

  $nb_seconds = $date_ref_unixtime - $birthdate_unixtime;

  if ($nb_seconds < 0)
  {
    return null;
  }

  $nb_years = $nb_seconds / (60*60*24*365.25);
  if ($nb_years >= 2)
  {
    return sprintf(l10n('%d years'), $nb_years);
  }
  
  $nb_months = $nb_seconds / (60*60*24*30.4); // average 30.4 days each month
  if ($nb_months >= 2)
  {
    return sprintf(l10n('%d months'), $nb_months);
  }

  $nb_days = $nb_seconds / (60*60*24);
  if ($nb_days >= 2)
  {
    return sprintf(l10n('%d days'), $nb_days);
  }

  $nb_hours = $nb_seconds / (60*60);
  if ($nb_hours >= 2)
  {
    return sprintf(l10n('%d hours'), $nb_hours);
  }
  
  $nb_minutes = $nb_seconds / 60;
  if ($nb_minutes >= 2)
  {
    return sprintf(l10n('%d minutes'), $nb_minutes);
  }

  return sprintf(l10n('%d seconds'), $nb_seconds);
}
?>