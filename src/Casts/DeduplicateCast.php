<?php

namespace Michaelr0\LaravelDedupe\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DeduplicateCast implements CastsAttributes
{
    protected string $dedupe_key;
    protected bool $associative;

    /**
     * Create a new cast class instance.
     *
     * @param  string|null  $dedup_key
     * @return void
     */
    public function __construct(string $dedupe_key = 'dedup_id', bool $associative = true)
    {
        $this->dedupe_key = $dedupe_key;
        $this->associative = $associative;
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, string $key, $value, array $attributes): array|object
    {
        if ($decoded = json_decode($value, true)) {
            $value = $decoded;

            if ( ! empty($value[$this->dedupe_key]) && $parent = $model::find($value[$this->dedupe_key], $key)) {
                $value = collect($parent->$key)->replaceRecursive($value)->toArray();
            }
        }

        return $this->associative ? (array) $value : (object) $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        if ( ! empty($value[$this->dedupe_key]) && $parent = $model::find($value[$this->dedupe_key], $key)) {
            $value = $this->diff_recursive(
                (array) $parent->$key,
                (array) $value
            );
        }

        return json_encode($value);
    }

    /**
     * Recursive differences between $parent_array and $child_array.
     * @param array $parent_array
     * @param array $child_array
     * @return array
     */
    protected function diff_recursive(array $parent_array, array $child_array): array
    {
        $diff_array = $child_array;

        foreach (collect($parent_array)->except($this->dedupe_key) as $key => $val) {
            if (isset($parent_array[$key], $child_array[$key]) && is_iterable($parent_array[$key]) && is_iterable($child_array[$key])) {
                $diff_array[$key] = $this->diff_recursive(
                    (array) $parent_array[$key],
                    (array) $child_array[$key]
                );
            } elseif (isset($parent_array[$key], $child_array[$key]) && $parent_array[$key] === $child_array[$key]) {
                unset($diff_array[$key]);
            }
        }

        return $diff_array;
    }
}
