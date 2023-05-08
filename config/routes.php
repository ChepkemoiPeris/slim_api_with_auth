<?php

$app->post("/create-guest","GuestController:createGuest");

$app->get("/view-guests" ,"GuestController:viewGuests");

$app->get("/get-single-guest/{id}","GuestController:getSingleGuest");

$app->patch("/edit-single-guest/{id}","GuestController:editGuest");

$app->delete("/delete-guest/{id}","GuestController:deleteGuest");

$app->get("/count-guests" ,"GuestController:countGuests");


$app->group("/auth",function() use ($app){

    $app->post("/login","AuthController:Login");
    $app->post("/register","AuthController:Register");
});