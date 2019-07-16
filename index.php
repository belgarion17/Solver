<?php
/**
 * Created by PhpStorm.
 * User: Teddy
 * Date: 09/07/2019
 * Time: 06:18
 */

include __DIR__.'/SudokuSolver.php';
use \Solver\SudokuSolver;

function doTestEasy() {

    echo '<h1>Making a easy test.</h1>';

    $sudokuSolver = new SudokuSolver();

    $sudokuSolver->setValue(11, 9);
    $sudokuSolver->setValue(21, 4);
    $sudokuSolver->setValue(31, 1);
    $sudokuSolver->setValue(33, 6);
    $sudokuSolver->setValue(51, 6);
    $sudokuSolver->setValue(61, 2);
    $sudokuSolver->setValue(62, 4);
    $sudokuSolver->setValue(81, 8);
    $sudokuSolver->setValue(83, 1);
    $sudokuSolver->setValue(92, 9);
    $sudokuSolver->setValue(71, 3);

    $sudokuSolver->setValue(14, 5);
    $sudokuSolver->setValue(26, 1);
    $sudokuSolver->setValue(34, 8);
    $sudokuSolver->setValue(35, 4);
    $sudokuSolver->setValue(36, 7);
    $sudokuSolver->setValue(44, 6);
    $sudokuSolver->setValue(45, 3);
    $sudokuSolver->setValue(65, 1);
    $sudokuSolver->setValue(66, 5);
    $sudokuSolver->setValue(74, 4);
    $sudokuSolver->setValue(75, 2);
    $sudokuSolver->setValue(76, 9);
    $sudokuSolver->setValue(84, 3);
    $sudokuSolver->setValue(96, 8);

    $sudokuSolver->setValue(18, 7);
    $sudokuSolver->setValue(27, 3);
    $sudokuSolver->setValue(29, 8);
    $sudokuSolver->setValue(39, 5);
    $sudokuSolver->setValue(48, 5);
    $sudokuSolver->setValue(49, 2);
    $sudokuSolver->setValue(59, 7);
    $sudokuSolver->setValue(77, 5);
    $sudokuSolver->setValue(79, 1);
    $sudokuSolver->setValue(89, 9);
    $sudokuSolver->setValue(99, 6);

    $sudokuSolver->showSolvedSoduku(true, true);
}

function doTestMoyen() {

    echo '<h1>Making a middle test.</h1>';

    $sudokuSolver = new SudokuSolver();

    $sudokuSolver->setValue(11, 9);
    $sudokuSolver->setValue(21, 4);
    $sudokuSolver->setValue(23, 1);
    $sudokuSolver->setValue(33, 8);
    $sudokuSolver->setValue(41, 7);
    $sudokuSolver->setValue(52, 8);
    $sudokuSolver->setValue(62, 5);
    $sudokuSolver->setValue(82, 2);

    $sudokuSolver->setValue(15, 5);
    $sudokuSolver->setValue(24, 3);
    $sudokuSolver->setValue(26, 2);
    $sudokuSolver->setValue(34, 9);
    $sudokuSolver->setValue(35, 6);
    $sudokuSolver->setValue(45, 3);
    $sudokuSolver->setValue(46, 1);
    $sudokuSolver->setValue(54, 5);
    $sudokuSolver->setValue(56, 6);
    $sudokuSolver->setValue(64, 4);
    $sudokuSolver->setValue(65, 7);
    $sudokuSolver->setValue(75, 2);
    $sudokuSolver->setValue(76, 9);
    $sudokuSolver->setValue(84, 8);
    $sudokuSolver->setValue(86, 3);
    $sudokuSolver->setValue(95, 4);

    $sudokuSolver->setValue(28, 5);
    $sudokuSolver->setValue(48, 8);
    $sudokuSolver->setValue(58, 7);
    $sudokuSolver->setValue(69, 2);
    $sudokuSolver->setValue(77, 3);
    $sudokuSolver->setValue(87, 9);
    $sudokuSolver->setValue(89, 5);
    $sudokuSolver->setValue(99, 7);

    $sudokuSolver->showSolvedSoduku(true, true);
}

function doTestDifficile() {

    echo '<h1>Making a difficult test.</h1>';

    $sudokuSolver = new SudokuSolver();

    $sudokuSolver->setValue(21, 4);
    $sudokuSolver->setValue(13, 2);
    $sudokuSolver->setValue(31, 7);
    $sudokuSolver->setValue(51, 8);
    $sudokuSolver->setValue(53, 6);
    $sudokuSolver->setValue(63, 3);
    $sudokuSolver->setValue(73, 4);
    $sudokuSolver->setValue(82, 2);
    $sudokuSolver->setValue(82, 5);

    $sudokuSolver->setValue(14, 7);
    $sudokuSolver->setValue(15, 1);
    $sudokuSolver->setValue(16, 4);
    $sudokuSolver->setValue(36, 8);
    $sudokuSolver->setValue(46, 1);
    $sudokuSolver->setValue(64, 5);
    $sudokuSolver->setValue(74, 9);
    $sudokuSolver->setValue(94, 4);
    $sudokuSolver->setValue(95, 2);
    $sudokuSolver->setValue(96, 3);

    $sudokuSolver->setValue(27, 2);
    $sudokuSolver->setValue(28, 7);
    $sudokuSolver->setValue(37, 6);
    $sudokuSolver->setValue(47, 4);
    $sudokuSolver->setValue(57, 7);
    $sudokuSolver->setValue(59, 1);
    $sudokuSolver->setValue(79, 8);
    $sudokuSolver->setValue(89, 7);
    $sudokuSolver->setValue(97, 1);

    $sudokuSolver->showSolvedSoduku(true, true);
}

function doTestDifficile2() {

    echo '<h1>Making a difficult test.</h1>';

    $sudokuSolver = new SudokuSolver();

    $sudokuSolver->setValue(21, 6);
    $sudokuSolver->setValue(31, 3);
    $sudokuSolver->setValue(53, 8);
    $sudokuSolver->setValue(63, 5);
    $sudokuSolver->setValue(72, 1);
    $sudokuSolver->setValue(81, 7);
    $sudokuSolver->setValue(83, 4);
    $sudokuSolver->setValue(92, 9);
    $sudokuSolver->setValue(14, 2);
    $sudokuSolver->setValue(16, 6);
    $sudokuSolver->setValue(34, 1);
    $sudokuSolver->setValue(44, 9);
    $sudokuSolver->setValue(54, 7);
    $sudokuSolver->setValue(64, 3);
    $sudokuSolver->setValue(46, 2);
    $sudokuSolver->setValue(56, 5);
    $sudokuSolver->setValue(66, 1);
    $sudokuSolver->setValue(76, 3);
    $sudokuSolver->setValue(94, 5);
    $sudokuSolver->setValue(96, 4);
    $sudokuSolver->setValue(18, 7);
    $sudokuSolver->setValue(27, 1);
    $sudokuSolver->setValue(29, 8);
    $sudokuSolver->setValue(38, 4);
    $sudokuSolver->setValue(47, 3);
    $sudokuSolver->setValue(57, 2);
    $sudokuSolver->setValue(79, 4);
    $sudokuSolver->setValue(89, 1);

    $sudokuSolver->showSolvedSoduku(true, true);
}

function doTestTresDifficile() {

    echo '<h1>Making a very difficult test.</h1>';

    $sudokuSolver = new SudokuSolver();

    $sudokuSolver->setValue(22, 6);
    $sudokuSolver->setValue(23, 2);
    $sudokuSolver->setValue(41, 4);
    $sudokuSolver->setValue(52, 1);
    $sudokuSolver->setValue(62, 3);
    $sudokuSolver->setValue(71, 7);
    $sudokuSolver->setValue(83, 8);
    $sudokuSolver->setValue(93, 1);

    $sudokuSolver->setValue(14, 5);
    $sudokuSolver->setValue(24, 4);
    $sudokuSolver->setValue(25, 7);
    $sudokuSolver->setValue(34, 8);
    $sudokuSolver->setValue(76, 8);
    $sudokuSolver->setValue(85, 2);
    $sudokuSolver->setValue(86, 1);
    $sudokuSolver->setValue(96, 6);

    $sudokuSolver->setValue(17, 4);
    $sudokuSolver->setValue(27, 3);
    $sudokuSolver->setValue(39, 6);
    $sudokuSolver->setValue(48, 9);
    $sudokuSolver->setValue(58, 6);
    $sudokuSolver->setValue(69, 5);
    $sudokuSolver->setValue(87, 9);
    $sudokuSolver->setValue(88, 7);

    $sudokuSolver->showSolvedSoduku(true, true);
}

//doTestEasy();
doTestMoyen();
//doTestDifficile();
//doTestTresDifficile();

//if ( isset($_POST) && ! empty($_POST) ) {
//
//    $sudokuSolver = new SudokuSolver();
//
//    foreach ( $_POST as $cell => $value ) {
//        if ( "" !== $value ) {
//            $row = substr($cell, 1, 1);
//            $column = substr($cell, 3, 1);
//            $sudokuSolver->setValue($column*10+$row, (int)$value);
//        }
//    }
//
//    $sudokuSolver->showSolvedSoduku(true);
//
//} else {
//    SudokuSolver::showGetValuesForm();
//}

