<?php

namespace Osmianski\Trello;

/**
 * @property Card[] $cards
 */
class List_ extends TrelloObject
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'cards': return $this->trello->collection(Card::class,
                json_decode($this->trello->get("/lists/{$this->id}/cards")));
        }

        return parent::default($property);
    }
    #endregion

}