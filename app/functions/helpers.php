<?php

function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die();
}

function flash(string $index, string $message)
{
    return \App\Services\FlashMessageService::add($index, $message);
}

function error($message)
{
    return '<div class="alert alert-danger" role="alert"><strong>' . $message . '</strong></div>';
}

function success($message)
{
    return '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>' . $message . '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}
