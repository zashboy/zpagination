<?php
   /**
      * Created on Tue Aug 07 2018
      *
      * class.Pagination.php
      *
      * Simple bootstrap 4 pagination class with average number pagination and breadcrumbs, nothing fancy just does the job
      *
      * @category  pagination
      * @package   helper
      * @author    zashboy
      * @license   https://github.com/zashboy/zpagination/blob/master/LICENSE
      * @version   0.0.1
      * @link      https://github.com/zashboy/zpagination
      * @since     File available since Release 0.0.1
      * Copyright (c) 2018 zashboy.com
     */

class Pagination
{

    /** @var int $total number of items */
    protected $total = NULL;

    /** @var string $uri the url of the page */
    protected $uri = NULL;

    /** @var integer $itemsperpage the number of the items per page */
    protected $itemsperpage = NULL;

    /** @var integer $currentpage the number of the current page */
    protected $currentpage = NULL;

    /**
      * Created on Tue Aug 07 2018
      * @name   __construct
      * @desc   Class constructor, declare default variables, fixes the url problem
      * @param  ?????
      * @return ?????
     */
 
   public function __construct()
   {

       $this->db = new Database(); //it is only necessary if you want to get the total number of items from the database
       $this->total = 0; //totla number of items default value
       $this->itemsperpage = ITEMS_PER_PAGE; //I have defined a global variable to give the default value
       $this->currentpage = 1; // default value
       $this->range = 5; // default range
        //delete the last number from the url
       $this->rawuri = explode('/', trim(filter_var(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), FILTER_SANITIZE_URL), '/'));;
       if(is_numeric(end($this->rawuri))){
        array_pop($this->rawuri);
       } 
       $uri = implode("/", $this->rawuri);
       $this->uri = $uri;
    }

    /**
      * Created on Tue Aug 07 2018
      * @name   setTotal()
      * @desc   You can use this method to set the total number of items from outside
      * @param  integer $total
      * @return integer
     */

   public function setTotal(int $total = NULL)
   {
       $this->total = $total;
    }

    /**
      * Created on Tue Aug 07 2018
      * @name   setTotalFromDB
      * @desc   get the total number of items from the database, this method can be called from the controller see examples
      * @param  array $sql the array of sql statement (it is just for my database handler) see examples
      * @return integer on success null on fail
     */

   public function setTotalFromDB(array $sql = NULL)
   {
        //set the sql array request to count
        $sql['reqData'] = 'COUNT(*) AS total';

        if($sql){
            $request = $this->db->select($sql);
            if($request){
                $this->total = $request[0]['total'];
            } else {
                $this->total = NULL;
            }
        }
    }
   /**
      * Created on Tue Aug 07 2018
      * @name   setUri
      * @desc   Set the uri, it is necessary to build the hyperlinks 
      * @param  string &uri
      * @return string
     */

    public function setUri(string $uri = NULL)
    {
        $this->uri = $uri;
    }
   /**
      * Created on Tue Aug 07 2018
      * @name   setItemsPerPage
      * @desc   You can set the maximum number of items you want to show on each page
      * @param  int $itemsperpage
      * @return int
     */

    public function setItemsPerPage(int $itemsperpage = NULL)
    {
        $this->itemsperpage = $itemsperpage;
    }

    /**
      * Created on Tue Aug 07 2018
      * @name   setCurrentPage
      * @desc   you can set the number of the current page
      * @param  int $currentpage
      * @return int
     */

    public function setCurrentPage(int $currentpage = NULL)
    {
        $this->currentpage = $currentpage;
    }

   /**
      * Created on Tue Aug 07 2018
      * @name   showPagination
      * @desc   This method create the pagination html, you need to include this into your view 
      * @return html
     */

    public function showPagination()
    {  
        
        $pagestotal = ($this->total > $this->itemsperpage) ? ceil($this->total / $this->itemsperpage) : 1;

        if($this->total > $this->itemsperpage){

            ob_start();
            echo '<div class="row d-flex justify-content-center">';
            echo '<ul class="pagination">';

            //prev
            if ($this->currentpage > 1) {
            echo '<li class="page-item"><a class="page-link" href="/' . $this->uri . '/'. ($this->currentpage - 1) . '">Previous</a></li>';
            }

            if($pagestotal > $this->range){               
                $start = ($this->currentpage <= $this->range) ? 1 : ($this->currentpage - $this->range);
                $end   = ($pagestotal - $this->currentpage >= $this->range) ? ($this->currentpage+$this->range) : $pagestotal;
            }else{
                $start = 1;
                $end   = $pagestotal;
            }   
            //pages
            for ($i = $start; $i <= $end; $i++) {
                $active = (($i==$this->currentpage) || ($this->currentpage == 0 && $i==1)) ? ' active' : NULL; 
                echo '<li class="page-item' . $active . '"><a class="page-link" href="/' . $this->uri . '/'. $i . '">' . $i . '</a></li>';
            }

            //if total pages are more than the current page
            if ($this->currentpage < $pagestotal && $pagestotal != 1) {
                echo '<li class="page-item"><a class="page-link" href="/' . $this->uri . '/'. ($this->currentpage < 1 ? 2 : $this->currentpage + 1) . '">Next</a></li>';
            }
            
            echo '</ul>';
            echo '</div>';

            return ob_get_clean();

        }

    }

   /**
      * Created on Tue Aug 07 2018
      * @name   showBreadCrumbs
      * @desc   This method create the pbreadcrumbs html, you need to include this into your view 
      * @return html
     */

    public function showBreadCrumbs()
    {

        ob_start();
        echo '<div class="row">';
        echo '<ul class="breadcrumb">';
        echo '<li class="breadcrumb-item"><a href="/home">Home</a></li>'; //home page with the url /home 
        for ($i = 0; $i <= (count($this->rawuri) - 1); $i++){
            if((count($this->rawuri)-1) == $i){
                echo '<li class="breadcrumb-item">'.$this->rawuri[$i].'</li>';
            } else {
                echo '<li class="breadcrumb-item"><a href="/';
                    for($c = 0; $c <= $i; $c++){
                        $slash = ($i == $c) ? NULL : '/';
                        echo $this->rawuri[$c] . $slash ;
                    }
                echo '">'.$this->rawuri[$i].'</a></li>';
            }
        }
        echo '</ul>';
        echo '</div>';
        return ob_get_clean();
    }

}
?>