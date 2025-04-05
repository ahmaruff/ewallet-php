<?php

namespace App\Commands;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;


class ModelFilterQueryCommand
{

    public static function search(Builder $query, string $field='name',$keyword=null) : Builder
    {
        if($keyword){
            return $query->where($field, 'like', '%'.$keyword.'%');
        }
        return $query;
    }

    public static function filterByDate(Builder $query, string $column='created_at', string $startDate = null, string $endDate = null, $date=null, $timezone="Asia/Jakarta") : Builder
    {
        // if date is present, ignore startDate & endDate
        if(!is_null($date)) {
            $startDateParsed = Carbon::parse($date, $timezone)->startOfDay()->setTimezone('UTC');
            $endDateParsed = Carbon::parse($date, $timezone)->endOfDay()->setTimezone('UTC');
            $query = $query->whereBetween($column, [$startDateParsed, $endDateParsed]);
            return $query;
        }

        if(!is_null($startDate)) {
            $startDateParsed = Carbon::parse($startDate, $timezone)->startOfDay()->setTimezone('UTC');
            $query = $query->where($column, '>=', $startDateParsed);
        }

        if(!is_null($endDate)) {
            $endDateParsed = Carbon::parse($endDate, $timezone)->endOfDay()->setTimezone('UTC');
            $query = $query->where($column, '<=', $endDateParsed);
        }

        return $query;
    }

    public static function orderByField(Builder $query, $field='created_at', $direction = 'asc'): Builder
    {
        return $query->orderBy($field, $direction);
    }

    public static function paginate(Builder $query, $perPage=10, $page=1) : LengthAwarePaginator
    {
        return $query->paginate($perPage,['*'],'page', $page);
    }
}
