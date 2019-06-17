<?php

namespace App\System\Games\Swertres;

class SwertresService
{
    /**
     * Bet Validity.
     *
     * Test whether a bet entry or number is valid for Swertres.
     *
     * @param $bet
     * @return boolean
     */
    public function isValidBet($bet)
    {
        if (is_numeric($bet) && strlen($bet) === 3) {
            return true;
        }
        return false;
    }

    /**
     * Permute for rambled bet.
     *
     * @param $bet
     * @return array
     */
    public function permutation($bet)
    {
        if (!$this->isValidBet($bet)) {
            return [];
        }

        $items = str_split($bet);
        $combinations = [$items];
        foreach ($this->permutationAlgorithm($items) as $permutation) {
            if (!in_array($permutation, $combinations)) {
                $combinations[] = $permutation;
            }
        }
        return $combinations;
    }

    /**
     * Count permutation.
     *
     * @param $bet
     * @return int
     */
    public function countPermutation($bet)
    {
        return count($this->permutation($bet));
    }

    /**
     * Permutation algorithm.
     *
     * Reference: https://stackoverflow.com/questions/5506888/permutations-all-possible-sets-of-numbers
     *
     * @param array $elements
     * @return \Generator
     */
    private function permutationAlgorithm(array $elements)
    {
        if (count($elements) <= 1) {
            yield $elements;
        } else {
            foreach ($this->permutationAlgorithm(array_slice($elements, 1)) as $permutation) {
                foreach (range(0, count($elements) - 1) as $i) {
                    yield array_merge(
                        array_slice($permutation, 0, $i),
                        [$elements[0]],
                        array_slice($permutation, $i)
                    );
                }
            }
        }
    }
}
