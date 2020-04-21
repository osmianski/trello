<?php

namespace Osmianski\Trello;

/**
 * @property Field[] $fields
 * @property Action[] $actions
 */
class Card extends TrelloObject
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'fields': return $this->trello->collection(Field::class,
                json_decode($this->trello->get("/cards/{$this->id}/customFieldItems")));
            case 'actions': return $this->trello->collection(Action::class,
                json_decode($this->trello->get("/cards/{$this->id}/actions")));
        }

        return parent::default($property);
    }
    #endregion

    public function getField(FieldDefinition $fieldDefinition) {
        return $this->trello->whereRawValueEquals($this->fields,
            'idCustomField', $fieldDefinition->id);
    }

    public function getLastMoveTo(List_ $list) {
        foreach (array_reverse($this->actions) as $action) {
            if (($action->raw->data->listAfter->id ?? null) !== $list->id) {
                continue;
            }

            if (($action->raw->data->listBefore->id ?? null) === $list->id) {
                continue;
            }

            return $action;
        }

        return null;
    }

    public function updateField(FieldDefinition $fieldDefinition, $value) {
        $this->trello->put("/cards/{$this->id}/" .
            "customField/{$fieldDefinition->id}/item",
            (object)['value' => (object)$value]);
    }

    public function update($values) {
        $this->trello->put("/cards/{$this->id}", (object)$values);
    }
}