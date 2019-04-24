<?php


namespace Alvinhu\ChildSelect;


interface ChildSelectRecursiveHolder
{
    public function onParentChanged($parent, $value);
}