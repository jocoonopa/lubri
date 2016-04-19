<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\StoreGoalRequest;
use App\Model\Pos\Store\Store;
use App\Model\Pos\Store\StoreGoal;
use Carbon\Carbon;
use Input;

class StoreGoalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {               
        $year = Input::get('year', Carbon::now()->format('Y'));
        $this->createGoal($year);

        return view('pos.store_goal.index', ['goalGroups' => $this->getGoalGroups($year)]);
    }

    protected function getGoalGroups($year)
    {
        $goalGroups = [];

        for ($month = 1; $month <= 12; $month ++) {
            $goalGroups[$month] = StoreGoal::with(['store'])
                ->findByYear($year)
                ->findByMonth($month)
                ->get()
                ->sortBy(function ($goal) {
                return $goal->store->sn;
            });
        }
       
        return $goalGroups;
    }

    protected function createGoal($year)
    {
        Store::findActive()->get()->each(function ($store) use ($year) {
            for ($month = 1; $month <= 12; $month ++) {
                $goal = StoreGoal::findByStore($store)->findByYear($year)->findByMonth($month)->first();

                if (!$goal) {
                  
                    $goal = new StoreGoal;

                    $goal->store_id = $store->id;
                    $goal->year = $year;
                    $goal->month = $month;
                    
                    $goal->save();
                }
            } 
        });                       
    }

    public function update(StoreGoalRequest $request, StoreGoal $goal)
    {
        $attr = $request->get('attr');
        $goal->$attr = $request->get('val');
        $goal->save();

        return $goal->id;
    }
}
