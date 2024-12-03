<?php

namespace TextUI\Input\Form;

use LengthException;
use TextUI\Input\EntryInterface;
use TextUI\Output\HLine;
use TextUI\Screen\Command;
use function count;

class Form {
    
    protected readonly string $title;
    protected array $entries = [];
    protected array $answers = [];
    public bool $alwaysCleanScreen = true;
    public bool $reviewAnswers = true;
    
    
    
    public function __construct(string $title)
    {
        $this->title = $title;
    }
    
    public function addEntry(string $id, EntryInterface $entry): Form
    {
        $this->entries[$id] = $entry;
        return $this;
    }
    
    public function getAnswers(): array {
        return $this->answers;
    }
    
    public function ask(): void
    {
        $this->clearScreen();
        
        $this->drawTitle();
        $this->askAnswers();
        $hline = new HLine('=');
        $hline->draw();
        
        if($this->reviewAnswers){
            $this->reviewAnswers();
        }
    }
    
    protected function reviewAnswers(): void
    {
        $this->clearScreen();
    }
    
    protected function clearScreen(): void
    {
        if($this->alwaysCleanScreen){
            Command::send(Command::CLEAR_SCREEN);
        }
    }
    
    protected function askAnswers(): void
    {
        if(count($this->entries) === 0){
            throw new LengthException('No entriy is defined for de form.');
        }
        
        foreach ($this->entries as $id => $entry){
            $this->answers[$id] = $entry->read();
        }
    }
    
    protected function drawTitle(): void
    {
        $topHLine = new HLine('=');
        $bottomHLine = new HLine('-');
        
        $topHLine->draw();
        echo $this->title, PHP_EOL;
        $bottomHLine->draw();
        
    }
}