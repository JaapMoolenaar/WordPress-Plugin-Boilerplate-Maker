<?php

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends App\Command
{
    protected $replaces;
    protected $protecteds;
    
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('build')

            // the short description shown while running "php bin/console list"
            ->setDescription('Generate the plugin.')
            
            ->setDefinition(
                new InputDefinition([
                    // "--pluginname=<filepath>" or "-p <filepath>"
                    new InputOption('pluginname', 'p', InputOption::VALUE_OPTIONAL),
                ])
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        
        $pluginname = $input->getOption('pluginname');
        if (!$pluginname) {
            $pluginname = $this->ask(
                'Please enter the name of the plugin (my-new-plugin):', 
                'my-new-plugin'
            );
        }

        $destinationpath = $this->getDestinationPath($pluginname);
        $sourcepath = $this->getSourcePath();
        
        $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcepath), 
            RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach($objects as $filepath => $object){
            if ($object->getFilename() == '.' 
                || $object->getFilename() == '..'
            ) {
                continue;
            }
            
            $newpath = str_replace($sourcepath, $destinationpath, $filepath);
            
            if (!$object->isDir()) { 
                $newfolder = dirname($newpath);
                
                if (!file_exists($newfolder)) {
                    mkdir($newfolder, 0777, true);
                }

                if (!$object->isDir()) { 
                    copy($filepath, $newpath);

                    $this->handleContents($pluginname, $newpath);
                }
            }
            
            $this->handleRename($pluginname, $newpath);
        }
    }
    
    protected function getDestinationPath($pluginname)
    {
        $path = _output_path($this->mainName($pluginname)).DIRECTORY_SEPARATOR;
        
        $this->writeln("Output path: $path");
        
        if (file_exists($path)) {
            if (!$this->confirm('Path already exists, overwrite (y/n)?', false)) {
                return;
            }
            
            $this->removeDir($path);
        } else {
            mkdir($path, 0777, true);
        }
        
        return $path;
    }
    
    protected function getSourcePath()
    {
        $zipfile = _output_path('master.zip');
        $zipfolder = _output_path('master').DIRECTORY_SEPARATOR;
        $masterfolder = _output_path('master/WordPress-Plugin-Boilerplate-master').DIRECTORY_SEPARATOR;
        
        $this->writeln("Source path: $masterfolder");
        
        $download = true;
        if (file_exists($zipfile)) {
            if (!$this->confirm('Master already exists ('.date('d-m-Y H:i', filemtime($zipfile)).'), download again (y/n)?', false)) {
                $download = false;
            }
        }
        
        if ($download) {
            file_put_contents($zipfile, file_get_contents('https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/archive/master.zip'));
        }
        
        if ($download || !file_exists($zipfolder)) {
            $zip = new \ZipArchive();
            $zip->open($zipfile);
            $zip->extractTo($zipfolder);
        }
        
        return $masterfolder;
    }
    
    protected function handleRename($pluginname, $filepath) 
    {   
        $replaces = $this->replaces($pluginname);
        
        $path = pathinfo($filepath, PATHINFO_DIRNAME).DIRECTORY_SEPARATOR;
        $name = pathinfo($filepath, PATHINFO_FILENAME);
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        
        $name = str_replace(array_keys($replaces), $replaces, $name);
        
        rename($filepath, $path.$name.'.'.$ext);
    }
    
    protected function handleContents($pluginname, $filepath) 
    {            
        $replaces = $this->replaces($pluginname);
        $protecteds = $this->protecteds();
        
        $filecontents = file_get_contents($filepath);
        
        // Protect the protecteds
        $filecontents = str_replace(array_keys($protecteds), $protecteds, $filecontents);
        
        // Do the replacements:
        $filecontents = str_replace(array_keys($replaces), $replaces, $filecontents);
        
        // Put back the protecteds
        $filecontents = str_replace($protecteds, array_keys($protecteds), $filecontents);
        
        file_put_contents($filepath, $filecontents);
    }
    
    protected function replaces($pluginname) 
    {   
        if (null === $this->replaces) {
            $this->replaces = [
                'plugin-name' => $this->mainName($pluginname),
                'plugin_name' => $this->snakeName($pluginname),
                'Plugin_Name' => $this->snakeNameUpper($pluginname),
            ];
        }
        
        return $this->replaces;
    }
    
    protected function protecteds() 
    {   
        if (null === $this->protecteds) {
            $this->protecteds = [
                '$plugin_name' => '{{__UNIQUENAME001__}}',
                '$this->plugin_name' => '{{__UNIQUENAME002__}}',
                'get_plugin_name' => '{{__UNIQUENAME003__}}',
            ];
        }
        
        return $this->protecteds;
    }
    
    protected function mainName($pluginname) 
    {
        return implode('-', $this->words($pluginname));
    }
    
    protected function mainNameUpper($pluginname) 
    {
        $words = $this->words($pluginname);
        $words = array_map('ucfirst', $words);
        
        return implode('-', $words);
    }
    
    protected function snakeName($pluginname) 
    {
        return implode('_', $this->words($pluginname));
    }
    
    protected function snakeNameUpper($pluginname) 
    {
        $words = $this->words($pluginname);
        $words = array_map('ucfirst', $words);
        
        return implode('_', $words);
    }
    
    protected function words($pluginname)
    {
        $pluginname = preg_replace('/([A-Z][a-z])/', ' $1', $pluginname);
        $pluginname = strtolower($pluginname);
        $pluginname = str_replace(['-', '_'], ' ', $pluginname);
        $pluginname = trim($pluginname);
        
        return explode(' ', $pluginname);
    }
    
    protected function removeDir($dir) 
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getPathname());
        }

        rmdir($dir);
    }
}