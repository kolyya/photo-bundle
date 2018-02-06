<?php
/**
 * Сервис
 * сохраняет фотографии
 * удаляет фотографии
 * возвращает главную - первую фотографию объекта
 */

namespace Kolyya\PhotoBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function savePhoto($config, $photo, UploadedFile $file)
    {
        // генерируем имя для фото
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        // сохраняем файл во временную папку
        $tmpPath = '../var/tmp_img/';
        $tmpFile = $tmpPath.$fileName;
        $file->move($tmpPath, $fileName);

        // сохраняем по форматам
        Image::configure(array('driver' => 'gd'));
        foreach ($config['formats'] as $key => $format){
            $image = Image::make($tmpFile);

            if($format['resize'] && is_array($format['resize']) && sizeof($format['resize']) === 2)
                $image->resize($format['resize'][0],$format['resize'][1]);

            if(is_integer($format['heighten']))
                $image->heighten($format['heighten']);

            $image->save($config['path'].'/'.$key.'/'.$fileName);
        }

        // сохраняем в БД
        $photo->setImg($fileName);
        $this->em->persist($photo);
        try {
            $this->em->flush();
        } catch (OptimisticLockException $e) {
        }

        // удаляем временный файл
        if(file_exists($tmpFile)) unlink($tmpFile);
    }

    public function deletePhoto($config, $photo)
    {
        // перебираем все папки из пути фото
        foreach (scandir($config['path']) as $item){
            if(in_array($item,array('.','..'))) continue;

            $path = $config['path'].'/'.$item.'/'.$photo->getImg();

            if(file_exists($path)) unlink($path);

        }

        $this->em->remove($photo);
        try {
            $this->em->flush();
        } catch (OptimisticLockException $e) {
        }
    }
}