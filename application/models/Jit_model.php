<?php

class Jit_model extends CI_Model{


    function __construct()
    {
        parent::__construct();
    }

    public function get_lga($state)
    {

        $this->db->select('lga.name');
        $this->db->from('states');
		$this->db->join('lga', 'lga.state_id = states.id ','left');
		$this->db->where('states.name', $state);
       $data = $this->db->get();

     

       $html		= '';
	    $html	.= '<option value="">Select LGA</option>	';
		
		foreach ($data->result() as $row) {
			$html .= '<option value="'.$row->name.'">'.$row->name.'</option>	';
        }

        echo $html;
    
    }

    public function get_state()
    {
        $this->db->select('name');
        $this->db->from('states');

        $data = $this->db->get();

        if($data->num_rows() > 0)
        {
            return $data->result();;
        }
    }

    public function get_courier()
    {
        $this->db->select('courier_id');
        $this->db->from('couriers');

        $data = $this->db->get();

        if($data->num_rows() > 0)
        {
            return $data->result();;
        }
    }

    public function store_merchant($data)
    {
        $this->db->insert('transactions', $data);
        return $this->db->insert_id();
    }

    public function update_transaction($data)
    {
        $this->db->set('transaction_status', 'successful');
        $this->db->where('transaction_id', $data);
        $this->db->update('transactions');
    }

    public function get_transaction($where)
    {
        $this->db->select('transaction_id')->from('transactions');
        $this->db->where(['transaction_id' => $where]);
        $transaction = $this->db->get();
        if($transaction->num_rows() > 0)
        {
            return true;
        }else{
            return false;
        }
    }

    public function get_courier_details($id)
    {
        $this->db->select('courier_name, email')->from('couriers')
                ->where('courier_id', $id);
        $courier = $this->db->get();
        if($courier->num_rows() > 0) 
        {
            return $courier->row();
        }else{
            return false;
        }
    }
}
?>