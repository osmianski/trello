Trello automation scripts. 

If my personal workflow doesn't match yours, feel free to fork and customize.

Created using [OsmScripts](https://github.com/osmscripts/osmscripts).

## Installation

1. Install this package:

        composer global require osmianski/trello

    **Note**. For development, require a `master` branch instead:

        composer global require osmianski/trello:dev-master@dev

2. `cd` to config directory. You can keep configuration settings in any directory, I recommend Composer's global installation directory:

        cd ~/.config/composer
    
3. Configure access credentials: 

        trello var key={key}
        trello var token={token}
    
    Obtain `{key}` from <https://trello.com/app-key> and `{token}` by clicking "manually generate a token" link at the beginning of the same page.

## Task Workflow

Work on tasks in some Trello board (**Tasks board**). When task is completed, move it to "Done" list on the same board.

## Archive Done Tasks

Run `trello archive` script daily. It moves all cards from "Done" list on the Tasks board to another Trello board (**Done board**): 

1. Configure the Done board:

        cd ~/.config/composer
        trello var done_board={done_board_url}
        
    Take board URL from board `Menu -> More -> Link to this board`.        
 
2. Add a cron job using `crontab -e` (if not on Linux, use your OS job scheduler):

        0   22 * * * (date && cd ~/.config/composer && ~/.config/composer/vendor/bin/trello archive {task_board_url}) >> ~/trello-archive.log

    Or run the archive script manually:

        cd ~/.config/composer
        trello archive {task_board_url}

## Configure The Done Board

1. Use calendar power-up. The archive script arranges done cards on the calendar.

2. Configure card labels the same way as in the Tasks board.     
