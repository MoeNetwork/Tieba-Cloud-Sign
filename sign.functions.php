<?php
if (!defined('SYSTEM_ROOT')) { die('Insufficient Permissions'); } 

/**
 * 已弃用，下列函数仅为了兼容旧插件
 */

function getCookie($pid) { misc::getCookie($pid); }

function DoSign_Mobile($uid,$kw,$id,$pid,$fid) { misc::DoSign_Mobile($uid,$kw,$id,$pid,$fid); }

function DoSign_Default($uid,$kw,$id,$pid,$fid) { misc::DoSign_Default($uid,$kw,$id,$pid,$fid); }

function DoSign_Client($uid,$kw,$id,$pid,$fid){ misc::DoSign_Client($uid,$kw,$id,$pid,$fid); }

function DoSign_All($uid,$kw,$id,$table,$sign_mode,$pid,$fid) { misc::DoSign_All($uid,$kw,$id,$table,$sign_mode,$pid,$fid); }

function DoSign($table,$sign_mode) { misc::DoSign($table,$sign_mode); }