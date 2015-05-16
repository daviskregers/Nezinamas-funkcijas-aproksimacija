<?php
ob_start();
require_once('./test/classes/GDRenderer.php');
require 'helpers.php';

$individs = (json_decode($_REQUEST['img']));
$individs = str_replace('s:1:" "', 's:1:"+"', $individs);
$individs = unserialize($individs);

$c_id = new Counter();
$objTree = new GDRenderer(30, 10, 30, 50, 20);
$objTree = draw_tree($objTree, $individs, $c_id);
$objTree->setBGColor(array(255, 255, 255));
$objTree->setNodeColor(array(0, 128, 255));
$objTree->setLinkColor(array(0, 64, 128));
// $objTree->setNodeLinks(GDRenderer::LINK_BEZIER);
// $objTree->setNodeBorder(array(0, 128, 255), 2);

$objTree->stream();