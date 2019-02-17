# API Dokumentation

## Generelle Informationen zur API des Waffen-Sachkundetrainers

* Die Ausgabe erfolgt ausschließlich in JSON (utf-8)
* Es muss immer ein API Key übergeben werden
* Es muss immer eine Aktion gesetzt werden
* Wenn im Rückgabeobjekt "errorMsg" gesetzt ist, liegt ein Fehler vor
* Es wird POST, GET und FILES des Requests verarbeitet
    * POST und GET können nicht germischt genutzt nutzt werden. Ist POST gefüllt, werden GET Paramater ignoriert und umgekehrt!

### Standard Parameter

* string api_key
* string action

### Berechtigungsstufen

* APPLICATION_USERLEVEL_GUEST (int 10)
* APPLICATION_USERLEVEL_USER (int 20)
* APPLICATION_USERLEVEL_ADMIN (int 30)

## API-Methoden

* [Api](./Api.md#api)
    * [FindByLogin](./Api.md#findbylogin)
    * [RequestPasswordReset](./Api.md#requestpasswordreset)
* [Topic](./Topic.md#topic)
    * [FindById](./Topic.md#findbyid)
    * [FindAll](./Topic.md#findall)
    * [Add](./Topic.md#add)
    * [Update](./Topic.md#update)
    * [Delete](./Topic.md#delete)
* [Subtopic](./Subtopic.md#subtopic)
    * [FindById](./Subtopic.md#findbyid)
    * [FindAll](./Subtopic.md#findall)
    * [Add](./Subtopic.md#add)
    * [Update](./Subtopic.md#update)
    * [Delete](./Subtopic.md#delete)
* [Question](./Question.md#question)
    * [FindById](./Question.md#findbyid)
    * [FindByRandom](./Question.md#findbyrandom)
    * [FindAll](./Question.md#findall)
    * [Add](./Question.md#add)
    * [Update](./Question.md#update)
    * [Delete](./Question.md#delete)
* [Answere](./Answere.md#answere)
    * [FindById](./Answere.md#findbyid)
    * [FindByQuestionId](./Answere.md#findbyquestionid)
    * [FindAll](./Answere.md#findall)
    * [Add](./Answere.md#add)
    * [Update](./Answere.md#update)
    * [Delete](./Answere.md#delete)
* [Comment](./Comment.md#answere)
    * [FindById](./Comment.md#findbyid)
    * [FindByQuestionId](./Comment.md#findbyquestionid)
    * [FindAll](./Comment.md#findall)
    * [Add](./Comment.md#add)
    * [Update](./Comment.md#update)
    * [Delete](./Comment.md#delete)
* [Favourite](./Favourite.md#favourite)
    * [FindAll](./Favourite.md#findall)
    * [Add](./Favourite.md#add)
    * [Remove](./Favourite.md#remove)
* [User](./User.md#user)
    * [FindByApiKey](./User.md#findbyapikey)
    * [FindById](./User.md#findbyid)
    * [FindAll](./User.md#findall)
    * [FindByFields](./User.md#findbyfields)
    * [Add](./User.md#add)
    * [Update](./User.md#update)
    * [CreateApiKey](./User.md#createapikey)
    * [Delete](./User.md#delete)

# PHP API Klasse

Die folgende PHP Klasse stellen wir Ihnen als Beispiel frei zur Verfügung, wenn Sie Software entwickeln, die auf die API zugreifen soll.

Wichtig: Der API Zugriff ist keine Selbstverständlichkeit und stellen wir Missbrauch fest, wird der verwendete API Schlüssel gesperrt.

## Beispiel zur Verwendung

```
<?php
	// [...]
	use LetsShoot\Utility\Api;
	
	$wstrainerApi = new Api( 'https://waffensachkunde-trainer.de/Backend/Api/', $config['wstrainerApiKey'], $config['wstrainerProxyHost'], $config['wstrainerProxyPort'], $config['wstrainerProxyUser'], $config['wstrainerProxyPass'] );
	$Question = $wstrainerApi->Call('Question', 'FindById', array('id'=>123));
	$this->view->assign('Question',$Question);
	// [...]
?>
```

## PHP Klasse
	
```php
<?php
namespace LetsShoot\Utility;

/**
 * Class Api
 * @package LetsShoot\Utility
 */
class Api {
    /**
     * @var string $base_uri
     */
    private $base_uri = '';

    /**
     * @var string $api_key
     */
    private $api_key = '';

    /**
     * @var string $proxy_host
     */
    private $proxy_host = '';

    /**
     * @var string $proxy_port
     */
    private $proxy_port;

    /**
     * @var string $proxy_user
     */
    private $proxy_user;

    /**
     * @var string $proxy_pass
     */
    private $proxy_pass;

    /**
     * Api constructor.
     * @param string $api_url
     * @param string $api_key
     * @param string $proxy_host
     * @param string $proxy_port
     * @param string $proxy_user
     * @param string $proxy_pass
     */
    public function __construct(string $api_url, $api_key = '', string $proxy_host = '', string $proxy_port = '', string $proxy_user = '', string $proxy_pass = '') {
        $this->setBaseUri($api_url);
        $this->setApiKey($api_key);
        $this->setProxyHost($proxy_host);
        $this->setProxyPort($proxy_port);
        $this->setProxyUser($proxy_user);
        $this->setProxyPass($proxy_pass);
    }

    /**
     * @param string $api
     * @param string $action
     * @param array $params
     * @return mixed
     */
    public function Call(string $api, string $action, array $params = array()) {
        // Set API Key
        if(!isset($params['api_key'])) {
            $params['api_key'] = $this->getApiKey();
        }
        $params['action'] = $action;

        // Proxy configuration
        $proxy = '';
        $proxyauth = '';
        $proxy_host = $this->getProxyHost();
        $proxy_port = $this->getProxyPort();
        $proxy_user = $this->getProxyUser();
        $proxy_pass = $this->getProxyPass();
        if(!empty($proxy_host) && !empty($proxy_port)) {
            $proxy = $proxy_host . ':' . $proxy_port;
            if(!empty($proxy_user)) {
                $proxyauth = $proxy_user.':'.$proxy_pass;
            }
        }

        // Set $_FILES to params
        if($this->__hasUpload()) {
            foreach($_FILES AS $fkey => $fvals) {
                if(is_string($fvals['tmp_name']) && is_uploaded_file($fvals['tmp_name'])) {
                    $params[$fkey] = new \CURLFile($fvals['tmp_name'], $fvals['type'], $fvals['name']);
                }
                elseif (is_array($fvals['tmp_name'])) {
                    foreach( $fvals['tmp_name'] AS $key => $tmp_name) {
                        if(is_uploaded_file($tmp_name)) {
                            $params[$fkey . '[' . $key . ']'] = new \CURLFile($tmp_name, $fvals['type'][$key], $fvals['name'][$key]);
                        }
                    }
                }
            }
        }

        // Set URI
        $url = $this->getBaseUri() . $api . '/';

        // Init curl and set data
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if(!empty($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            if(!empty($proxy)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
            }
        }
        curl_setopt($ch, CURLOPT_POST, true);
        if(!$this->__hasUpload()) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        if($result !== false) {
            $result = json_decode($result);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->base_uri;
    }

    /**
     * @param string $base_uri
     */
    public function setBaseUri(string $base_uri): void
    {
        $this->base_uri = $base_uri;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->api_key;
    }

    /**
     * @param string $api_key
     */
    public function setApiKey(string $api_key): void
    {
        $this->api_key = $api_key;
    }

    /**
     * @return string
     */
    public function getProxyHost(): string
    {
        return $this->proxy_host;
    }

    /**
     * @param string $proxy_host
     */
    public function setProxyHost(string $proxy_host): void
    {
        $this->proxy_host = $proxy_host;
    }

    /**
     * @return string
     */
    public function getProxyPort(): string
    {
        return $this->proxy_port;
    }

    /**
     * @param string $proxy_port
     */
    public function setProxyPort(string $proxy_port): void
    {
        $this->proxy_port = $proxy_port;
    }

    /**
     * @return string
     */
    public function getProxyUser(): string
    {
        return $this->proxy_user;
    }

    /**
     * @param string $proxy_user
     */
    public function setProxyUser(string $proxy_user): void
    {
        $this->proxy_user = $proxy_user;
    }

    /**
     * @return string
     */
    public function getProxyPass(): string
    {
        return $this->proxy_pass;
    }

    /**
     * @param string $proxy_pass
     */
    public function setProxyPass(string $proxy_pass): void
    {
        $this->proxy_pass = $proxy_pass;
    }

    /**
     * @return bool
     */
    private function __hasUpload(): bool {
        if(isset($_FILES)) {
            foreach($_FILES AS $file) {
                if(is_array($file['size'])) {
                    foreach($file['size'] AS $size) {
                        if($size>0) {
                            return true;
                        }
                    }
                }
                else {
                    if($file['size']>0) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
?>
```
