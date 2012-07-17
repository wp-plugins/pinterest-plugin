<?php
    /*
    Plugin Name: Pinterest Plugin
    Plugin URI: http://www.WordPressPinterestPlugin.com
    Description: Display a Pinterest "Pin It" button on top of your images, only when people move their mouse over the image.
    Version: 1.0 
    Author: Promotioner.com
    Author URI: http://www.WordPressPinterestPlugin.com    
    /*============================================================================================================ */    
    function sn_activate() {
        global $wpdb;
        $table = $wpdb->prefix."pinterest_sn";
        $structure = "CREATE TABLE IF NOT EXISTS $table (
        status INT(1) NOT NULL DEFAULT '0'        
        );";
        $wpdb->query($structure);
    } 
    register_activation_hook( __FILE__, 'sn_activate' );

    //deactivation
    function sn_deactivate() {
        global $wpdb;
        $table = $wpdb->prefix."pinterest_sn";
        $structure = "DROP TABLE IF EXISTS $table";
        $wpdb->query($structure);
    } 
    register_deactivation_hook( __FILE__, 'sn_deactivate' );   
    class pin_success
    {
        function pin_success() 
        {
            global $wpdb;   
            $table = $wpdb->prefix."pinterest_sn";
            $query="select * from ".$table;
            $result=$wpdb->get_results($query);            
            if(isset($result))
            {                
                if($result[0]->status==1)
                {
                    $flag=1;
                    add_filter('the_content',  array('pin_success', 'success_pinterest'));                    
                }
                else
                {
                    add_filter('the_content',  array('pin_success', 'nosn_pinterest'));
                }
            }    
            else
            {                                
                add_filter('the_content',  array('pin_success', 'nosn_pinterest'));
            }    

        }
        function nosn_pinterest($content) {
            global $post;
            $posturl = urlencode(get_permalink()); //Get the post URL
            $pindiv = '<div class="sn_pinterest">';
            $pinurl = '<a href="http://pinterest.com/pin/create/button/?url='.$posturl.'&media=';
            $pindescription = '&description='.urlencode(get_the_title());
            $pinfinish = '" target="_blank" class="sn_pin"></a>';
            $pinend = '</div>';
            $pattern = '/<img(.*?)src="(.*?).(bmp|gif|jpeg|jpg|png)"(.*?) \/>/i';        
            $replacement = $pindiv.$pinurl.'$2.$3'.$pindescription.$pinfinish.'<img$1src="$2.$3" $4 />'.$pinend;
            $content = preg_replace( $pattern, $replacement, $content );                    
            return $content;
        }   
        function success_pinterest($content)
        {
            global $post;
            preg_match_all('/<img[^>]+>/i',$content, $result);  
            foreach($result as $img)
            {                
                foreach($img as $img_tag)
                {
                    preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $img_tag, $matches );
                    $url="http://pinterest.com/pin/create/button/?url=";                    
                    $src = $matches[ 1 ][ 0 ];                
                    $posturl = urlencode(get_permalink()); //Get the post URL
                    $pindescription = '&description='.urlencode(get_the_title());
                    $random=rand();
                    $pindiv = '<div class="sn_pinterest">';
                    $pinurl = '<a href="javascript:void(0)"';            
                    $pinfinish = '" target="_blank" class="sn_pin test" onclick="show(\''.$random.'\')"></a>';
                    $pinend = '<div style="display: none;font-family: "helvetica neue",arial,sans-serif;
                    font-size: 20px; line-height:125%">
                    <div id="'.$random.'">                    
                    <table width="550" border="0" cellspacing="0" cellpadding="10">
                    <tr>
                    <td><p><strong>Pinterest Affiliate Connect </strong></p>
                    <p>Enter your email address and we will tag it to the end of the URL to convert it into an affiliate link. You will earn monies when your referrals visit our website and take action.
                    <br>
                    <br>
                    <span class="style3">'.urldecode($posturl).'?u=<span class="style2">you@email.com</span></span></p>      
                    <table cellspacing="0" cellpadding="3">
                    <tr>
                    <td><strong>Email:</strong></td>
                    <td><input type="text" name="email" id="email_pin_'.$random.'" class="pin"></td>
                    <input type="hidden" name="url" id="url_pin_'.$random.'" value="'.$url.'">
                    <input type="hidden" name="src" id="src_pin_'.$random.'" value="'.$src.'">
                    <input type="hidden" name="post_url" id="post_url_pin_'.$random.'" value="'.$posturl.'">
                    <input type="hidden" name="desc" id="desc_pin_'.$random.'" value="'.$pindescription.'">
                    <td valign="bottom"><img src="'.plugins_url( 'PinLightbox.png', __FILE__ ).'" width="78px" height="42px" onclick="pinit(\''.$random.'\')"></td>
                    </tr>
                    </table>      
                    <p class="style3">No thank you, <a href="javascript:void(0)" onclick="pinit_without(\''.$random.'\')">pin it</a> without converting it into an affiliate link. </p>      </td>
                    </tr>
                    </table>                                        
                    </div>
                    </div>';
                    $replacement=$pindiv.$pinurl.$pinfinish.$img_tag."</div>".$pinend;
                    $content=str_replace($img_tag,$replacement,$content);                    
                }                                
            }                                                                 
            return $content;
        }
    }    
    function menu()
    {        
        add_options_page('Dashboard', 'Pinterest Plugin', 'manage_options', 'pinterestsn','overview');            
    }               
    function overview()
    {
        global $wpdb;
        $flag=0;        
        $table = $wpdb->prefix."pinterest_sn";
        $query="select * from ".$table;
        $result=$wpdb->get_results($query);
        if(isset($result))
        {
            if($result[0]->status==1)
            {
                $flag=1;
            }
        }       
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {        
            if(isset($_POST['connect']))
            {
                $query="insert into ".$table."(status)value(1)";                         
                $wpdb->query($query);
                $flag=1;   
            }       
            else
            {
                $structure = "TRUNCATE $table";
                $wpdb->query($structure);
                $flag=0;
            }     
        }        
    ?>
    <h2>Connect Pinterest to <a href="http://Promotioner.com" target="_blank">Promotioner.com</a></h2>
    <form method="post" action="">
        <table>
            <?php
                if($flag==1)
                {
                ?>
                <tr><td><input type="checkbox" name="connect" checked="checked"></td><td>Connect to Success nexus(optional)</td></tr>                    
                <?
                }
                else
                {
                ?>
                <tr><td><input type="checkbox" name="connect"></td><td>Connect to Promotioner(optional)</td></tr>                    
                <?    
                }
            ?>            
            <tr><td><input type="submit" value="Submit"></td></tr>                    
        </table>               
    </form>
    <p>If you use <a href="http://Promotioner.com">Promotioner.com's affiliate management software</a> to empower your readers to promote you, then you may want to activate this option.</p>

    <p>Once activated, whenever your readers click on the "Pin it" button, it will ask for their email address before it pins the image to their Pinterest board. It will hyperlink the pinned image to their affiliate URL. So that when their referrals buy from you, they earn a commission.</p>

    <p>Note: for this functionality to work, you should already have activated the Promotioner.com WordPress plugin. Or manually copy-pasted the Promotioner.com javascript tracking code to your theme.</p>
    <p><a href="http://Promotioner.com/buy-us-a-beer/" target="_blank"><img src="<?php echo plugins_url( 'icon_beer.gif', __FILE__ )?>" alt=""></a> This plugin is beerware. Please <a href="http://promotioner.com/buy-us-a-beer/" target="_blank">buy us a beer</a> and support this plugin.
        (Suggested: $3 a beer or $7.5 for a pitcher)</p>
    <?    
    }    
    function sn_add_dashboard_widgets()
    {
        wp_add_dashboard_widget('success_dashboard_widget', 'Read the latest post on Advanced Internet Marketing Strategies blog', 'sn_pin_rss');    
    }
    function sn_pin_rss()
    {
        echo '<div class="rss-widget">';
        wp_widget_rss_output(array(
        'url' => 'http://feeds.feedburner.com/successnexus/', 
        'title' => 'Success Nexus',
        'items' => 1, //how many posts to show
        'show_summary' => 0, // 0 = false and 1 = true 
        'show_author' => 0,
        'show_date' => 0
        ));
        echo "</div>";
    }
    $successnexus = new pin_success;
    add_action('admin_menu', 'menu');                 
    add_action('wp_dashboard_setup', 'sn_add_dashboard_widgets');                                                             
    wp_enqueue_script( 'pn_fancy', plugins_url( 'jquery.js', __FILE__ ));    
    add_action('init','jsregister');
    function jsregister()
    {
        wp_register_script( 'fb', WP_PLUGIN_URL.'/pinterest-plugin/fancybox/jquery.fancybox-1.3.4.pack.js','jquery', false );

        wp_enqueue_script( 'fb' );

        wp_register_script( 'fbe', WP_PLUGIN_URL.'/pinterest-plugin/fancybox/jquery.easing-1.3.pack.js','jquery', false );

        wp_enqueue_script( 'fbe' );

        wp_register_style('fbs', WP_PLUGIN_URL.'/pinterest-plugin/fancybox/jquery.fancybox-1.3.4.css');
        wp_enqueue_style( 'fbs');
    }
    wp_enqueue_script( 'pn_sn', plugins_url( 'snpin.js', __FILE__ ));    
    wp_enqueue_style('sn_pinterest', plugins_url('sn_pinterest.css', __FILE__ ));
?>