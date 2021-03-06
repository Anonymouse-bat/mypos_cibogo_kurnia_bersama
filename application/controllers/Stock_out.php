<?php defined('BASEPATH') or exit('No direct script access allowed');

class Stock_out extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		check_not_login();
		$this->load->library('form_validation');
		$this->load->model(['Items_m', 'Stock_out_m']);
	}

	public function index()
	{
		$data['row'] = $this->Stock_out_m->get()->result();
		$this->template->load('v_template', 'transaksi/stock_out/v_stock_out', $data);
	}

	public function stock_out_add()
	{
		$item        = $this->Items_m->get()->result();

		$data = [
			'row' => $item,
		];

		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		$this->form_validation->set_rules('name_items', 'Nama Items', 'trim|required');
		$this->form_validation->set_rules('qty_items', 'Qty Items', 'trim|required');
		$this->form_validation->set_rules('qty_stock_out', 'Qty Stock out', 'trim|required');
		$this->form_validation->set_rules('detail', 'Detail', 'trim|required');

		if ($this->form_validation->run() == FALSE) {
			$this->template->load('v_template', 'transaksi/stock_out/v_stock_out_add', $data);
		} else {

			$post = $this->input->post(NULL, TRUE);

			$this->Stock_out_m->add($post);
			$this->Stock_out_m->update_stock_out_item($post);

			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-success"><strong>Success!</strong> Data berhasil disimpan</div>');
				redirect('stock_out');
			}
		}
	}

	public function del()
	{
		$stock_id 	= $this->uri->segment(3);
		$items_id 	= $this->uri->segment(4);

		$qty_stock_out 	= $this->Stock_out_m->get($stock_id)->row()->qty_stock_out;

		$data     	= [
			'qty_stock_out'     	=> $qty_stock_out,
			'items_id'   		=> $items_id
		];


		$this->Stock_out_m->del_stock_out_item($data);
		$this->Stock_out_m->del_stock_out($stock_id);

		if ($this->db->affected_rows() > 0) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger"><strong>Success!</strong> Data berhasil dihapus</div>');
			redirect('stock_out');
		}
	}
}
