<?php

namespace App\Models;
use CodeIgniter\Model;

class M_Coin_Data extends Model
{
    protected $table = 'btcdatadb'; // default table

    protected $table_btcdatadb = 'btcdatadb';
    protected $table_tbl_coin = 'tbl_coin';

    protected $primaryKey = 'id';
    protected $allowedFields = [
    'id', 'date', 'id_coin', 'open_time', 'open_price', 'high_price', 'low_price',
    'close_price', 'volume', 'close_time', 'quote_volume',
    'number_of_trades', 'taker_base_volume', 'taker_quote_volume',
    'ma20', 'ma50'];

    function get_list_coin()
    {
      return  $this->db->table($this->table_tbl_coin)
      ->get()->getResultArray();
	  }

    public function get_coinname_by_id($id_coin)
    {
        return $this->db->table($this->table_tbl_coin)
            ->select('coinname')
            ->where('id_coin', $id_coin)
            ->get()
            ->getRow('coinname');
    }


    function get_all_data()
    {
      return $this->table($this->table_btcdatadb)->findAll();
    }

    function get_data_by_coin_id($coin_id, $number_of_records)
    {
      return $this->where('id_coin', $coin_id)
          ->orderBy('open_time', 'ASC')
          ->findAll($number_of_records);
    }

    function get_data_for_candlestickchart($id_coin, int $limit)
    {
      $limit = (int)$limit;
      return $this->select('date, open_price, high_price, low_price, close_price')
        ->where('id_coin', $id_coin)
        ->orderBy('open_time', 'DESC')
        ->findAll($limit);
    }

    function get_data_by_coin_id_n_day($coin_id, $days)
    {
      $days = (int)$days;
      $milliseconds_in_a_day = 86400000;
      $current_time_milliseconds = round(microtime(true) * 1000);
      $start_time = $current_time_milliseconds - ($days * $milliseconds_in_a_day);

      return $this->where('id_coin', $coin_id)
          ->where('open_time >=', $start_time)
          ->orderBy('open_time', 'ASC')
          ->findAll();
    }

    function get_ma20(int $limit , $coin_id)
    {

      return $this->select('date, ma20, ma50')
          ->where('id_coin', $coin_id)
          ->orderBy('open_time', 'DESC')
          ->findAll($limit);
    }

    
}