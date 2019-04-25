<?php

namespace Alvinhu\ChildSelect;

use Laravel\Nova\Fields\Field;

class ChildSelect extends Field
{
    public $component = 'child-select';

    protected $options;

    public function __construct($name, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta(['searchAlgorithm' => 'parent_shallow']);
    }

    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions($parameters = [])
    {
        return call_user_func($this->options, $parameters);
    }

    public function parent($attribute)
    {
        $this->withMeta(['parentAttribute' => $attribute]);
        return $this;
    }

    public function rootRecursiveSearch()
    {
        return $this->withMeta(['searchAlgorithm' => 'root_recursive']);
    }

    public function parentRecursiveSearch()
    {
        return $this->withMeta(['searchAlgorithm' => 'parent_recursive']);
    }
    
    public function childrenRecursiveSearch()
    {
        return $this->withMeta(['searchAlgorithm' => 'children_recursive']);
    }

}
