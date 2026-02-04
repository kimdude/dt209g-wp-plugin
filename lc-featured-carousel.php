<?php
/**
 * Plugin Name: Lilla Caféet Carousel
 * Description: Carousel with featured posts.
 * Version: 0.1
 * Author: Kim Dudenhöfer
 */

add_action("init", "lc_register_shortcodes");

//Registering shortcode for carousel
function lc_register_shortcodes() {
    add_shortcode("carousel", "lc_carousel");
}

//Creating carousel with featured posts
function lc_carousel() {

    //Queueing styling
    wp_enqueue_style(
        "lc-carousel-style",
        plugin_dir_url(__FILE__) . "style.css",
        [],
        "1.0"
    );


    ob_start();
    ?>

    <div class="preview-services">

        <!-- Getting products that should be displayed on homepage -->
        <?php
        $products = new WP_Query([  
            "post_type" => "product",
            "meta_key" => "display_on_homepage",
            "meta_value" => "Yes",
            "posts_per_page" => 10
        ]);

        if($products->have_posts()) {
            while($products->have_posts()) {
                $products->the_post();
                ?>

                    <a href="<?php the_permalink(); ?>">
                        <article>
                            <?php
                            if(has_post_thumbnail()) {
                                the_post_thumbnail("product-thumbnail");
                            }
                            ?>
                            <div>
                                <h3><?php the_title(); ?></h3>
                            </div>
                        </article>
                    </a>
                <?php
            }
            wp_reset_postdata();
        }
        ?>

    </div>

    <?php
    return ob_get_clean();
}

