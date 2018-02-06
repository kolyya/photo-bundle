<?php

namespace Kolyya\PhotoBundle\Twig\Extension;

use Kolyya\PhotoBundle\Templating\Helper\PhotoHelper;

class PhotoExtension extends \Twig_Extension
{
    /**
     * @var PhotoHelper
     */
    protected $helper;

    /**
     * @param PhotoHelper $helper
     */
    public function __construct(PhotoHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('kolyya_photo_manager', array($this, 'getManager'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getManager($objectId, $objectName, $photos)
    {
        return $this->helper->getManager($objectId, $objectName, $photos);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kolyya_hold';
    }
}
