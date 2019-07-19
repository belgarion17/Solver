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
    private $availableScopes = ['row', 'column', 'cadran'];
    private $clusteredGridSettedValues;

    public function __construct()
    {
        $this->clusteredGridSettedValues = [];
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
     * @param string $scope
     *
     * @return int
     */
    public function getCellScope(int $cell, string $scope): int {
        if ( 'column' === $scope ) {
            return (int)($cell/10);
        } elseif ( 'row' === $scope ) {
            return $cell%10;
        } elseif ( 'cadran' === $scope ) {
            $row = $this->getCellScope($cell, 'row');
            $column = $this->getCellScope($cell, 'column');
            return (int)floor(($row-1)/3)*3+(int)floor(($column-1)/3)+1;
        }

        return 0;
    }

    /**
     * @param string $scope
     * @param int $scopeValue
     * @param int $value
     */
    public function updatePossibilitiesInScope(string $scope, int $scopeValue, int $value): void {
        foreach ( $this->possibilities as $cell => $possibilitiesArray ) {

            $cellScopeValue = $this->getCellScope($cell, $scope);

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
     * @param bool $asTest
     */
    public function setValue(int $cell, int $value, bool $asTest = false): void {
        if ( ! isset($this->grid[$cell]) ) {
            $this->grid[$cell] = $value;
            unset($this->possibilities[$cell]);
            foreach ( $this->availableScopes as $scope ) {
                $this->updatePossibilitiesInScope($scope, $this->getCellScope($cell, $scope), $value);
                $this->clusteredGridSettedValues[$scope][$this->getCellScope($cell, $scope)][$cell] = $value;
            }
        }
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
                if ( $this->getCellScope($cell, $scope) === $scopeValue ) {
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

    /**
     * @return int
     */
    private function setUniquePossibilitiesIntoValues(): int {
        $startedPossibilities = count($this->possibilities);

        foreach ( $this->availableScopes as $scope ) {
            $this->insertUniquePossibilitiesInScope($scope);
        }

        return count($this->possibilities)-$startedPossibilities;
    }

    /**
     * @param array $cells
     *
     * @return array
     */
    public function getCellsAppreranceBypossibilities(array $cells): array {
        $possibilitiesArray = [];

        foreach ($cells as $cell) {
            $cellPossiblities = $this->possibilities[$cell];
            foreach ($cellPossiblities as $cellPossiblity => $possible) {
                if ($possible) {
                    $possibilitiesArray[$cellPossiblity][] = $cell;
                }
            }
        }

        return $possibilitiesArray;
    }

    /**
     * @param int $pair
     * @param int $possibilityNumber
     * @param string $scope
     */
    public function dropPossibilyUsingPair(int $pair, int $possibilityNumber, string $scope): void {
        foreach ( $this->groupCellsByScope($scope, array_keys($this->possibilities))[$this->getCellScope($pair, $scope)] as $cellInScopeNotSetted ) {
            $cellInScopeNotSetted = (int)$cellInScopeNotSetted;
            if ( $this->getCellScope( $cellInScopeNotSetted, 'cadran' ) === $this->getCellScope( $pair, 'cadran' ) ) {
                continue;
            } elseif ( isset($this->possibilities[$cellInScopeNotSetted][$possibilityNumber]) && $this->possibilities[$cellInScopeNotSetted][$possibilityNumber] ) {
                unset($this->possibilities[$cellInScopeNotSetted][$possibilityNumber]);
            }
        }
    }

    /**
     * @return int
     */
    public function dropPossibilitiesByAlignPair(): int {
        $startedPossibilities = count($this->possibilities);
        $cellsGroupedByCadran = $this->groupCellsByScope('cadran', array_keys($this->possibilities));

        foreach ($cellsGroupedByCadran as $cadranNumber => $cells) {
            foreach ($this->getCellsAppreranceBypossibilities($cells) as $possibilityNumber => $cellsArray) {
                $possibilityNumber = (int)$possibilityNumber;
                $cellsWithPossibilityCount = count($cellsArray);
                if ($cellsWithPossibilityCount > 3 || 1 === $cellsWithPossibilityCount) {
                    continue;
                } else if ( 2 === $cellsWithPossibilityCount ) {
                    if ($this->getCellScope($cellsArray[0], 'row') === $this->getCellScope($cellsArray[1], 'row')) {
                        $this->dropPossibilyUsingPair($cellsArray[0], $possibilityNumber, 'row');
                    } elseif ($this->getCellScope($cellsArray[0], 'column') === $this->getCellScope($cellsArray[1], 'column')) {
                        $this->dropPossibilyUsingPair($cellsArray[0], $possibilityNumber, 'column');
                    }
                } elseif ( 3 === $cellsWithPossibilityCount ) {
                    if ($this->getCellScope($cellsArray[0], 'row') === $this->getCellScope($cellsArray[1], 'row')
                        && $this->getCellScope($cellsArray[1], 'row') === $this->getCellScope($cellsArray[2], 'row')) {
                        $this->dropPossibilyUsingPair($cellsArray[0], $possibilityNumber, 'row');
                    } elseif ($this->getCellScope($cellsArray[0], 'column') === $this->getCellScope($cellsArray[1], 'column')
                            && $this->getCellScope($cellsArray[1], 'column') === $this->getCellScope($cellsArray[2], 'column')) {
                        $this->dropPossibilyUsingPair($cellsArray[0], $possibilityNumber, 'column');
                    }
                }
            }
        }

        return $startedPossibilities-count($this->possibilities);
    }

    /**
     * @param string $scope
     * @param array $possiblePairs
     *
     * @return array
     */
    public function getAssociatedPairsByScope(string $scope, array $possiblePairs): array {
        $pairs = [];
        if ( empty($possiblePairs) ) {
            return $pairs;
        }

        $possiblePairsWithPossibilitiesGroupedByScope = [];

        $possiblePairsGroupedByScope = $this->groupCellsByScope($scope, array_keys($possiblePairs));
        foreach ($possiblePairsGroupedByScope as $ScopeValue => $cellsArray) {
            if ( 1 === count($cellsArray) ) {
                unset($possiblePairs[$cellsArray[0]]);
            }
        }

        $possiblePairsGroupedByScope = $this->groupCellsByScope($scope, array_keys($possiblePairs));
        foreach ( $possiblePairsGroupedByScope as $ScopeValue => $cellsArray ) {
            foreach ($cellsArray as $order => $cell) {
                $possiblePairsWithPossibilitiesGroupedByScope[$ScopeValue][$cell] = $this->possibilities[$cell];
            }
        }

        foreach ($possiblePairsWithPossibilitiesGroupedByScope as $ScopeValue => $cellsArrayWithPossibilities) {
            foreach ( $cellsArrayWithPossibilities as $cell1 => $possibilities1) {
                foreach ( $cellsArrayWithPossibilities as $cell2 => $possibilities2) {
                    if ($cell1 === $cell2) {
                        continue;
                    }

                    $possibilitiesValues1 = array_keys($possibilities1);
                    $possibilitiesValues2 = array_keys($possibilities2);

                    if ( $possibilitiesValues1[0] === $possibilitiesValues2[0] && $possibilitiesValues1[1] === $possibilitiesValues2[1]) {
                        $pairs[] = [$cell1, $cell2];
                    }
                }
            }
        }

        $toDelete = [];
        foreach ( $pairs as $order1 => $pair1 ) {
            foreach ( $pairs as $order2 => $pair2 ) {
                if ( $order1 === $order2 ) {
                    continue;
                }

                if ( $pair1[0] === $pair2[1] && $pair1[1] === $pair2[0] && !isset($toDelete[$order2])) {
                    $toDelete[] = $order2;
                }
            }
        }

        foreach ($toDelete as $orderToDelete) {
            unset($pairs[$orderToDelete]);
        }

        return $pairs;
    }

    /**
     * @return int
     */
    public function dropPossibilitiesByAssociatedPair() {
        $startedPossibilities = count($this->possibilities);
        $possiblePairs = [];
        $pairs = [];

        foreach ($this->possibilities as $cell => $cellPossibilities) {
            if ( 2 === count($cellPossibilities) ) {
                $possiblePairs[$cell] = $cellPossibilities;
            }
        }

        foreach ( $this->availableScopes as $scope) {
            $pairs[$scope] = $this->getAssociatedPairsByScope($scope, $possiblePairs);
        }

        foreach ( $pairs as $scope => $pairsArray ) {
            foreach ( $pairsArray as $order => $pairArray ) {
                foreach ( $this->possibilities as $cell => $possiblilities ) {
                    if ( $this->getCellScope($pairArray[0], $scope) === $this->getCellScope($cell, $scope)
                        && !in_array($cell, $pairArray)
                    ) {
                        foreach ( $this->possibilities[$pairArray[0]] as $possibility => $boolValue ) {

                            if ( isset( $this->possibilities[$cell][$possibility] ) ) {
                                unset( $this->possibilities[$cell][$possibility] );
                            }
                        }
                    }
                }
            }
        }

        return $startedPossibilities-count($this->possibilities);
    }

    /**
     * @return SudokuSolver
     */
    public function resolve(): self {

        do {
            $addedNumbers = 0;

            $addedNumbers += $this->majPossibilitiesToValues();
            $addedNumbers += $this->setUniquePossibilitiesIntoValues();
            $addedNumbers += $this->dropPossibilitiesByAlignPair();
            $addedNumbers += $this->dropPossibilitiesByAssociatedPair();

            /* TODO strategie dropPossibilitiesClosedSets() */
        } while ( 0 !== count($this->possibilities) && 0 !== $addedNumbers );

        return $this;
    }

    /**
     * @return array
     */
    public function isSolved(): array {
        $wrongCells = [];
        $missing = [];
        $wrongValue = [];

        /* Check for missing values */
        for ($row=1; $row<=9; $row++) {
            for ($column=1; $column<=9; $column++) {
                $cell = 10*$column+$row;
                if ( ! isset( $this->grid[$cell] ) ) {
                    $missing[] = $cell;
                }
            }
        }

        /* Check for incorrect values */

        foreach ( $this->availableScopes as $scope ) {
            for ($scopeValue=1; $scopeValue<=9; $scopeValue++) {
                $scopeCells = [];
                if ( isset($this->clusteredGridSettedValues[$scope]) && isset($this->clusteredGridSettedValues[$scope][$scopeValue]) ) {
                    $scopeCells = $this->clusteredGridSettedValues[$scope][$scopeValue];
                }
                $valuesInScope = array_values($scopeCells);
                $duplicateValues = array_unique(array_diff_assoc($valuesInScope, array_unique($valuesInScope)));

                foreach ( $scopeCells as $cell => $value ) {
                    if ( in_array($value, $duplicateValues) ) {
                        $wrongValue[] = $cell;
                    }
                }

            }
        }

        $wrongCells['missing'] = $missing;
        $wrongCells['wrongValue'] = $wrongValue;
        return $wrongCells;
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
            <table style="text-align: center; border: 1px black solid; margin-bottom: 40px">
                <tbody style="text-align: center">
                <?php for ($row=1; $row<=9; $row++) :  ?>
                    <tr>
                        <?php for ($column=1; $column<=9; $column++) :  ?>
                            <?php
                                $inputName = 'l'.$row.'c'.$column;
                            ?>
                            <td style="<?php echo ( $column%3 === 0 && $column !== 9 ) ? 'border-right: 2px solid black; padding-right: 3px; ' : ''; ?><?php echo ( $row%3 === 0 && $row !== 9 ) ? 'border-bottom: 2px solid black; padding-bottom: 3px;' : ''; ?>">
                                <label for="<?php echo $inputName; ?>"></label><input type="text" name="<?php echo $inputName; ?>" id="<?php echo $inputName; ?>" style="width:<?php echo cellSize; ?>px; height:<?php echo cellSize; ?>px">
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
            <button type="submit" name="calculer">Envoyer</button>
            <button type="submit" name="generer">Générer code</button>
        </form>
        <?php
    }

    /**
     * @param bool $andPossibilities
     * @param array $wrongCells
     * @param bool $showMissing
     */
    public function showSudoku(bool $andPossibilities = false, array $wrongCells = [], $showMissing = false): void {
        ?>
        <table style="text-align: center; border: 1px black solid; margin-bottom: 40px;">
            <tbody>
            <?php for ($row=1; $row<=9; $row++) :  ?>
                <tr>
                    <?php for ($column=1; $column<=9; $column++) :  ?>
                        <td style="
                                border-left:<?php echo $column%3 === 1 ? '3' : '1'; ?>px solid black;
                                <?php echo $column === 9 ? 'border-right: 3px solid black;' : ''; ?>
                                border-top:<?php echo $row%3 === 1 ? '3' : '1'; ?>px solid black;
                                <?php echo $row === 9 ? 'border-bottom: 3px solid black;' : ''; ?>
                                width:<?php echo cellSize; ?>px; height:<?php echo cellSize; ?>px;
                                ">
                            <?php
                            $cell = $column*10+$row;
                            $value = $this->grid[$cell];
                            $color = ( isset($wrongCells['wrongValue']) && in_array($cell, $wrongCells['wrongValue']) ) ? 'red' : '#00DD00';

                            if ( $andPossibilities ) {
                                if ( null !== $value ) {
                                    echo '<span style="color: '.$color.'; font-weight: bold">';
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
        if ( $showMissing ) {
            foreach ( $wrongCells['missing'] as $cell) {
                echo('missing values on column '.$this->getCellScope($cell, 'column').' row '.$this->getCellScope($cell, 'row').'<br/>');
            }
        }
    }

    /**
     * @param string $scope
     * @param array $cells
     *
     * @return array
     */
    public function groupCellsByScope(string $scope, array $cells) {
        $groupedCellsArray = [];

        foreach ( $cells as $cell ) {
            $cellScopeValue = $this->getCellScope($cell, $scope);
            $groupedCellsArray[$cellScopeValue][] = $cell;
        }

        return $groupedCellsArray;
    }

    /**
     * @param bool $andPossibilities
     * @param bool $withStart
     */
    public function showSolvedSoduku(bool $andPossibilities = false, $withStart = false): void {
        if ( $withStart ) {
            $this->showSudoku($andPossibilities, $this->isSolved());
        }
        $this->resolve();
        $this->showSudoku($andPossibilities, $this->isSolved(), false);
    }
}
