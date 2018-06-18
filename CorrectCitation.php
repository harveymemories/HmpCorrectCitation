<?php


class CorrectCitationPlugin extends Omeka_Plugin_AbstractPlugin
{
  protected $_filters = array('item_citation');

  public function filterItemCitation($citation, $args)
  {
        $citation = '';

        $creators = metadata('item', array('Dublin Core', 'Creator'), array('all' => true));
        // Strip formatting and remove empty creator elements.
        $creators = array_filter(array_map('strip_formatting', $creators));
        if ($creators) {
            switch (count($creators)) {
                case 1:
                    $creator = $creators[0];
                    break;
                case 2:
                    /// Chicago-style item citation: two authors
                    $creator = __('%1$s and %2$s', $creators[0], $creators[1]);
                    break;
                case 3:
                    /// Chicago-style item citation: three authors
                    $creator = __('%1$s, %2$s, and %3$s', $creators[0], $creators[1], $creators[2]);
                    break;
                default:
                    /// Chicago-style item citation: more than three authors
                    $creator = __('%s et al.', $creators[0]);
            }
            $citation .= "$creator, ";
        }
        else {
            $citation .= "Anonymous, ";
        }

        $title = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
        if ($title) {
          //$citation .= "&#8220;$title,&#8221; ";
          $citation .= __('&#8220;%1$s,&#8221; ', $title);
        }

        $siteTitle = strip_formatting(option('site_title'));
        if ($siteTitle) {
            $citation .= "<em>$siteTitle</em>, ";
        }

        $accessed = format_date(time(), Zend_Date::DATE_LONG);
        $url = html_escape(record_url('item', null, true));
        /// Chicago-style item citation: access date and URL
        $citation .= __('accessed %1$s, %2$s.', $accessed, $url);

    return $citation;
  }

}
?>
