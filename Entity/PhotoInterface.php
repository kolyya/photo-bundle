<?php

namespace Kolyya\PhotoBundle\Entity;

interface PhotoInterface
{
    public function getObject();
    public function setObject($object);
}