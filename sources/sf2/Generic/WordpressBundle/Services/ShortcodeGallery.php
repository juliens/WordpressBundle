<?php

namespace Generic\WordpressBundle\Services;

class ShortcodeGallery implements Shortcode
{
    private $attr = null;

    public function __construct($attr)
    {
        $this->attr = $attr;
    }

    public function getName()
    {
        return "gallery";
    }

    public function process($params=array())
    {
        $attr = array();
        $post = get_post();

        static $instance = 0;
        $instance++;

        if (!empty($params['ids'])) {
            // 'ids' is explicitly ordered, unless you specify otherwise.
            if (empty($attr['orderby'])) {
                $attr['orderby'] = 'post__in';
            }
            $attr['include'] = $params['ids'];
        }

        // Allow plugins/themes to override the default gallery template.
        $output = apply_filters('post_gallery', '', $attr);
        if ($output != '')
            return $output;

        // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
        if (isset($attr['orderby'])) {
            $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
            if (!$attr['orderby'])
                unset($attr['orderby']);
        }

        extract(shortcode_atts(array(
                    'order'      => 'ASC',
                    'orderby'    => 'menu_order ID',
                    'id'         => $post ? $post->ID : 0,
                    'itemtag'    => '',
                    'icontag'    => '',
                    'captiontag' => '',
                    'columns'    => 3,
                    'size'       => 'thumbnail',
                    'include'    => '',
                    'exclude'    => '',
                    'link'       => ''
                ), $attr, 'gallery'));

        $id = intval($id);
        if ('RAND' == $order)
            $orderby = 'none';

        if (!empty($include)) {
            $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));

            $attachments = array();
            foreach ($_attachments as $key => $val) {
                $attachments[$val->ID] = $_attachments[$key];
            }
        } elseif (!empty($exclude)) {
            $attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
        } else {
            $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
        }

        if (empty($attachments))
            return '';

        if (is_feed()) {
            $output = "\n";
            foreach ($attachments as $att_id => $attachment)
                $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
            return $output;
        }

        $columns = intval($columns);
        $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
        $float = is_rtl() ? 'right' : 'left';

        $selector = "gallery-{$instance}";

        $gallery_style = $gallery_div = '';
        if (apply_filters('use_default_gallery_style', true))
            $gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
		</style>";
        $size_class = sanitize_html_class($size);
        $gallery_div = "<span id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
        $output = apply_filters('gallery_style', $gallery_style . "\n\t\t" . $gallery_div);


        $first = current($attachments);
        $id = key($attachments);
        $nbPhotos = count($attachments);
        $image_meta  = wp_get_attachment_metadata($id);
        $path = $first->guid;
        $thumb = explode('/', $path);
        array_pop($thumb);
        array_push($thumb, $image_meta['sizes']['thumbnail']['file']);
        $thumb = implode('/', $thumb);
        $output .= '<span class="pic-block-photo">';
        $output .= "<a class='fancybox' rel='{$selector}' href='{$path}' title='{$first->post_excerpt}'>";
        $output .= "<img src='{$thumb}' alt='Image {$id}' class='photo-thumb' />";
        $output .= '</a>';
        $output .= '</span>';
        $output .= '<span class="number-resume">'.$nbPhotos.'</span> ';
        $output .= "<span class='icon-photo-small'></span><div class='hidden'>";

        $i = 0;
        unset($attachments[$id]);
        foreach ($attachments as $id => $attachment) {
            $image_meta  = wp_get_attachment_metadata($id);
            $path = $attachment->guid;
            $thumb = explode('/', $path);
            array_pop($thumb);
            array_push($thumb, $image_meta['sizes']['thumbnail']['file']);
            $thumb = implode('/', $thumb);

            $class = (0 === $i) ? ' class="photo-thumb"' : null;
            $image_output = "<a class='fancybox' rel='{$selector}' href='{$path}' title='{$attachment->post_excerpt}'>";
            $image_output .= "<img src='{$thumb}' alt='Image {$id}'{$class} />";
            $image_output .= '</a>';

            $output .= $image_output;
        }

        $output .= "</div>
		</span>\n";
        return $output;
    }
}