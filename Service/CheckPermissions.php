<?php

namespace Kolyya\PhotoBundle\Service;

class CheckPermissions implements CheckPermissionsInterface
{
    public function canUpload($object)
    {
        return true;
    }

    public function canDelete($photo)
    {
        return true;
    }

    public function canSort($object)
    {
        return true;
    }
}