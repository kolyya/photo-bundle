<?php

namespace Kolyya\PhotoBundle\Templating\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Templating\Helper\Helper;

class PhotoHelper extends Helper
{

    private $em;
    private $container;
    private $templating;
    private $objects;

    public function __construct(EntityManager $em, Container $container, $templating, $objects)
    {
        $this->em = $em;
        $this->container = $container;
        $this->templating = $templating;
        $this->objects = $objects;
    }

    public function getManager($objectId, $objectName, $photos){

        $config = $this->objects[$objectName];

        return $this->templating->render('@KolyyaPhoto/Photo/manager.html.twig',array(
            'id' => $objectId,
            'name' => $objectName,
            'photos' => $photos,
            'path' => '/'.$config['path'].'/'.$config['manager_format']
        ));
    }


    /**
     * Returns the name of the helper.
     *
     * @return string The helper name
     */
    public function getName()
    {
        return 'kolyya_photo';
    }
}
