# Kolyya Photo Bundle
Allows you to upload an unlimited number of photos for the object,
delete them and sort them. Also allows you to specify several formats for storing photos.

## Requires
* [intervention/image: >=2.3](https://packagist.org/packages/intervention/image)
* [Dropzone Js](http://www.dropzonejs.com/)
* [jQuery UI](https://jqueryui.com/)

## Installation

1. Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

    ```bash
    $ composer require kolyya/photo-bundle
    ```

2. Enable bundle in your `AppKernel.php`

    ```php
    // AppKernel.php
    public function registerBundles()
       {
           $bundles = [
           // ...
           new Kolyya\PhotoBundle\KolyyaPhotoBundle(),
           // ...
           ];
       //...

    ```

3. Define routes
    
    ```yaml
    # app/config/routing.yml
    kolyya_photo:
        resource: "@KolyyaPhotoBundle/Resources/config/routing.yml"
    ``` 

4. 
    4.1 Create an entity(ies) for photos, 
    inheriting from `Kolyya\PhotoBundle\Entity\Photo`.
    
    4.2 Set methods `getObject()` and `setObject()`

5. Add [object(s)](#object) in [Config](#Config) `config.yml`

    ```yaml
    kolyya_photo:
         objects:
             product:
              object_class: AppBundle\Entity\Product
              photo_class: AppBundle\Entity\ProductPhoto
              check_permissions: app.check_permissions
              path: images/product
              manager_format: small
              formats:
                  small:
                      resize: [120,120]
                  medium:
                      heighten: 250
                  full:
                      resize: false
                      heighten: false
             <ANOTHER_OBJECT>
             ...
    ```
    
## Usage
1. On the page where the photo editor form will be
include [Dropzone Js](http://www.dropzonejs.com/) and [jQuery UI](https://jqueryui.com/)
    ```twig
    {# Dropzone Js #}
    <link rel="stylesheet" href="{{asset('lib/dropzone/dropzone.css') }}">
    <script type="text/javascript" src="{{asset('lib/dropzone/dropzone.js') }}"></script>
    {# Jquery Ui #}
    <script type="text/javascript" src="{{ asset('lib/jquery-ui/jquery-ui.min.js') }}"></script>
    ```

2. Include a handler template with parameters `objectId`, `objectName`, `photos`
    ```twig
    {{ kolyya_photo_manager(<objectId>, <objectName>, <photos>) }}
    ```
    
## Config

### Object
*  **object_class** - Object Entity for which photos are added;
*  **photo_class** - Photo Entity;
*  **check_permissions** - _optional_ - [Service](#Check Permissions) to verify the rights 
to actions with this object;
*  **path** - Path, relative to the web directory for storing photos;
*  **manager_format** - Format of Photo, to show in handler;
*  **formats** - List of [formats](#format) to store photos.

### Format
_The format name is the folder name for this format_

* **resize** | boolean/array | optional | Fit in a specific format
(default: false)
* **heighten** | boolean/integer | optional | Fit to width
(default: false)

### Check Permissions
When you need to check the permissions, one of the methods works:
`canUpload()`, `canDelete()` or `canSort()`.

* **$object** - Product Object
* **$photo** - Photo Object
```php
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
```
This example allows everyone to do anything.
