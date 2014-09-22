<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shopcart extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->helper(array('url','form'));
    $this->load->library(array('udp_cart'));
    $this->load->model('shopcart_model');

    $this->cart = new Udp_cart('cart');
    $this->favor = new Udp_cart('favor');
    $this->compare = new Udp_cart('compare');
  }

  public function send_email()
  {
    set_time_limit(0);
    $interval = 60;

    do{
      $config = array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_user' => 'leesu88@gmail.com',
        'smtp_pass' => 'audilgul',
        'smtp_port' => '465',
        'smtp_timeout' => 5,
        'validate' => TRUE,
      );
      $this->load->library('email',$config);
      $this->email->clear();
      $this->email->set_newline("\r\n");
      $this->email->from('your@example.com', 'Your Name');
      $this->email->to('cherub0526@livemail.tw');

      $this->email->subject('Email Test');
      $this->email->message('Testing the email class.');

      $this->email->send();

      //echo $this->email->print_debugger();
      sleep($interval);
      }while(true);
  }

  public function test()
  {
    $this->shop1 = new Udp_cart('shop1');
    $this->shop2 = new Udp_cart('shop2');

    $article = array("id" => 1,"qty" => mt_rand(1,10),"name" => "shoes","price" => "10");
    $article["options"] = array("color" => "black", "size" => "4");

    $article2 = array("id" => 1,"qty" => mt_rand(1,2),"name" => "shoes","price" => "10");
    $article2["options"] = array("color" => "black", "size" => "4");

    $this->shop1->insert($article);
    $this->shop2->insert($article2);

    foreach($this->shop1->get_content() as $item)
    {
      echo "id : " . $item['id'] . "name : " . $item['name'] . " qty : " . $item['qty'] . "<br>";
    }
    echo "<hr> 2 <br>";
    foreach($this->shop2->get_content() as $item)
    {
      echo "id : " . $item['id'] . "name : " . $item['name'] . " qty : " . $item['qty'] . "<br>";
    }
  }

  public function index()
  {
    $data = array(
      'cart' => $this->cart->get_content(),
      'item_per_page' => 4,
      'total_pages' => 2,
      'view' => array(
        'shopcart/header','shopcart/top_container'
      ),
      'contain_view' => array(
        'banner/home_banner','shopcart/product_tab','shopcart/bestsellers','shopcart/recently_review','shopcart/top_brands','shopcart/footer'
      ),
    );
    $this->load->view('template',$data);
  }

  public function category()
  {
    $data = array(
      'cart' => $this->cart->get_content(),
      'view' => array(
        'shopcart/header'
      ),
      'contain_view' => array(
        'shopcart/category','shopcart/footer'
      ),
    );
    $this->load->view('template',$data);
  }

  public function pages($product_id = FALSE)
  {
    $data = array(
      'cart' => $this->cart->get_content(),
      'view' => array(
        'shopcart/header'
      ),
      'contain_view' => array(
        'shopcart/animate','shopcart/single_product','banner/home_banner','shopcart/recently_review','shopcart/footer'
      ),
    );
    $this->load->view('template',$data);
  }

  public function cart()
  {
    $data = array(
      'cart' => $this->cart->get_content(),
      'view' => array(
        'shopcart/header'
      ),
      'contain_view' => array(
        'cart/cart','shopcart/footer'
      ),
    );
    $this->load->view('template',$data);
  }

  public function auth()
  {
    $data = array(
      'cart' => $this->cart->get_content(),
      'view' => array(
        'shopcart/header'
      ),
      'contain_view' => array(
        'shopcart/auth','shopcart/recently_review','shopcart/top_brands','shopcart/footer'
      ),
    );
    $this->load->view('template',$data);
  }

  public function checkout()
  {
    $data = array(
      'cart' => $this->cart->get_content(),
      'view' => array(
        'shopcart/header'
      ),
      'contain_view' => array(
        'cart/checkout','shopcart/footer'
      ),
    );
    $this->load->view('template',$data);
  }

  public function fetch_pages()
  {
    for($i=0;$i<4;$i++)
    {
      $this->load->view('listing/fetch_pages');
    }
  }


  public function add_cart()
  {
    $product_id = $this->input->post('product_id');
    //$product = $this->shopcart_model->get_product($product_id)->result();

    $cart = array("id" => rand(1,10),"qty" => mt_rand(1,10),"name" => "shoes","price" => "10");
    $this->cart->insert($cart);

    $data['cart'] = $this->cart->get_content();
    $this->load->view('listing/top_cart',$data);
  }

  public function destroy_cart()
  {
    $product_id = $this->input->post('product_id');
    if( !$this->cart->remove_item($product_id)){}

    $data['cart'] = $this->cart->get_content();

    $this->load->view('listing/top_cart',$data);
  }

  public function cart_update()
  {
    $rowid = $this->input->post('rowid');
    $id = $this->input->post('id');
    $name = $this->input->post('name');
    $price = $this->input->post('price');
    $qty = $this->input->post('qty');

    for($i=0; $i < count($id); $i++)
    {
      if($qty[$i] == 0)
      {
        $this->cart->remove_item($rowid[$i]);
      }
      else
      {
        $cart = array(
          'id' => $id[$i],
          'qty' => $qty[$i],
          'price' => $price[$i],
          'name' => $name[$i],
        );

        $this->cart->update($cart);
      }

    }

    redirect('shopcart/cart','refresh');
  }

  public function add_favor()
  {
    $product_id = $this->input->post('product_id');
    //$product = $this->shopcart_model->get_product($product_id)->result();

    $favor = array("id" => rand(1,10),"qty" => 1,"name" => "shoes","price" => "10");
    $this->favor->insert($favor);

    echo anchor('shopcart/favor','<i class="fa fa-heart"></i>喜歡商品<span class="value">(' . $this->favor->total_articles() . ')</span>');
  }

  public function add_compare()
  {
    $product_id = $this->input->post('product_id');
    //$product = $this->shopcart_model->get_product($product_id)->result();

    $compare = array("id" => rand(1,10),"qty" => 1,"name" => "shoes","price" => "10");
    $this->compare->insert($compare);

    echo anchor('shopcart/compare','<i class="fa fa-exchange"></i>比較商品<span class="value">(' . $this->compare->total_articles() . ')</span>');
  }



}

/* End of file shopcart.php */
/* Location: ./application/controllers/shopcart.php */