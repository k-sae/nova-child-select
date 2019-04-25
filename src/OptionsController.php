<?php

namespace Alvinhu\ChildSelect;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class OptionsController extends Controller
{
    public function index(NovaRequest $request)
    {
        $attribute = $request->query('attribute');
        $parentValue = $request->query('parent');
        $isRecursive = $request->query('recursive');
        $resource = $request->newResource();

        if ($isRecursive) {
            if (!$resource instanceof ChildSelectRecursiveHolder)
                throw new \Exception("The parent resource must implement: "
                    . ChildSelectRecursiveHolder::class .
                    " check the documentation for more info about the recursive search");
          $options =  $resource->onParentChanged($attribute, $parentValue);
        } else {
            $fields = $resource->updateFields($request);
            $field = $fields->findFieldByAttribute($attribute);
            if (!$options)
                throw new \Exception("Child wasn't not found try to use the recursive search instead");
            $options = $field->getOptions($parentValue);
        }
        
        $result = [];

        foreach ($options as $key => $option) {
            $result[] = [
                'label' => $option,
                'value' => $key,
            ];
        }

        return $result;
    }
}
