<?php

namespace App\Models;
use CodeIgniter\Model;

class M_Coin_Data extends Model
{
    protected $table = 'btcdatadb';

    protected $table_btcdatadb = 'btcdatadb';
    protected $table_tbl_coin = 'tbl_coin';

    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'id_coin', 'timeframe', 'date', 'open_time',
        'open_price', 'high_price', 'low_price', 'close_price',
        'volume', 'close_time', 'quote_volume',
        'number_of_trades', 'taker_base_volume', 'taker_quote_volume',
        'ma20', 'ma50', 'crossed_ma20_ma50',
    ];

    public function get_list_coin()
    {
        return $this->db->table($this->table_tbl_coin)->get()->getResultArray();
    }

    public function get_coinname_by_id($id_coin)
    {
        return $this->db->table($this->table_tbl_coin)
            ->select('coinname')
            ->where('id_coin', $id_coin)
            ->get()
            ->getRow('coinname');
    }

    public function get_all_data()
    {
        return $this->table($this->table_btcdatadb)->findAll();
    }

    public function get_data_by_coin_id($coin_id, $number_of_records, $timeframe = '12h')
    {
        return $this->where('id_coin', $coin_id)
            ->where('timeframe', $timeframe)
            ->orderBy('open_time', 'ASC')
            ->findAll($number_of_records);
    }

    public function get_data_for_candlestickchart($id_coin, int $limit, string $timeframe = '12h')
    {
        $limit = (int)$limit;
        return $this->select('date, open_price, high_price, low_price, close_price')
            ->where('id_coin', $id_coin)
            ->where('timeframe', $timeframe)
            ->orderBy('open_time', 'DESC')
            ->findAll($limit);
    }

    public function get_data_by_coin_id_n_day($coin_id, $days, string $timeframe = '12h')
    {
        $days = (int)$days;
        $start = round(microtime(true) * 1000) - ($days * 86400000);
        return $this->where('id_coin', $coin_id)
            ->where('timeframe', $timeframe)
            ->where('open_time >=', $start)
            ->orderBy('open_time', 'ASC')
            ->findAll();
    }

    public function get_ma20(int $limit, $coin_id, string $timeframe = '12h')
    {
        return $this->select('date, ma20, ma50')
            ->where('id_coin', $coin_id)
            ->where('timeframe', $timeframe)
            ->orderBy('open_time', 'DESC')
            ->findAll($limit);
    }

    // Single query: INSERT IGNORE INTO btcdatadb (...) VALUES (...),(...),...
    // Uses DB-level unique key (id_coin, timeframe, open_time) to skip duplicates.
    public function insertBatchIgnore(array $rows): void
    {
        if (empty($rows)) return;
        $fields   = array_keys($rows[0]);
        $cols     = implode(', ', array_map(fn($f) => "`$f`", $fields));
        $ph       = '(' . implode(', ', array_fill(0, count($fields), '?')) . ')';
        $values   = implode(', ', array_fill(0, count($rows), $ph));
        $bindings = [];
        foreach ($rows as $row) {
            foreach ($row as $v) $bindings[] = $v;
        }
        $this->db->query("INSERT IGNORE INTO `{$this->table}` ($cols) VALUES $values", $bindings);
    }
}
