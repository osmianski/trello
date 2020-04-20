<?php

namespace Osmianski\Trello;

use OsmScripts\Core\Object_;
use OsmScripts\Core\Script;
use OsmScripts\Core\Variables;

/**
 * Dependencies:
 *
 * @property Variables $variables Helper for managing script variables
 *
 * Script Variables:
 *
 * @property string $key
 * @property string $token
 *
 * Info retrieved from Trello:
 *
 * @property Board[] $boards
 */
class Trello extends Object_
{
    public $base_url = 'https://api.trello.com/1';

    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'variables': return $script->singleton(Variables::class);

            case 'key': return $this->variables->get('key');
            case 'token': return $this->variables->get('token');

            case 'boards': return $this->collection(Board::class,
                json_decode($this->get('/members/me/boards')));
        }

        return parent::default($property);
    }
    #endregion

    protected function url($route) {
        $route .= (mb_strpos($route, '?') === false) ? '?' : '&';

        return "{$this->base_url}{$route}key={$this->key}&token={$this->token}";
    }
    public function get($route) {
        if (($result = file_get_contents($this->url($route),
            false)) === false)
        {
            throw new \Exception("'GET '{$route}' failed");
        }

        return $result;
    }

    public function put($route, $value) {
        $context  = stream_context_create([
            'http' => [
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'PUT',
                'content' => json_encode($value),
            ]
        ]);

        if (($result = file_get_contents($this->url($route),
            false, $context)) === false)
        {
            throw new \Exception("'PUT '{$route}' failed");
        }

        return $result;
    }

    public function collection($class, $data) {
        $result = [];

        foreach($data as $raw) {
            $result[$raw->id] = new $class(['raw' => $raw]);
        }

        return $result;
    }

    public function whereRawValueEquals($collection, $field, $value) {
        foreach ($collection as $item) {
            if ($item->raw->$field === $value) {
                return $item;
            }
        }

        return null;
    }

    public function whereRawValuePrefixes($collection, $field, $value) {
        foreach ($collection as $item) {
            if (mb_strpos($value, $item->raw->$field) === 0) {
                return $item;
            }
        }

        return null;
    }

    public function getBoard($url) {
        return $this->whereRawValuePrefixes($this->boards,
            'shortUrl', $url);
    }
}