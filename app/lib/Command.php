<?php
namespace App;

use Symfony\Component\Console\Command\Command AS SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Command extends SymfonyCommand
{
    protected $_input;
    protected $_output;
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;
    }
   
    /**
     * @param integer $count
     * @param string $firstmessage
     * @return ProgressBar
     */
    protected function makeProgressbar($count, $firstmessage = '') 
    {
        // create a new progress bar
        $progress = new ProgressBar($this->_output, $count);
        $progress->setBarWidth(56);

        // define a format
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %message%');
        if ($firstmessage) {
            $progress->setMessage($firstmessage);
        }
        
        // start and displays the progress bar
        $progress->start();
        
        return $progress;
    }
    
    protected function confirm($question, $default = false)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion($question, $default);

        return $helper->ask($this->_input, $this->_output, $question);
    }
    
    protected function ask($question, $default = null)
    {
        $helper = $this->getHelper('question');
        $question = new Question($question, $default);

        return $helper->ask($this->_input, $this->_output, $question);
    }
    
    public function __call($name, $arguments) {
        if (method_exists($this->_output, $name)) {
            return call_user_func_array([$this->_output, $name], $arguments);
        }
    }
}