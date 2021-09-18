<?php
class Elementor_Widget_Breadcrumb extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'elementor_breadcrumb';
    }

    public function get_title()
    {
        return __('Simple Breadcrumb', 'elementor_breadcrumb');
    }

    public function get_icon()
    {
        return 'eicon-navigation-horizontal';
    }

    public function get_categories()
    {
        return [ 'general' ];
    }

    protected function _register_controls()
    {
        $this->start_controls_section(
            'content_section',
            [
              'label' => __('Content', 'elementor_breadcrumb'),
              'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'delimiter_character',
            [
              'label' => __('Delimiter', 'elementor_breadcrumb'),
              'type' => \Elementor\Controls_Manager::TEXT,
              'default' => __('»', 'elementor_breadcrumb'),
              'placeholder' => __('Type your text here', 'elementor_breadcrumb'),
            ]
        );

        $this->add_control(
            'home_text',
            [
              'label' => __('Home text', 'elementor_breadcrumb'),
              'type' => \Elementor\Controls_Manager::TEXT,
              'default' => __('Home', 'elementor_breadcrumb'),
              'placeholder' => __('Type your text here', 'elementor_breadcrumb'),
            ]
        );

        $this->add_control(
            'text_align',
            [
                        'label' => __('Alignment', 'elementor_breadcrumb'),
                        'type' => \Elementor\Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                'title' => __('Left', 'elementor_breadcrumb'),
                                'icon' => 'eicon-text-align-left',
                            ],
                            'center' => [
                                'title' => __('Center', 'elementor_breadcrumb'),
                                'icon' => 'eicon-text-align-center',
                            ],
                            'right' => [
                                'title' => __('Right', 'elementor_breadcrumb'),
                                'icon' => 'eicon-text-align-right',
                            ],
                        ],
                        'default' => 'center',
                        'toggle' => true,
                    ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'elementor_breadcrumb'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => __('Typography', 'elementor_breadcrumb'),
                'selector' => '{{WRAPPER}} .breadcrumb',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $delimiter = $settings["delimiter_character"];
        $home = $settings["home_text"];
        $before = '<span class="current-page">';
        $after = '</span>';

        if (!is_home() && !is_front_page() || is_paged()) {
            echo '<nav class="breadcrumb" style="text-align: ' . $settings['text_align'] . '">';

            global $post;
            $homeLink = get_bloginfo('url');
            echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';

            if (is_category()) {
                global $wp_query;
                $cat_obj = $wp_query->get_queried_object();
                $thisCat = $cat_obj->term_id;
                $thisCat = get_category($thisCat);
                $parentCat = get_category($thisCat->parent);
                if ($thisCat->parent != 0) {
                    echo(get_category_parents($parentCat, true, ' ' . $delimiter . ' '));
                }
                echo $before . single_cat_title('', false) . $after;
            } elseif (is_day()) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
                echo $before . get_the_time('d') . $after;
            } elseif (is_month()) {
                echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
                echo $before . get_the_time('F') . $after;
            } elseif (is_year()) {
                echo $before . get_the_time('Y') . $after;
            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
                    echo $before . get_the_title() . $after;
                } else {
                    $cat = get_the_category();
                    $cat = $cat[0];
                    echo get_category_parents($cat, true, ' ' . $delimiter . ' ');
                    echo $before . get_the_title() . $after;
                }
            } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
                $post_type = get_post_type_object(get_post_type());
                echo $before . $post_type->labels->singular_name . $after;
            } elseif (is_attachment()) {
                $parent = get_post($post->post_parent);
                $cat = get_the_category($parent->ID);
                $cat = $cat[0];
                echo get_category_parents($cat, true, ' ' . $delimiter . ' ');
                echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
                echo $before . get_the_title() . $after;
            } elseif (is_page() && !$post->post_parent) {
                echo $before . get_the_title() . $after;
            } elseif (is_page() && $post->post_parent) {
                $parent_id = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
                    $parent_id = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                foreach ($breadcrumbs as $crumb) {
                    echo $crumb . ' ' . $delimiter . ' ';
                }
                echo $before . get_the_title() . $after;
            } elseif (is_search()) {
                echo $before . 'Ergebnisse für Ihre Suche nach "' . get_search_query() . '"' . $after;
            } elseif (is_tag()) {
                echo $before . 'Beiträge mit dem Schlagwort "' . single_tag_title('', false) . '"' . $after;
            } elseif (is_404()) {
                echo $before . 'Fehler 404' . $after;
            }

            if (get_query_var('paged')) {
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) {
                    echo ' (';
                }
                echo ': ' . __('Seite') . ' ' . get_query_var('paged');
                if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) {
                    echo ')';
                }
            }

            echo '</nav>';
        }
    }

    protected function _content_template()
    {
    }
}
