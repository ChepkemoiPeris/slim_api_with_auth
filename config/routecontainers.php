<?php

return function($container){
$container['GuestController'] = function(){
    return new \App\Controllers\GuestController;
};
$container['AuthController'] = function(){
    return new \App\Controllers\AuthController;
};
};