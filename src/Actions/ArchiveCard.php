<?php

namespace Osmianski\Trello\Actions;

use Osmianski\Trello\Action;
use Osmianski\Trello\Board;
use Osmianski\Trello\Card;
use Osmianski\Trello\Commands\Archive;
use Osmianski\Trello\List_;
use OsmScripts\Core\Object_;

/**
 * Constructor-assigned properties:
 *
 * @property Archive $command
 * @property Card $card
 *
 * Calculated properties:
 *
 * @property List_ $source_list
 * @property Board $target_board
 * @property Action $move_action
 * @property array $move_date
 * @property string $target_list_name
 * @property List_ $target_list
 */
class ArchiveCard extends Object_
{
    #region Properties
    public function default($property) {
        switch ($property) {
            case 'source_list': return $this->command->done_list;
            case 'target_board': return $this->command->done_board;
            case 'move_action':
                return $this->card->getLastMoveTo($this->source_list);
            case 'move_date': return date_parse($this->move_action->date);
            case 'target_list_name':
                return sprintf("%04d.%02d",
                    $this->move_date['year'], $this->move_date['month']);
            case 'target_list':
                return $this->target_board->getList($this->target_list_name) ?:
                    $this->target_board->createList($this->target_list_name, 'top');
        }

        return parent::default($property);
    }
    #endregion

    public function run() {
        if (!$this->move_action) {
            // if card is created in `Done` list, leave it there
            return;
        }

        $this->card->update([
            'idBoard' => $this->target_board->id,
            'idList' => $this->target_list->id,
            'pos' => 'top',
            'due' => $this->move_action->date,
            'dueComplete' => true,
        ]);
    }
}