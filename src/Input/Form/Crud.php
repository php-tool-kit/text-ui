<?php
namespace TextUI\Input\Form;

use TextUI\Enum\HAlign;
use TextUI\IO;
use TextUI\Output\HLine;
use TextUI\Screen\Command;
use TextUI\Utils;
use function count;

/**
 * CRUD for command line.
 *
 * @author everton3x
 */
class Crud
{

    public bool $running = false;
    protected readonly string $title;
    protected ?string $message = null;
    protected int $currentPage = 1;
    protected array $pages = [];
    protected array $data = [];
    protected readonly int $rowsPerPage;
    protected array $tableSpec = [];
    protected ?int $selectedRow = null;
    protected string $labelPage = 'Page';
    protected string $labelSelectedPage = 'Selected page';
    protected string $labelPreviousPage = 'Prev. page';
    protected string $labelNextPage = 'Next page';
    protected string $charSelectPage= 'p';
    protected string $charSelectNextPage= '+';
    protected string $charSelectPreviousPage= '-';
    protected string $labelSelectRow = 'Select/Deselect row';
    protected string $labelInsert = 'New';
    protected string $labelUpdate = 'Edit';
    protected string $labelDelete = 'Delete';
    protected string $labelView = 'View';
    protected string $labelExit = 'Quit';
    protected string $labelExtra = '';
    protected string $charInsert = 'N';
    protected string $charUpdate = 'E';
    protected string $charDelete = 'D';
    protected string $charView = 'V';
    protected string $charExit = 'Q';
    protected string $charExtra = '';
    protected string $unknownCommandMessage = '⚠️ Unknown command.';
    protected string $pageNotExistsMessage = '⚠ ️This page does not exist.';
    protected string $insertSuccessMessage = '🎉 New record added successfully!';
    protected string $insertFailMessage = '⚠ Failed to add new record!';
    protected string $noRecordSelectedMessage = '⚠ No record selected!';
    protected $callbackInsert = null;
    protected $callbackUpdate = null;
    protected $callbackDelete = null;
    protected $callbackView = null;
    protected $callbackExit = null;
    protected $callbackExtra = null;
    public int $minColWidth = 100;
    protected string $minColWidthMessage = 'The terminal does not have the minimum of %d columns, so the screen may not display correctly. Increase the window width to fix this.';
    



    public function __construct(string $title)
    {
        $this->title = $title;
        $this->setRowsPerPage();
        $this->callbackExit = fn(Crud $crud) => exit(0);
    }
    
    protected function checkMinColWidth(): void
    {
        if(!$this->minColWidth === 0){
            return;
        }
        if(Utils::detectScreenColumns() < $this->minColWidth){
            printf($this->minColWidthMessage, $this->minColWidth);
            exit;
        }
    }
    
    /**
     * Removes the selection of records.
     * 
     * @return Crud
     */
    public function clearSelected(): Crud
    {
        $this->selectedRow = null;
        return $this;
    }
    
    /**
     * Gets the index of the given record.
     * 
     * @param array $record
     * @return int|bool
     */
    public function getRecordIndex(array $record): int|bool
    {
        return array_search($record, $this->data, true);
    }
    
    /**
     * Configures the message for the non-existence of the selected record.
     * 
     * @param string $message
     * @return Crud
     */
    public function setNoRecrodSelectedMessage(string $message): Crud
    {
        $this->noRecordSelectedMessage = $message;
        return $this;
    }
    
    /**
     * Sets the message for successful insertion.
     * 
     * @param string $message
     * @return Crud
     */
    public function setInsertSuccessMessage(string $message): Crud
    {
        $this->insertSuccessMessage = $message;
        return $this;
    }
    
    /**
     * Configures a callback function for the exit command.
     * 
     * @param callable|null $callback Function that receives Crud as a parameter.
     * @return Crud
     */
    public function setExitCallback(?callable $callback): Crud
    {
        $this->callbackExit = $callback;
        return $this;
    }
    
    /**
     * Configures a callback function for the insert command.
     * 
     * @param callable|null $callback Function that receives Crud as a parameter.
     * @return Crud
     */
    public function setInsertCallback(?callable $callback): Crud
    {
        $this->callbackInsert = $callback;
        return $this;
    }
    
    /**
     * Configures a callback function for the update command.
     * 
     * @param callable|null $callback Function that receives Crud as a parameter.
     * @return Crud
     */
    public function setUpdateCallback(?callable $callback): Crud
    {
        $this->callbackUpdate = $callback;
        return $this;
    }
    
    /**
     * Configures a callback function for the delete command.
     * 
     * @param callable|null $callback Function that receives Crud as a parameter.
     * @return Crud
     */
    public function setDeleteCallback(?callable $callback): Crud
    {
        $this->callbackDelete = $callback;
        return $this;
    }
    
    /**
     * Configures a callback function for the view command.
     * 
     * @param callable|null $callback Function that receives Crud as a parameter.
     * @return Crud
     */
    public function setViewCallback(?callable $callback): Crud
    {
        $this->callbackView = $callback;
        return $this;
    }
    
    /**
     * Configures a callback function for the extra (defined by user) command.
     * 
     * @param callable|null $callback Function that receives Crud as a parameter.
     * @return Crud
     */
    public function setExtraCallback(?callable $callback): Crud
    {
        $this->callbackExtra = $callback;
        return $this;
    }
    
    /**
     * Sets messge for not exist page.
     * 
     * @param string $message
     * @return Crud
     */
    public function setPageNotExistsMessage(string $message): Crud
    {
        $this->pageNotExistsMessage = $message;
        return $this;
    }
    
    /**
     * Sets message for unknow command.
     * 
     * @param string $message
     * @return Crud
     */
    public function setUnknowCommandMessage(string $message): Crud
    {
        $this->unknownCommandMessage = $message;
        return $this;
    }
    
    /**
     * Sets character for extra (defined by user) command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharExtra(string $char): Crud
    {
        $this->charExtra = $char;
        return $this;
    }
    
    /**
     * Sets character for exit command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharExit(string $char): Crud
    {
        $this->charExit = $char;
        return $this;
    }
    
    /**
     * Sets character for insert command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharInsert(string $char): Crud
    {
        $this->charInsert = $char;
        return $this;
    }
    
    /**
     * Sets character for update command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharUpdate(string $char): Crud
    {
        $this->charUpdate = $char;
        return $this;
    }
    
    /**
     * Sets character for delete command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharDelete(string $char): Crud
    {
        $this->charDelete = $char;
        return $this;
    }
    
    /**
     * Sets character for view command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharView(string $char): Crud
    {
        $this->charView = $char;
        return $this;
    }
    
    /**
     * Sets label for extra (define by user) command.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelExtra(string $label): Crud
    {
        $this->labelExtra = $label;
        return $this;
    }
    
    /**
     * Sets label for exit command.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelExit(string $label): Crud
    {
        $this->labelExit = $label;
        return $this;
    }
    
    /**
     * Sets label for view command.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelView(string $label): Crud
    {
        $this->labelView = $label;
        return $this;
    }
    
    /**
     * Sets label for delete command.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelDelete(string $label): Crud
    {
        $this->labelDelete = $label;
        return $this;
    }
    
    /**
     * Sets label for update command.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelUpdate(string $label): Crud
    {
        $this->labelUpdate = $label;
        return $this;
    }
    
    /**
     * Sets label for insert command.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelInsert(string $label): Crud
    {
        $this->labelInsert = $label;
        return $this;
    }
    
    /**
     * Sets label for select row command.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelSelectRow(string $label): Crud
    {
        $this->labelSelectRow = $label;
        return $this;
    }
    
    /**
     * Sets character for select page command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharSelectPage(string $char): Crud
    {
        $this->charSelectPage = $char;
        return $this;
    }
    
    /**
     * Sets character for select next page command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharSelectNextPage(string $char): Crud
    {
        $this->charSelectNextPage = $char;
        return $this;
    }
    
    /**
     * Sets character for select previous page command.
     * 
     * @param string $char
     * @return Crud
     */
    public function setCharSelectPreviousPage(string $char): Crud
    {
        $this->charSelectPreviousPage = $char;
        return $this;
    }
    
    /**
     * Sets label for page navigation indicator.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelPage(string $label): Crud
    {
        $this->labelPage = $label;
        return $this;
    }
    
    /**
     * Sets label for current page.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelSelectedPage(string $label): Crud
    {
        $this->labelSelectedPage = $label;
        return $this;
    }
    
    /**
     * Sets label for previous page.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelPreviousPage(string $label): Crud
    {
        $this->labelPreviousPage = $label;
        return $this;
    }
    
    /**
     * Sets label for next page.
     * 
     * @param string $label
     * @return Crud
     */
    public function setLabelNextPage(string $label): Crud
    {
        $this->labelNextPage = $label;
        return $this;
    }
    
    /**
     * Sets table specification.
     * 
     * Less than or equal to 1. The total of all widths must be less than or 
     * equal to 1. Enter the width for all columns.
     * 
     * @param string $id
     * @param string $label
     * @param string|null $align
     * @param float|null $width 
     * @return Crud
     */
    public function setTableSpecFor(string $id, string $label, ?HAlign $align = null, ?float $width = null): Crud
    {
        $this->tableSpec[$id]['label'] = $label;
        if(!is_null($width)){
            $this->tableSpec[$id]['width'] = $width;
        }
        if(!is_null($align)){
            $this->tableSpec[$id]['align'] = $align;
        }
        return $this;
    }

    /**
     * Sets data for table.
     * 
     * @param array $data
     * @return Crud
     */
    public function setData(array $data): Crud
    {
        $this->pages = array_chunk($data, $this->rowsPerPage, false);
        $this->data = $data;
        return $this;
    }

    protected function setRowsPerPage(): void
    {
        $tableHeight = $this->getTableDimensions()['height'];
//        Table rows minus 1 row for:
//            Header text;
//            Header bottom row;
//            Table bottom row.
        $this->rowsPerPage = $tableHeight - 3;
    }

    /**
     * Sets message for show on table top.
     * 
     * @param string $message
     * @return Crud
     */
    public function setMessage(string $message): Crud
    {
        $this->message = $message;
        return $this;
    }
    
    /**
     * Sets min col width.
     * 
     * If greater than 0, then the user will receive a warning if the terminal 
     * has fewer columns than defined.
     * 
     * @param int $width
     * @return Crud
     */
    public function setMinColWidth(int $width): Crud
    {
        $this->minColWidth = $width;
        return $this;
    }
    
    /**
     * Set min col width alert message.
     * 
     * @param string $message
     * @return Crud
     */
    public function setMinColWidthMessage(string $message): Crud
    {
        $this->minColWidthMessage = $message;
        return $this;
    }

    /**
     * Run CRUD.
     * 
     * @return bool
     */
    public function run(): bool
    {
        $this->clearScreen();
        $this->checkMinColWidth();
        $this->drawTitle();
        $this->drawMessage();
        $this->showTable();
        $this->drawNavigationBar();
        $this->drawCommandBar();
        $this->loadUserCommand();
        return $this->running = true;
    }
    
    /**
     * Gets selected record.
     * 
     * @return array
     */
    public function getSelectedRecord(): array
    {
        $recordIndex = $this->selectedRow - 1;
        $pageIndex = $this->currentPage - 1;
        return $this->pages[$pageIndex][$recordIndex];
    }
    
    /**
     * Get data.
     * 
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
    
    protected function loadUserCommand(): void
    {
        echo "> ";
        $command = trim(IO::readRawStdin());
        $this->selectCommand($command);
        return;
    }
    
    protected function selectCommand(string|int $command): void
    {
        $matches = [];
        if(preg_match('/^[0-9]+$/', $command, $matches) === 1){
            $this->selectOrDeselectRow((int) $command);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charSelectPage).'([0-9]+)$/i', $command, $matches) === 1){
            $page = (int) str_replace($this->charSelectPage, '', $command);
            $this->goToPage($page);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charSelectPreviousPage).'([0-9]*)$/i', $command, $matches) === 1){
            $decreases = (int) str_replace($this->charSelectPreviousPage, '', $command);
            $page = $this->currentPage - $decreases;
            if($page < 1) {
                $page = 1;
            }
            $this->goToPage($page);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charSelectNextPage).'([0-9]*)$/i', $command, $matches) === 1){
            $increments = (int) str_replace($this->charSelectPreviousPage, '', $command);
            $page = $this->currentPage + $increments;
            if($page > count($this->pages)) {
                $page = count($this->pages);
            }
            $this->goToPage($page);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charInsert).'$/i', $command, $matches) === 1){
            $callback = $this->callbackInsert;
            $callback($this);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charUpdate).'$/i', $command, $matches) === 1){
            if(is_null($this->selectedRow)){
                $this->message = $this->noRecordSelectedMessage;
                return;
            }
            $callback = $this->callbackUpdate;
            $callback($this);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charDelete).'$/i', $command, $matches) === 1){
            if(is_null($this->selectedRow)){
                $this->message = $this->noRecordSelectedMessage;
                return;
            }
            $callback = $this->callbackDelete;
            $callback($this);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charView).'$/i', $command, $matches) === 1){
            if(is_null($this->selectedRow)){
                $this->message = $this->noRecordSelectedMessage;
                return;
            }
            $callback = $this->callbackView;
            $callback($this);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charExit).'$/i', $command, $matches) === 1){
            $callback = $this->callbackExit;
            $callback($this);
            return;
        }
        
        if(preg_match('/^'.preg_quote($this->charExtra).'$/i', $command, $matches) === 1){
            $callback = $this->callbackExtra;
            $callback($this);
            return;
        }
        
        $this->message = $this->unknownCommandMessage;
    }
    
    protected function goToPage(int $page): void
    {
        if(($page < 1) || ($page > count($this->pages))){
            $this->message = $this->pageNotExistsMessage;
            return;
        }
        $this->currentPage = $page;
        return;
    }
    
    protected function selectOrDeselectRow(int $rowId): void
    {
        if($rowId === $this->selectedRow){
            $this->selectedRow = null;
        } else {
            $this->selectedRow = $rowId;
        }
    }
    
    protected function getTotalPages(): int
    {
        return count($this->pages);
    }
    
    protected function drawCommandBar(): void
    {
        $btnSelectRow = "[{$this->labelSelectRow}: #]";
        $btnInsert = '';
        if(!is_null($this->callbackInsert)){
            $btnInsert = "<[{$this->charInsert}] {$this->labelInsert}>";
        }
        $btnUpdate = '';
        if(!is_null($this->callbackUpdate)){
            $btnUpdate = "<[{$this->charUpdate}] {$this->labelUpdate}>";
        }
        $btnDelete = '';
        if(!is_null($this->callbackDelete)){
            $btnDelete = "<[{$this->charDelete}] {$this->labelDelete}>";
        }
        $btnView = '';
        if(!is_null($this->callbackView)){
            $btnView = "<[{$this->charView}] {$this->labelView}>";
        }
        $btnExit = '';
        if(!is_null($this->callbackExit)){
            $btnExit = "<[{$this->charExit}] {$this->labelExit}>";
        }
        $btnExtra = '';
        if(!is_null($this->callbackExtra)){
            $btnExit = "<[{$this->charExtra}] {$this->labelExtra}>";
        }
        echo str_pad(join(' ', [$btnSelectRow, $btnView, $btnInsert, $btnUpdate, $btnDelete, $btnExtra, $btnExit]), Utils::detectScreenColumns(), ' ', STR_PAD_LEFT). PHP_EOL;
    }
    
    protected function drawNavigationBar(): void
    {
        echo "{$this->labelPage} {$this->currentPage} / {$this->getTotalPages()}";
        if($this->getTotalPages() > 1){
            echo "[{$this->labelSelectedPage}: {$this->charSelectPage}#] [{$this->labelPreviousPage}: {$this->charSelectPreviousPage}#] [{$this->labelNextPage}: {$this->charSelectNextPage}#]";
        }
            echo PHP_EOL;
    }

    protected function drawMessage(): void
    {
        if (is_null($this->message)) {
            echo ' ', PHP_EOL;
        } else {
            echo $this->message, PHP_EOL;
        }
        $this->message = null;
    }

    protected function drawTitle(): void
    {
        $line = new HLine('=');

        $line->draw();
        echo $this->title, PHP_EOL;
        $line->draw();
    }

    protected function getTableDimensions(): array
    {
        $screenCols = Utils::detectScreenColumns();
        $screenLines = Utils::detectScreenLines();
//        Screen columns minus 1 column for:
//            Left margin of the table;
//            Right margin of the table;
        $tableWidth = $screenCols - 2;
//        Screen lines minus 1 line for:
//            Top title line;
//            Title text;
//            Bottom title line;
//            Message text
//            Top table line;
//            Bottom table line;
//            Navigation options line;
//            CRUD options line;
//            CRUD command line.
        $tableHeight = $screenLines - 9;
        return ['width' => $tableWidth, 'height' => $tableHeight];
    }

    protected function showTable(): void
    {
        $line = new HLine('-');

        echo $this->getTableHeaderStr();
        $line->draw();

        echo $this->getTableRowsStr();

        $line->draw();
    }

    protected function getPageData(): array
    {
        return $this->pages[$this->currentPage - 1];
    }

    protected function getTableRowsStr(): string
    {
        $rows = $this->getPageData();
        $colWidths = $this->getTableColWidths();
        $str = '';
        $selected = $this->selectedRow;
        if(!is_null($selected)){
            $selected--;
        }
        foreach ($rows as $keyRow => $row) {
            if(is_null($selected) || $selected !== $keyRow){
                $str .= ' ';
            }else{
                $str .= '>';
            }
            $numRow = $keyRow + 1;
            $str .= str_pad($numRow, 3, ' ', STR_PAD_LEFT).' ';
            
            $arr = [];
            foreach ($row as $keyCol => $col) {
                if (in_array($keyCol, $this->getTableHeader()) === false) {
                    continue;
                }
                if (strlen($col) >= $colWidths[$keyCol] - 1) {
                    $arr[] = substr($col, 0, $colWidths[$keyCol] - 1);
                } else {
                    $arr[] = str_pad($col, $colWidths[$keyCol] - 1, ' ', $this->getAlignForCol($keyCol));
                }
            }
            $str .= join(' ', $arr) . PHP_EOL;
        }
        return $str;
    }

    protected function getTableHeaderStr(): string
    {
        $colWidths = $this->getTableColWidths();
        $str = '   # ';

        $arr = [];
        foreach ($this->getTableLabels() as $key => $item) {
            if (strlen($item) >= $colWidths[$key] - 1) {
                $arr[] = substr($item, 0, $colWidths[$key] - 1);
            } else {
                $arr[] = str_pad($item, $colWidths[$key] - 1, ' ', $this->getAlignForCol($key));
            }
        }
        $str .= join(' ', $arr);
        return $str . PHP_EOL;
    }

    protected function getAlignForCol(string $col): int
    {
        if (key_exists($col, $this->tableSpec)) {
            if (key_exists('align', $this->tableSpec[$col])) {
                switch ($this->tableSpec[$col]['align']) {
                    case HAlign::Left:
                        return STR_PAD_RIGHT;
                    case HAlign::Center:
                        return STR_PAD_BOTH;
                    case HAlign::Right:
                        return STR_PAD_LEFT;
                    default :
                        return STR_PAD_RIGHT;
                }
            } else {
                return STR_PAD_RIGHT;
            }
        }
        return STR_PAD_RIGHT;
    }

    protected function getTableColWidths(): array
    {
        if ($this->tableSpec === []) {
            $width = (int) (($this->getTableDimensions()['width']-4) / count($this->getTableHeader()));
            $widths = array_fill_keys($this->getTableHeader(), $width);
            return $widths;
        }

        $widths = [];
        foreach ($this->getTableHeader() as $key) {
            $widths[$key] = (int) (($this->getTableDimensions()['width']-4) * $this->tableSpec[$key]['width']);
        }
        return $widths;
    }

    protected function getTableHeader(): array
    {

        if ($this->tableSpec !== []) {
            return array_keys($this->tableSpec);
        }
        return array_keys($this->getPageData()[array_key_first($this->getPageData())]);
    }
    
    protected function getTableLabels(): array
    {

        if ($this->tableSpec !== []) {
            $labels = [];
            foreach ($this->tableSpec as $key => $item){
                $labels[$key] = $item['label'];
            }
            return $labels;
        }
        return array_keys($this->getPageData()[array_key_first($this->getPageData())]);
    }

    protected function clearScreen(): void
    {
        Command::send(Command::CLEAR_SCREEN);
    }
}
