<?php

spl_autoload_extensions(".php");
spl_autoload_register(function($name) {
    $filepath = __DIR__ . "/../../" . str_replace('\\', '/', $name) . ".php";
    require_once $filepath;
});

$commands = include "Commands/registry.php";

// 第2引数は実行するコマンド
$inputCommand = $argv[1];

// PHPでそれらをインスタンス化できるすべてのコマンドクラス名をループ
foreach ($commands as $commandClass) {
    $alias = $commandClass::getAlias();

    if($inputCommand === $alias){
        if(in_array('--help',$argv)){
            fwrite(STDOUT, $commandClass::getHelp());
            exit(0);
        }
        else{
            $command = new $commandClass();
            $result = $command->execute();
            exit($result);
        }
    }
}

fwrite(STDOUT,"Failed to run any commands\n");
