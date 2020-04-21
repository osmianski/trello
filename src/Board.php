<?php

namespace Osmianski\Trello;

/**
 * @property FieldDefinition[] $field_definitions
 * @property List_[] $lists
 */
class Board extends TrelloObject
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'field_definitions': return $this->trello->collection(FieldDefinition::class,
                json_decode($this->trello->get("/boards/{$this->id}/customFields")));
            case 'lists': return $this->trello->collection(List_::class,
                json_decode($this->trello->get("/boards/{$this->id}/lists")));
        }

        return parent::default($property);
    }
    #endregion

    public function getFieldDefinition($name) {
        return $this->trello->whereRawValueEquals($this->field_definitions,
            'name', $name);
    }

    public function getList($name) {
        return $this->trello->whereRawValueEquals($this->lists,
            'name', $name);
    }

    public function createList($name, string $pos = 'bottom') {
        $this->trello->post("/lists?name=" . urlencode($name) .
            "&idBoard={$this->id}&pos={$pos}");

        unset($this->lists);

        return $this->getList($name);
    }
}