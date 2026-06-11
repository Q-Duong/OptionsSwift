<?php

namespace App\Builders;

use App\Models\Accountant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class AccountantBuilder extends Builder
{
    public function getAllAccountant()
    {
        $accountants = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('car_ktvs', 'car_ktvs.order_id', '=', 'orders.id')
            ->where('car_active', 1)
            ->orderBy('ord_start_day', 'ASC')
            ->select(
                'accountants.id',
                'accountants.order_id',
                'accountant_month',
                'accountant_distance',
                'accountant_deadline',
                'accountant_number',
                'accountant_date',
                'accountant_payment',
                'accountant_day_payment',
                'accountant_method',
                'accountant_amount_paid',
                'accountant_owe',
                'accountant_discount_day',
                'accountant_doctor_read',
                'accountant_doctor_date_payment',
                'accountant_35X43',
                'accountant_polime',
                'accountant_8X10',
                'accountant_10X12',
                'accountant_film_bag',
                'accountant_note',
                'accountant_status',
                'ord_start_day',
                'ord_form',
                'ord_note',
                'ord_cty_name',
                'order_vat',
                'order_quantity',
                'order_cost',
                'order_price',
                'order_percent_discount',
                'order_discount',
                'order_profit',
                'status_id',
                'car_name',
                'unit_name',
            )->get();
        return $accountants;
    }

    public function getAccountantForUpdateOrder($order_id)
    {
        $accountant = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->where('accountants.order_id', $order_id)
            ->select(
                'accountants.order_id',
                'order_quantity',
                'order_vat',
                'order_cost',
                'order_percent_discount',
                'order_price',
                'unit_code',
                'unit_name',
                'ord_cty_name',
                'ord_start_day',
                'ord_form',
                'ord_select',
                'status_id',
                'accountant_distance',
                'accountant_doctor_read',
                'accountant_35X43',
                'accountant_polime',
                'accountant_8X10',
                'accountant_10X12',
                'accountant_film_bag',
                'accountant_note',
                'accountant_status',
            )->first();

        return $accountant;
    }

    public function getAccountantByFilter($year, $type)
    {
        $accountants = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('car_ktvs', 'car_ktvs.order_id', '=', 'orders.id')
            ->where('car_active', 1)
            ->orderBy('ord_start_day', 'ASC')
            ->select(
                'accountants.id',
                'accountants.order_id',
                'accountant_month',
                'accountant_distance',
                'accountant_deadline',
                'accountant_number',
                'accountant_date',
                'accountant_payment',
                'accountant_day_payment',
                'accountant_method',
                'accountant_amount_paid',
                'accountant_owe',
                'accountant_discount_day',
                'accountant_doctor_read',
                'accountant_doctor_date_payment',
                'accountant_35X43',
                'accountant_polime',
                'accountant_8X10',
                'accountant_10X12',
                'accountant_film_bag',
                'accountant_note',
                'accountant_status',
                'ord_type',
                'ord_start_day',
                'ord_form',
                'ord_note',
                'ord_cty_name',
                'order_vat',
                'order_quantity',
                'order_cost',
                'order_price',
                'order_percent_discount',
                'order_discount',
                'order_profit',
                'status_id',
                'car_name',
                'unit_name',
            );
        if ($year != 'all') {
            $accountants->where(DB::raw('YEAR(ord_start_day)'), '=', $year);
        }
        if ($type != 'all') {
            $accountants->where('ord_type', $type);
        }
        return $accountants->get();
    }

    public function getAllContractByFilter($year)
    {
        $contracts = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->orderBy('accountant_number', 'ASC')
            ->select(
                'accountants.id',
                'accountants.order_id',
                'accountant_number',
                'accountant_date',
                'unit_name',
                'ord_start_day',
                'liquidation_number',
                'contract_type',
                'contract_date',
                'contract_status',
            );
        if ($year != 'all') {
            $contracts->where(DB::raw('YEAR(ord_start_day)'), '=', $year);
        }
        return $contracts->get()->unique('accountant_number')
            ->sortBy('accountant_number')
            ->values();
    }

    public function getStatistics($firstDayofThisMonth, $lastDayofThisMonth)
    {
        $statistics = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->whereBetween('order_details.ord_start_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->whereBetween('order_details.ord_end_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->select(
                'status_id',
                'order_quantity',
                'schedule_status',
                'accountant_35X43',
                'accountant_8X10',
                'accountant_10X12',
                'ord_select',
                'accountant_doctor_read'
            )
            ->orderBy('order_details.ord_start_day', 'ASC')
            ->get();
        return $statistics;
    }

    public function exportAccountant($firstDayofThisMonth, $lastDayofThisMonth)
    {
        $accountants = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('car_ktvs', 'car_ktvs.order_id', '=', 'orders.id')
            ->join('status', 'status.id', '=', 'orders.status_id')
            ->where('car_active', 1)
            ->select(
                'accountants.id',
                'accountants.order_id',
                'accountant_month',
                'accountant_distance',
                'accountant_deadline',
                'accountant_number',
                'accountant_date',
                'accountant_payment',
                'accountant_day_payment',
                'accountant_method',
                'accountant_amount_paid',
                'accountant_owe',
                'accountant_discount_day',
                'accountant_doctor_read',
                'accountant_doctor_date_payment',
                'accountant_35X43',
                'accountant_polime',
                'accountant_8X10',
                'accountant_10X12',
                'accountant_film_bag',
                'accountant_note',
                'accountant_status',
                'ord_start_day',
                'ord_type',
                'ord_form',
                'ord_note',
                'ord_cty_name',
                'order_vat',
                'order_quantity',
                'order_cost',
                'order_price',
                'order_percent_discount',
                'order_discount',
                'order_profit',
                'status_id',
                'car_name',
                'unit_code',
                'unit_name',
            )
            ->whereBetween('order_details.ord_start_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->whereBetween('order_details.ord_end_day', [$firstDayofThisMonth, $lastDayofThisMonth])
            ->orderBy('order_details.ord_start_day', 'ASC');
        return $accountants;
    }

    public function getQueryBuilderBySearchData($searchData, $year, $type)
    {

        $query = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('car_ktvs', 'car_ktvs.order_id', '=', 'orders.id')
            ->where('car_active', 1)
            ->orderBy('ord_start_day', 'ASC')
            ->select(
                'accountants.id',
                'accountants.order_id',
                'accountant_month',
                'accountant_distance',
                'accountant_deadline',
                'accountant_number',
                'accountant_date',
                'accountant_payment',
                'accountant_day_payment',
                'accountant_method',
                'accountant_amount_paid',
                'accountant_owe',
                'accountant_discount_day',
                'accountant_doctor_read',
                'accountant_doctor_date_payment',
                'accountant_35X43',
                'accountant_polime',
                'accountant_8X10',
                'accountant_10X12',
                'accountant_film_bag',
                'accountant_note',
                'accountant_status',
                'ord_type',
                'ord_start_day',
                'ord_form',
                'ord_note',
                'ord_cty_name',
                'order_vat',
                'order_quantity',
                'order_cost',
                'order_price',
                'order_percent_discount',
                'order_discount',
                'order_profit',
                'status_id',
                'car_name',
                'unit_name',
            );

        if ($year != 'all') {
            $query->where(DB::raw('YEAR(ord_start_day)'), '=', $year);
        }
        if ($type != 'all') {
            $query->where('ord_type', $type);
        }
        //Order Id
        if (isset($searchData['order_id']) && !empty($searchData['order_id'])) {
            $query->whereIn('accountants.order_id', explode(',', $searchData['order_id']));
        }
        //Status Id
        if (isset($searchData['status_id']) && !empty($searchData['status_id'])) {
            $query->whereIn('status_id', explode(',', $searchData['status_id']));
        }
        //Car Name
        if (isset($searchData['car_name']) && !empty($searchData['car_name'])) {
            $query->whereIn('car_name', explode(',', $searchData['car_name']));
        }
        //Unit Name
        if (isset($searchData['unit_name']) && !empty($searchData['unit_name'])) {
            $query->whereIn('unit_name', explode(',', $searchData['unit_name']));
        }
        //Order Start Day
        if (isset($searchData['ord_start_day']) && !empty($searchData['ord_start_day'])) {
            $query->whereIn('ord_start_day', explode(',', $searchData['ord_start_day']));
        }
        //Cty Name
        if (isset($searchData['ord_cty_name']) && !empty($searchData['ord_cty_name'])) {
            $query->whereIn('ord_cty_name', explode(',', $searchData['ord_cty_name']));
        }
        //Order Form
        if (isset($searchData['ord_form']) && !empty($searchData['ord_form'])) {
            $query->whereIn('ord_form', explode(',', $searchData['ord_form']));
        }
        //Order Note
        if (isset($searchData['ord_note'])) {
            if ($searchData['ord_note'] == 'empty') {
                $query->whereNull('ord_note');
            } else {
                $query->whereIn('ord_note', explode(',', $searchData['ord_note']));
            }
        }
        //Accountant Month
        if (isset($searchData['accountant_month']) && !empty($searchData['accountant_month'])) {
            $query->whereIn('accountant_month', explode(',', $searchData['accountant_month']));
        }
        //Accountant Distance
        if (isset($searchData['accountant_distance']) && !empty($searchData['accountant_distance'])) {
            $query->whereIn('accountant_distance', explode(',', $searchData['accountant_distance']));
        }
        //Accountant Deadline
        if (isset($searchData['accountant_deadline']) && !empty($searchData['accountant_deadline'])) {
            if ($searchData['accountant_deadline'] == 'empty') {
                $query->whereNull('accountant_deadline');
            } else {
                $query->whereIn('accountant_deadline', explode(',', $searchData['accountant_deadline']));
            }
        }
        //Accountant Number
        if (isset($searchData['accountant_number'])) {
            if ($searchData['accountant_number'] == 'empty') {
                $query->whereNull('accountant_number');
            } else {
                $query->whereIn('accountant_number', explode(',', $searchData['accountant_number']));
            }
        }
        //Accountant Date
        if (isset($searchData['accountant_date']) && !empty($searchData['accountant_date'])) {
            if ($searchData['accountant_date'] == 'empty') {
                $query->whereNull('accountant_date');
            } else {
                $query->whereIn('accountant_date', explode(',', $searchData['accountant_date']));
            }
        }
        //Accountant Status
        if (isset($searchData['accountant_status'])) {
            $query->whereIn('accountant_status', explode(',', $searchData['accountant_status']));
        }
        //Accountant Day Payment
        if (isset($searchData['accountant_day_payment']) && !empty($searchData['accountant_day_payment'])) {
            if ($searchData['accountant_day_payment'] == 'empty') {
                $query->whereNull('accountant_day_payment');
            } else {
                $query->whereIn('accountant_day_payment', explode(',', $searchData['accountant_day_payment']));
            }
        }
        //Accountant Method
        if (isset($searchData['accountant_method']) && !empty($searchData['accountant_method'])) {
            if ($searchData['accountant_method'] == 'empty') {
                $query->whereNull('accountant_method');
            } else {
                $query->whereIn('accountant_method', explode(',', $searchData['accountant_method']));
            }
        }
        //Accountant Amount Paid
        if (isset($searchData['accountant_amount_paid'])) {
            $query->whereIn('accountant_amount_paid', explode(',', $searchData['accountant_amount_paid']));
        }
        //Accountant Owe
        if (isset($searchData['accountant_owe'])) {
            $query->whereIn('accountant_owe', explode(',', $searchData['accountant_owe']));
        }
        //Accountant Discount Day
        if (isset($searchData['accountant_discount_day']) && !empty($searchData['accountant_discount_day'])) {
            if ($searchData['accountant_discount_day'] == 'empty') {
                $query->whereNull('accountant_discount_day');
            } else {
                $query->whereIn('accountant_discount_day', explode(',', $searchData['accountant_discount_day']));
            }
        }
        //Accountant Doctor Read
        if (isset($searchData['accountant_doctor_read']) && !empty($searchData['accountant_doctor_read'])) {
            if ($searchData['accountant_doctor_read'] == 'empty') {
                $query->whereNull('accountant_doctor_read');
            } else {
                $query->whereIn('accountant_doctor_read', explode(',', $searchData['accountant_doctor_read']));
            }
        }
        //Accountant Doctor Date Payment
        if (isset($searchData['accountant_doctor_date_payment']) && !empty($searchData['accountant_doctor_date_payment'])) {
            if ($searchData['accountant_doctor_date_payment'] == 'empty') {
                $query->whereNull('accountant_doctor_date_payment');
            } else {
                $query->whereIn('accountant_doctor_date_payment', explode(',', $searchData['accountant_doctor_date_payment']));
            }
        }
        //Accountant 35X43
        if (isset($searchData['accountant_35X43'])) {
            if ($searchData['accountant_35X43'] == 'empty') {
                $query->whereNull('accountant_35X43');
            } else {
                $query->whereIn('accountant_35X43', explode(',', $searchData['accountant_35X43']));
            }
        }
        //Accountant Polime
        if (isset($searchData['accountant_polime'])) {
            if ($searchData['accountant_polime'] == 'empty') {
                $query->whereNull('accountant_polime');
            } else {
                $query->whereIn('accountant_polime', explode(',', $searchData['accountant_polime']));
            }
        }
        //Accountant 8X10
        if (isset($searchData['accountant_8X10'])) {
            if ($searchData['accountant_8X10'] == 'empty') {
                $query->whereNull('accountant_8X10');
            } else {
                $query->whereIn('accountant_8X10', explode(',', $searchData['accountant_8X10']));
            }
        }
        //Accountant 10X12
        if (isset($searchData['accountant_10X12'])) {
            if ($searchData['accountant_10X12'] == 'empty') {
                $query->whereNull('accountant_10X12');
            } else {
                $query->whereIn('accountant_10X12', explode(',', $searchData['accountant_10X12']));
            }
        }
        //Accountant Film Bag
        if (isset($searchData['accountant_film_bag'])) {
            if ($searchData['accountant_film_bag'] == 'empty') {
                $query->whereNull('accountant_film_bag');
            } else {
                $query->whereIn('accountant_film_bag', explode(',', $searchData['accountant_film_bag']));
            }
        }
        //Accountant Note
        if (isset($searchData['accountant_note'])) {
            if ($searchData['accountant_note'] == 'empty') {
                $query->whereNull('accountant_note');
            } else {
                $query->whereIn('accountant_note', explode(',', $searchData['accountant_note']));
            }
        }
        //Order VAT
        if (isset($searchData['order_vat'])) {
            if ($searchData['order_vat'] == 'empty') {
                $query->whereNull('order_vat');
            } else {
                $query->whereIn('order_vat', explode(',', $searchData['order_vat']));
            }
        }
        //Order Quantity
        if (isset($searchData['order_quantity'])) {
            $query->whereIn('order_quantity', explode(',', $searchData['order_quantity']));
        }
        //Order Cost
        if (isset($searchData['order_cost'])) {
            $query->whereIn('order_cost', explode(',', $searchData['order_cost']));
        }
        //Order Price
        if (isset($searchData['order_price'])) {
            $query->whereIn('order_price', explode(',', $searchData['order_price']));
        }
        //Order Percent Discount
        if (isset($searchData['order_percent_discount'])) {
            if ($searchData['order_percent_discount'] == 'empty') {
                $query->whereNull('order_percent_discount');
            } else {
                $query->whereIn('order_percent_discount', explode(',', $searchData['order_percent_discount']));
            }
        }
        //Order Discount
        if (isset($searchData['order_discount'])) {
            $query->whereIn('order_discount', explode(',', $searchData['order_discount']));
        }
        //Order Profit
        if (isset($searchData['order_profit'])) {
            $query->whereIn('order_profit', explode(',', $searchData['order_profit']));
        }

        return $query;
    }

    public function getContractQueryBuilderBySearchData($searchData, $year)
    {

        $query = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->orderBy('accountant_number', 'ASC')
            ->select(
                'accountants.id',
                'accountants.order_id',
                'accountant_number',
                'accountant_date',
                'unit_name',
                'ord_start_day',
                'liquidation_number',
                'contract_type',
                'contract_date',
                'contract_status',
            );

        if ($year != 'all') {
            $query->where(DB::raw('YEAR(ord_start_day)'), '=', $year);
        }
        //Unit Name
        if (isset($searchData['unit_name']) && !empty($searchData['unit_name'])) {
            $query->whereIn('unit_name', explode(',', $searchData['unit_name']));
        }
        //Accountant Number
        if (isset($searchData['accountant_number'])) {
            $query->whereIn('accountant_number', explode(',', $searchData['accountant_number']));
        }
        //Accountant Date
        if (isset($searchData['accountant_date']) && !empty($searchData['accountant_date'])) {
            $query->whereIn('accountant_date', explode(',', $searchData['accountant_date']));
        }
        //Liquidation Number
        if (isset($searchData['liquidation_number']) && !empty($searchData['liquidation_number'])) {
            if ($searchData['liquidation_number'] == 'empty') {
                $query->whereNull('liquidation_number');
            } else {
                $query->whereIn('liquidation_number', explode(',', $searchData['liquidation_number']));
            }
        }
        //Contract Type
        if (isset($searchData['contract_type']) && !empty($searchData['contract_type'])) {
            if ($searchData['contract_type'] == 'empty') {
                $query->whereNull('contract_type');
            } else {
                $query->whereIn('contract_type', explode(',', $searchData['contract_type']));
            }
        }
        //Contract Date
        if (isset($searchData['contract_date']) && !empty($searchData['contract_date'])) {
            if ($searchData['contract_date'] == 'empty') {
                $query->whereNull('contract_date');
            } else {
                $query->whereIn('contract_date', explode(',', $searchData['contract_date']));
            }
        }
        //Contract Status
        if (isset($searchData['contract_status'])) {
            $query->whereIn('contract_status', explode(',', $searchData['contract_status']));
        }
        return $query->get()->unique('accountant_number')
            ->sortBy('accountant_number')
            ->values();
    }

    public function renewFilterWhenUpdated($currentChange, $year)
    {
        $filters = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->join('car_ktvs', 'car_ktvs.order_id', '=', 'orders.id')
            ->where('car_active', 1)
            ->orderBy('ord_start_day', 'ASC')
            ->select($currentChange);
        if ($year != 'all') {
            $filters->where(DB::raw('YEAR(ord_start_day)'), '=', $year);
        }
        $filters->get();
        $paramsNull = ['accountant_deadline', 'accountant_number', 'accountant_date', 'accountant_day_payment', 'order_percent_discount', 'accountant_discount_day', 'accountant_doctor_date_payment', 'accountant_35X43', 'accountant_polime', 'accountant_8X10', 'accountant_10X12', 'accountant_film_bag'];
        if ($currentChange == 'order_vat') {
            $uniqueFilters = $filters->where($currentChange, '!=', null)->pluck($currentChange)->map(function ($item) {
                return mb_strtoupper($item, 'UTF-8');
            })->unique()->sort();
        } elseif (in_array($currentChange, $paramsNull)) {
            $uniqueFilters = $filters->where($currentChange, '!=', null)->pluck($currentChange)->unique()->sort();
        } else {
            $uniqueFilters = $filters->pluck($currentChange)->unique()->sort();
        }

        return $uniqueFilters;
    }

    public function renewFilterWhenContractUpdated($currentChange, $year)
    {
        $filters = Accountant::join('orders', 'orders.id', '=', 'accountants.order_id')
            ->join('units', 'units.id', '=', 'orders.unit_id')
            ->join('order_details', 'order_details.id', '=', 'orders.order_detail_id')
            ->orderBy('accountant_number', 'ASC')
            ->select($currentChange);
        if ($year != 'all') {
            $filters->where(DB::raw('YEAR(ord_start_day)'), '=', $year);
        }
        $filters->get();
        $paramsNull = ['liquidation_number', 'contract_date'];
        if (in_array($currentChange, $paramsNull)) {
            $uniqueFilters = $filters->where($currentChange, '!=', null)->pluck($currentChange)->unique()->sort();
        } else {
            $uniqueFilters = $filters->pluck($currentChange)->unique()->sort();
        }

        return $uniqueFilters;
    }
}
