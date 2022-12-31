<?php
defined('BIRTHDATE_PATH') or die('Hacking attempt!');

/**
 * add a prefilter on photo page
 */
function birthdate_loc_end_picture()
{
  global $template, $picture;

  if (!empty($picture['current']['date_creation']))
  {
    list($ymd, $hms) = explode(' ', $picture['current']['date_creation']);
    list($year, $month, $day) = explode('-', $ymd);

    if (checkdate($month, $day, $year))
    {
      $tpl_tags = $template->get_template_vars('related_tags');

      if (!empty($tpl_tags))
      {
        $template->clear_assign('related_tags');

        foreach ($tpl_tags as $tag)
        {
          if (isset($tag['birthdate']))
          {
            $age_label = birthdate_compute_age($tag['birthdate'], $picture['current']['date_creation']);
            if (isset($age_label))
            {
              $tooltip = sprintf(
                l10n('on this photo %s is %s old'),
                $tag['name'],
                $age_label
                );
              
              $tag['name'].= '</a> <span class="" title="'.$tooltip.'">('.$age_label.')</span><a>';
            }
          }
          
          $template->append('related_tags', $tag);
        }
      }
    }
  }
}
?>
