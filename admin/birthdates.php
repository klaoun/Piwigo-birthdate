<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

// +-----------------------------------------------------------------------+
// | Check Access and exit when user status is not ok                      |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

// +-----------------------------------------------------------------------+
// | add/edit birthdate                                                    |
// +-----------------------------------------------------------------------+

if (isset($_POST['submit_add']))
{
  check_input_parameter('who', $_POST, false, '/^~~\d+~~$/');

  $tag_id = str_replace('~', '', $_POST['who']);

  // does the tag exists?
  $query = '
SELECT *
  FROM '.TAGS_TABLE.'
  WHERE id = '.$tag_id.'
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) == 0)
  {
    die('this tag does not exist');
  }

  $who = pwg_db_fetch_assoc($result);

  if (!strtotime($_POST['birthdate']))
  {
    die('hacking attempt: invalid "birthdate" option');
  }
  
  single_update(
    TAGS_TABLE,
    array('birthdate' => date('Y-m-d H:i:s', strtotime($_POST['birthdate']))),
    array('id' => $who['id'])
    );
}

// +-----------------------------------------------------------------------+
// | remove birthdate                                                      |
// +-----------------------------------------------------------------------+

if (isset($_GET['delete']))
{
  check_input_parameter('delete', $_GET, false, PATTERN_ID);

  $query = '
SELECT *
  FROM '.TAGS_TABLE.'
  WHERE id = '.$_GET['delete'].'
;';
  $result = pwg_query($query);
  if (pwg_db_num_rows($result) == 0)
  {
    die('this tag does not exist');
  }

  $tag = pwg_db_fetch_assoc($result);
  $name = trigger_event('render_tag_name', $tag['name']);
  
  $query = '
UPDATE '.TAGS_TABLE.'
  SET birthdate = NULL
  WHERE id = '.$_GET['delete'].'
;';
  $result = pwg_query($query);

  $_SESSION['page_infos'] = array(
    sprintf(
      l10n('Birthdate removed for %s'),
      $name
      )
    );
  redirect(BIRTHDATE_ADMIN);
}

// +-----------------------------------------------------------------------+
// | template init                                                         |
// +-----------------------------------------------------------------------+

// define template file
$template->set_filename(
  'birthdate_content',
  realpath(BIRTHDATE_PATH . 'admin/template/birthdates.tpl')
  );

// +-----------------------------------------------------------------------+
// | prepare form                                                          |
// +-----------------------------------------------------------------------+

// edit mode?
if (isset($_GET['edit']))
{
  check_input_parameter('edit', $_GET, false, PATTERN_ID);
  
  $query = '
UPDATE '.TAGS_TABLE.'
  SET birthdate = NULL
  WHERE id = '.$_GET['edit'].'
;';
  $result = pwg_query($query);
  $row = pwg_db_fetch_assoc($result);

  if (isset($row['id']))
  {
    $category_options_selected = $row['category_id'];
   
    $template->assign(
      array(
        'edit' => $row['id'],
        'who_options_selected' => $row['type'],
        'user_options_selected' => $row['user_id'],
        'group_options_selected' => $row['group_id'],
        'recursive' => get_boolean($row['recursive']),
        'create_subcategories' => get_boolean($row['create_subcategories']),
        'moderated' => get_boolean($row['moderated']),
        )
      );
  }
}
else
{
  $query = '
SELECT
    id,
    name
  FROM '.TAGS_TABLE.'
;';
  $tags = get_taglist($query, false);

  $template->assign(
    array(
      'tags' => $tags,
      )
    );
}

// +-----------------------------------------------------------------------+
// | birthdate list                                                        |
// +-----------------------------------------------------------------------+

$query = '
SELECT *
  FROM '.TAGS_TABLE.'
  WHERE birthdate IS NOT NULL
  ORDER BY name
;';
$result = pwg_query($query);

$birthdates = array();
$tag_ids = array();
$tag_infos = array();

while ($row = pwg_db_fetch_assoc($result))
{
  array_push($birthdates, $row);
  array_push($tag_ids, $row['id']);
}

if (!empty($tag_ids))
{
  $query = '
SELECT
    tag_id,
    COUNT(*) AS counter,
    MIN(date_creation) AS min_date,
    MAX(date_creation) AS max_date
  FROM '.IMAGE_TAG_TABLE.'
    JOIN '.IMAGES_TABLE.' ON image_id=id
  WHERE tag_id in ('.implode(',', $tag_ids).')
  GROUP BY tag_id
;';
  $result = pwg_query($query);

  while ($row = pwg_db_fetch_assoc($result))
  {
    $tag_infos[ $row['tag_id'] ] = $row;
  }
}

foreach ($birthdates as $birthdate)
{
  $photos_label = l10n('No photo');
  if (isset($tag_infos[ $birthdate['id'] ]))
  {
    $url = make_index_url(array('tags' => array($birthdate)));

    $photos_label = '<a href="'.$url.'" target="_blank">';
    $photos_label.= l10n_dec('%d photo', '%d photos', $tag_infos[ $birthdate['id'] ]['counter']);
    $photos_label.= '</a> (';
    
    $from = $tag_infos[ $birthdate['id'] ]['min_date'];
    $to = $tag_infos[ $birthdate['id'] ]['max_date'];
    
    if (date('Y-m-d', strtotime($from)) == date('Y-m-d', strtotime($to)))
    {
      $photos_label.= format_date($from);
    }
    else
    {
      $photos_label.= sprintf(
        l10n('from %s to %s'),
        format_date($from),
        format_date($to)
        );
    }

    $photos_label.= ')';
  }
  
  $template->append(
    'birthdates',
    array(
      'NAME' => $birthdate['name'],
      'BIRTHDATE' => format_date($birthdate['birthdate'], true),
      'PHOTOS' => $photos_label,
      'U_DELETE' => BIRTHDATE_ADMIN.'&amp;delete='.$birthdate['id'],
      'HIGHLIGHT' => (isset($_POST['who']) and '~~'.$birthdate['id'].'~~' == $_POST['who']) ? true : false,
      'TAG_ID' => $birthdate['id'],
      'BIRTHDATE_RAW' => date('Y-m-d H:i', strtotime($birthdate['birthdate'])),
      )
    );
}
?>