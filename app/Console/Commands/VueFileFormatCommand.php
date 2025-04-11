<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/*
* This command is used to format Vue files in the specified order.
* The order is specified by the user in the format argument.
* The format argument is a string of 3 characters, each character can be s,t,l
* s: scripts, t: template, l: style
* The command will search for all Vue files in the specified folder and format them in the specified order.
* The command will ask for confirmation before updating the file.
* The command will also run test function if --test option is passed.
*/
class VueFileFormatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vuejs:format {format : has 3 char(s,t,l) order of the file format s:scripts, t:template, l:style} {--test : for run test function} {folder? : the folder to search for vue files ,empty means start in root folder,resources/js/ is root folder} {--ask : ask before updating the file}';
    
   
    /**
     * The console command description.
     *
     * @var string
     */
  
    protected $description = "Format Vue files in the specified order";
   

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get the format argument
        $format = $this->argument('format');
        $folderInput = $this->argument('folder');
        $folderInput = trim($folderInput);
        if(empty($folderInput)){
            $folderInput = '';
        }else{
            // add / to the end of the folder name if not exist
            if(substr($folderInput, -1) != '/'){
                $folderInput .= '/';
            }
            // remove / from the start of the folder name if exist
            if(substr($folderInput, 0, 1) == '/'){
                $folderInput = substr($folderInput, 1);
            }
        }
        $ask = $this->option('ask');
        $test = $this->option('test');
        $this->info('Format: ' . $format);
        $this->info('Ask: ' . $ask);
        if($test){
            $this->test($format);
            return;
        }
        // check if the format is valid
        // check if the format is 3 characters long
        // check if the format is s,t,l
        // check if the format is s,t,l in any order
        if (strlen($format) != 3) {
            $this->error('Invalid format');
            return;
        }
        if (!preg_match('/^[stl]{3}$/', $format)) {
            $this->error('Invalid format');
            return;
        }
        $folder = base_path('resources/js/'.$folderInput); // specify the folder to search for vue files
        // get all files in the folder and subfolders
    
        $dir = new \RecursiveDirectoryIterator($folder);
        $iterator = new \RecursiveIteratorIterator($dir);
        $skippedAll = false;
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() == 'vue') {
                $file = $file->getPathname();
                 
            if( $ask == 'true'){
                $this->info('Processing file: ' . $file);
                // yes or no or skip all
                $response = $this->choice('Do you want to format this file?', ['y'=>'yes', 'n'=>'no','s'=> 'skip all'], 0);
                //$response = $this->confirm('Do you want to format this file?');
                if($response == 's'){
                    break;
                    $skippedAll = true;
                    $response = 'n';
                }
            }else{
                $response = 'y';
            }
            if($response == 'n'){
                $this->info('Skipping file: ' . $file);
                continue;
            }
            
                $this->formattingFile($format,$file);
            }
        }
    
        $this->info('Files formatted successfully');
        
    }
    protected function test($format){
        $file = base_path('resources/js/Components/ui/sidebar/SidebarMenuButton.vue');
        $this->formattingFile($format,$file);
        $this->info('File formatted successfully');
    }
    function extractRootTemplate($content) {
        $templateStart = strpos($content, '<template');
        if ($templateStart === false) return '';
        
        $templateEnd = null;
        $depth = 0;
        $length = strlen($content);
        $i = $templateStart;
        
        while ($i < $length) {
            if (substr($content, $i, 9) === '<template') {
                $depth++;
                $i += 9;
            } elseif (substr($content, $i, 10) === '</template') {
                $depth--;
                $i += 10;
                if ($depth === 0) {
                    $templateEnd = strpos($content, '>', $i) + 1;
                    break;
                }
            } else {
                $i++;
            }
        }
        
        if ($templateEnd === null) return null;
        
        $templateTag = substr($content, $templateStart, $templateEnd - $templateStart);
        $templateContent = substr($templateTag, strpos($templateTag, '>') + 1);
        $templateContent = substr($templateContent, 0, strrpos($templateContent, '<'));
        
        return trim($templateContent);
    }
    
    
    protected function formattingFile($format,$file){
        
            $this->info('Processing file: ' . $file);
            $content = file_get_contents($file);
            $script = '';
            $template = '';
            $style = '';
            // I found issue when <template> tag has template tag inside it
            // so I need to use regex to get the content of the root template tag

            // get the content of the template tag
         
            /*preg_match('/<template>(.*?)<\/template>/s', $content, $matches);
            if (isset($matches[1])) {
                $template = $matches[1];
            }*/
            $template = $this->extractRootTemplate($content);
            preg_match_all('/<script(.*?)>(.*?)<\/script>/s', $content, $matches);
            if (isset($matches[0])) {
                foreach ($matches[0] as $match) {
                    if($script == '') {
                        $script = $match;
                    }
                    else {
                        $script .= "\n".$match;
                    }
                    
                }
            }
            preg_match_all('/<style(.*?)>(.*?)<\/style>/s', $content, $matches);
            if (isset($matches[0])) {
                foreach ($matches[0] as $match) {
                    if($style == '') {
                        $style = $match;
                    }
                    else {
                        $style .= "\n\r".$match;
                    }
                    
                }
            }
            // now update the file with the new format
            $content = '';
            for ($index = 0; $index < strlen($format); $index++) {
                $char = $format[$index];
                if ($char == 's') {
                    $content .= $script;
                } elseif ($char == 't') {
                    $content .= "<template>\n$template\n</template>";
                } elseif ($char == 'l') {
                    $content .= $style;
                }
                if($index!== (strlen($format)-1)) $content .= "\n\n";
            }
          
                file_put_contents($file, $content);
               
        
    }
}
