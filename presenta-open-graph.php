<?php
/*
Plugin Name: PRESENTA Open-Graph
Plugin URI: https://www.presenta.cc/open-graph-wordpress-plugin
Description: PRESENTA Open-Graph plugin generates social preview images and tags automatically for each post or page.
Tags: social, social sharing, open graph, social image, twitter card, open graph
Requires at least: 4.0
Version: 1.0.0
Author: PRESENTA
Author URI: https://www.presenta.cc
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$PRESENTA_SERVICE_URL = 'https://cloud.preso.cc/v1/url/';

function presenta_head_meta_data() {

  if(is_admin()) return;

  if ( is_category() || is_tag() ) return;

  global $post, $PRESENTA_SERVICE_URL;

  if (empty($post)) return;

  $pTemplateID = get_option('presenta_plugin_template_id');
  $hasYoast = get_option('presenta_plugin_template_yoast');
  if (empty($pTemplateID)) return;

  $post_id   = $post->ID;
  $author_id = $post->post_author;
  
  $post_date    = get_the_modified_date();
  $post_author  = get_the_author_meta('display_name', $author_id);
  $post_title   = wp_strip_all_tags(get_the_title($post_id));
  $post_excerpt = wp_strip_all_tags(get_the_excerpt($post_id));
  $post_image   = get_the_post_thumbnail_url($post_id);
  $post_url     = get_permalink($post_id);

  $site_name = get_bloginfo('name');
  $site_url = site_url();

  if (!is_singular()){
    $post_title = $site_name;
  }

  if(empty($post_image) && !empty($unsplash_topic)){
    $post_image = "https://source.unsplash.com/random/800x600/?sky";
  }

  $url = $PRESENTA_SERVICE_URL . esc_attr($pTemplateID);
  $url .= "?title=" . $post_title;
  $url .= "&subtitle=" . $post_date;
  $url .= "&image=" . $post_image;

  $output =  "\n" . '<!-- PRESENTA OG start -->' . "\n";

  if($hasYoast != '1'){
    $output .= '<meta property="og:type" content="website">' . "\n";
    $output .= '<meta property="og:title" content="' . esc_attr($post_title) . '">' . "\n";
    $output .= '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    $output .= '<meta property="og:description" content="' . esc_attr($post_excerpt) . '">' . "\n";
    $output .= '<meta property="og:url" content="' . esc_url($post_url) . '">' . "\n";

    $output .= '<meta name="twitter:card" content="summary_large_image"  />' . "\n";
    $output .= '<meta name="twitter:title" content="' . esc_attr($post_title) . '"  />' . "\n";
    $output .= '<meta name="twitter:site" content="' . esc_attr($site_name) . '"  />' . "\n";
    $output .= '<meta name="twitter:description" content="' . esc_attr($post_excerpt) . '"  />' . "\n";
    $output .= '<meta name="twitter:url" content="' . esc_url($post_url) . '"  />' . "\n";
  }

  $output .= '<meta name="twitter:image" content="' . $url . '"  />' . "\n";
  $output .= '<meta property="og:image" content="' . $url . '"  />' . "\n";

  $output .= '<!-- PRESENTA OG end -->' . "\n\n";

  echo $output;
  
}
add_action('wp_head', 'presenta_head_meta_data', 1);











function presenta_plugin_options_validate(){
  // validate input
}

function presenta_plugin_section_callback(){
}

function presenta_plugin_template_id_callback(){
    $setting = get_option('presenta_plugin_template_id');
    ?>
    <input placeholder="i.e. xxxxxxxx:yyyyyyyyy" type="text" name="presenta_plugin_template_id" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <?php
}

function presenta_plugin_template_yoast_callback(){
  $setting = get_option('presenta_plugin_template_yoast');
  ?>
  <input type="text" name="presenta_plugin_template_yoast" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
  <?php
}


function presenta_register_settings() {
    add_settings_section( 'presenta_plugin_section', 'Open-Graph image generator for WordPress', 'presenta_plugin_section_callback', 'presenta_plugin_options' );
    add_settings_field( 'presenta_plugin_template_id', 'Template ID', 'presenta_plugin_template_id_callback', 'presenta_plugin_options', 'presenta_plugin_section' );
    add_settings_field( 'presenta_plugin_template_yoast', 'Yoast Fix', 'presenta_plugin_template_yoast_callback', 'presenta_plugin_options', 'presenta_plugin_section' );
    
    register_setting( 'presenta_plugin_options', 'presenta_plugin_section' );
    register_setting( 'presenta_plugin_options', 'presenta_plugin_template_id' ); //, 'presenta_plugin_options_validate'
    register_setting( 'presenta_plugin_options', 'presenta_plugin_template_yoast' ); //, 'presenta_plugin_options_validate'
}
add_action( 'admin_init', 'presenta_register_settings' );









function presenta_render_plugin_setting_panel(){
  global $PRESENTA_SERVICE_URL;
  ?>
    <h1>PRESENTA Open-Graph</h1>
    <form action="options.php" method="post" class="presenta_form">
        <?php 
          //settings_errors();
          settings_fields( 'presenta_plugin_options' );
          do_settings_sections( 'presenta_plugin_options' ); 
          submit_button();
        ?>
    </form>

    <p>Choose the template you prefer the most, review the options below, then, Save Changes.</p>
    <p><input type="checkbox" id="presenta_yoast_fix" /> I have Yoast or other SEO plugins active. This option forces PRESENTA handling only the image tag.</p>
    <!--<p><select>
      <option>-- Disabled --</option>
      <option>Sky</option>
      <option>Home</option>
      <option>Tech</option>
    </select> Choose the topic (or disable it) for image fallback (post/page without Featured image) picked randomly from Unsplash.</p>
    -->

    <div id="presenta_gallery_container">
      <div class="presenta_template">
        <div class="presenta_template_inner">
          <img src="<?php echo plugin_dir_url( __FILE__ ) . 'none.jpg'; ?>" />
        </div>
      </div>
    </div>

    
    <script>
  
        const src = [
          {"id": "zGywhb2oJn:mzT3zNoLn"},
          {"id": "zGywhb2oJn:BEaGHrXOs"},
          {"id": "zGywhb2oJn:0vx3VZVjP"},
          {"id": "zGywhb2oJn:MiKViTKFM"},

          {"id": "zGywhb2oJn:Gx5I8aeto"},
          {"id": "zGywhb2oJn:9CF5pEILq"},
          {"id": "zGywhb2oJn:6JaJpv7Qo"},
          {"id": "zGywhb2oJn:JCNwsKA6w"}
        ]


        <?php $templateID = get_option('presenta_plugin_template_id'); ?>
        const actual = "<?php echo esc_attr($templateID); ?>"
    
        <?php $yoastFix = get_option('presenta_plugin_template_yoast'); ?>
        const checkYoast = document.querySelector('#presenta_yoast_fix')
        const hasYoast = "<?php echo esc_attr($yoastFix); ?>"
        if(hasYoast == '1') checkYoast.checked = true
        checkYoast.addEventListener('change', e => {
          const v = e.target.checked
          const field = document.querySelector('[name="presenta_plugin_template_yoast"]')
          field.value = v ? 1 : 0
        })

        const base = '<?php echo esc_url($PRESENTA_SERVICE_URL); ?>'

        const wrapper = document.querySelector('#presenta_gallery_container')

        src.forEach((t,i) => {
          const el = document.createElement('div')
          el.classList.add('presenta_template')
          
          const inn = document.createElement('div')
          inn.classList.add('presenta_template_inner')
          el.append(inn)
          
          const img = document.createElement('img')
          img.setAttribute('src', base + t.id + '?title=Contrary to popular belief, Lorem Ipsum is not simply random text.&subtitle=January 1, 2022&image=https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=900&q=80')
          inn.append(img)
          
          img.setAttribute('data-id', t.id)
          img.setAttribute('data-index', i+1)

          if(t.id == actual) img.classList.add('selected')
          
          wrapper.append(el)
        })

        if(!actual){
          const first = wrapper.querySelector('.presenta_template_inner img:first-child')
          first.classList.add('selected')
        }



        wrapper.addEventListener('click', e => {
          const id = e.target.getAttribute('data-id')
          const index = e.target.getAttribute('data-index')
          const field = document.querySelector('[name="presenta_plugin_template_id"]')
          field.value = id
          
          const list = [...wrapper.querySelectorAll('.presenta_template_inner img')]
          list.forEach(d => {
            d.classList.remove('selected')
          })
          
          list[+index].classList.add('selected')

        })


    </script>
    <style>
      #presenta_gallery_container * {
        box-sizing: border-box;
      }
      #presenta_gallery_container{
        display:flex;
        flex-wrap: wrap;
        padding-right: 20px;
      }
      .presenta_template{
        width: 33.333333%;
      }
      .presenta_template_inner{
        padding:10px;
      }
      .presenta_template .selected{
        border:5px solid #1E66A8;
      }
      .presenta_template img{
        display:block;
        width:100%;
        height:auto;
        box-shadow: 0 0 10px #ccc;
      }
      .presenta_form table{
        display:none;
      }
    </style>
    <?php
}


function presenta_add_settings_page() {
    add_options_page( 'PRESENTA OG Settings', 'PRESENTA OG', 'manage_options', 'presenta-og-plugin', 'presenta_render_plugin_setting_panel' );
}
add_action( 'admin_menu', 'presenta_add_settings_page' );



