<?php

namespace Kolyya\PhotoBundle\Service;


interface CheckPermissionsInterface
{
    public function canUpload($object);

    public function canDelete($photo);

    public function canSort($object);
}