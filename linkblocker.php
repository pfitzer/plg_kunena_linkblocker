<?php
/**
 * @author Michael Pfister <michael@mp-development.de>
 * @package
 * @copyright    Copyright (C) 2012 Michael Pfister.  All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
defined ( '_JEXEC' ) or die ();
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.methods' );

class plgKunenaLinkblocker extends JPlugin {

    /**
     *
     * @var string
     */
    const REGEX = '/<a\s[^>]*href\s*=\s*(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU';

    /**
     *
     * @param JDispatcher $subject
     * @param array $config
     */
    public function __construct(&$subject, $config)
    {
        $user =& JFactory::getUser();
        // Do not load if Kunena version is not supported or Kunena is offline
        if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::installed())) return;

        $app =& JFactory::getApplication();
        if ($app->isAdmin() || 0 == $user->guest) return;

        parent::__construct ( $subject, $config );

        JPlugin::loadLanguage('plg_kunena_linkblocker');

    }

    /**
     *
     * @param string $context
     * @param stdClass $row
     * @param JRegistry $params
     * @param int $page
     * @return boolean
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {

        $row->text = preg_replace(self::REGEX, $this->_getReplaceLink(), $row->text);

        return true;
    }

    /**
     *
     * @return string
     */
    private function _getReplaceLink()
    {
        switch ($this->params->getValue('register', 0)) {
            default:
            case 0:
                $link = JHtml::link(JRoute::_('index.php?option=com_users&view=registration'), JText::_('PLG_KUNENA_LINKBLOCKER_REPLACE'));
                break;
            case 1:
                $link = JHtml::link(JRoute::_('index.php?option=com_community&view=register'), JText::_('PLG_KUNENA_LINKBLOCKER_REPLACE'));
                break;
        }

        return $link;
    }
}
