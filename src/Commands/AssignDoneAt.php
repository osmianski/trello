<?php

namespace Osmianski\Trello\Commands;

use Osmianski\Trello\Board;
use Osmianski\Trello\Card;
use Osmianski\Trello\FieldDefinition;
use Osmianski\Trello\List_;
use Osmianski\Trello\Trello;
use OsmScripts\Core\Command;
use OsmScripts\Core\Script;

/** @noinspection PhpUnused */

/**
 * `assign:done_at` shell command class.
 *
 * Dependencies:
 *
 * @property Trello $trello
 *
 * Hard-Coded Constants:
 *
 * @property string $board_url
 * @property string $list_name
 *
 * Trello data:
 *
 * @property Board $board
 * @property List_ $list
 * @property FieldDefinition $done_at_field
 */
class AssignDoneAt extends Command
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'trello': return $script->singleton(Trello::class);

            case 'board_url': return 'https://trello.com/b/tnFgSJtY';
            case 'list_name': return 'Done';

            case 'board':
                return $this->trello->getBoard($this->board_url);
            case 'list':
                return $this->board->getList($this->list_name);
            case 'done_at_field':
                return $this->board->getFieldDefinition('Done At');
        }

        return parent::default($property);
    }

    #endregion

    protected function configure() {
        // TODO: describe the command usage, arguments and options
    }

    protected function handle() {
        foreach ($this->list->cards as $card) {
            if ($card->getField($this->done_at_field)) {
                continue;
            }

            $action = $this->getMoveAction($card);
            $card->updateField($this->done_at_field, ['date' => $action->date]);
        }

    }

    protected function getMoveAction(Card $card) {
        foreach (array_reverse($card->actions) as $action) {
            if (($action->raw->data->listAfter->id ?? null) !== $this->list->id) {
                continue;
            }

            if (($action->raw->data->listBefore->id ?? null) === $this->list->id) {
                continue;
            }

            return $action;
        }

        return null;
    }
}