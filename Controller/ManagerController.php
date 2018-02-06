<?php

namespace Kolyya\PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ManagerController extends Controller
{
    private $config;

    /**
     * Загрузка фото на сервер
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadAction(Request $request)
    {
        $id = $request->query->getInt('id',0);
        $name = $request->query->get('name');

        // получаем конфиг
        $config = $this->getConfig($request);
        if(!$config)
            return $this->error('no_config');

        // находим объект
        $object = $this->getDoctrine()->getRepository($config['object_class'])->find($id);
        if(!$object)
            return $this->error('no_object');

        // проверяем права доступа к действию
        if(!$this->get($config['check_permissions'])->canUpload($object))
            return $this->error('not_allowed');

        $photo = new $config['photo_class'];
        $photo->setObject($object);

        // загружаем фотографию
        $PM = $this->container->get('kolyya_photo.photo_manager');
        $PM->savePhoto($config, $photo,$request->files->get('file'));

        // возвращаем список фото
        $photos = $object->getPhotos();

        return new JsonResponse(array(
            'success' => true,
            'count' => sizeof($photos),
            'template' => $this->renderView('@KolyyaPhoto/Photo/partials/photos_list.html.twig',array(
                'id' => $id,
                'name' => $name,
                'photos' => $photos,
                'path' => '/'.$config['path'].'/'.$config['manager_format']
            ))
        ));
    }

    public function deleteAction(Request $request)
    {
        $photoId = $request->query->getInt('photoId');
        $name = $request->query->get('name');

        // получаем конфиг
        $config = $this->getConfig($request);
        if(!$config)
            return $this->error('no_config');

        // находим фото
        $photo = $this->getDoctrine()->getRepository($config['photo_class'])->find($photoId);
        if(!$photo)
            return $this->error('no_photo');

        // проверяем права доступа к действию
        if(!$this->get($config['check_permissions'])->canDelete($photo))
            return $this->error('not_allowed');

        // удаляем фотографию
        $PM = $this->container->get('kolyya_photo.photo_manager');
        $PM->deletePhoto($config, $photo);

        // возвращаем список фото
        $object = $photo->getObject();
        $photos = $object->getPhotos();

        return new JsonResponse(array(
            'success' => true,
            'count' => sizeof($photos),
            'template' => $this->renderView('@KolyyaPhoto/Photo/partials/photos_list.html.twig',array(
                'id' => $object->getId(),
                'name' => $name,
                'photos' => $photos,
                'path' => '/'.$config['path'].'/'.$config['manager_format']
            ))
        ));
    }

    public function sortAction(Request $request)
    {
        $id = $request->query->getInt('id',0);
        $name = $request->query->get('name');
        $sort = $request->query->getAlnum('sort','');

        // получаем конфиг
        $config = $this->getConfig($request);
        if(!$config)
            return $this->error('no_config');

        // находим объект
        $object = $this->getDoctrine()->getRepository($config['object_class'])->find($id);
        if(!$object)
            return $this->error('no_object');

        // проверяем права доступа к действию
        if(!$this->get($config['check_permissions'])->canSort($object))
            return $this->error('not_allowed');

        // получаем фотографии по id из списка сортировки
        $photos = $this->getDoctrine()->getRepository($config['photo_class'])->findBy(array(
            $name => $id,
            'id' => $sort
        ));

        // если количество для сортировки и в бд не совпало.
        if(count($photos) != count($sort))
            return $this->error('count sort');

        $photosList = [];
        $PM = $this->container->get('kolyya_photo.photo_manager');
        foreach ($photos as $photo) {
            $photosList[$photo->getId()] = $photo->getImg();
        }

        $em = $this->getDoctrine()->getManager();
        foreach($sort as $key => $item){
            $photos[$key]->setImg($photosList[$item]);

            $em->persist($photos[$key]);
            $em->flush();
        }

        return new JsonResponse(array(
            'success' => true,
        ));

    }

    private function getConfig(Request $request)
    {
        $config = null;

        $name = $request->query->get('name');

        $objectsConfig = $this->getParameter('kolyya_photo.objects');
        if(!isset($objectsConfig[$name]))
            return $this->error('no_config');
        $config = $objectsConfig[$name];

        return $config;
    }

    private function error($text)
    {
        return new JsonResponse(array(
            'success' => false,
            'error' => $text
        ));
    }
}
