<?php

namespace Osmianski\Trello;

use OsmScripts\Core\Object_;
use OsmScripts\Core\Object_ as BaseObject;
use OsmScripts\Core\Script;
use OsmScripts\Core\Str;

/**
 * Dependencies
 *
 * @property Trello $trello
 *
 * Raw Data
 *
 * @property object $raw
 *
 * Properties:
 *
 * @property string $id
 */
class TrelloObject extends Object_
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'trello': return $script->singleton(Trello::class);
        }

        /* @var Str $str */
        $str = $script->singleton(Str::class);
        $camelProperty = $str->camel($property);
        if (isset($this->raw->$camelProperty)) {
            return $this->raw->$camelProperty;
        }

        return parent::default($property);
    }
    #endregion

}