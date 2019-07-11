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

    public function getScopeValue(int $cell, string $scope): int {
        $cellScopeValue = 0;

        if ( 'row' === $scope ) {
            $cellScopeValue = $this->getCellRow($cell);
        } elseif ( 'column' === $scope ) {
            $cellScopeValue = $this->getCellColumn($cell);
        } elseif ( 'cadran' === $scope ) {
            $cellScopeValue = $this->getCellCadran($cell);
        }

        return $cellScopeValue;
    }

    /**
     * @param string $scope
     * @param int $scopeValue
     * @param int $value
     */
    public function updatePossibilitiesInScope(string $scope, int $scopeValue, int $value): void {
        foreach ( $this->possibilities as $cell => $possibilitiesArray ) {

            $cellScopeValue = $this->getScopeValue($cell, $scope);

            if ( $cellScopeValue === $scopeValue && null === $this->getValue($cell) ) {
                unset($this->possibilities[$cell][$value]);
                if ( 1 === count($this->possibilities[$cell]) ) {
                    $this->setValue($cell, array_keys($this->possibilities[$cell])[0]);
                }
            }
        }
    }

    /**
     * @param int $cell
     * @param int $value
     */
    public function updatePossibilitiesInAllScopes(int $cell, int $value): void {
        $this->updatePossibilitiesInScope('column', $this->getCellColumn($cell), $value);
        $this->updatePossibilitiesInScope('row', $this->getCellRow($cell), $value);
        $this->updatePossibilitiesInScope('cadran', $this->getCellCadran($cell), $value);
    }

    /**
     * @param int $cell
     * @param int $value
     * @param bool $asTest
     */
    public function setValue(int $cell, int $value, bool $asTest = false): void {
        $this->grid[$cell] = $value;
        unset($this->possibilities[$cell]);
        $this->updatePossibilitiesInAllScopes($cell, $value);
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
                    continue;
                    /* TODO matching case if a test is running */
                }
            }
        }

        return count($this->possibilities)-$startedPossibilities;
    }

    /**
     * @param string $scope
     * @param int $scopeValue
     * @param $possibilityToCheck
     *
     * @return array
     */
    private function getPossibilityOccuranceInScope(string $scope, int $scopeValue, $possibilityToCheck): array {
        $occurrence = 0;
        $lastCell = 0;
        foreach ($this->possibilities as $cell => $possibilitiesArray) {
            if ( isset($possibilitiesArray[$possibilityToCheck]) && $possibilitiesArray[$possibilityToCheck]) {
                if (    ( 'row' === $scope && $this->getCellRow($cell) === $scopeValue )
                        || ( 'column' === $scope && $this->getCellColumn($cell) === $scopeValue )
                        || ( 'cadran' === $scope && $this->getCellCadran($cell) === $scopeValue ) ) {
                    $occurrence++;
                    $lastCell = $cell;
                }
            }
        }
        return [
            'total' => $occurrence,
            'lastOccurrence' => $lastCell
        ];
    }

    /**
     * @param string $scope
     *
     * @return int
     */
    private function insertUniquePossibilitiesInScope(string $scope): int {
        $startedPossibilities = count($this->possibilities);

        for ( $scopeValue = 1; $scopeValue<=9; $scopeValue++) {
            for ( $possibilityToCheck = 1; $possibilityToCheck<=9; $possibilityToCheck++) {
                $occurrences = $this->getPossibilityOccuranceInScope($scope, $scopeValue, $possibilityToCheck);
                if ( 1 === $occurrences['total'] ) {
                    $this->setValue($occurrences['lastOccurrence'], $possibilityToCheck);
                }
            }
        }

        return count($this->possibilities)-$startedPossibilities;
    }

    private function setUniquePossibilitiesIntoValues(): int {
        $startedPossibilities = count($this->possibilities);

        $this->insertUniquePossibilitiesInScope('row');
        $this->insertUniquePossibilitiesInScope('column');
        $this->insertUniquePossibilitiesInScope('cadran');

        return count($this->possibilities)-$startedPossibilities;
    }

    /**
     * @return SudokuSolver
     */
    public function resolve(): self {

        do {
            $addedNumbers = 0;

            $addedNumbers += $this->majPossibilitiesToValues();
            $addedNumbers += $this->setUniquePossibilitiesIntoValues();
            /* TODO strategie dropPossibilitiesByAlignPair() */
            /* TODO strategie dropPossibilitiesByAssociatedPair() */

        } while ( 0 !== count($this->possibilities) && 0 !== $addedNumbers );

        return $this;
    }

    /**
     * @param int $cell
     * @param int $testedValue
     *
     * @return bool
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
                            <?php
                                $inputName = 'l'.$row.'c'.$column;
                            ?>
                            <td>
                                <label for="<?php echo $inputName; ?>"></label><input type="number" name="<?php echo $inputName; ?>" id="<?php echo $inputName; ?>" style="width:<?php echo cellSize; ?>px; height:<?php echo cellSize; ?>px">
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
                                        for ($subColumn=1; $subColumn<=3; $subColumn++) :
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
        var_dump($this->groupCellsByScope('cadran', $this->possibilities));
    }

    public function groupCellsByScope(string $scope, array $cells) {
        $groupedCellsArray = [];

        foreach ( $cells as $cell => $isPossible ) {
            $cellScopeValue = $this->getScopeValue($cell, $scope);
            $groupedCellsArray[$cellScopeValue][] = $cell;
        }

        return $groupedCellsArray;
    }

    public function showSolvedSoduku(bool $andPossibilities = false): void {
        $this->resolve();
        $this->showSoduku($andPossibilities);
    }
}
