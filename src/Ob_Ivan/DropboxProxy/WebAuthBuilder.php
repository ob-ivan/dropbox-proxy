<?php
namespace Ob_Ivan\DropboxProxy;

use Dropbox\AppInfo;
use Dropbox\ArrayEntryStore;
use Dropbox\WebAuth;

class WebAuthBuilder
{
    /**
     * Инициализирует билдер необходимыми для его работы параметрами.
     *
     *  @param  string  $appInfoFilename    Путь до файла, содержащего ключ и секрет приложения.
     *  @param  string  $clientIdentifier   Имя приложения для подстановки в заголовки запроса.
     *  @param  string  $redirectUri        Адрес, на который страница авторизации будет переадресовывать.
     *  @param  array   $storage            Массив или ArrayAccess-объект, который может долго хранить csrf-токены.
     *  @param  string  $csrfTokenKey       Ключ, по которому будут сохраняться csrf-токены.
    **/
    public function __construct(
        $appInfoFilename,
        $clientIdentifier,
        $redirectUri,
        $storage,
        $csrfTokenKey
    ) {
        $this->appInfoFilename  = $appInfoFilename;
        $this->clientIdentifier = $clientIdentifier;
        $this->redirectUri      = $redirectUri;
        $this->storage          = $storage;
        $this->csrfTokenKey     = $csrfTokenKey;
    }

    public function getWebAuth()
    {
        return new WebAuth(
            AppInfo::loadFromJsonFile($this->appInfoFilename),
            $this->clientIdentifier,
            $this->redirectUri,
            new ArrayEntryStore($this->storage, $this->csrfTokenKey)
        );
   }
}
