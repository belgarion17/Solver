<?php
/**
 * Created by PhpStorm.
 * User: Teddy
 * Date: 09/07/2019
 * Time: 06:18
 */

declare( strict_types=1 );

namespace Solver;
define('cellSize', 40);

class SudokuSolver
{

    /*
        TODO make resolution with strategies, each strategy return the added number
    */

    private $possibilities;
    private $grid;

    public function __construct()
    {
        for ($column=1; $column<=9; $column++) {
            for ($row=1; $row<=9; $row++) {
                $this->possibilities[$column*10+$row] = $this::initCell();
                $this->grid[$column*10+$row] = null;
            }
        }
    }

    /**
     * @return array
     */
    private function initCell(): array {
        $possibilities = [];
        for ($number=1; $number<=9; $number++) {
            $possibilities[$number] = true;
        }
        return $possibilities;
    }

    /**
     * @param int $column
     * @param int $value
     */
    private function updateColumn(int $column, int $value): void {
        foreach ( $this->possibilities as $cell => $possibilitiesArray ) {
            if ($this->getCellColumn($cell) === $column && null === $this->getValue($cell) ) {
                unset($this->possibilities[$cell][$value]);
            }
        }
    }

    /**
     * @param int $row
     * @param int $value
     */
    private function updateRow(int $row, int $value): void {
        foreach ( $this->possibilities as $cell => $possibilitiesArray ) {
            if ( $this->getCellRow($cell) === $row && null === $this->getValue($cell) ) {
                unset($this->possibilities[$cell][$value]);
            }
        }
    }

    /**
     * @param int $cadran
     * @param int $value
     */
    private function updateCadran(int $cadran, int $value): void {
        foreach ( $this->possibilities as $cell => $possibilitiesArray ) {
            if ( $this->getCellCadran($cell) === $cadran && null === $this->getValue($cell) ) {
                unset($this->possibilities[$cell][$value]);
            }
        }
    }

    /**
     * @param int $cell
     *
     * @return int
     */
    public function getCellColumn(int $cell): int {
        return (int)($cell/10);
    }


    /**
     * @param int $cell
     *
     * @return int
     */
    public function getCellRow(int $cell): int {
        return $cell%10;
    }

    /**
     * @param int $cell
     *
     * @return int
     */
    public function getCellCadran(int $cell): int {
        $row = $this->getCellRow($cell);
        $column = $this->getCellColumn($cell);
        return (int)floor(($row-1)/3)*3+(int)floor(($column-1)/3)+1;
    }

    /**
     * @param int $cell
     * @param int $value
     */
    public function setValue(int $cell, int $value, bool $asTest = false): void {
        $this->grid[$cell] = $value;
        unset($this->possibilities[$cell]);
        $this->updateColumn($this->getCellColumn($cell), $value);
        $this->updateRow($this->getCellRow($cell), $value);
        $this->updateCadran($this->getCellCadran($cell), $value);
    }

    /**
     * @param int $cell
     *
     * @return int|null
     */
    private function getValue(int $cell): ?int {
        return $this->grid[$cell];
    }

    /**
     * @return int
     */
    private function majPossibilitiesToValues(): int {

        $startedPossibilities = count($this->possibilities);

        foreach ( $this->possibilities as $cell => $possibilitiesArray) {
            if ( 1 === count($possibilitiesArray) ) {
                $possibilityValue = array_values($possibilitiesArray)[0];
                $possibilityNumber = array_keys($possibilitiesArray)[0];
                if ($possibilityValue) {
                    $this->setValue($cell, $possibilityNumber);
                } else {
                    /* TODO matching case if a test is running */
                }
            }
        }

        return count($this->possibilities)-$startedPossibilities;
    }

    /**
     * @param $row
     * @param $possibilityToCheck
     *
     * @return array
     */
    private function getPossibilityOccuranceInRow($row, $possibilityToCheck): array {
        /* TODO possible to do it in less loops by checking multiple rows in the foreach */
        $occurance = 0;
        $lastCell = 0;
        foreach ($this->possibilities as $cell => $possibilitiesArray) {
            if ( $this->getCellRow($cell) === $row && isset($possibilitiesArray[$possibilityToCheck]) && $possibilitiesArray[$possibilityToCheck]) {
                $occurance++;
                $lastCell = $cell;
            }
        }
        return [
            'total' => $occurance,
            'lastOccurance' => $lastCell
        ];
    }

    /**
     * @return int
     */
    private function insertUniquePossibilitiesInRows(): int {
        $startedPossibilities = count($this->possibilities);

        for ( $row = 1; $row<=9; $row++) {
            for ( $possibilityToCheck = 1; $possibilityToCheck<=9; $possibilityToCheck++) {
                $occurances = $this->getPossibilityOccuranceInRow($row, $possibilityToCheck);
                if ( 1 === $occurances['total'] ) {
                    $this->setValue($occurances['lastOccurance'], $possibilityToCheck);
                }
            }
        }

        return count($this->possibilities)-$startedPossibilities;
    }

    /**
     * @param $column
     * @param $possibilityToCheck
     *
     * @return int
     */
    private function getPossibilityOccuranceInColumn($column, $possibilityToCheck): array {
        /* TODO possible to do it in less loops by checking multiple rows in the foreach */
        $occurance = 0;
        $lastCell = 0;
        foreach ($this->possibilities as $cell => $possibilitiesArray) {
            if ($this->getCellColumn($cell) === $column && isset($possibilitiesArray[$possibilityToCheck]) && $possibilitiesArray[$possibilityToCheck]) {
                $occurance++;
                $lastCell = $cell;
            }
        }
        return [
            'total' => $occurance,
            'lastOccurance' => $lastCell
        ];
    }

    /**
     * @return int
     */
    private function insertUniquePossibilitiesInColumns(): int {
        $startedPossibilities = count($this->possibilities);

        for ( $column = 1; $column<=9; $column++) {
            for ( $possibilityToCheck = 1; $possibilityToCheck<=9; $possibilityToCheck++) {
                $occurances = $this->getPossibilityOccuranceInColumn($column, $possibilityToCheck);
                if ( 1 === $occurances['total'] ) {
                    $this->setValue($occurances['lastOccurance'], $possibilityToCheck);
                }
            }
        }

        return count($this->possibilities)-$startedPossibilities;
    }

    /**
     * @return SudokuSolver
     */
    public function resolve(): self {

        do {
            $addedNumbers = 0;

            $addedNumbers += $this->majPossibilitiesToValues();
            $addedNumbers += $this->insertUniquePossibilitiesInRows();
            $addedNumbers += $this->insertUniquePossibilitiesInColumns();
            /* TODO strategy insertUniquePossibilitiesInCadrans */
        } while ( 0 !== count($this->possibilities) && 0 !== $addedNumbers );

        return $this;
    }

    /**
     *  Test a possibilitie and return if correct
     */
    public function makeTest(int $cell, int $testedValue): bool {

        /* TODO */

        return false;
    }

    static function showGetValuesForm(): void {
        ?>
        <form method="post">
            <table style="text-align: center; border: 1px black solid">
                <tbody style="text-align: center">
                <?php for ($row=1; $row<=9; $row++) :  ?>
                    <tr>
                        <?php for ($column=1; $column<=9; $column++) :  ?>
                            <td>
                                <input type="number" name="l<?php echo $row; ?>c<?php echo $column; ?>" style="width:<?php echo cellSize; ?>px; height:<?php echo cellSize; ?>px">
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
            <button type="submit">Envoyer</button>
        </form>
        <?php
    }

    public function showSoduku(bool $andPossibilities = false): void {
        ?>
        <table style="text-align: center; border: 1px black solid">
            <tbody>
            <?php for ($row=1; $row<=9; $row++) :  ?>
                <tr>
                    <?php for ($column=1; $column<=9; $column++) :  ?>
                        <td style="border: 1px black solid; width:<?php echo cellSize; ?>px; height:<?php echo cellSize; ?>px; ">
                            <?php
                            $value = $this->grid[$column*10+$row];

                            if ( $andPossibilities ) {
                                if ( null !== $value ) {
                                    echo '<span style="color: red; font-weight: bold">';
                                    echo $value;
                                    echo '</span>';
                                } else {
                                    echo '<table style="font-size: 10px; width: 100%; height: 100%">';
                                    echo '<tbody style="text-align: center">';
                                    $possibility = 1;
                                    for ($subrow=1; $subrow<=3; $subrow++) :
                                        echo '<tr>';
                                        for ($subcolumn=1; $subcolumn<=3; $subcolumn++) :
                                            echo '<td>';
                                            $showPossibility = $this->possibilities[10*$column+$row][$possibility] ?? false;
                                            echo $showPossibility ? $possibility : '' ;
                                            $possibility++;
                                            echo '</td>';
                                        endfor;
                                        echo '</tr>';
                                    endfor;
                                    echo '</tbody>';
                                    echo '</table>';
                                }
                            } else {
                                echo null === $value ? '*' : $value;
                            }

                            ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
            </tbody>
        </table>
        <?php
        var_dump($this->possibilities);
    }

    public function showSolvedSoduku(bool $andPossibilities = false): void {
        $this->resolve();
        $this->showSoduku($andPossibilities);
    }
}
