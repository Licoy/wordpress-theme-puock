<?php

namespace Puock\Theme\classes\meta;

abstract class PuockAbsMeta
{
    private static $args = array();

    private $cache_data = null;

    protected $instance_args = array();

    protected $id;

    public static function newPostMeta($id, $args = array())
    {
        self::$args['post'][$id] = $args;
    }

    public static function newTaxonomyMeta($id, $args = array())
    {
        self::$args['taxonomy'][$id] = $args;
    }

    public static function newSection($id, $args = array())
    {
        self::$args['section'][$id] = $args;
    }

    public static function load()
    {
        foreach (self::$args as $type => $args) {
            if ($type === 'post') {
                foreach ($args as $id => $meta) {
                    new MetaBox($id, $meta);
                }
            }
        }
    }

    public function getValue($post_id, $attr_id, $default = '')
    {
        if ($this->instance_args['single']) {
            return get_post_meta($post_id, $attr_id, true);
        } else {
            if ($this->cache_data === null) {
                $this->cache_data = get_post_meta($post_id, $this->id, true);
            }
            if (isset($this->cache_data[$attr_id])) {
                return $this->cache_data[$attr_id];
            }
        }
        return $default;
    }

    /**
     * @param $post_id
     * @param $args array[id|title|std|type|desc|options]
     * @return void
     */
    public function baseSaveData($post_id)
    {
        $args = $this->instance_args;
        if (!wp_verify_nonce(@$_POST[$args['id'] . '_noncename'], plugin_basename(__FILE__))) {
            return $post_id;
        }
        $data = array();
        foreach ($args['options'] as $option) {
            $val = $_POST[$option['id']] ?? '';
            if ($args['single']) {
                update_post_meta($post_id, $option['id'], $val);
            } else {
                $data[$option['id']] = $val;
            }
        }
        if (!$args['single']) {
            update_post_meta($post_id, $args['id'], $data);
        }
    }

    /**
     * @param $args array[id|title|std|type|desc|options]
     * @return void
     */
    public function baseRender($post)
    {
        $args = $this->instance_args;
        echo '<input type="hidden" name="' . $args['id'] . '_noncename" id="' . $args['id'] . '_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
        echo "<table class='form-table' role='presentation'><tbody>";
        foreach ($args['options'] as $arg) {
            echo "<tr>";
            $desc = '';
            if (isset($arg['desc'])) {
                $desc = "<p class='description'>{$arg['desc']}</p>";
            }
            $value = $this->getValue($post->ID, $arg['id'], $arg['std'] ?? '');
            switch ($arg['type']) {
                case 'title':
                    echo '<h4>' . $arg['title'] . '</h4>';
                    break;
                case 'des':
                    echo '<p>' . $arg['desc'] . '</p>';
                    break;
                case 'textarea':
                    echo '<th>' . $arg['title'] . '</th>';
                    echo '<td><textarea cols="40" rows="2" name="' . $arg['id'] . '">' . $value . '</textarea>' . $desc . '</td>';
                    break;
                case 'color':
                    echo '<th>' . $arg['title'] . '</th>';
                    echo '<td><input type="color" value="' . $value . '" name="' . $arg['id'] . '"/>' . $desc . '</td>';
                    break;
                case 'select':
                    if (@$arg['multiple'] && is_string($value)) {
                        $value = explode(',', $value);
                    }
                    echo '<th>' . $arg['title'] . '</th>';
                    echo '<td><select ' . (@$arg['multiple'] ? 'multiple' : '') . '  name="' . $arg['id'] . (@$arg['multiple'] ? '[]' : '') . '">';
                    foreach ($arg['options'] as $option) {
                        if (is_array($value)) {
                            $selected = in_array($option['value'], $value) ? 'selected' : '';
                        } else {
                            $selected = $value == $option['value'] ? 'selected' : '';
                        }
                        echo '<option value="' . $option['value'] . '" ' . $selected . '>' . $option['label'] . '</option>';
                    }
                    echo "</select>{$desc}</td>";
                    break;
                case 'radio':
                    echo '<th>' . $arg['title'] . '</th>';
                    echo '<td>';
                    foreach ($arg['options'] as $option) {
                        $checked = "";
                        if ($value == $option['value']) {
                            $checked = 'checked = "checked"';
                        }
                        echo '<input ' . $checked . ' type="radio" class="kcheck" value="' . $option['value'] . '" name="' . $option['id'] . '_value"/>' . $option['label'];
                    }
                    echo $desc . '</td>';
                    break;
                case 'checkbox':
                    echo '<th>' . $arg['title'] . '</th>';
                    $checked = '';
                    if ($value == 'true')
                        $checked = 'checked = "checked"';
                    echo '<td><label><input type="checkbox" name="' . $arg['id'] . '" value="true"  ' . $checked . ' />' . $arg['title'] . '</label>' . $desc . '</td>';
                    break;
                default:
                    echo '<th>' . $arg['title'] . '</th>';
                    echo '<td><input type="text" size="40" name="' . $arg['id'] . '" value="' . $value . '" />' . $desc . '</td>';
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
}
