<?php

namespace App\Controllers;

use App\Models\M_Coin_Data;

/**
 * C_PSAR — Calculates Parabolic SAR for 5 parameter sets across all coins and timeframes.
 *
 * Sets (see docs/09_ROADMAP/09_03_psar_parameter_research.md):
 *   A — Conservative : 0.01 / 0.01 / 0.10
 *   B — Standard     : 0.02 / 0.02 / 0.20  (Wilder defaults)
 *   C — Moderate     : 0.02 / 0.02 / 0.30
 *   D — Aggressive   : 0.03 / 0.03 / 0.30
 *   E — Fast         : 0.05 / 0.05 / 0.40
 */
class C_PSAR extends BaseController
{
    private const TIMEFRAMES = ['15m', '30m', '1h', '4h', '6h', '12h'];

    private const SETS = [
        'a' => ['start' => 0.01, 'step' => 0.01, 'max' => 0.10],
        'b' => ['start' => 0.02, 'step' => 0.02, 'max' => 0.20],
        'c' => ['start' => 0.02, 'step' => 0.02, 'max' => 0.30],
        'd' => ['start' => 0.03, 'step' => 0.03, 'max' => 0.30],
        'e' => ['start' => 0.05, 'step' => 0.05, 'max' => 0.40],
    ];

    /**
     * PSAR_Batch() — Calculate all 5 PSAR sets for every coin × timeframe and save to DB.
     *
     * Each coin+timeframe sequence is processed independently (PSAR is stateful).
     * psar_af and psar_ep are stored so incremental updates can continue the sequence
     * without recalculating from scratch.
     */
    public function PSAR_Batch()
    {
        set_time_limit(0);

        $model = new M_Coin_Data();
        $coins = $model->get_list_coin();

        foreach ($coins as $coin) {
            foreach (self::TIMEFRAMES as $timeframe) {
                $rows = $model->where('id_coin', $coin['id_coin'])
                              ->where('timeframe', $timeframe)
                              ->orderBy('open_time', 'ASC')
                              ->findAll();

                if (count($rows) < 2) continue;

                // Collect all updates for this coin+timeframe across all 5 sets
                $updates = [];

                foreach (self::SETS as $key => $params) {
                    $result = $this->computePSAR($rows, $params['start'], $params['step'], $params['max']);

                    foreach ($result as $i => $psar) {
                        if ($psar === null) continue;

                        if (!isset($updates[$i])) {
                            $updates[$i] = ['id' => $rows[$i]['id']];
                        }

                        $updates[$i]["psar_{$key}_value"] = $psar['value'];
                        $updates[$i]["psar_{$key}_trend"] = $psar['bull'] ? 1 : 0;
                        $updates[$i]["psar_{$key}_af"]    = $psar['af'];
                        $updates[$i]["psar_{$key}_ep"]    = $psar['ep'];
                    }
                }

                if (!empty($updates)) {
                    $model->updateBatch(array_values($updates), 'id');
                }
            }
        }

        return redirect()->to('database/1/12h/100')->with('success', 'PSAR calculated for all 5 parameter sets!');
    }

    /**
     * computePSAR() — Standard Parabolic SAR algorithm (Wilder, 1978).
     *
     * @param array $rows     DB rows ordered ASC by open_time (must have high_price, low_price, close_price)
     * @param float $afStart  Initial acceleration factor
     * @param float $afStep   AF increment on each new extreme point
     * @param float $afMax    AF ceiling
     *
     * @return array  Same length as $rows. Index 0 = null (no prior candle).
     *                Each entry: ['value' => float, 'bull' => bool, 'af' => float, 'ep' => float]
     */
    private function computePSAR(array $rows, float $afStart, float $afStep, float $afMax): array
    {
        $n   = count($rows);
        $out = array_fill(0, $n, null);

        if ($n < 2) return $out;

        // Seed from first two candles
        $bull = (float)$rows[1]['close_price'] >= (float)$rows[0]['close_price'];
        $af   = $afStart;

        if ($bull) {
            $ep  = max((float)$rows[0]['high_price'], (float)$rows[1]['high_price']);
            $sar = min((float)$rows[0]['low_price'],  (float)$rows[1]['low_price']);
        } else {
            $ep  = min((float)$rows[0]['low_price'],  (float)$rows[1]['low_price']);
            $sar = max((float)$rows[0]['high_price'], (float)$rows[1]['high_price']);
        }

        $out[1] = ['value' => round($sar, 8), 'bull' => $bull, 'af' => round($af, 4), 'ep' => round($ep, 8)];

        for ($i = 2; $i < $n; $i++) {
            $high = (float)$rows[$i]['high_price'];
            $low  = (float)$rows[$i]['low_price'];

            $newSar = $sar + $af * ($ep - $sar);

            if ($bull) {
                // SAR must not exceed the prior two lows
                $newSar = min($newSar, (float)$rows[$i - 1]['low_price'], (float)$rows[$i - 2]['low_price']);

                if ($low <= $newSar) {
                    // Reversal to bearish
                    $bull   = false;
                    $newSar = $ep;
                    $ep     = $low;
                    $af     = $afStart;
                    // After flip, SAR must not be below the prior two highs
                    $newSar = max($newSar, (float)$rows[$i - 1]['high_price'], (float)$rows[$i - 2]['high_price']);
                } elseif ($high > $ep) {
                    $ep = $high;
                    $af = min($af + $afStep, $afMax);
                }
            } else {
                // SAR must not be below the prior two highs
                $newSar = max($newSar, (float)$rows[$i - 1]['high_price'], (float)$rows[$i - 2]['high_price']);

                if ($high >= $newSar) {
                    // Reversal to bullish
                    $bull   = true;
                    $newSar = $ep;
                    $ep     = $high;
                    $af     = $afStart;
                    // After flip, SAR must not exceed the prior two lows
                    $newSar = min($newSar, (float)$rows[$i - 1]['low_price'], (float)$rows[$i - 2]['low_price']);
                } elseif ($low < $ep) {
                    $ep = $low;
                    $af = min($af + $afStep, $afMax);
                }
            }

            $sar    = $newSar;
            $out[$i] = [
                'value' => round($sar, 8),
                'bull'  => $bull,
                'af'    => round($af, 4),
                'ep'    => round($ep, 8),
            ];
        }

        return $out;
    }
}
