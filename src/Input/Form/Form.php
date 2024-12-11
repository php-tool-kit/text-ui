<?php
namespace TextUI\Input\Form;

use DateTime;
use InvalidArgumentException;
use LengthException;
use TextUI\Input\EntryInterface;
use TextUI\Output\HLine;
use TextUI\Output\Table;
use TextUI\Screen\Command;
use function count;

/**
 * Class Form
 *
 * Handles user input forms in a CLI environment.
 *
 * @package TextUI\Input\Form
 * @author everton3x
 */
class Form
{

    /**
     * @var string $title The title of the form.
     */
    protected readonly string $title;

    /**
     * @var array $entries Stores the form entries.
     */
    protected array $entries = [];

    /**
     * @var array $answers Stores the user's answers.
     */
    protected array $answers = [];

    /**
     * @var bool $alwaysCleanScreen Determines if the screen should be cleared.
     */
    public bool $alwaysCleanScreen = true;

    /**
     * @var bool $reviewAnswers Determines if the answers should be reviewed.
     */
    public bool $reviewAnswers = true;

    /**
     * @var string $titleReview The title displayed during review.
     */
    protected string $titleReview = 'The data provided are:';

    /**
     * @var array $btnReviewRestartForm Button configuration for restarting the form.
     */
    protected array $btnReviewRestartForm = ['R', 'Restart form'];

    /**
     * @var array $btnReviewCancelForm Button configuration for canceling the form.
     */
    protected array $btnReviewCancelForm = ['C', 'Cancel form'];

    /**
     * @var array $btnReviewSaveForm Button configuration for saving the form.
     */
    protected array $btnReviewSaveForm = ['S', 'Save data'];

    /**
     * @var string $dateFormatReview Date format used during review.
     */
    protected string $dateFormatReview = 'Y-m-d';

    /**
     * Form constructor.
     *
     * @param string $title The title of the form.
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }
    
    /**
     * Sets defaults values for entries.
     * 
     * @param array $defaults Array with entry id and default value.
     * @return Form
     */
    public function setDefaultsValues(array $defaults): Form
    {
        foreach($defaults as $id => $value){
            if(key_exists($id, $this->entries) && method_exists($this->entries[$id], 'setDefault')){
                $this->entries[$id]->setDefault($value);
            }
        }
        return $this;
    }

    /**
     * Sets the date format for reviewing answers.
     *
     * @param string $format The date format.
     * @return Form
     */
    public function setDateFormatReview(string $format): Form
    {
        $this->dateFormatReview = $format;
        return $this;
    }

    /**
     * Sets the review title.
     *
     * @param string $title The review title.
     * @return Form
     */
    public function setTitleReview(string $title): Form
    {
        $this->titleReview = $title;
        return $this;
    }

    /**
     * Sets the button configuration for restarting the form.
     *
     * @param array $btn The button configuration.
     * @return Form
     */
    public function setBtnReviewRestartForm(array $btn): Form
    {
        $this->btnReviewRestartForm = $btn;
        return $this;
    }

    /**
     * Sets the button configuration for canceling the form.
     *
     * @param array $btn The button configuration.
     * @return Form
     */
    public function setBtnReviewCancelForm(array $btn): Form
    {
        $this->btnReviewCancelForm = $btn;
        return $this;
    }

    /**
     * Sets the button configuration for saving the form.
     *
     * @param array $btn The button configuration.
     * @return Form
     */
    public function setBtnReviewSaveForm(array $btn): Form
    {
        $this->btnReviewSaveForm = $btn;
        return $this;
    }

    /**
     * Adds an entry to the form.
     *
     * @param string $id The ID of the entry.
     * @param EntryInterface $entry The entry object.
     * @return Form
     */
    public function addEntry(string $id, EntryInterface $entry): Form
    {
        $this->entries[$id] = $entry;
        return $this;
    }

    /**
     * Retrieves the user's answers.
     *
     * @return array The user's answers.
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * Displays the form, collects input, and reviews answers if enabled.
     *
     * @return bool Whether the form was successfully submitted.
     */
    public function ask(): bool
    {
        $this->clearScreen();

        $this->drawTitle();
        $this->askAnswers();
        $hline = new HLine('=');
        $hline->draw();

        $return = true;
        if ($this->reviewAnswers) {
            $return = $this->reviewAnswers();
        }
        return $return;
    }

    /**
     * Reviews the user's answers and provides options.
     *
     * @return bool Whether the form was successfully submitted.
     */
    protected function reviewAnswers(): bool
    {
        $this->clearScreen();
        $topHLine = new HLine('=');
        $bottomHLine = new HLine('-');

        $topHLine->draw();

        $data = [];
        foreach ($this->entries as $id => $entry) {
            if($entry instanceof \TextUI\Input\HiddenEntry){
                continue;
            }
            $data[$id] = [
                $entry->label,
                $this->parseAnswer($this->answers[$id])
            ];
        }

        $table = new Table($data);
        $table->setIntersectionChar(' ');
        $table->setSimpleHorizontalBorderChar(' ');
        $table->setSimpleVerticalBorderChar(' ');
        $table->setSpecialHorizontalBorderChar(' ');
        $table->draw();

        $bottomHLine->draw();

        $action = $this->drawButtons();

        if ($action === strtolower($this->btnReviewSaveForm[0])) {
            return true;
        } elseif (($action === strtolower($this->btnReviewCancelForm[0]))) {
            $this->answers = [];
            return false;
        } elseif (($action === strtolower($this->btnReviewRestartForm[0]))) {
            return $this->ask();
        } else {
            throw new InvalidArgumentException('Invalid option.');
        }
    }

    /**
     * Parses an answer for display.
     *
     * @param string|int|float|array|DateTime $answer The answer to parse.
     * @return string The parsed answer.
     */
    protected function parseAnswer(string|int|float|array|DateTime $answer): string
    {
        if ($answer instanceof DateTime) {
            return $this->parseAnswerForDateTime($answer);
        }

        if (is_string($answer)) {
            return $answer;
        }

        if (is_numeric($answer)) {
            return $answer;
        }

        if (is_array($answer)) {
            return $this->parseAnswerForArray($answer);
        }

        throw new InvalidArgumentException('Entry is invalid type.');
    }

    /**
     * Parses an array answer for display.
     *
     * @param array $answer The array answer.
     * @return string The parsed answer.
     */
    protected function parseAnswerForArray(array $answer): string
    {
        return join(', ', $answer);
    }

    /**
     * Parses a DateTime answer for display.
     *
     * @param DateTime $answer The DateTime answer.
     * @return string The parsed answer.
     */
    protected function parseAnswerForDateTime(DateTime $answer): string
    {
        return $answer->format($this->dateFormatReview);
    }

    /**
     * Displays the review buttons and captures user input.
     *
     * @return string The action selected by the user.
     */
    protected function drawButtons(): string
    {
        printf(
            "[%s] %s\t[%s] %s\t[%s] %s : ",
            $this->btnReviewSaveForm[0],
            $this->btnReviewSaveForm[1],
            $this->btnReviewRestartForm[0],
            $this->btnReviewRestartForm[1],
            $this->btnReviewCancelForm[0],
            $this->btnReviewCancelForm[1]
        );
        $action = strtolower(trim(fgets(STDIN)));

        if (
            $action === strtolower($this->btnReviewSaveForm[0]) || $action === strtolower($this->btnReviewRestartForm[0]) || $action === strtolower($this->btnReviewCancelForm[0])
        ) {
            return $action;
        }

        $this->drawButtons();
    }

    /**
     * Clears the screen if the alwaysCleanScreen property is true.
     *
     * @return void
     */
    protected function clearScreen(): void
    {
        if ($this->alwaysCleanScreen) {
            Command::send(Command::CLEAR_SCREEN);
        }
    }

    /**
     * Asks the user for answers to the form entries.
     *
     * @return void
     */
    protected function askAnswers(): void
    {
        if (count($this->entries) === 0) {
            throw new LengthException('No entry is defined for the form.');
        }

        foreach ($this->entries as $id => $entry) {
            $this->answers[$id] = $entry->read();
        }
    }

    /**
     * Displays the title of the form.
     *
     * @return void
     */
    protected function drawTitle(): void
    {
        $topHLine = new HLine('=');
        $bottomHLine = new HLine('-');

        $topHLine->draw();
        echo $this->title, PHP_EOL;
        $bottomHLine->draw();
    }
}
