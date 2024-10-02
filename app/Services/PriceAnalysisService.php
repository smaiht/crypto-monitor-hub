<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PriceAnalysisService
{
    public function analyze($exchange, $symbol, $datetime)
    {
        $parsedDatetime = Carbon::parse($datetime);
        $tableInfo = $this->getTableInfo($parsedDatetime);

        $result = DB::connection($exchange)
            ->table($tableInfo['table'])
            ->where('symbol', $symbol)
            ->where($tableInfo['timeField'], '<=', $parsedDatetime)
            ->orderBy($tableInfo['timeField'], 'desc')
            ->first();

        return $result ? (array) $result : null;
    }

    private function getTableInfo($datetime)
    {
        $baseTableName = env('TICKER_TABLE_NAME');
        $now = Carbon::now();

        if ($datetime->diffInHours($now) <= 24) {
            return [
                'table' => $baseTableName,
                'timeField' => 'time'
            ];
        } elseif ($datetime->diffInDays($now) <= 7) {
            return [
                'table' => "{$baseTableName}_1m",
                'timeField' => 'bucket'
            ];
        } else {
            return [
                'table' => "{$baseTableName}_1h",
                'timeField' => 'bucket'
            ];
        }
    }
}
