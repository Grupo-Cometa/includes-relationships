<?php

namespace GrupoCometa\Includes\Query;

use GrupoCometa\Builder\QueryString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CountRelationship
{

    private array|string $count;

    public function __construct(private Builder|HasMany|BelongsTo|HasOne|BelongsToMany $builder, private Request $request)
    {
        $function = gettype($this->request->count) . 'BuildWithCount';
        $this->$function();
    }

    private function arrayBuildWithCount()
    {
        foreach ($this->request->count as $relation => $paramns) {

            if (gettype($relation) == 'integer') $relation = $paramns;

            $this->count[$relation] =  function ($query) use ($paramns) {
                if (gettype($paramns) == 'string') return $query->where('id', '<>', null);
                (new QueryString($query, $paramns))->getBuilder();
            };
        }

        $this->builder = $this->builder->withCount($this->count);
    }

    private function  stringBuildWithCount()
    {
        $this->count = explode(',', $this->request->count);
        $this->builder = $this->builder->withCount($this->count);
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}
