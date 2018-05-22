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
	    $html	.= '<option value="">Merchant LGA</option>	';
		
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
        $this->db->select('courier_name');
        $this->db->from('couriers');

        $data = $this->db->get();

        if($data->num_rows() > 0)
        {
            return $data->result();;
        }
    }

    public function get_courier_id($where)
    {
        $this->db->select('courier_id')->from('couriers');
        $this->db->where(['courier_name' => $where]);
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