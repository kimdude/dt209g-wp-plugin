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

    <div>
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
            $scroll_markers = [];

            while($products->have_posts()) {
                $products->the_post();

                //Adding item to list
                $id = get_the_id();
                array_push($scroll_markers, $id);

                ?>

                    <a href="<?php the_permalink(); ?>">
                        <article class="card">
                            <?php
                            if(has_post_thumbnail()) {
                                the_post_thumbnail("product-thumbnail");
                            } else {          
                            ?>
                            <div class="default-img">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/favicon.svg" width="100" alt="Lilla Caféet">
                            </div>
                            <?php
                            }
                            ?>
                            <div>
                                <h3><?php the_title(); ?></h3>
                            </div>
                        </article>
                    </a>
                <?php
            }
            ?>
        </div>

        <!-- Creating scrollmarkers -->
        <div class="card-markers">
            <?php
                foreach($scroll_markers as $marker) {
                    ?>
                        <div class="marker"></div>
                    <?php
                }
            ?>
        </div>

        <?php
        wp_reset_postdata();
        }
        ?>

        <!-- Scrollmarker functionality -->
        <script>

            //Getting elements
            const container = document.querySelector(".preview-services");
            const markers_container = document.querySelector(".card-markers");
            const cards = document.getElementsByClassName("card");
            const markers = document.getElementsByClassName("marker");

            //Setting card index by position or direct index
            let card_by_position = true;

            window.onload = () => {
                check_width();
            }

            //Checking if scroll is needed
            function check_width() {
                if(container.scrollWidth > container.offsetWidth) {
                    markers_container.style.display = "flex";
                } else {
                    markers_container.style.display = "none";
                }
            }

            //Figuring out current card/position
            function current_card() {

                if(card_by_position){

                    const px_to_left = container.scrollLeft;
                    const card_width = cards[0].getBoundingClientRect().width;
                    const position = Math.round(px_to_left/card_width);

                    select_marker(position);
                }

            }

            //Scroll when clicking marker
            function scroll(index) {

                //Finding card
                card_by_position = false;
                const card = cards[index];

                //Scrolling to card
                card.scrollIntoView({
                    behavior: "smooth",
                    inline: "center",
                    block: "nearest"
                });

                select_marker(index);

                //Resetting marker selection to select by position
                setTimeout(() => { card_by_position = true; }, 5000);
            }

            //Setting current marker to active
            function select_marker(index) {

                    //Removing class from previous marker
                    Array.from(markers).forEach(marker => {
                        marker.classList.remove("marker-active");
                    });

                    //Adding class to current marker
                    const current_marker = markers[index];
                    current_marker.classList.add("marker-active");
            }

            //Eventlisteners
            container.addEventListener("scroll", current_card);
            Array.from(markers).forEach((marker, index) => {
                marker.addEventListener("click", () => scroll(index));
            });
            
        </script>
    </div>

    <?php
    return ob_get_clean();
}

