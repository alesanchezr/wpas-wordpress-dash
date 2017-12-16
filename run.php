<?php

    //print_r($argv);
    
    
    class Runner{
        
        var $themeName = '';
        var $themeDir = '';
        var $srcDirectory = "src/php/";
        
        function __construct($themeName){
            $this->themeName = $themeName;
            $this->themeDir = "./wp-content/themes/".$this->themeName."/";
        }
        
        function start(){
            if(!$this->themeExists()){
                $create = Runner::printWarning('The theme does not seem to exist! Do you want to create it? (Y/n)', true);
                if(strtolower($create) == 'y') $this->createTheme();
                else echo "Good bye then! \n";
            }
            else{
                $create = Runner::printWarning('I have found an active theme! Do you want to create the controller and types folder inside? (Y/n)', true);
                if(strtolower($create) == 'y') $this->updateTheme();
                else echo "Good bye then! \n";
            } 
        }
        
        function createBaseFiles(){
            echo "Creating files... \n";
            $files = ['footer.php', 'functions.php', 'header.php', 'index.php','style.css'];
            foreach($files as $file)
            {
                $content = file_get_contents('./vendor/alesanchezr/wpas-wordpress-dash/src/WPAS/CLI/templates/'.$file);
                $this->saveFile($this->themeDir.$file,$content);
            }
        }
        function createTypes(){
            
            if(is_dir($this->themeDir.$this->srcDirectory.'Types/')) return true;
            
            if (!mkdir($this->themeDir.$this->srcDirectory.'Types/', 0777, true)) {
                Runner::printError( "Fail to create the directory structure, check the directory permisions" );
            }
            $typeFiles = ['PostPostType.php'];
            foreach($typeFiles as $file){
                $content = file_get_contents('./vendor/alesanchezr/wpas-wordpress-dash/src/WPAS/CLI/templates/'.$file);
                $this->saveFile($this->themeDir.$this->srcDirectory.'Types/'.$file,$content);
            }
        }
        function createControllers(){
            if(is_dir($this->themeDir.$this->srcDirectory.'Controller/')) return true;
            
            if (!mkdir($this->themeDir.$this->srcDirectory.'Controller/', 0777, true)) {
                Runner::printError( "Fail to create the controler directory, check the directory permisions" );
            }
            $controllerFiles = ['PostController.php'];
            foreach($controllerFiles as $file){
                $content = file_get_contents('./vendor/alesanchezr/wpas-wordpress-dash/src/WPAS/CLI/templates/'.$file);
                $this->saveFile($this->themeDir.$this->srcDirectory.'Controller/'.$file,$content);
            }
        }
        function saveFile($url, $content){
            
            $myfile = fopen($url, "w") or Runner::printError("Unable to open file ".$url." \n");
            fwrite($myfile, $content);
            fclose($myfile);
            
            return $myFile;
        }
        
        function themeExists(){
            if(is_dir($this->themeDir)) return true;
            
            return false;
        }
        function updateComposer(){
            echo "Including autoload in composer... \n";        
            $composerJSON = file_get_contents('composer.json');
            $composerObj = (array) json_decode($composerJSON);
            if(empty($composerObj['autoload'])) $composerObj['autoload'] = [];
            $composerObj['autoload'] = [
                'psr-0' => [
                    "php" => $this->themeDir."src"
                ]
            ];
            $composerNew = json_encode($composerObj, JSON_PRETTY_PRINT);
            $this->saveFile('composer.json', $composerNew);
            echo shell_exec('composer dump-autoload') . "\n";
            Runner::printSuccess("The composer.json was update successfully \n");
        }
        
        
        function updateTheme(){
            echo "Updating your current theme... \n";
            $this->createTypes();
            $this->createControllers();
            $this->updateComposer();
            Runner::printSuccess("The theme was updated successfully! \n");
        }
        function createTheme(){
            
            echo "Creating theme folder $this->themeDir... \n";
            if (!mkdir($this->themeDir, 0777, true)) {
                Runner::printError( "Fail to create the directory structure, check the directory permisions" );
            }
            
            $this->createBaseFiles();        
            
            $this->createTypes();
            $this->createControllers();
            
            $this->updateComposer();
            Runner::printSuccess("The theme was created successfully \n");
            
            
        }
        
        static function printWarning($message, $input=false){
            
            $output = "\e[1;33;40m".$message."\e[0m\n";
            if(!$input){
                echo $output;
                exit;
            } 
            else return readline($output);
        }
        static function printError($message){
            
            echo "\e[1;37;41m".$message."\e[0m\n";
            exit;
            
        }
        static function printSuccess($message){
            
            echo "\e[1;37;42m".$message."\e[0m\n";
            exit;
            
        }
        
        
    }
    
    if(!is_array($argv) || empty($argv[1])) Runner::printError("You need to specify the name of the theme \n");
    $runner = new Runner($argv[1]);
    $runner->start();