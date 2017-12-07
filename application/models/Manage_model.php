<?php
class Manage_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function get_keywords($slug = FALSE)
	{
	    if ($slug === FALSE)
	    {
	        $query = $this->db->get('keywords');
	        return $query->result_array();
	    }

	    $query = $this->db->get_where('keywords', array('keyword' => $slug));
	    return $query->row_array();
	}
	public function get_message()
	{
	    $query = $this->db->get('message');
	    return $query->result_array();
	}
}
