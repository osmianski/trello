<?php

namespace Osmianski\Trello\Commands;

use Osmianski\Trello\Actions\ArchiveCard;
use Osmianski\Trello\Board;
use Osmianski\Trello\List_;
use Osmianski\Trello\Trello;
use OsmScripts\Core\Command;
use OsmScripts\Core\Script;

/** @noinspection PhpUnused */

/**
 * `archive` shell command class.
 *
 * Dependencies:
 *
 * @property Trello $trello
 *
 * Calculated properties:
 *
 * @property Board $source_board
 * @property Board $target_board
 * @property List_ $source_list
 */
class Archive extends Command
{
    // hard-coded constants
    public $source_board_url = 'https://trello.com/b/tnFgSJtY';
    public $source_list_name = 'Done';
    public $target_board_url = 'https://trello.com/b/z3Ql8zxP';

    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'trello': return $script->singleton(Trello::class);

            case 'source_board':
                return $this->trello->getBoard($this->source_board_url);
            case 'source_list':
                return $this->source_board->getList($this->source_list_name);
            case 'target_board':
                return $this->trello->getBoard($this->target_board_url);
        }

        return parent::default($property);
    }
    #endregion

    protected function configure() {
        // TODO: describe the command usage, arguments and options
    }

    protected function handle() {
        foreach ($this->source_list->cards as $card) {
            $action = new ArchiveCard([
                'command' => $this,
                'card' => $card,
            ]);

            $action->run();
        }
    }
}