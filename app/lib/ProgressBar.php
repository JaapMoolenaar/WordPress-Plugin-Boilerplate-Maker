<?php
namespace App;

use Symfony\Component\Console\Helper\ProgressBar as BaseProgressBar;

class ProgressBar extends BaseProgressBar
{
    public function setMaxSteps($max)
    {
        $setMaxStepsMethod = new \ReflectionMethod(BaseProgressBar::class, 'setMaxSteps');
        $setMaxStepsMethod->setAccessible(true);
        return $setMaxStepsMethod->invoke($this, $max);
    }
    
    public function getStepWidth()
    {
        $getStepWidthMethod = new \ReflectionMethod(BaseProgressBar::class, 'getStepWidth');
        $getStepWidthMethod->setAccessible(true);
        return $getStepWidthMethod->invoke($this);
    }
    
    public function getMaxSteps()
    {
        $maxSteps = new \ReflectionProperty(BaseProgressBar::class, 'max');
        $maxSteps->setAccessible(true);
        return $maxSteps->getValue($this);
    }
}