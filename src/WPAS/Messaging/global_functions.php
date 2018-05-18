<?php
use WPAS\Messaging\WPASAdminNotifier as Noti;

function wpas_notify_info($msg){
    Noti::addTransientMessage(Noti::INFO,$msg);
}
function wpas_notify_error($msg){
    Noti::addTransientMessage(Noti::ERROR,$msg);
}
function wpas_notify_success($msg){
    Noti::addTransientMessage(Noti::SUCCESS,$msg);
}
function wpas_notify_warning($msg){
    Noti::addTransientMessage(Noti::WARNING,$msg);
}