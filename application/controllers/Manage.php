<?php
class Manage extends CI_Controller {

	public function index()
	{
		$data['keywords'] = $this->manage_model->get_keywords();
		$data['title'] = '回复关键字';
		$this->load->view('templates/header', $data);
		$this->load->view('manage/index', $data);
		$this->load->view('templates/footer');
	}

	public function message($limit = FALSE)
	{
		if (!$limit) 
		{$limit = 30;}
		$data['message'] = $this->manage_model->get_message();
		$data['title'] = '用户消息管理';
		$this->load->view('templates/header', $data);
		$this->load->view('manage/message', $data);
		$this->load->view('templates/footer');
	}

    public function __construct()
    {
        parent::__construct();
        $this->load->model('manage_model');
        $this->load->helper('url_helper');
    }
}
