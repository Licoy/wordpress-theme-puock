<?php

namespace Puock\Theme\classes\meta;

class MetaBox extends PuockAbsMeta
{
    protected $instance_args = array(
        'single' => true,
        'post_type' => 'post',
        'context' => 'normal',
        'priority' => 'high',
    );

    /**
     * @param string $id
     * @param array $args
     */
    public function __construct(string $id, array $args)
    {
        $this->id = $id;
        $args['id'] = $id;
        $this->instance_args = array_merge($this->instance_args, $args);
        add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
        add_action('save_post', array(&$this, 'baseSaveData'));
    }

    public function add_meta_boxes($post_type)
    {
        $type = $this->instance_args['post_type'];
        if ((is_string($type) && $type === $post_type) || (is_array($type) && in_array($post_type, $type))) {
            add_meta_box($this->id, $this->instance_args['title'], array(&$this, 'baseRender'), $this->instance_args['post_type'],
                $this->instance_args['context'], $this->instance_args['priority']);
        }
    }

}
