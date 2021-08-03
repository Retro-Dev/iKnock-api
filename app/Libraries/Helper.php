<?php
/**
 * Created by Arman Sheikh
 * Braintree SDK 3.27.0
 * Date: 1/26/2018
 * Time: 8:14 PM
 */
namespace  App\Libraries;

//defined('BASEPATH') OR exit('No direct script access allowed');

//require_once 'braintree_sdk/lib/Braintree.php';

class Helper
{

    /**
     * Get 1 year month from today
     * @return array of year months
     */
    public static function getYearMonthsFromToday()
    {
        $current_month = date('n');
        $months = [];

        for($i=1;$i<=12;$i++)
        {
            $months[$current_month] = date('M', mktime(0, 0, 0, $current_month+1, 0, 0));
            if($current_month == 12) {
                $current_month = 1;
                $months[$current_month] = date('M', mktime(0, 0, 0, $current_month+1, 0, 0));
                $i++;
            }
            if($current_month == 1){
                $current_month = 12;
                $months[$current_month] = date('M', mktime(0, 0, 0, $current_month+1, 0, 0));
                $i++;
            }

            $current_month--;
        }
        return $months;
    }

    /**
     * Get 1 year month from today
     * @return days array
     */
    public static function getMonthDaysFromToday()
    {
        $month_days = [];
        for($i=0; $i<=30; $i++) {
            $date = date('d', strtotime("-$i days", strtotime(date('Y-m-d'))));
            $month_days[$date] = $date;

        }

        return $month_days;
    }

    /**
     * Get 1 week days from today
     * @return days array
     */
    public static function getWeekDaysFromToday()
    {
        $days   = [];
        $period = new \DatePeriod(
            new \DateTime(), // Start date of the period
            new \DateInterval('P1D'), // Define the intervals as Periods of 1 Day
            6 // Apply the interval 6 times on top of the starting date
        );

        foreach ($period as $day)
        {
            $day_index = $day->format('d');
            $days[$day_index] = $day->format('D');
        }
        return $days;
    }

    /**
     * Get 24 hours from current hour
     * @return hours array
     */
    public static function getHoursFromToday()
    {
        $day_hours = [];
        for ($i = 0; $i <= 24; $i++) {
            $date = date('H', strtotime("-$i hour", strtotime(date('Y-m-d H:i'))));
            $day_hours[$date] = $date;

        }
        return $day_hours;
    }

    public static function directory_exists($dirs)
    {
        $add = "";
        if (count($dirs) > 0) {
            foreach ($dirs as $dir) {
                $add .="/$dir";
                if (!is_dir($add)) {
                    mkdir($add, 0777, true);
                }
            }
        }
    }


}
