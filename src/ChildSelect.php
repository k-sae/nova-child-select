<?php

namespace Alvinhu\ChildSelect;

use Laravel\Nova\Fields\Field;

class ChildSelect extends Field
{
    public $component = 'child-select';

    protected $options;

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

    public function recursive()
    {
        return $this->withMeta(['recursive' => true]);
    }
}
