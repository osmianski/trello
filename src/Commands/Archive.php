<?php

namespace Osmianski\Trello\Commands;

use Osmianski\Trello\Actions\ArchiveCard;
use Osmianski\Trello\Board;
use Osmianski\Trello\List_;
use Osmianski\Trello\Trello;
use OsmScripts\Core\Command;
use OsmScripts\Core\Script;
use OsmScripts\Core\Variables;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `archive` shell command class.
 *
 * Dependencies:
 *
 * @property Variables $variables Helper for managing script variables
 * @property Trello $trello
 *
 * Calculated properties:
 *
 * @property Board $task_board
 * @property List_ $done_list
 * @property Board $done_board
 */
class Archive extends Command
{
    #region Properties
    public function default($property) {
        /* @var Script $script */
        global $script;

        switch ($property) {
            case 'variables': return $script->singleton(Variables::class);
            case 'trello': return $script->singleton(Trello::class);

            case 'task_board':
                return $this->trello->getBoard(
                    $this->input->getArgument('task-board'));
            case 'done_list':
                return $this->task_board->getList(
                    $this->input->getOption('done-list'));
            case 'done_board':
                return $this->trello->getBoard(
                    $this->input->getOption('done-board'));
        }

        return parent::default($property);
    }
    #endregion

    protected function configure() {
        parent::configure();

        $this
            ->setDescription("Archives Trello cards from Done list in the Tasks board to the Done board.")
            ->addArgument('task-board', InputArgument::REQUIRED,
                "Task board URL")
            ->addOption('done-board', null, InputOption::VALUE_REQUIRED,
                "Done board URL", $this->variables->get('done_board'))
            ->addOption('done-list', null, InputOption::VALUE_REQUIRED,
                "Done list name in the task board", 'Done');
        ;
    }

    protected function handle() {
        foreach ($this->done_list->cards as $card) {
            $action = new ArchiveCard([
                'command' => $this,
                'card' => $card,
            ]);

            $action->run();
        }
    }
}