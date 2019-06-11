<?php

namespace Alvinhu\ChildSelect;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;

class OptionsController extends Controller
{
    public function index(NovaRequest $request)
    {
        $attribute = $request->query('attribute');
        $parentValue = $request->query('parent');
        $searchAlgorithm = $request->query('searchAlgorithm');
        $resource = $request->newResource();

        if (Str::endsWith($searchAlgorithm,'_recursive')) {

            if (!$resource instanceof ChildSelectRecursiveHolder)
                throw new \Exception("The parent resource must implement: "
                    . ChildSelectRecursiveHolder::class .
                    " check the documentation for more info about the recursive search");

          $options =  $resource->onParentChanged($attribute, $parentValue);
        } else {

            $fields = $resource->updateFields($request);
            $field = $fields->findFieldByAttribute($attribute);


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
